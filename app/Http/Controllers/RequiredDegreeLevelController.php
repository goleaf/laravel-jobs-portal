<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRequiredDegreeLevelRequest;
use App\Http\Requests\DestroyRequiredDegreeLevelRequest;
use App\Http\Requests\DropdownRequiredDegreeLevelRequest;
use App\Http\Requests\IndexRequiredDegreeLevelRequest;
use App\Http\Requests\ShowRequiredDegreeLevelRequest;
use App\Http\Requests\UpdateRequiredDegreeLevelRequest;
use App\Http\Resources\RequiredDegreeLevelResource;
use App\Models\RequiredDegreeLevel;
use App\Repositories\RequiredDegreeLevelRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequiredDegreeLevelController extends AppBaseController
{
    /** @var RequiredDegreeLevelRepository */
    private $requiredDegreeLevelRepository;

    public function __construct(RequiredDegreeLevelRepository $requiredDegreeLevelRepo)
    {
        $this->requiredDegreeLevelRepository = $requiredDegreeLevelRepo;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(IndexRequiredDegreeLevelRequest $request)
    {
        try {
            $query = RequiredDegreeLevel::query();

            // Apply scopes based on request parameters
            if ($request->has('active')) {
                $query = $request->boolean('active') ? $query->active() : $query->inactive();
            }

            if ($request->has('default')) {
                $query = $request->boolean('default') ? $query->default() : $query->custom();
            }

            if ($request->filled('search')) {
                $query->search($request->input('search'));
            }

            if ($request->has('with_jobs')) {
                $query = $request->boolean('with_jobs') ? $query->withJobs() : $query->withoutJobs();
            }

            // Apply sorting
            $sortBy = $request->input('sort_by', 'name');
            $sortDirection = $request->input('sort_direction', 'asc');

            if ('alphabetical' === $sortBy) {
                $query->alphabetical();
            } elseif ('popular' === $sortBy) {
                $query->popular();
            } else {
                $query->orderBy($sortBy, $sortDirection);
            }

            $requiredDegreeLevels = $query->paginate($request->input('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => __('required_degree_levels.index.success'),
                'data' => RequiredDegreeLevelResource::collection($requiredDegreeLevels),
                'meta' => [
                    'total' => $requiredDegreeLevels->total(),
                    'per_page' => $requiredDegreeLevels->perPage(),
                    'current_page' => $requiredDegreeLevels->currentPage(),
                    'last_page' => $requiredDegreeLevels->lastPage(),
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Required Degree Level index error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('required_degree_levels.index.error'),
                'error' => config('app.debug') ? $e->getMessage() : __('common.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequiredDegreeLevelRequest $request)
    {
        try {
            $requiredDegreeLevel = RequiredDegreeLevel::create($request->validated());

            Log::info('Required Degree Level created', [
                'id' => $requiredDegreeLevel->id,
                'name' => $requiredDegreeLevel->name,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('required_degree_levels.store.success'),
                'data' => new RequiredDegreeLevelResource($requiredDegreeLevel),
            ], 201);
        } catch (\Exception $e) {
            Log::error('Required Degree Level creation error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('required_degree_levels.store.error'),
                'error' => config('app.debug') ? $e->getMessage() : __('common.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(ShowRequiredDegreeLevelRequest $request, RequiredDegreeLevel $requiredDegreeLevel)
    {
        try {
            // Load relationships if requested
            if ($request->has('include')) {
                $includes = explode(',', $request->input('include'));
                $allowedIncludes = ['jobs', 'candidates'];
                $validIncludes = array_intersect($includes, $allowedIncludes);

                if (!empty($validIncludes)) {
                    $requiredDegreeLevel->load($validIncludes);
                }
            }

            return response()->json([
                'success' => true,
                'message' => __('required_degree_levels.show.success'),
                'data' => new RequiredDegreeLevelResource($requiredDegreeLevel),
            ]);
        } catch (\Exception $e) {
            Log::error('Required Degree Level show error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('required_degree_levels.show.error'),
                'error' => config('app.debug') ? $e->getMessage() : __('common.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequiredDegreeLevelRequest $request, RequiredDegreeLevel $requiredDegreeLevel)
    {
        try {
            $oldData = $requiredDegreeLevel->toArray();
            $requiredDegreeLevel->update($request->validated());

            Log::info('Required Degree Level updated', [
                'id' => $requiredDegreeLevel->id,
                'old_data' => $oldData,
                'new_data' => $requiredDegreeLevel->fresh()->toArray(),
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('required_degree_levels.update.success'),
                'data' => new RequiredDegreeLevelResource($requiredDegreeLevel->fresh()),
            ]);
        } catch (\Exception $e) {
            Log::error('Required Degree Level update error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('required_degree_levels.update.error'),
                'error' => config('app.debug') ? $e->getMessage() : __('common.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DestroyRequiredDegreeLevelRequest $request, RequiredDegreeLevel $requiredDegreeLevel)
    {
        try {
            // Check if required degree level has associated jobs
            if ($requiredDegreeLevel->jobs()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => __('required_degree_levels.destroy.has_jobs'),
                ], 422);
            }

            // Check if required degree level has associated candidates
            if ($requiredDegreeLevel->candidates()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => __('required_degree_levels.destroy.has_candidates'),
                ], 422);
            }

            $requiredDegreeLevelData = $requiredDegreeLevel->toArray();
            $requiredDegreeLevel->delete();

            Log::info('Required Degree Level deleted', [
                'deleted_data' => $requiredDegreeLevelData,
                'user_id' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => __('required_degree_levels.destroy.success'),
            ]);
        } catch (\Exception $e) {
            Log::error('Required Degree Level deletion error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('required_degree_levels.destroy.error'),
                'error' => config('app.debug') ? $e->getMessage() : __('common.something_went_wrong'),
            ], 500);
        }
    }

    /**
     * Get active required degree levels for dropdowns.
     */
    public function dropdown(DropdownRequiredDegreeLevelRequest $request)
    {
        try {
            $requiredDegreeLevels = RequiredDegreeLevel::active()
                ->alphabetical()
                ->select('id', 'name')
                ->get()
            ;

            return response()->json([
                'success' => true,
                'message' => __('required_degree_levels.dropdown.success'),
                'data' => $requiredDegreeLevels->map(function ($level) {
                    return [
                        'value' => $level->id,
                        'label' => $level->name,
                        'text' => $level->name,
                    ];
                }),
            ]);
        } catch (\Exception $e) {
            Log::error('Required Degree Level dropdown error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => __('required_degree_levels.dropdown.error'),
                'error' => config('app.debug') ? $e->getMessage() : __('common.something_went_wrong'),
            ], 500);
        }
    }
}

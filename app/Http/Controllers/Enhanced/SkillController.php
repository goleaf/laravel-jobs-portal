<?php

namespace App\Http\Controllers\Enhanced;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Skill\CreateSkillRequest;
use App\Http\Requests\Skill\DestroySkillRequest;
use App\Http\Requests\Skill\IndexSkillRequest;
use App\Http\Requests\Skill\ShowSkillRequest;
use App\Http\Requests\Skill\UpdateSkillUpdateSkillRequest;
use App\Models\Skill;
use App\Repositories\SkillRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Enhanced SkillController - Enhanced patterns implementation.
 *
 * Demonstrates modern Laravel controller patterns with:
 * - Advanced caching strategies
 * - Comprehensive error handling
 * - Performance optimization
 * - Enhanced repository usage
 * - Bulk operations support
 */
class SkillController extends AppBaseController
{
    /**
     * Cache TTL for skill-related operations (1 hour).
     */
    private const CACHE_TTL = 3600;

    /** @var SkillRepository */
    private $skillRepository;

    public function __construct(SkillRepository $skillRepository)
    {
        $this->skillRepository = $skillRepository;
    }

    /**
     * Display a listing of skills with enhanced filtering and search.
     */
    public function index(IndexSkillRequest $request)
    {
        try {
            // Check if this is an API request
            if ($this->isApiRequest($request)) {
                return $this->getSkillsApi($request);
            }

            // For web requests, return the view with enhanced data
            $data = $this->prepareSkillsIndexData($request);

            return view('skills.index', $data);
        } catch (\Exception $e) {
            Log::error('Error in SkillController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            if ($this->isApiRequest($request)) {
                return $this->sendServerError('Failed to retrieve skills');
            }

            return redirect()->back()->with('error', 'Failed to load skills');
        }
    }

    /**
     * Store a newly created skill with enhanced validation.
     */
    public function store(CreateSkillRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $input = $request->validated();

            // Add default values
            $input['is_active'] = $input['is_active'] ?? true;
            $input['created_by'] = auth()->id();

            $skill = $this->skillRepository->create($input);

            // Clear related caches
            $this->clearSkillCaches();

            // Log the creation
            Log::info('Skill created successfully', [
                'skill_id' => $skill->id,
                'skill_name' => $skill->name,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->sendResponse($skill->load(['jobs', 'candidates']), __('messages.flash.skill_save'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating skill', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            return $this->sendServerError('Failed to create skill');
        }
    }

    /**
     * Display the specified skill with enhanced data loading.
     */
    public function show(Skill $skill, ShowSkillRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->buildCacheKey('skill.show', $skill->id);

            $skillData = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($skill) {
                return $skill->load([
                    'jobs' => function ($query) {
                        $query->active()->with(['company', 'jobType'])->latest()->limit(10);
                    },
                    'candidates' => function ($query) {
                        $query->active()->with(['user'])->latest()->limit(10);
                    },
                ]);
            });

            // Get skill statistics using model scopes
            $statistics = [
                'total_jobs' => $skill->jobs()->active()->count(),
                'total_candidates' => $skill->candidates()->active()->count(),
                'recent_jobs' => $skill->jobs()->active()->recent(30)->count(),
                'recent_candidates' => $skill->candidates()->active()->recent(30)->count(),
            ];

            return $this->sendResponse([
                'skill' => $skillData,
                'statistics' => $statistics,
            ], 'Skill retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error retrieving skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to retrieve skill');
        }
    }

    /**
     * Update the specified skill with enhanced validation.
     */
    public function update(UpdateSkillUpdateSkillRequest $request, Skill $skill): JsonResponse
    {
        try {
            DB::beginTransaction();

            $input = $request->validated();
            $input['updated_by'] = auth()->id();

            $this->skillRepository->update($input, $skill->id);

            // Clear related caches
            $this->clearSkillCaches($skill->id);

            // Log the update
            Log::info('Skill updated successfully', [
                'skill_id' => $skill->id,
                'skill_name' => $skill->name,
                'updated_by' => auth()->id(),
                'changes' => $skill->getChanges(),
            ]);

            DB::commit();

            return $this->sendSuccess(__('messages.flash.skill_update'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);

            return $this->sendServerError('Failed to update skill');
        }
    }

    /**
     * Remove the specified skill with enhanced dependency checking.
     */
    public function destroy(Skill $skill, DestroySkillRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Enhanced dependency checking using model relationships and scopes
            $jobsCount = $skill->jobs()->active()->count();
            $candidatesCount = $skill->candidates()->active()->count();

            if ($jobsCount > 0 || $candidatesCount > 0) {
                return $this->sendError(
                    __('messages.flash.skill_cant_delete'),
                    [
                        'jobs_count' => $jobsCount,
                        'candidates_count' => $candidatesCount,
                        'message' => 'Skill is being used by jobs or candidates and cannot be deleted',
                    ],
                    422
                );
            }

            // Log before deletion
            Log::info('Skill deletion initiated', [
                'skill_id' => $skill->id,
                'skill_name' => $skill->name,
                'deleted_by' => auth()->id(),
            ]);

            $skill->delete();

            // Clear related caches
            $this->clearSkillCaches($skill->id);

            DB::commit();

            return $this->sendSuccess(__('messages.flash.skill_delete'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting skill', [
                'skill_id' => $skill->id,
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to delete skill');
        }
    }

    /**
     * Get skills for autocomplete/select inputs.
     */
    public function getSkillsForSelect(Request $request): JsonResponse
    {
        try {
            $cacheKey = $this->buildCacheKey('skills.select', $request->get('search', ''));

            $skills = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
                $query = Skill::active();

                if ($request->filled('search')) {
                    $query->search($request->get('search'));
                }

                return $query->alphabetical()
                    ->limit(50)
                    ->get(['id', 'name'])
                    ->map(function ($skill) {
                        return [
                            'id' => $skill->id,
                            'text' => $skill->name,
                            'name' => $skill->name,
                        ];
                    });
            });

            return $this->sendResponse($skills, 'Skills retrieved for selection');
        } catch (\Exception $e) {
            Log::error('Error retrieving skills for select', [
                'error' => $e->getMessage(),
                'search' => $request->get('search'),
            ]);

            return $this->sendServerError('Failed to retrieve skills');
        }
    }

    /**
     * Bulk operations for skills.
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'exists:skills,id',
        ]);

        try {
            DB::beginTransaction();

            $skillIds = $request->get('skill_ids');
            $action = $request->get('action');
            $affectedCount = 0;

            switch ($action) {
                case 'activate':
                    $affectedCount = Skill::whereIn('id', $skillIds)->update(['is_active' => true]);

                    break;

                case 'deactivate':
                    $affectedCount = Skill::whereIn('id', $skillIds)->update(['is_active' => false]);

                    break;

                case 'delete':
                    // Check for dependencies before deletion using scopes
                    $skillsWithDependencies = Skill::whereIn('id', $skillIds)
                        ->where(function ($query) {
                            $query->has('jobs')->orHas('candidates');
                        })
                        ->pluck('name')
                        ->toArray();

                    if (! empty($skillsWithDependencies)) {
                        return $this->sendError(
                            'Some skills cannot be deleted as they are in use',
                            ['skills_in_use' => $skillsWithDependencies],
                            422
                        );
                    }

                    $affectedCount = Skill::whereIn('id', $skillIds)->delete();

                    break;
            }

            // Clear caches
            $this->clearSkillCaches();

            // Log bulk action
            Log::info('Bulk skill action performed', [
                'action' => $action,
                'skill_ids' => $skillIds,
                'affected_count' => $affectedCount,
                'performed_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->sendSuccess("Successfully {$action}d {$affectedCount} skill(s)");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error performing bulk skill action', [
                'action' => $request->get('action'),
                'skill_ids' => $request->get('skill_ids'),
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to perform bulk action');
        }
    }

    /**
     * Get skills for API requests with enhanced filtering.
     */
    private function getSkillsApi(Request $request): JsonResponse
    {
        $cacheKey = $this->buildCacheKey('skills.api', $request->all());

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            $query = Skill::query();

            // Apply Enhanced scopes for filtering
            if ($request->filled('search')) {
                $query->search($request->get('search'));
            }

            if ($request->filled('is_active')) {
                $query->active();
            }

            if ($request->filled('category')) {
                $query->byCategory($request->get('category'));
            }

            if ($request->filled('popular')) {
                $query->popular();
            }

            // Apply sorting
            $sortBy = $request->get('sort', 'name');
            $sortDirection = $request->get('direction', 'asc');

            if (in_array($sortBy, ['name', 'created_at', 'updated_at'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->alphabetical();
            }

            return $query->paginate($request->get('per_page', 15));
        });

        return $this->sendPaginatedResponse($data, 'Skills retrieved successfully');
    }

    /**
     * Prepare data for skills index view.
     */
    private function prepareSkillsIndexData(Request $request): array
    {
        $cacheKey = $this->buildCacheKey('skills.index.data', $request->all());

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            // Get skills with enhanced scopes
            $skills = Skill::with(['jobs', 'candidates'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->search($request->get('search'));
                })
                ->when($request->filled('category'), function ($query) use ($request) {
                    $query->byCategory($request->get('category'));
                })
                ->active()
                ->alphabetical()
                ->paginate(20);

            // Get skill statistics using model scopes
            $statistics = [
                'total_skills' => Skill::count(),
                'active_skills' => Skill::active()->count(),
                'skills_with_jobs' => Skill::has('jobs')->count(),
                'skills_with_candidates' => Skill::has('candidates')->count(),
                'most_popular_skill' => Skill::popular()->first()?->name ?? 'N/A',
                'recent_skills_count' => Skill::recent(7)->count(),
            ];

            // Get popular skills using scopes
            $popularSkills = Skill::popular()->limit(10)->get();

            return [
                'skills' => $skills,
                'statistics' => $statistics,
                'popularSkills' => $popularSkills,
                'filters' => $request->only(['search', 'category']),
            ];
        });
    }

    /**
     * Clear skill-related caches.
     */
    private function clearSkillCaches(?int $skillId = null): void
    {
        $cacheKeys = [
            'skills.statistics',
            'skills.api.*',
            'skills.index.data.*',
            'skills.select.*',
        ];

        if ($skillId) {
            $cacheKeys[] = "skill.show.{$skillId}";
        }

        foreach ($cacheKeys as $pattern) {
            if (str_contains($pattern, '*')) {
                Cache::tags(['skills'])->flush();
            } else {
                Cache::forget($pattern);
            }
        }
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profession;
use App\Models\ProfessionCategory;
use App\Http\Requests\StoreProfessionRequest;
use App\Http\Requests\UpdateProfessionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfessionController extends Controller
{
    /**
     * Display a listing of professions
     */
    public function index(Request $request): JsonResponse
    {
        $query = Profession::with(['translations', 'category.translations'])
            ->active()
            ->ordered();

        // Filter by category
        if ($request->has('category_id')) {
            $query->inCategory($request->integer('category_id'));
        }

        // Filter by skill level
        if ($request->has('skill_level')) {
            $query->skillLevel($request->get('skill_level'));
        }

        // Filter by featured
        if ($request->boolean('featured_only')) {
            $query->featured();
        }

        // Search by name/description in current locale
        if ($request->has('search')) {
            $locale = $request->get('locale', app()->getLocale());
            $searchTerm = $request->get('search');
            $query->search($searchTerm, $locale);
        }

        // Filter by ISCO code
        if ($request->has('isco_code')) {
            $query->where('isco_code', 'LIKE', $request->get('isco_code') . '%');
        }

        $professions = $query->paginate($request->get('per_page', 15));

        // Transform the data to include localized names
        $locale = $request->get('locale', app()->getLocale());
        
        $professions->getCollection()->transform(function ($profession) use ($locale) {
            return [
                'id' => $profession->id,
                'code' => $profession->code,
                'isco_code' => $profession->isco_code,
                'name' => $profession->getName($locale),
                'description' => $profession->getDescription($locale),
                'category_id' => $profession->category_id,
                'category_name' => $profession->category->getName($locale),
                'category_code' => $profession->category->code,
                'skill_level' => $profession->skill_level,
                'is_active' => $profession->is_active,
                'is_featured' => $profession->is_featured,
                'sort_order' => $profession->sort_order,
                'metadata' => $profession->metadata,
                'skills_required' => $profession->getSkillsRequired($locale),
                'education_requirements' => $profession->getEducationRequirements($locale),
                'jobs_count' => $profession->getJobCount(),
                'active_jobs_count' => $profession->getActiveJobCount(),
                // 'path' => array_map(fn($item) => [
                //     'id' => $item->id,
                //     'code' => $item->code,
                //     'name' => $item->getName($locale)
                // ], $profession->getFullPath($locale)),
                'created_at' => $profession->created_at,
                'updated_at' => $profession->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $professions,
            'locale' => $locale,
            'filters' => [
                'skill_levels' => ['High', 'Medium', 'Low'],
                'categories' => ProfessionCategory::with(['translations'])->active()->get()->map(function ($cat) use ($locale) {
                    return [
                        'id' => $cat->id,
                        'code' => $cat->code,
                        'name' => $cat->getName($locale),
                    ];
                }),
            ],
        ]);
    }

    /**
     * Store a newly created profession
     */
    public function store(StoreProfessionRequest $request): JsonResponse
    {
        $profession = Profession::create($request->validated());

        // Create translations
        if ($request->has('translations')) {
            foreach ($request->get('translations') as $locale => $translation) {
                $profession->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                    'skills_required' => $translation['skills_required'] ?? null,
                    'education_requirements' => $translation['education_requirements'] ?? null,
                ]);
            }
        }

        $profession->load(['translations', 'category.translations']);

        return response()->json([
            'success' => true,
            'message' => 'Profession created successfully',
            'data' => $this->transformProfession($profession, $request->get('locale', app()->getLocale())),
        ], 201);
    }

    /**
     * Display the specified profession
     */
    public function show(Request $request, Profession $profession): JsonResponse
    {
        $profession->load(['translations', 'category.translations', 'jobs.translations']);
        
        $locale = $request->get('locale', app()->getLocale());

        $data = [
            'id' => $profession->id,
            'code' => $profession->code,
            'isco_code' => $profession->isco_code,
            'name' => $profession->getName($locale),
            'description' => $profession->getDescription($locale),
            'category_id' => $profession->category_id,
            'category' => [
                'id' => $profession->category->id,
                'code' => $profession->category->code,
                'name' => $profession->category->getName($locale),
                'description' => $profession->category->getDescription($locale),
            ],
            'skill_level' => $profession->skill_level,
            'is_active' => $profession->is_active,
            'is_featured' => $profession->is_featured,
            'sort_order' => $profession->sort_order,
            'metadata' => $profession->metadata,
            'skills_required' => $profession->getSkillsRequired($locale),
            'education_requirements' => $profession->getEducationRequirements($locale),
            // 'path' => array_map(fn($item) => [
            //     'id' => $item->id,
            //     'code' => $item->code,
            //     'name' => $item->getName($locale)
            // ], $profession->getFullPath($locale)),
            'jobs' => $profession->jobs->map(function ($job) use ($locale) {
                return [
                    'id' => $job->id,
                    'title' => $job->title, // Assuming job has title
                    'company_name' => $job->company->name ?? null,
                    'location' => $job->location ?? null,
                    'is_active' => $job->is_active ?? true,
                ];
            }),
            'statistics' => [
                'total_jobs' => $profession->getJobCount(),
                'active_jobs' => $profession->getActiveJobCount(),
                'has_jobs' => $profession->hasJobs(),
            ],
            'translations' => $profession->translations->map(function ($translation) {
                return [
                    'locale' => $translation->locale,
                    'language_name' => $translation->language_name,
                    'name' => $translation->name,
                    'description' => $translation->description,
                    'skills_required' => $translation->skills_required,
                    'education_requirements' => $translation->education_requirements,
                    'is_complete' => $translation->isComplete(),
                    'completion_percentage' => $translation->getCompletionPercentage(),
                ];
            }),
            'created_at' => $profession->created_at,
            'updated_at' => $profession->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'locale' => $locale,
        ]);
    }

    /**
     * Update the specified profession
     */
    public function update(UpdateProfessionRequest $request, Profession $profession): JsonResponse
    {
        $profession->update($request->validated());

        // Update translations
        if ($request->has('translations')) {
            foreach ($request->get('translations') as $locale => $translation) {
                $profession->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                        'skills_required' => $translation['skills_required'] ?? null,
                        'education_requirements' => $translation['education_requirements'] ?? null,
                    ]
                );
            }
        }

        $profession->load(['translations', 'category.translations']);

        return response()->json([
            'success' => true,
            'message' => 'Profession updated successfully',
            'data' => $this->transformProfession($profession, $request->get('locale', app()->getLocale())),
        ]);
    }

    /**
     * Remove the specified profession
     */
    public function destroy(Profession $profession): JsonResponse
    {
        // Check if profession is used in jobs
        if ($profession->hasJobs()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete profession that is used in jobs',
                'jobs_count' => $profession->getJobCount(),
            ], 422);
        }

        $profession->delete();

        return response()->json([
            'success' => true,
            'message' => 'Profession deleted successfully',
        ]);
    }

    /**
     * Search professions with advanced filtering
     */
    public function search(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());
        $query = Profession::with(['translations', 'category.translations'])
            ->active();

        // Text search
        if ($request->has('q')) {
            $query->search($request->get('q'), $locale);
        }

        // Category filter
        if ($request->has('categories')) {
            $categories = is_array($request->get('categories')) 
                ? $request->get('categories') 
                : explode(',', $request->get('categories'));
            $query->whereIn('category_id', $categories);
        }

        // Skill level filter
        if ($request->has('skill_levels')) {
            $skillLevels = is_array($request->get('skill_levels')) 
                ? $request->get('skill_levels') 
                : explode(',', $request->get('skill_levels'));
            $query->whereIn('skill_level', $skillLevels);
        }

        // ISCO code filter
        if ($request->has('isco_code')) {
            $query->where('isco_code', 'LIKE', $request->get('isco_code') . '%');
        }

        // Featured filter
        if ($request->boolean('featured_only')) {
            $query->featured();
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');

        if ($sortBy === 'name') {
            // Sort by translated name
            $query->join('profession_translations as pt', function ($join) use ($locale) {
                $join->on('professions.id', '=', 'pt.profession_id')
                     ->where('pt.locale', '=', $locale);
            })->orderBy('pt.name', $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $professions = $query->paginate($request->get('per_page', 20));

        // Transform results
        $professions->getCollection()->transform(function ($profession) use ($locale) {
            return [
                'id' => $profession->id,
                'code' => $profession->code,
                'isco_code' => $profession->isco_code,
                'name' => $profession->getName($locale),
                'description' => $profession->getDescription($locale),
                'category_name' => $profession->category->getName($locale),
                'skill_level' => $profession->skill_level,
                'is_featured' => $profession->is_featured,
                'jobs_count' => $profession->getActiveJobCount(),
                'skills_required' => array_slice($profession->getSkillsRequired($locale) ?? [], 0, 3), // First 3 skills
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $professions,
            'locale' => $locale,
            'search_params' => $request->only(['q', 'categories', 'skill_levels', 'isco_code', 'featured_only']),
        ]);
    }

    /**
     * Get profession statistics
     */
    public function statistics(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());

        $stats = [
            'total_professions' => Profession::count(),
            'active_professions' => Profession::active()->count(),
            'featured_professions' => Profession::featured()->count(),
            'professions_with_jobs' => Profession::whereHas('jobs')->count(),
            'by_skill_level' => Profession::selectRaw('skill_level, count(*) as count')
                ->groupBy('skill_level')
                ->pluck('count', 'skill_level'),
            'by_category' => ProfessionCategory::withCount(['professions' => function ($query) {
                $query->active();
            }])->get()->map(function ($category) use ($locale) {
                return [
                    'category_id' => $category->id,
                    'category_name' => $category->getName($locale),
                    'professions_count' => $category->professions_count,
                ];
            }),
            'top_demanded' => Profession::withCount(['jobs' => function ($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('jobs_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($profession) use ($locale) {
                return [
                    'id' => $profession->id,
                    'name' => $profession->getName($locale),
                    'jobs_count' => $profession->jobs_count,
                ];
            }),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'locale' => $locale,
        ]);
    }

    /**
     * Bulk update professions
     */
    public function bulkUpdate(Request $request): JsonResponse
    {
        $request->validate([
            'profession_ids' => 'required|array',
            'profession_ids.*' => 'exists:professions,id',
            'updates' => 'required|array',
            'updates.is_active' => 'sometimes|boolean',
            'updates.is_featured' => 'sometimes|boolean',
            'updates.skill_level' => 'sometimes|in:High,Medium,Low',
        ]);

        $professionIds = $request->get('profession_ids');
        $updates = $request->get('updates');

        $updated = Profession::whereIn('id', $professionIds)->update($updates);

        return response()->json([
            'success' => true,
            'message' => "Successfully updated {$updated} professions",
            'updated_count' => $updated,
        ]);
    }

    /**
     * Transform profession for API response
     */
    private function transformProfession(Profession $profession, string $locale): array
    {
        return [
            'id' => $profession->id,
            'code' => $profession->code,
            'isco_code' => $profession->isco_code,
            'name' => $profession->getName($locale),
            'description' => $profession->getDescription($locale),
            'category_id' => $profession->category_id,
            'category_name' => $profession->category->getName($locale),
            'skill_level' => $profession->skill_level,
            'is_active' => $profession->is_active,
            'is_featured' => $profession->is_featured,
            'sort_order' => $profession->sort_order,
            'metadata' => $profession->metadata,
            'skills_required' => $profession->getSkillsRequired($locale),
            'education_requirements' => $profession->getEducationRequirements($locale),
            'created_at' => $profession->created_at,
            'updated_at' => $profession->updated_at,
        ];
    }
} 
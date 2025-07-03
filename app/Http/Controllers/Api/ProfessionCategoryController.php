<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProfessionCategory;
use App\Http\Requests\StoreProfessionCategoryRequest;
use App\Http\Requests\UpdateProfessionCategoryRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProfessionCategoryController extends Controller
{
    /**
     * Display a listing of profession categories
     */
    public function index(Request $request): JsonResponse
    {
        $query = ProfessionCategory::with(['translations', 'parent.translations', 'children.translations'])
            ->active()
            ->ordered();

        // Filter by level
        if ($request->has('level')) {
            $query->level($request->integer('level'));
        }

        // Filter by parent
        if ($request->has('parent_id')) {
            $query->where('parent_id', $request->integer('parent_id'));
        }

        // Get only root categories
        if ($request->boolean('roots_only')) {
            $query->root();
        }

        // Search by name in current locale
        if ($request->has('search')) {
            $locale = $request->get('locale', app()->getLocale());
            $searchTerm = $request->get('search');
            
            $query->whereHas('translations', function ($q) use ($locale, $searchTerm) {
                $q->where('locale', $locale)
                  ->where(function ($subQ) use ($searchTerm) {
                      $subQ->where('name', 'LIKE', "%{$searchTerm}%")
                           ->orWhere('description', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        $categories = $query->paginate($request->get('per_page', 15));

        // Transform the data to include localized names
        $locale = $request->get('locale', app()->getLocale());
        
        $categories->getCollection()->transform(function ($category) use ($locale) {
            return [
                'id' => $category->id,
                'code' => $category->code,
                'name' => $category->getName($locale),
                'description' => $category->getDescription($locale),
                'parent_id' => $category->parent_id,
                'parent_name' => $category->parent ? $category->parent->getName($locale) : null,
                'level' => $category->level,
                'sort_order' => $category->sort_order,
                'is_active' => $category->is_active,
                'metadata' => $category->metadata,
                'children_count' => $category->children()->count(),
                'professions_count' => $category->professions()->count(),
                // 'path' => array_map(fn($cat) => [
                //     'id' => $cat->id,
                //     'code' => $cat->code,
                //     'name' => $cat->getName($locale)
                // ], $category->getPath()),
                'created_at' => $category->created_at,
                'updated_at' => $category->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $categories,
            'locale' => $locale,
        ]);
    }

    /**
     * Store a newly created profession category
     */
    public function store(StoreProfessionCategoryRequest $request): JsonResponse
    {
        $category = ProfessionCategory::create($request->validated());

        // Create translations
        if ($request->has('translations')) {
            foreach ($request->get('translations') as $locale => $translation) {
                $category->translations()->create([
                    'locale' => $locale,
                    'name' => $translation['name'],
                    'description' => $translation['description'] ?? null,
                ]);
            }
        }

        $category->load(['translations', 'parent.translations']);

        return response()->json([
            'success' => true,
            'message' => 'Profession category created successfully',
            'data' => $this->transformCategory($category, $request->get('locale', app()->getLocale())),
        ], 201);
    }

    /**
     * Display the specified profession category
     */
    public function show(Request $request, ProfessionCategory $professionCategory): JsonResponse
    {
        $professionCategory->load(['translations', 'parent.translations', 'children.translations', 'professions.translations']);
        
        $locale = $request->get('locale', app()->getLocale());

        $data = [
            'id' => $professionCategory->id,
            'code' => $professionCategory->code,
            'name' => $professionCategory->getName($locale),
            'description' => $professionCategory->getDescription($locale),
            'parent_id' => $professionCategory->parent_id,
            'parent' => $professionCategory->parent ? [
                'id' => $professionCategory->parent->id,
                'code' => $professionCategory->parent->code,
                'name' => $professionCategory->parent->getName($locale),
            ] : null,
            'level' => $professionCategory->level,
            'sort_order' => $professionCategory->sort_order,
            'is_active' => $professionCategory->is_active,
            'metadata' => $professionCategory->metadata,
            'children' => $professionCategory->children->map(function ($child) use ($locale) {
                return [
                    'id' => $child->id,
                    'code' => $child->code,
                    'name' => $child->getName($locale),
                    'professions_count' => $child->professions()->count(),
                ];
            }),
            'professions' => $professionCategory->professions->map(function ($profession) use ($locale) {
                return [
                    'id' => $profession->id,
                    'code' => $profession->code,
                    'name' => $profession->getName($locale),
                    'skill_level' => $profession->skill_level,
                ];
            }),
            // 'path' => array_map(fn($cat) => [
            //     'id' => $cat->id,
            //     'code' => $cat->code,
            //     'name' => $cat->getName($locale)
            // ], $professionCategory->getPath()),
            'translations' => $professionCategory->translations->map(function ($translation) {
                return [
                    'locale' => $translation->locale,
                    'language_name' => $translation->language_name,
                    'name' => $translation->name,
                    'description' => $translation->description,
                    'is_complete' => $translation->isComplete(),
                ];
            }),
            'created_at' => $professionCategory->created_at,
            'updated_at' => $professionCategory->updated_at,
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'locale' => $locale,
        ]);
    }

    /**
     * Update the specified profession category
     */
    public function update(UpdateProfessionCategoryRequest $request, ProfessionCategory $professionCategory): JsonResponse
    {
        $professionCategory->update($request->validated());

        // Update translations
        if ($request->has('translations')) {
            foreach ($request->get('translations') as $locale => $translation) {
                $professionCategory->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $translation['name'],
                        'description' => $translation['description'] ?? null,
                    ]
                );
            }
        }

        $professionCategory->load(['translations', 'parent.translations']);

        return response()->json([
            'success' => true,
            'message' => 'Profession category updated successfully',
            'data' => $this->transformCategory($professionCategory, $request->get('locale', app()->getLocale())),
        ]);
    }

    /**
     * Remove the specified profession category
     */
    public function destroy(ProfessionCategory $professionCategory): JsonResponse
    {
        // Check if category has children or professions
        if ($professionCategory->children()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has subcategories',
            ], 422);
        }

        if ($professionCategory->professions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has professions',
            ], 422);
        }

        $professionCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Profession category deleted successfully',
        ]);
    }

    /**
     * Get category hierarchy tree
     */
    public function tree(Request $request): JsonResponse
    {
        $locale = $request->get('locale', app()->getLocale());
        
        $categories = ProfessionCategory::with(['translations', 'children.translations', 'children.children.translations'])
            ->root()
            ->active()
            ->ordered()
            ->get();

        $tree = $categories->map(function ($category) use ($locale) {
            return $this->buildCategoryTree($category, $locale);
        });

        return response()->json([
            'success' => true,
            'data' => $tree,
            'locale' => $locale,
        ]);
    }

    /**
     * Get available languages for translations
     */
    public function languages(): JsonResponse
    {
        $languages = [
            'en' => 'English',
            'lt' => 'Lietuvių',
            'ru' => 'Русский',
            'pl' => 'Polski',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            'zh' => '中文',
            'ar' => 'العربية',
            'pt' => 'Português',
            'tr' => 'Türkçe',
            'it' => 'Italiano',
            'ja' => '日本語',
            'hi' => 'हिन्दी',
        ];

        return response()->json([
            'success' => true,
            'data' => $languages,
        ]);
    }

    /**
     * Transform category for API response
     */
    private function transformCategory(ProfessionCategory $category, string $locale): array
    {
        return [
            'id' => $category->id,
            'code' => $category->code,
            'name' => $category->getName($locale),
            'description' => $category->getDescription($locale),
            'parent_id' => $category->parent_id,
            'parent_name' => $category->parent ? $category->parent->getName($locale) : null,
            'level' => $category->level,
            'sort_order' => $category->sort_order,
            'is_active' => $category->is_active,
            'metadata' => $category->metadata,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }

    /**
     * Build hierarchical category tree
     */
    private function buildCategoryTree(ProfessionCategory $category, string $locale): array
    {
        $data = [
            'id' => $category->id,
            'code' => $category->code,
            'name' => $category->getName($locale),
            'description' => $category->getDescription($locale),
            'level' => $category->level,
            'sort_order' => $category->sort_order,
            'metadata' => $category->metadata,
            'professions_count' => $category->professions()->count(),
            'children' => [],
        ];

        if ($category->children->isNotEmpty()) {
            $data['children'] = $category->children->map(function ($child) use ($locale) {
                return $this->buildCategoryTree($child, $locale);
            })->toArray();
        }

        return $data;
    }
} 
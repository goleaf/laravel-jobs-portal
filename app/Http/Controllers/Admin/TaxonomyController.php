<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TaxonomyCreateRequest;
use App\Http\Requests\TaxonomyUpdateRequest;
use App\Models\Taxonomy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * TaxonomyController - Enhanced with Context7 patterns
 * 
 * Manages taxonomies with full CRUD operations, search, filtering,
 * and bulk operations following Laravel best practices.
 */
class TaxonomyController extends Controller
{
    /**
     * Display a listing of taxonomies.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Taxonomy::query();

        // Search functionality
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        // Filter by visibility
        if ($request->filled('visibility')) {
            if ($request->visibility === 'public') {
                $query->public();
            } elseif ($request->visibility === 'private') {
                $query->private();
            }
        }

        // Sort options
        $sortBy = $request->get('sort_by', 'name');
        $sortDirection = $request->get('sort_direction', 'asc');
        
        switch ($sortBy) {
            case 'name':
                $query->alphabetical();
                break;
            case 'created_at':
                $query->orderBy('created_at', $sortDirection);
                break;
            case 'sort_order':
                $query->ordered();
                break;
            case 'terms_count':
                $query->withTermCounts()->orderBy('terms_count', $sortDirection);
                break;
            default:
                $query->alphabetical();
        }

        $taxonomies = $query->withCount('terms')
                           ->paginate(20)
                           ->withQueryString();

        // Get available types for filter
        $types = Taxonomy::distinct('type')->pluck('type')->toArray();

        if ($request->expectsJson()) {
            return response()->json([
                'taxonomies' => $taxonomies,
                'types' => $types,
                'filters' => [
                    'search' => $request->search,
                    'type' => $request->type,
                    'status' => $request->status,
                    'visibility' => $request->visibility,
                    'sort_by' => $sortBy,
                    'sort_direction' => $sortDirection,
                ]
            ]);
        }

        return view('admin.taxonomies.index', compact('taxonomies', 'types'));
    }

    /**
     * Show the form for creating a new taxonomy.
     */
    public function create(): View
    {
        $types = Taxonomy::TYPES;
        
        return view('admin.taxonomies.create', compact('types'));
    }

    /**
     * Store a newly created taxonomy.
     */
    public function store(TaxonomyCreateRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $taxonomy = Taxonomy::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Taxonomy created successfully.',
                'taxonomy' => $taxonomy->load('terms')
            ], 201);
        }

        return redirect()
            ->route('admin.taxonomies.index')
            ->with('success', 'Taxonomy created successfully.');
    }

    /**
     * Display the specified taxonomy.
     */
    public function show(Taxonomy $taxonomy): View|JsonResponse
    {
        $taxonomy->load(['terms' => function ($query) {
            $query->ordered();
        }]);

        // Get usage statistics
        $usage_stats = [
            'total_terms' => $taxonomy->terms_count,
            'active_terms' => $taxonomy->terms()->active()->count(),
            'total_usage' => $taxonomy->terms()->sum('usage_count'),
            'recently_used' => $taxonomy->terms()
                                      ->whereNotNull('last_used_at')
                                      ->where('last_used_at', '>=', now()->subDays(30))
                                      ->count(),
        ];

        if (request()->expectsJson()) {
            return response()->json([
                'taxonomy' => $taxonomy,
                'usage_stats' => $usage_stats
            ]);
        }

        return view('admin.taxonomies.show', compact('taxonomy', 'usage_stats'));
    }

    /**
     * Show the form for editing the specified taxonomy.
     */
    public function edit(Taxonomy $taxonomy): View
    {
        $types = Taxonomy::TYPES;
        
        return view('admin.taxonomies.edit', compact('taxonomy', 'types'));
    }

    /**
     * Update the specified taxonomy.
     */
    public function update(TaxonomyUpdateRequest $request, Taxonomy $taxonomy): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        
        // Generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $taxonomy->update($data);
        $taxonomy->clearCaches();

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Taxonomy updated successfully.',
                'taxonomy' => $taxonomy->fresh()->load('terms')
            ]);
        }

        return redirect()
            ->route('admin.taxonomies.index')
            ->with('success', 'Taxonomy updated successfully.');
    }

    /**
     * Remove the specified taxonomy.
     */
    public function destroy(Taxonomy $taxonomy): RedirectResponse|JsonResponse
    {
        // Check if taxonomy has terms
        if ($taxonomy->terms()->count() > 0) {
            if (request()->expectsJson()) {
                return response()->json([
                    'message' => 'Cannot delete taxonomy with existing terms. Please remove all terms first.'
                ], 422);
            }

            return redirect()
                ->route('admin.taxonomies.index')
                ->with('error', 'Cannot delete taxonomy with existing terms. Please remove all terms first.');
        }

        $taxonomy->clearCaches();
        $taxonomy->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'message' => 'Taxonomy deleted successfully.'
            ]);
        }

        return redirect()
            ->route('admin.taxonomies.index')
            ->with('success', 'Taxonomy deleted successfully.');
    }

    /**
     * Toggle taxonomy status (active/inactive).
     */
    public function toggleStatus(Taxonomy $taxonomy): JsonResponse
    {
        $taxonomy->update([
            'is_active' => !$taxonomy->is_active
        ]);

        $taxonomy->clearCaches();

        return response()->json([
            'message' => 'Taxonomy status updated successfully.',
            'is_active' => $taxonomy->is_active
        ]);
    }

    /**
     * Bulk operations on taxonomies.
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'taxonomy_ids' => 'required|array',
            'taxonomy_ids.*' => 'exists:taxonomies,id'
        ]);

        $taxonomies = Taxonomy::whereIn('id', $request->taxonomy_ids);
        $count = $taxonomies->count();

        switch ($request->action) {
            case 'activate':
                $taxonomies->update(['is_active' => true]);
                $message = "$count taxonomies activated successfully.";
                break;

            case 'deactivate':
                $taxonomies->update(['is_active' => false]);
                $message = "$count taxonomies deactivated successfully.";
                break;

            case 'delete':
                // Check for taxonomies with terms
                $taxonomiesWithTerms = $taxonomies->has('terms')->count();
                if ($taxonomiesWithTerms > 0) {
                    return response()->json([
                        'message' => "Cannot delete $taxonomiesWithTerms taxonomies that have terms."
                    ], 422);
                }

                $taxonomies->delete();
                $message = "$count taxonomies deleted successfully.";
                break;
        }

        // Clear caches for affected taxonomies
        foreach ($request->taxonomy_ids as $id) {
            $taxonomy = Taxonomy::find($id);
            if ($taxonomy) {
                $taxonomy->clearCaches();
            }
        }

        return response()->json(['message' => $message]);
    }

    /**
     * Export taxonomies data.
     */
    public function export(Request $request): JsonResponse
    {
        $query = Taxonomy::query();

        // Apply same filters as index
        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        $taxonomies = $query->with('terms')->get();

        $exportData = $taxonomies->map(function ($taxonomy) {
            return [
                'id' => $taxonomy->id,
                'name' => $taxonomy->name,
                'slug' => $taxonomy->slug,
                'description' => $taxonomy->description,
                'type' => $taxonomy->type,
                'is_hierarchical' => $taxonomy->is_hierarchical,
                'is_active' => $taxonomy->is_active,
                'is_public' => $taxonomy->is_public,
                'sort_order' => $taxonomy->sort_order,
                'meta' => $taxonomy->meta,
                'terms_count' => $taxonomy->terms->count(),
                'terms' => $taxonomy->terms->map(function ($term) {
                    return [
                        'name' => $term->name,
                        'slug' => $term->slug,
                        'description' => $term->description,
                        'is_active' => $term->is_active,
                        'sort_order' => $term->sort_order,
                        'usage_count' => $term->usage_count,
                    ];
                }),
                'created_at' => $taxonomy->created_at->toISOString(),
                'updated_at' => $taxonomy->updated_at->toISOString(),
            ];
        });

        return response()->json([
            'data' => $exportData,
            'exported_at' => now()->toISOString(),
            'total_taxonomies' => $exportData->count(),
        ]);
    }

    /**
     * Get terms for a specific taxonomy (AJAX endpoint).
     */
    public function terms(Taxonomy $taxonomy): JsonResponse
    {
        $terms = $taxonomy->terms()
                         ->active()
                         ->ordered()
                         ->get();

        return response()->json([
            'terms' => $terms
        ]);
    }
}

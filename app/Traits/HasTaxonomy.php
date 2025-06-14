<?php

namespace App\Traits;

use App\Models\Taxonomy;
use App\Models\Term;
use App\Models\Taggable;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

/**
 * HasTaxonomy Trait
 * 
 * Provides taxonomy functionality to any Eloquent model.
 * Allows models to be tagged with terms from various taxonomies.
 */
trait HasTaxonomy
{
    /**
     * Get all taxonomies for this model through terms.
     */
    public function taxonomies(): Collection
    {
        return $this->terms()
                    ->with('taxonomy')
                    ->get()
                    ->pluck('taxonomy')
                    ->unique('id');
    }

    /**
     * Get all terms for this model.
     */
    public function terms(): MorphToMany
    {
        return $this->morphToMany(Term::class, 'taggable')
                    ->withPivot(['taxonomy_id', 'sort_order', 'meta'])
                    ->withTimestamps()
                    ->orderBy('pivot_sort_order')
                    ->orderBy('name');
    }

    /**
     * Get terms by taxonomy.
     */
    public function termsByTaxonomy(string $taxonomyType): MorphToMany
    {
        return $this->morphToMany(Term::class, 'taggable')
                    ->withPivot(['taxonomy_id', 'sort_order', 'meta'])
                    ->withTimestamps()
                    ->whereHas('taxonomy', function ($query) use ($taxonomyType) {
                        $query->where('type', $taxonomyType);
                    })
                    ->orderBy('pivot_sort_order')
                    ->orderBy('name');
    }

    /**
     * Get active terms for this model.
     */
    public function activeTerms(): MorphToMany
    {
        return $this->terms()->where('terms.is_active', true);
    }

    /**
     * Get featured terms for this model.
     */
    public function featuredTerms(): MorphToMany
    {
        return $this->terms()->where('terms.is_featured', true);
    }

    // =============================================
    // ADD TERMS METHODS
    // =============================================

    /**
     * Add a single term to this model.
     */
    public function addTerm($term, string $taxonomyType = null, int $sortOrder = 0, array $meta = []): void
    {
        $termModel = $this->resolveTerm($term, $taxonomyType);
        
        if ($termModel) {
            $this->attachTerm($termModel, $sortOrder, $meta);
        }
    }

    /**
     * Add multiple terms to this model.
     */
    public function addTerms(array $terms, string $taxonomyType = null): void
    {
        foreach ($terms as $index => $term) {
            $this->addTerm($term, $taxonomyType, $index);
        }
    }

    /**
     * Sync terms for a specific taxonomy.
     */
    public function syncTerms(array $terms, string $taxonomyType): void
    {
        $taxonomy = Taxonomy::where('type', $taxonomyType)->first();
        
        if (!$taxonomy) {
            return;
        }

        // Get current term IDs for this taxonomy
        $currentTermIds = $this->terms()
                              ->where('taxonomy_id', $taxonomy->id)
                              ->pluck('terms.id')
                              ->toArray();

        // Resolve new terms
        $newTermIds = [];
        foreach ($terms as $term) {
            $termModel = $this->resolveTerm($term, $taxonomyType);
            if ($termModel) {
                $newTermIds[] = $termModel->id;
            }
        }

        // Detach removed terms
        $toDetach = array_diff($currentTermIds, $newTermIds);
        foreach ($toDetach as $termId) {
            $this->detachTerm($termId);
        }

        // Attach new terms
        $toAttach = array_diff($newTermIds, $currentTermIds);
        foreach ($toAttach as $termId) {
            $termModel = Term::find($termId);
            if ($termModel) {
                $this->attachTerm($termModel);
            }
        }
    }

    // =============================================
    // REMOVE TERMS METHODS
    // =============================================

    /**
     * Remove a term from this model.
     */
    public function removeTerm($term, string $taxonomyType = null): void
    {
        $termModel = $this->resolveTerm($term, $taxonomyType);
        
        if ($termModel) {
            $this->detachTerm($termModel->id);
        }
    }

    /**
     * Remove multiple terms from this model.
     */
    public function removeTerms(array $terms, string $taxonomyType = null): void
    {
        foreach ($terms as $term) {
            $this->removeTerm($term, $taxonomyType);
        }
    }

    /**
     * Remove all terms from this model.
     */
    public function removeAllTerms(): void
    {
        $this->terms()->detach();
    }

    /**
     * Remove all terms from a specific taxonomy.
     */
    public function removeTermsByTaxonomy(string $taxonomyType): void
    {
        $taxonomy = Taxonomy::where('type', $taxonomyType)->first();
        
        if ($taxonomy) {
            $this->terms()
                 ->wherePivot('taxonomy_id', $taxonomy->id)
                 ->detach();
        }
    }

    // =============================================
    // QUERY METHODS
    // =============================================

    /**
     * Get terms for a specific taxonomy.
     */
    public function getTerms(string $taxonomyType): Collection
    {
        return $this->termsByTaxonomy($taxonomyType)->get();
    }

    /**
     * Get a specific term.
     */
    public function getTerm(string $termName, string $taxonomyType = null): ?Term
    {
        $query = $this->terms()->where('terms.name', $termName);
        
        if ($taxonomyType) {
            $query->whereHas('taxonomy', function ($q) use ($taxonomyType) {
                $q->where('type', $taxonomyType);
            });
        }
        
        return $query->first();
    }

    /**
     * Check if model has a specific term.
     */
    public function hasTerm($term, string $taxonomyType = null): bool
    {
        $termModel = $this->resolveTerm($term, $taxonomyType);
        
        if (!$termModel) {
            return false;
        }
        
        return $this->terms()->where('terms.id', $termModel->id)->exists();
    }

    /**
     * Check if model has any terms from a taxonomy.
     */
    public function hasTermsFromTaxonomy(string $taxonomyType): bool
    {
        return $this->termsByTaxonomy($taxonomyType)->exists();
    }

    /**
     * Get term names for a taxonomy.
     */
    public function getTermNames(string $taxonomyType): array
    {
        return $this->getTerms($taxonomyType)->pluck('name')->toArray();
    }

    /**
     * Get term slugs for a taxonomy.
     */
    public function getTermSlugs(string $taxonomyType): array
    {
        return $this->getTerms($taxonomyType)->pluck('slug')->toArray();
    }

    // =============================================
    // SCOPE METHODS
    // =============================================

    /**
     * Scope models with specific terms.
     */
    public function scopeWithTerms($query, array $terms, string $taxonomyType = null)
    {
        return $query->whereHas('terms', function ($q) use ($terms, $taxonomyType) {
            if (is_array($terms[0])) {
                // Multiple term conditions
                $q->where(function ($subQuery) use ($terms, $taxonomyType) {
                    foreach ($terms as $termGroup) {
                        $subQuery->orWhereIn('terms.name', $termGroup);
                    }
                });
            } else {
                // Single term condition
                $q->whereIn('terms.name', $terms);
            }
            
            if ($taxonomyType) {
                $q->whereHas('taxonomy', function ($taxonomyQuery) use ($taxonomyType) {
                    $taxonomyQuery->where('type', $taxonomyType);
                });
            }
        });
    }

    /**
     * Scope models with a specific term.
     */
    public function scopeWithTerm($query, $term, string $taxonomyType = null)
    {
        return $query->whereHas('terms', function ($q) use ($term, $taxonomyType) {
            if (is_string($term)) {
                $q->where('terms.name', $term);
            } elseif (is_numeric($term)) {
                $q->where('terms.id', $term);
            } elseif ($term instanceof Term) {
                $q->where('terms.id', $term->id);
            }
            
            if ($taxonomyType) {
                $q->whereHas('taxonomy', function ($taxonomyQuery) use ($taxonomyType) {
                    $taxonomyQuery->where('type', $taxonomyType);
                });
            }
        });
    }

    /**
     * Scope models without specific terms.
     */
    public function scopeWithoutTerms($query, array $terms, string $taxonomyType = null)
    {
        return $query->whereDoesntHave('terms', function ($q) use ($terms, $taxonomyType) {
            $q->whereIn('terms.name', $terms);
            
            if ($taxonomyType) {
                $q->whereHas('taxonomy', function ($taxonomyQuery) use ($taxonomyType) {
                    $taxonomyQuery->where('type', $taxonomyType);
                });
            }
        });
    }

    /**
     * Scope models by taxonomy type.
     */
    public function scopeByTaxonomy($query, string $taxonomyType)
    {
        return $query->whereHas('terms.taxonomy', function ($q) use ($taxonomyType) {
            $q->where('type', $taxonomyType);
        });
    }

    // =============================================
    // HELPER METHODS
    // =============================================

    /**
     * Resolve term from various input types.
     */
    protected function resolveTerm($term, string $taxonomyType = null): ?Term
    {
        if ($term instanceof Term) {
            return $term;
        }
        
        if (is_numeric($term)) {
            return Term::find($term);
        }
        
        if (is_string($term)) {
            $query = Term::where('name', $term);
            
            if ($taxonomyType) {
                $query->whereHas('taxonomy', function ($q) use ($taxonomyType) {
                    $q->where('type', $taxonomyType);
                });
            }
            
            $existingTerm = $query->first();
            
            if ($existingTerm) {
                return $existingTerm;
            }
            
            // Create new term if taxonomy type is provided
            if ($taxonomyType) {
                $taxonomy = Taxonomy::firstOrCreate(
                    ['type' => $taxonomyType],
                    [
                        'name' => ucfirst(str_replace('_', ' ', $taxonomyType)),
                        'slug' => $taxonomyType,
                        'is_active' => true,
                        'is_public' => true,
                    ]
                );
                
                return $taxonomy->getOrCreateTerm($term);
            }
        }
        
        return null;
    }

    /**
     * Attach a term to this model.
     */
    protected function attachTerm(Term $term, int $sortOrder = 0, array $meta = []): void
    {
        // Check if already attached
        if ($this->terms()->where('terms.id', $term->id)->exists()) {
            return;
        }
        
        $this->terms()->attach($term->id, [
            'taxonomy_id' => $term->taxonomy_id,
            'sort_order' => $sortOrder,
            'meta' => $meta,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Increment term usage
        $term->incrementUsage();
    }

    /**
     * Detach a term from this model.
     */
    protected function detachTerm(int $termId): void
    {
        $this->terms()->detach($termId);
        
        // Decrement term usage
        $term = Term::find($termId);
        if ($term && $term->usage_count > 0) {
            $term->decrement('usage_count');
        }
    }

    // =============================================
    // CONVENIENCE METHODS
    // =============================================

    /**
     * Tag with job categories.
     */
    public function tagWithJobCategories(array $categories): void
    {
        $this->syncTerms($categories, 'job_category');
    }

    /**
     * Tag with skills.
     */
    public function tagWithSkills(array $skills): void
    {
        $this->syncTerms($skills, 'skill');
    }

    /**
     * Tag with industries.
     */
    public function tagWithIndustries(array $industries): void
    {
        $this->syncTerms($industries, 'industry');
    }

    /**
     * Tag with locations.
     */
    public function tagWithLocations(array $locations): void
    {
        $this->syncTerms($locations, 'location');
    }

    /**
     * Tag with benefits.
     */
    public function tagWithBenefits(array $benefits): void
    {
        $this->syncTerms($benefits, 'benefit');
    }

    /**
     * Get job categories.
     */
    public function getJobCategories(): Collection
    {
        return $this->getTerms('job_category');
    }

    /**
     * Get skills.
     */
    public function getSkills(): Collection
    {
        return $this->getTerms('skill');
    }

    /**
     * Get industries.
     */
    public function getIndustries(): Collection
    {
        return $this->getTerms('industry');
    }

    /**
     * Get locations.
     */
    public function getLocations(): Collection
    {
        return $this->getTerms('location');
    }

    /**
     * Get benefits.
     */
    public function getBenefits(): Collection
    {
        return $this->getTerms('benefit');
    }
} 
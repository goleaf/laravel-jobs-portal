<?php

namespace App\Http\Controllers\Enhanced;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\Plan\ChangeTrialPlanRequest;
use App\Http\Requests\Plan\CreatePlanRequest;
use App\Http\Requests\Plan\DestroyPlanRequest;
use App\Http\Requests\Plan\EditPlanRequest;
use App\Http\Requests\Plan\IndexPlanRequest;
use App\Http\Requests\Plan\ShowPlanRequest;
use App\Http\Requests\Plan\UpdatePlanUpdatePlanRequest;
use App\Models\Plan;
use App\Models\SalaryCurrency;
use App\Repositories\PlanRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Enhanced PlanController - Enhanced patterns implementation.
 *
 * Demonstrates modern Laravel controller patterns with:
 * - Advanced caching strategies
 * - Comprehensive error handling
 * - Performance optimization
 * - Enhanced repository usage
 * - Bulk operations support
 * - Subscription management
 */
class PlanController extends AppBaseController
{
    /**
     * Cache TTL for plan-related operations (1 hour).
     */
    private const CACHE_TTL = 3600;

    /** @var PlanRepository */
    private $planRepository;

    public function __construct(PlanRepository $planRepository)
    {
        $this->planRepository = $planRepository;
    }

    /**
     * Display a listing of plans with enhanced filtering and search.
     */
    public function index(IndexPlanRequest $request)
    {
        try {
            // Check if this is an API request
            if ($this->isApiRequest($request)) {
                return $this->getPlansApi($request);
            }

            // For web requests, return the view with enhanced data
            $data = $this->preparePlansIndexData($request);

            return view('plans.index', $data);
        } catch (\Exception $e) {
            Log::error('Error in PlanController@index', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);

            if ($this->isApiRequest($request)) {
                return $this->sendServerError('Failed to retrieve plans');
            }

            return redirect()->back()->with('error', 'Failed to load plans');
        }
    }

    /**
     * Store a newly created plan with enhanced validation.
     */
    public function store(CreatePlanRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $input = $request->validated();

            // Add default values
            $input['is_active'] = $input['is_active'] ?? true;
            $input['created_by'] = auth()->id();

            $plan = $this->planRepository->createPlan($input);

            // Clear related caches
            $this->clearPlanCaches();

            // Log the creation
            Log::info('Plan created successfully', [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'price' => $plan->price,
                'created_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->sendResponse($plan->load(['currency', 'activeSubscriptions']), __('messages.flash.plan_Save'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating plan', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
                'user_id' => auth()->id(),
            ]);

            return $this->sendServerError('Failed to create plan');
        }
    }

    /**
     * Display the specified plan with enhanced data loading.
     */
    public function show(Plan $plan, ShowPlanRequest $request): JsonResponse
    {
        try {
            $cacheKey = $this->buildCacheKey('plan.show', $plan->id);

            $planData = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($plan) {
                return $plan->load([
                    'currency',
                    'activeSubscriptions' => function ($query) {
                        $query->with(['user', 'company'])->latest()->limit(10);
                    },
                    'subscriptions' => function ($query) {
                        $query->with(['user', 'company'])->latest()->limit(20);
                    },
                ]);
            });

            // Get plan statistics using model scopes
            $statistics = [
                'total_subscriptions' => $plan->subscriptions()->count(),
                'active_subscriptions' => $plan->activeSubscriptions()->count(),
                'recent_subscriptions' => $plan->subscriptions()->recent(30)->count(),
                'revenue_generated' => $plan->subscriptions()->sum('amount'),
                'conversion_rate' => $this->calculateConversionRate($plan),
            ];

            return $this->sendResponse([
                'plan' => $planData,
                'statistics' => $statistics,
            ], 'Plan retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error retrieving plan', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to retrieve plan');
        }
    }

    /**
     * Show the form for editing the specified plan.
     */
    public function edit(Plan $plan, EditPlanRequest $request): JsonResponse
    {
        try {
            // Handle default currency assignment
            if (0 == $plan->salary_currency_id) {
                $defaultCurrency = SalaryCurrency::whereCurrencyName('USD US Dollar')->first();
                if ($defaultCurrency) {
                    $plan->salary_currency_id = $defaultCurrency->id;
                }
            }

            $planData = $plan->load(['currency', 'activeSubscriptions']);

            // Get available currencies
            $currencies = SalaryCurrency::active()->orderBy('currency_name')->get();

            return $this->sendResponse([
                'plan' => $planData,
                'currencies' => $currencies,
            ], __('messages.flash.plan_retrieve'));
        } catch (\Exception $e) {
            Log::error('Error editing plan', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to load plan for editing');
        }
    }

    /**
     * Update the specified plan with enhanced validation.
     */
    public function update(UpdatePlanUpdatePlanRequest $request, Plan $plan): JsonResponse
    {
        try {
            DB::beginTransaction();

            $input = $request->validated();
            $input['updated_by'] = auth()->id();

            $updatePlan = $this->planRepository->updatePlan($input, $plan);

            if (!$updatePlan) {
                DB::rollBack();

                return $this->sendError(__('messages.flash.plan_cant_update'));
            }

            // Clear related caches
            $this->clearPlanCaches($plan->id);

            // Log the update
            Log::info('Plan updated successfully', [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'updated_by' => auth()->id(),
                'changes' => $plan->getChanges(),
            ]);

            DB::commit();

            return $this->sendResponse($plan->fresh()->load(['currency', 'activeSubscriptions']), __('messages.flash.plan_update'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating plan', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);

            return $this->sendServerError('Failed to update plan');
        }
    }

    /**
     * Remove the specified plan with enhanced dependency checking.
     */
    public function destroy(Plan $plan, DestroyPlanRequest $request): JsonResponse
    {
        try {
            // Enhanced dependency checking
            $activeSubscriptionsCount = $plan->activeSubscriptions()->count();
            $totalSubscriptionsCount = $plan->subscriptions()->count();

            if ($activeSubscriptionsCount > 0) {
                return $this->sendError(__('messages.flash.plan_cant_delete')." Active subscriptions: {$activeSubscriptionsCount}");
            }

            // Check if this is the only trial plan
            if ($plan->is_trial_plan && 1 === Plan::trial()->count()) {
                return $this->sendError('Cannot delete the only trial plan. Please create another trial plan first.');
            }

            DB::beginTransaction();

            // Archive instead of delete if has historical subscriptions
            if ($totalSubscriptionsCount > 0) {
                $plan->update([
                    'is_active' => false,
                    'deleted_at' => now(),
                    'deleted_by' => auth()->id(),
                ]);
                $message = 'Plan archived successfully due to existing subscription history';
            } else {
                $this->planRepository->deletePlan($plan);
                $message = __('messages.flash.plan_delete');
            }

            // Clear related caches
            $this->clearPlanCaches($plan->id);

            // Log the deletion/archival
            Log::info('Plan deleted/archived successfully', [
                'plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'action' => $totalSubscriptionsCount > 0 ? 'archived' : 'deleted',
                'deleted_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->sendSuccess($message);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting plan', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to delete plan');
        }
    }

    /**
     * Change trial plan with enhanced validation and logging.
     */
    public function changeTrialPlan(Plan $plan, ChangeTrialPlanRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Ensure the plan is active
            if (!$plan->is_active) {
                return $this->sendError('Cannot set inactive plan as trial plan');
            }

            // Remove trial status from all other plans
            Plan::where('is_trial_plan', true)->update([
                'is_trial_plan' => false,
                'updated_at' => now(),
            ]);

            // Set current plan as trial
            $plan->update([
                'is_trial_plan' => true,
                'updated_by' => auth()->id(),
            ]);

            // Clear related caches
            $this->clearPlanCaches();

            // Log the change
            Log::info('Trial plan changed successfully', [
                'new_trial_plan_id' => $plan->id,
                'plan_name' => $plan->name,
                'changed_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->sendResponse($plan->fresh(), __('messages.flash.trial_plan_update'));
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error changing trial plan', [
                'plan_id' => $plan->id,
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to change trial plan');
        }
    }

    /**
     * Get plans for select/autocomplete with enhanced caching.
     */
    public function getPlansForSelect(Request $request): JsonResponse
    {
        try {
            $cacheKey = $this->buildCacheKey('plans.select', $request->only(['search', 'active_only']));

            $plans = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
                $query = Plan::select('id', 'name', 'price', 'salary_currency_id')
                    ->with('currency:id,currency_name,currency_icon')
                ;

                if ($request->filled('search')) {
                    $query->search($request->get('search'));
                }

                if ($request->boolean('active_only', true)) {
                    $query->active();
                }

                return $query->alphabetical()->limit(50)->get();
            });

            return $this->sendResponse($plans, 'Plans for select retrieved successfully');
        } catch (\Exception $e) {
            Log::error('Error getting plans for select', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
            ]);

            return $this->sendServerError('Failed to retrieve plans for select');
        }
    }

    /**
     * Bulk actions for plans (activate, deactivate, delete).
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'plan_ids' => 'required|array|min:1',
            'plan_ids.*' => 'exists:plans,id',
        ]);

        try {
            DB::beginTransaction();

            $planIds = $request->get('plan_ids');
            $action = $request->get('action');
            $affectedCount = 0;

            switch ($action) {
                case 'activate':
                    $affectedCount = Plan::whereIn('id', $planIds)->update([
                        'is_active' => true,
                        'updated_by' => auth()->id(),
                        'updated_at' => now(),
                    ]);

                    break;

                case 'deactivate':
                    $affectedCount = Plan::whereIn('id', $planIds)->update([
                        'is_active' => false,
                        'updated_by' => auth()->id(),
                        'updated_at' => now(),
                    ]);

                    break;

                case 'delete':
                    // Check for active subscriptions before deletion
                    $plansWithSubscriptions = Plan::whereIn('id', $planIds)
                        ->whereHas('activeSubscriptions')
                        ->pluck('name')
                        ->toArray()
                    ;

                    if (!empty($plansWithSubscriptions)) {
                        return $this->sendError('Cannot delete plans with active subscriptions: '.implode(', ', $plansWithSubscriptions));
                    }

                    $affectedCount = Plan::whereIn('id', $planIds)->delete();

                    break;
            }

            // Clear related caches
            $this->clearPlanCaches();

            // Log the bulk action
            Log::info('Bulk action performed on plans', [
                'action' => $action,
                'plan_ids' => $planIds,
                'affected_count' => $affectedCount,
                'performed_by' => auth()->id(),
            ]);

            DB::commit();

            return $this->sendSuccess("Successfully {$action}d {$affectedCount} plan(s)");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error performing bulk action on plans', [
                'action' => $request->get('action'),
                'plan_ids' => $request->get('plan_ids'),
                'error' => $e->getMessage(),
            ]);

            return $this->sendServerError('Failed to perform bulk action');
        }
    }

    /**
     * Get plans for API requests with enhanced filtering.
     */
    private function getPlansApi(Request $request): JsonResponse
    {
        $cacheKey = $this->buildCacheKey('plans.api', $request->all());

        $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            $query = Plan::query();

            // Apply Enhanced scopes for filtering
            if ($request->filled('search')) {
                $query->search($request->get('search'));
            }

            if ($request->filled('is_active')) {
                $query->active();
            }

            if ($request->filled('is_trial')) {
                $query->trial();
            }

            if ($request->filled('is_popular')) {
                $query->popular();
            }

            if ($request->filled('currency_id')) {
                $query->byCurrency($request->get('currency_id'));
            }

            if ($request->filled('price_range')) {
                $range = explode('-', $request->get('price_range'));
                if (2 === count($range)) {
                    $query->priceRange($range[0], $range[1]);
                }
            }

            // Apply sorting
            $sortBy = $request->get('sort', 'name');
            $sortDirection = $request->get('direction', 'asc');

            if (in_array($sortBy, ['name', 'price', 'created_at', 'updated_at'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->alphabetical();
            }

            return $query->with(['currency', 'activeSubscriptions'])->paginate($request->get('per_page', 15));
        });

        return $this->sendPaginatedResponse($data, 'Plans retrieved successfully');
    }

    /**
     * Prepare data for plans index view.
     */
    private function preparePlansIndexData(Request $request): array
    {
        $cacheKey = $this->buildCacheKey('plans.index.data', $request->all());

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($request) {
            // Get currencies with enhanced caching
            $currency = SalaryCurrency::active()->orderBy('id')->pluck('currency_name', 'id')->toArray();
            $currencyIcon = SalaryCurrency::active()->orderBy('id')->pluck('currency_icon', 'id')->toArray();

            // Get plans with enhanced scopes
            $plans = Plan::with(['currency', 'activeSubscriptions'])
                ->when($request->filled('search'), function ($query) use ($request) {
                    $query->search($request->get('search'));
                })
                ->when($request->filled('currency_id'), function ($query) use ($request) {
                    $query->byCurrency($request->get('currency_id'));
                })
                ->active()
                ->alphabetical()
                ->paginate(20)
            ;

            // Get plan statistics
            $statistics = $this->getPlanStatistics();

            // Get popular plans
            $popularPlans = Plan::popular()->with('currency')->limit(5)->get();

            return [
                'currency' => $currency,
                'currencyIcon' => $currencyIcon,
                'plans' => $plans,
                'statistics' => $statistics,
                'popularPlans' => $popularPlans,
                'filters' => $request->only(['search', 'currency_id']),
            ];
        });
    }

    /**
     * Get plan statistics for dashboard.
     */
    private function getPlanStatistics(): array
    {
        return Cache::remember('plans.statistics', self::CACHE_TTL, function () {
            return [
                'total_plans' => Plan::count(),
                'active_plans' => Plan::active()->count(),
                'trial_plans' => Plan::trial()->count(),
                'popular_plans' => Plan::popular()->count(),
                'total_subscriptions' => Plan::withCount('activeSubscriptions')->get()->sum('active_subscriptions_count'),
                'average_price' => Plan::active()->avg('price'),
                'most_expensive_plan' => Plan::active()->orderBy('price', 'desc')->first()?->name ?? 'N/A',
                'most_popular_plan' => Plan::popular()->first()?->name ?? 'N/A',
            ];
        });
    }

    /**
     * Calculate conversion rate for a plan.
     */
    private function calculateConversionRate(Plan $plan): float
    {
        $totalViews = $plan->views ?? 0; // Assuming you track plan views
        $totalSubscriptions = $plan->subscriptions()->count();

        if (0 === $totalViews) {
            return 0.0;
        }

        return round(($totalSubscriptions / $totalViews) * 100, 2);
    }

    /**
     * Clear plan-related caches.
     */
    private function clearPlanCaches(?int $planId = null): void
    {
        $tags = ['plans', 'plans.api', 'plans.index', 'plans.statistics', 'plans.select'];

        if ($planId) {
            $tags[] = "plan.show.{$planId}";
        }

        foreach ($tags as $tag) {
            Cache::tags($tag)->flush();
        }

        // Clear specific cache keys
        Cache::forget('plans.statistics');
        Cache::forget('plans.currencies');
    }
}

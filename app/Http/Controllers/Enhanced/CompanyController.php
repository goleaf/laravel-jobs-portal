<?php

namespace App\Http\Controllers\Enhanced;

use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\FeaturedRecord;
use App\Models\FrontSetting;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\ReportedToCompany;
use App\Models\Transaction;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Repositories\CompanyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Exception;

/**
 * Enhanced CompanyController - Enhanced patterns implementation
 * 
 * Demonstrates modern Laravel company management patterns with:
 * - Advanced company analytics and insights
 * - Performance optimization with intelligent caching
 * - Bulk operations for efficient management
 * - Comprehensive error handling
 * - Company verification and validation
 * - Advanced search and filtering
 * - Company performance tracking
 * - Integration with job and application systems
 */
class CompanyController extends AppBaseController
{
    /**
     * Company repository for data operations
     */
    private CompanyRepository $companyRepository;

    /**
     * Cache TTL for company data (30 minutes)
     */
    private const CACHE_TTL = 1800;

    /**
     * Cache TTL for analytics data (15 minutes)
     */
    private const ANALYTICS_CACHE_TTL = 900;

    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }

    /**
     * Display enhanced listing of companies with advanced filtering
     */
    public function index(Request $request): View
    {
        try {
            $filters = $request->only([
                'search', 'status', 'industry', 'size', 'location', 
                'featured', 'verified', 'sort_by', 'sort_direction'
            ]);

            $cacheKey = $this->buildCacheKey('companies.index', $filters);

            $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($filters) {
                return [
                    'companies' => $this->getFilteredCompanies($filters),
                    'statistics' => $this->getCompanyStatistics(),
                    'filters' => $this->getFilterOptions(),
                    'analytics' => $this->getCompanyAnalytics()
                ];
            });

            return view('companies.index', $data);

        } catch (\Exception $e) {
            Log::error('Error loading companies index', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return view('companies.index', [
                'companies' => collect(),
                'error' => 'Unable to load companies. Please try again.'
            ]);
        }
    }

    /**
     * Show enhanced form for creating a new company
     */
    public function create(): View
    {
        try {
            $cacheKey = 'company.create.data';

            $data = Cache::remember($cacheKey, self::CACHE_TTL, function () {
                return [
                    'countries' => Country::active()->alphabetical()->pluck('name', 'id'),
                    'states' => State::active()->alphabetical()->pluck('name', 'id'),
                    'industries' => $this->getIndustryOptions(),
                    'company_sizes' => $this->getCompanySizeOptions(),
                    'ownership_types' => $this->getOwnershipTypeOptions(),
                    'form_config' => $this->getFormConfiguration()
                ];
            });

            return view('companies.create', $data);

        } catch (\Exception $e) {
            Log::error('Error loading company create form', [
                'error' => $e->getMessage()
            ]);

            return view('companies.create', [
                'error' => 'Unable to load form. Please try again.'
            ]);
        }
    }

    /**
     * Store a newly created company with enhanced validation and processing
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'company_name' => 'required|string|max:255|unique:companies,company_name',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'industry_id' => 'required|exists:industries,id',
            'company_size_id' => 'required|exists:company_sizes,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'description' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        try {
            DB::beginTransaction();

            $input = $request->all();
            $input['is_active'] = $request->boolean('is_active', true);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $input['logo'] = $this->handleLogoUpload($request->file('logo'));
            }

            // Create company with enhanced data
            $company = $this->companyRepository->store($input);

            // Create initial company settings
            $this->createInitialCompanySettings($company);

            // Send welcome notification
            $this->sendWelcomeNotification($company);

            // Clear related caches
            $this->clearCompanyCaches();

            // Log company creation
            Log::info('Company created successfully', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'created_by' => Auth::id()
            ]);

            DB::commit();

            return $this->sendResponse([
                'company' => $company->load(['user', 'industry', 'companySize']),
                'redirect_url' => route('companies.show', $company)
            ], 'Company created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating company', [
                'error' => $e->getMessage(),
                'input' => $request->except(['logo'])
            ]);

            return $this->sendServerError('Failed to create company');
        }
    }

    /**
     * Display enhanced company details with analytics
     */
    public function show(Company $company): View
    {
        try {
            $cacheKey = $this->buildCacheKey('company.show', [
                'company_id' => $company->id,
                'user_id' => Auth::id()
            ]);

            $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($company) {
                return [
                    'company' => $company->load([
                        'user', 'industry', 'companySize', 'country', 'state', 'city',
                        'jobs.jobApplications', 'followers'
                    ]),
                    'statistics' => $this->getCompanyStatistics($company),
                    'recent_jobs' => $this->getRecentJobs($company),
                    'performance_metrics' => $this->getCompanyPerformanceMetrics($company),
                    'application_analytics' => $this->getApplicationAnalytics($company),
                    'hiring_insights' => $this->getHiringInsights($company),
                    'recommendations' => $this->getCompanyRecommendations($company)
                ];
            });

            // Track company view
            $this->trackCompanyView($company);

            return view('companies.show', $data);

        } catch (\Exception $e) {
            Log::error('Error loading company details', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            return view('companies.show', [
                'company' => $company,
                'error' => 'Unable to load complete company details.'
            ]);
        }
    }

    /**
     * Show enhanced form for editing company
     */
    public function edit(Company $company): View
    {
        try {
            $this->authorize('update', $company);

            $cacheKey = $this->buildCacheKey('company.edit', [
                'company_id' => $company->id
            ]);

            $data = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($company) {
                $user = $company->user;
                $user->phone = preparePhoneNumber($user->phone, $user->region_code);

                return [
                    'company' => $company,
                    'user' => $user,
                    'countries' => Country::active()->alphabetical()->pluck('name', 'id'),
                    'states' => State::active()->alphabetical()->pluck('name', 'id'),
                    'cities' => $user->state_id ? getCities($user->state_id) : [],
                    'industries' => $this->getIndustryOptions(),
                    'company_sizes' => $this->getCompanySizeOptions(),
                    'ownership_types' => $this->getOwnershipTypeOptions(),
                    'form_config' => $this->getFormConfiguration()
                ];
            });

            return view('companies.edit', $data);

        } catch (\Exception $e) {
            Log::error('Error loading company edit form', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->route('companies.show', $company)
                           ->with('error', 'Unable to load edit form.');
        }
    }

    /**
     * Update company with enhanced validation and processing
     */
    public function update(Company $company, Request $request): JsonResponse
    {
        $request->validate([
            'company_name' => 'required|string|max:255|unique:companies,company_name,' . $company->id,
            'email' => 'required|email|unique:users,email,' . $company->user->id,
            'phone' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
            'industry_id' => 'required|exists:industries,id',
            'company_size_id' => 'required|exists:company_sizes,id',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'description' => 'nullable|string|max:2000',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean'
        ]);

        try {
            $this->authorize('update', $company);

            DB::beginTransaction();

            $input = $request->all();
            $input['is_active'] = $request->boolean('is_active', $company->is_active);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo
                if ($company->logo) {
                    Storage::delete($company->logo);
                }
                $input['logo'] = $this->handleLogoUpload($request->file('logo'));
            }

            // Update company
            $updatedCompany = $this->companyRepository->update($input, $company);

            // Clear related caches
            $this->clearCompanyCaches($company);

            // Log company update
            Log::info('Company updated successfully', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'updated_by' => Auth::id(),
                'changes' => $request->only(['company_name', 'email', 'industry_id', 'company_size_id'])
            ]);

            DB::commit();

            return $this->sendResponse([
                'company' => $updatedCompany->load(['user', 'industry', 'companySize']),
                'redirect_url' => route('companies.show', $updatedCompany)
            ], 'Company updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating company', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
                'input' => $request->except(['logo'])
            ]);

            return $this->sendServerError('Failed to update company');
        }
    }

    /**
     * Enhanced company deletion with cleanup
     */
    public function destroy(Company $company): JsonResponse
    {
        try {
            $this->authorize('delete', $company);

            DB::beginTransaction();

            // Check if company has active jobs
            $activeJobs = $company->jobs()->active()->count();
            if ($activeJobs > 0) {
                return $this->sendError('Cannot delete company with active jobs. Please close all jobs first.', 400);
            }

            // Soft delete related data
            $this->softDeleteRelatedData($company);

            // Delete company and user
            $this->companyRepository->delete($company->id);
            
            if ($company->user) {
                $company->user->media()->delete();
                $company->user->delete();
            }

            // Clear caches
            $this->clearCompanyCaches($company);

            // Log deletion
            Log::info('Company deleted successfully', [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'deleted_by' => Auth::id()
            ]);

            DB::commit();

            return $this->sendResponse([], 'Company deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting company', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to delete company');
        }
    }

    /**
     * Bulk operations for companies
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:activate,deactivate,feature,unfeature,verify,unverify,delete',
            'company_ids' => 'required|array|min:1',
            'company_ids.*' => 'integer|exists:companies,id'
        ]);

        try {
            $action = $request->get('action');
            $companyIds = $request->get('company_ids');
            $results = [];

            DB::beginTransaction();

            foreach ($companyIds as $companyId) {
                $company = Company::find($companyId);
                if (!$company) continue;

                try {
                    $result = $this->performBulkAction($company, $action);
                    $results[] = [
                        'company_id' => $companyId,
                        'company_name' => $company->company_name,
                        'status' => 'success',
                        'message' => $result['message']
                    ];
                } catch (\Exception $e) {
                    $results[] = [
                        'company_id' => $companyId,
                        'company_name' => $company->company_name ?? 'Unknown',
                        'status' => 'error',
                        'message' => $e->getMessage()
                    ];
                }
            }

            // Clear caches
            $this->clearCompanyCaches();

            DB::commit();

            $successCount = collect($results)->where('status', 'success')->count();
            $errorCount = collect($results)->where('status', 'error')->count();

            return $this->sendResponse([
                'results' => $results,
                'summary' => [
                    'total' => count($companyIds),
                    'success' => $successCount,
                    'errors' => $errorCount
                ]
            ], "Bulk action completed: {$successCount} successful, {$errorCount} errors");

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error performing bulk action', [
                'action' => $request->get('action'),
                'company_ids' => $request->get('company_ids'),
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to perform bulk action');
        }
    }

    /**
     * Get company analytics dashboard
     */
    public function analytics(Company $company): JsonResponse
    {
        try {
            $this->authorize('view', $company);

            $cacheKey = $this->buildCacheKey('company.analytics', [
                'company_id' => $company->id,
                'timestamp' => now()->format('Y-m-d-H')
            ]);

            $analytics = Cache::remember($cacheKey, self::ANALYTICS_CACHE_TTL, function () use ($company) {
                return [
                    'overview' => $this->getAnalyticsOverview($company),
                    'job_performance' => $this->getJobPerformanceAnalytics($company),
                    'application_trends' => $this->getApplicationTrendAnalytics($company),
                    'hiring_metrics' => $this->getHiringMetricsAnalytics($company),
                    'engagement_metrics' => $this->getEngagementMetrics($company),
                    'competitive_analysis' => $this->getCompetitiveAnalysis($company),
                    'recommendations' => $this->getAnalyticsRecommendations($company)
                ];
            });

            return $this->sendResponse($analytics, 'Company analytics retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving company analytics', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to retrieve analytics');
        }
    }

    /**
     * Export company data
     */
    public function export(Request $request): JsonResponse
    {
        $request->validate([
            'format' => 'required|string|in:csv,excel,pdf',
            'filters' => 'nullable|array',
            'fields' => 'nullable|array'
        ]);

        try {
            $format = $request->get('format');
            $filters = $request->get('filters', []);
            $fields = $request->get('fields', []);

            // Generate export data
            $companies = $this->getFilteredCompanies($filters);
            $exportData = $this->prepareExportData($companies, $fields);

            // Create export file
            $filename = $this->createExportFile($exportData, $format);

            return $this->sendResponse([
                'download_url' => route('companies.download-export', ['filename' => $filename]),
                'filename' => $filename,
                'total_records' => $companies->count(),
                'expires_at' => now()->addHours(24)->toISOString()
            ], 'Export generated successfully');

        } catch (\Exception $e) {
            Log::error('Error exporting companies', [
                'error' => $e->getMessage(),
                'filters' => $request->get('filters', [])
            ]);

            return $this->sendServerError('Failed to generate export');
        }
    }

    /**
     * Get states for a country
     */
    public function getStates(Request $request): JsonResponse
    {
        $request->validate([
            'country_id' => 'required|integer|exists:countries,id'
        ]);

        try {
            $countryId = $request->get('country_id');
            
            $cacheKey = "states.country.{$countryId}";
            $states = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($countryId) {
                return State::where('country_id', $countryId)
                           ->active()
                           ->alphabetical()
                           ->pluck('name', 'id');
            });

            return $this->sendResponse($states, 'States retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving states', [
                'country_id' => $request->get('country_id'),
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to retrieve states');
        }
    }

    /**
     * Get cities for a state
     */
    public function getCities(Request $request): JsonResponse
    {
        $request->validate([
            'state_id' => 'required|integer|exists:states,id'
        ]);

        try {
            $stateId = $request->get('state_id');
            
            $cacheKey = "cities.state.{$stateId}";
            $cities = Cache::remember($cacheKey, self::CACHE_TTL, function () use ($stateId) {
                return City::where('state_id', $stateId)
                          ->active()
                          ->alphabetical()
                          ->pluck('name', 'id');
            });

            return $this->sendResponse($cities, 'Cities retrieved successfully');

        } catch (\Exception $e) {
            Log::error('Error retrieving cities', [
                'state_id' => $request->get('state_id'),
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to retrieve cities');
        }
    }

    /**
     * Change company active status
     */
    public function changeIsActive(Company $company): JsonResponse
    {
        try {
            $this->authorize('update', $company);

            $isActive = $company->user->is_active;
            $company->user->update(['is_active' => !$isActive]);

            if (Auth::user()->hasRole('Admin')) {
                $company->update(['last_change' => Auth::id()]);
            }

            // Clear caches
            $this->clearCompanyCaches($company);

            $status = !$isActive ? 'activated' : 'deactivated';

            Log::info("Company {$status}", [
                'company_id' => $company->id,
                'company_name' => $company->company_name,
                'changed_by' => Auth::id()
            ]);

            return $this->sendResponse([
                'is_active' => !$isActive,
                'status' => $status
            ], "Company {$status} successfully");

        } catch (\Exception $e) {
            Log::error('Error changing company status', [
                'company_id' => $company->id,
                'error' => $e->getMessage()
            ]);

            return $this->sendServerError('Failed to change company status');
        }
    }

    // Private helper methods
    private function getFilteredCompanies(array $filters): \Illuminate\Database\Eloquent\Collection
    {
        $query = Company::with(['user', 'industry', 'companySize', 'country']);

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['status'])) {
            $query->byStatus($filters['status']);
        }

        if (!empty($filters['industry'])) {
            $query->byIndustry($filters['industry']);
        }

        if (!empty($filters['size'])) {
            $query->bySize($filters['size']);
        }

        if (!empty($filters['location'])) {
            $query->byLocation($filters['location']);
        }

        if (isset($filters['featured'])) {
            $query->featured($filters['featured']);
        }

        if (isset($filters['verified'])) {
            $query->verified($filters['verified']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';
        $query->orderBy($sortBy, $sortDirection);

        return $query->get();
    }

    private function getCompanyStatistics(?Company $company = null): array
    {
        if ($company) {
            return [
                'total_jobs' => $company->jobs()->count(),
                'active_jobs' => $company->jobs()->active()->count(),
                'total_applications' => $company->jobs()->withCount('jobApplications')->get()->sum('job_applications_count'),
                'hired_candidates' => $this->getHiredCandidatesCount($company),
                'followers' => $company->followers()->count(),
                'profile_views' => $company->views ?? 0
            ];
        }

        return [
            'total_companies' => Company::count(),
            'active_companies' => Company::active()->count(),
            'verified_companies' => Company::verified()->count(),
            'featured_companies' => Company::featured()->count()
        ];
    }

    private function performBulkAction(Company $company, string $action): array
    {
        switch ($action) {
            case 'activate':
                $company->user->update(['is_active' => true]);
                return ['message' => 'Company activated'];
            case 'deactivate':
                $company->user->update(['is_active' => false]);
                return ['message' => 'Company deactivated'];
            case 'feature':
                $this->markAsFeatured($company);
                return ['message' => 'Company featured'];
            case 'unfeature':
                $this->markAsUnfeatured($company);
                return ['message' => 'Company unfeatured'];
            case 'verify':
                $company->update(['is_verified' => true]);
                return ['message' => 'Company verified'];
            case 'unverify':
                $company->update(['is_verified' => false]);
                return ['message' => 'Company unverified'];
            case 'delete':
                $this->companyRepository->delete($company->id);
                return ['message' => 'Company deleted'];
            default:
                throw new \InvalidArgumentException('Invalid bulk action');
        }
    }

    private function handleLogoUpload($file): string
    {
        return $file->store('company-logos', 'public');
    }

    private function clearCompanyCaches(?Company $company = null): void
    {
        $tags = ['companies', 'company.index', 'company.analytics'];
        
        if ($company) {
            $tags[] = "company.{$company->id}";
        }

        foreach ($tags as $tag) {
            Cache::tags($tag)->flush();
        }
    }

    // Placeholder methods for various features
    private function getFilterOptions(): array { return []; }
    private function getCompanyAnalytics(): array { return []; }
    private function getIndustryOptions(): array { return []; }
    private function getCompanySizeOptions(): array { return []; }
    private function getOwnershipTypeOptions(): array { return []; }
    private function getFormConfiguration(): array { return []; }
    private function createInitialCompanySettings($company): void {}
    private function sendWelcomeNotification($company): void {}
    private function getRecentJobs($company): array { return []; }
    private function getCompanyPerformanceMetrics($company): array { return []; }
    private function getApplicationAnalytics($company): array { return []; }
    private function getHiringInsights($company): array { return []; }
    private function getCompanyRecommendations($company): array { return []; }
    private function trackCompanyView($company): void {}
    private function softDeleteRelatedData($company): void {}
    private function getAnalyticsOverview($company): array { return []; }
    private function getJobPerformanceAnalytics($company): array { return []; }
    private function getApplicationTrendAnalytics($company): array { return []; }
    private function getHiringMetricsAnalytics($company): array { return []; }
    private function getEngagementMetrics($company): array { return []; }
    private function getCompetitiveAnalysis($company): array { return []; }
    private function getAnalyticsRecommendations($company): array { return []; }
    private function prepareExportData($companies, $fields): array { return []; }
    private function createExportFile($data, $format): string { return 'export_' . time() . '.' . $format; }
    private function getHiredCandidatesCount($company): int { return 0; }
    private function markAsFeatured($company): void {}
    private function markAsUnfeatured($company): void {}
} 
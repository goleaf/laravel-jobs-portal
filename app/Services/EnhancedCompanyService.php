<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use App\Exceptions\CompanyCreationException;
use App\Exceptions\CompanyUpdateException;
use App\Exceptions\CompanyDeletionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Exception;

class EnhancedCompanyService
{
    public function __construct(
        private FileService $fileService,
        private NotificationService $notificationService
    ) {}

    /**
     * Create a new company with proper transaction handling
     */
    public function createCompany(array $data): Company
    {
        DB::beginTransaction();

        try {
            // Generate unique slug
            if (isset($data['name'])) {
                $data['slug'] = $this->generateUniqueSlug($data['name']);
            }

            // Handle logo upload
            if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                $data['logo_path'] = $this->fileService->uploadCompanyLogo($data['logo']);
                unset($data['logo']);
            }

            // Set default status
            $data['status'] = $data['status'] ?? Company::STATUS_ACTIVE;
            $data['is_featured'] = $data['is_featured'] ?? Company::FEATURED_NO;

            // Create company
            $company = Company::create($data);

            // Send notification to admin
            $this->notificationService->notifyAdminNewCompany($company);

            DB::commit();
            return $company->load(['user', 'industry', 'companySize', 'ownershipType']);
        } catch (Exception $e) {
            DB::rollBack();
            throw new CompanyCreationException('Failed to create company: ' . $e->getMessage());
        }
    }

    /**
     * Update company with transaction handling
     */
    public function updateCompany(Company $company, array $data): Company
    {
        DB::beginTransaction();

        try {
            // Generate new slug if name changed
            if (isset($data['name']) && $data['name'] !== $company->name) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $company->id);
            }

            // Handle logo upload
            if (isset($data['logo']) && $data['logo'] instanceof UploadedFile) {
                // Delete old logo
                if ($company->logo_path) {
                    $this->fileService->deleteFile($company->logo_path);
                }

                $data['logo_path'] = $this->fileService->uploadCompanyLogo($data['logo']);
                unset($data['logo']);
            }

            // Update company
            $company->update($data);

            // Send notification if status changed
            if (isset($data['status']) && $data['status'] !== $company->getOriginal('status')) {
                $this->notificationService->notifyCompanyStatusChange($company);
            }

            DB::commit();
            return $company->fresh(['user', 'industry', 'companySize', 'ownershipType']);
        } catch (Exception $e) {
            DB::rollBack();
            throw new CompanyUpdateException('Failed to update company: ' . $e->getMessage());
        }
    }

    /**
     * Delete company with cascade handling
     */
    public function deleteCompany(Company $company): bool
    {
        DB::beginTransaction();

        try {
            // Delete related jobs
            $company->jobs()->delete();

            // Delete related applications
            foreach ($company->jobs as $job) {
                $job->jobApplications()->delete();
            }

            // Delete logo file
            if ($company->logo_path) {
                $this->fileService->deleteFile($company->logo_path);
            }

            // Delete company media
            $company->media()->delete();

            // Soft delete company
            $company->delete();

            // Notify admin
            $this->notificationService->notifyAdminCompanyDeleted($company);

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw new CompanyDeletionException('Failed to delete company: ' . $e->getMessage());
        }
    }

    /**
     * Search companies with advanced filters
     */
    public function searchCompanies(array $filters): LengthAwarePaginator
    {
        $query = Company::query();

        // Apply status filter (default to active)
        if (isset($filters['status'])) {
            if ($filters['status'] !== 'all') {
                $query->where('status', $filters['status']);
            }
        } else {
            $query->active();
        }

        // Search filter
        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        // Industry filter
        if (!empty($filters['industry_id'])) {
            $query->byIndustry($filters['industry_id']);
        }

        // Company size filter
        if (!empty($filters['company_size_id'])) {
            $query->where('company_size_id', $filters['company_size_id']);
        }

        // Location filter
        if (!empty($filters['location'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('location', 'LIKE', "%{$filters['location']}%")
                  ->orWhere('location2', 'LIKE', "%{$filters['location']}%");
            });
        }

        // Featured filter
        if (!empty($filters['featured'])) {
            $query->featured();
        }

        // Establishment year range
        if (!empty($filters['established_from'])) {
            $query->where('established_in', '>=', $filters['established_from']);
        }

        if (!empty($filters['established_to'])) {
            $query->where('established_in', '<=', $filters['established_to']);
        }

        // Load relationships
        $query->with(['industry', 'companySize', 'ownershipType', 'user']);

        // Sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get featured companies
     */
    public function getFeaturedCompanies(int $limit = 10): Collection
    {
        return Company::featured()
            ->active()
            ->with(['industry', 'companySize', 'user'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get company statistics
     */
    public function getCompanyStats(): array
    {
        return [
            'total_companies' => Company::count(),
            'active_companies' => Company::active()->count(),
            'featured_companies' => Company::featured()->count(),
            'pending_companies' => Company::where('status', Company::STATUS_PENDING)->count(),
            'companies_by_industry' => Company::select('industry_id')
                ->selectRaw('count(*) as count')
                ->groupBy('industry_id')
                ->with('industry:id,name')
                ->get(),
            'companies_by_size' => Company::select('company_size_id')
                ->selectRaw('count(*) as count')
                ->groupBy('company_size_id')
                ->with('companySize:id,size')
                ->get(),
            'recent_companies' => Company::with(['user', 'industry'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get()
        ];
    }

    /**
     * Mark company as featured
     */
    public function markAsFeatured(Company $company): bool
    {
        try {
            $company->markAsFeatured();
            $this->notificationService->notifyCompanyFeatured($company);
            return true;
        } catch (Exception $e) {
            throw new CompanyUpdateException('Failed to mark company as featured: ' . $e->getMessage());
        }
    }

    /**
     * Unmark company as featured
     */
    public function unmarkAsFeatured(Company $company): bool
    {
        try {
            $company->unmarkAsFeatured();
            $this->notificationService->notifyCompanyUnfeatured($company);
            return true;
        } catch (Exception $e) {
            throw new CompanyUpdateException('Failed to unmark company as featured: ' . $e->getMessage());
        }
    }

    /**
     * Activate company
     */
    public function activateCompany(Company $company): bool
    {
        try {
            $company->activate();
            $company->user->activate();
            $this->notificationService->notifyCompanyActivated($company);
            return true;
        } catch (Exception $e) {
            throw new CompanyUpdateException('Failed to activate company: ' . $e->getMessage());
        }
    }

    /**
     * Deactivate company
     */
    public function deactivateCompany(Company $company): bool
    {
        try {
            $company->deactivate();
            $company->user->deactivate();
            $this->notificationService->notifyCompanyDeactivated($company);
            return true;
        } catch (Exception $e) {
            throw new CompanyUpdateException('Failed to deactivate company: ' . $e->getMessage());
        }
    }

    /**
     * Get companies by user
     */
    public function getCompaniesByUser(User $user): Collection
    {
        if ($user->isEmployer()) {
            return collect([$user->company])->filter();
        }

        if ($user->hasRole(['Admin', 'Super Admin'])) {
            return Company::with(['industry', 'companySize', 'user'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return collect();
    }

    /**
     * Bulk update companies
     */
    public function bulkUpdateCompanies(array $companyIds, array $data): int
    {
        DB::beginTransaction();

        try {
            $updated = Company::whereIn('id', $companyIds)->update($data);
            
            // Send notifications if status changed
            if (isset($data['status'])) {
                $companies = Company::whereIn('id', $companyIds)->get();
                foreach ($companies as $company) {
                    $this->notificationService->notifyCompanyStatusChange($company);
                }
            }

            DB::commit();
            return $updated;
        } catch (Exception $e) {
            DB::rollBack();
            throw new CompanyUpdateException('Failed to bulk update companies: ' . $e->getMessage());
        }
    }

    /**
     * Export companies data
     */
    public function exportCompanies(array $filters = []): Collection
    {
        $query = Company::query();

        // Apply same filters as search
        if (!empty($filters['industry_id'])) {
            $query->byIndustry($filters['industry_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['featured'])) {
            $query->featured();
        }

        return $query->with(['industry', 'companySize', 'ownershipType', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    /**
     * Get similar companies
     */
    public function getSimilarCompanies(Company $company, int $limit = 5): Collection
    {
        return Company::where('id', '!=', $company->id)
            ->where('industry_id', $company->industry_id)
            ->active()
            ->with(['industry', 'companySize'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Generate unique slug for company
     */
    private function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $query = Company::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
            
            $query = Company::where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }

    /**
     * Validate company ownership
     */
    public function validateOwnership(Company $company, User $user): bool
    {
        return $user->hasRole(['Admin', 'Super Admin']) || 
               ($user->hasRole('Employer') && $user->id === $company->user_id);
    }

    /**
     * Get company dashboard data
     */
    public function getCompanyDashboardData(Company $company): array
    {
        return [
            'total_jobs' => $company->jobs()->count(),
            'active_jobs' => $company->activeJobs()->count(),
            'total_applications' => $company->jobs()->withCount('jobApplications')->get()->sum('job_applications_count'),
            'recent_applications' => $company->jobs()
                ->with(['jobApplications.candidate.user'])
                ->get()
                ->pluck('jobApplications')
                ->flatten()
                ->sortByDesc('created_at')
                ->take(10),
            'job_views' => $company->jobs()->sum('views'),
            'company_views' => $company->views ?? 0,
        ];
    }
} 
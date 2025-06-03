<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CompanyService
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Get all companies with pagination
     */
    public function getAllPaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Company::with(['user', 'industry', 'companySize', 'ownerShipType'])
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Get active companies with pagination
     */
    public function getActivePaginated(int $perPage = 15): LengthAwarePaginator
    {
        return Company::with(['user', 'industry', 'companySize'])
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create a new company with user
     */
    public function create(array $data): Company
    {
        DB::beginTransaction();
        
        try {
            // Create user first
            $userData = [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'user_type' => User::EMPLOYER,
                'is_active' => $data['is_active'] ?? true,
                'phone' => $data['phone'] ?? null,
                'country_id' => $data['country_id'] ?? null,
                'state_id' => $data['state_id'] ?? null,
                'city_id' => $data['city_id'] ?? null,
            ];
            
            $user = $this->userService->create($userData);
            
            // Create company
            $companyData = [
                'user_id' => $user->id,
                'ceo' => $data['ceo'],
                'industry_id' => $data['industry_id'],
                'ownership_type_id' => $data['ownership_type_id'],
                'company_size_id' => $data['company_size_id'],
                'established_in' => $data['established_in'],
                'website' => $data['website'] ?? null,
                'location' => $data['location'],
                'no_of_offices' => $data['no_of_offices'],
                'details' => $data['details'] ?? null,
                'unique_id' => $this->generateUniqueId($data['ceo']),
            ];
            
            $company = Company::create($companyData);
            
            DB::commit();
            return $company->load(['user', 'industry', 'companySize', 'ownerShipType']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Update company data
     */
    public function update(Company $company, array $data): Company
    {
        DB::beginTransaction();
        
        try {
            // Update user data if provided
            $userFields = ['first_name', 'last_name', 'email', 'phone', 'country_id', 'state_id', 'city_id'];
            $userData = array_intersect_key($data, array_flip($userFields));
            
            if (!empty($userData)) {
                $company->user->update($userData);
            }
            
            // Update company data
            $companyFields = [
                'ceo', 'industry_id', 'ownership_type_id', 'company_size_id',
                'established_in', 'website', 'location', 'no_of_offices', 'details'
            ];
            $companyData = array_intersect_key($data, array_flip($companyFields));
            
            if (!empty($companyData)) {
                $company->update($companyData);
            }
            
            DB::commit();
            return $company->fresh(['user', 'industry', 'companySize', 'ownerShipType']);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Delete company and deactivate user
     */
    public function delete(Company $company): bool
    {
        DB::beginTransaction();
        
        try {
            // Close all active jobs
            $company->jobs()->where('status', 1)->update(['status' => 2]); // Close jobs
            
            // Deactivate user instead of deleting for data integrity
            $company->user->update(['is_active' => false]);
            
            // Soft delete company if using SoftDeletes trait
            $company->delete();
            
            DB::commit();
            return true;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Search companies by various criteria
     */
    public function search(string $query): Collection
    {
        return Company::where(function($q) use ($query) {
                $q->where('ceo', 'like', "%{$query}%")
                  ->orWhere('location', 'like', "%{$query}%")
                  ->orWhereHas('user', fn($u) => $u->where('first_name', 'like', "%{$query}%")
                                                   ->orWhere('last_name', 'like', "%{$query}%"))
                  ->orWhereHas('industry', fn($i) => $i->where('name', 'like', "%{$query}%"));
            })
            ->with(['user', 'industry', 'companySize'])
            ->get();
    }

    /**
     * Get featured companies
     */
    public function getFeatured(int $limit = 10): Collection
    {
        return Company::whereHas('featured')
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->with(['user', 'industry', 'companySize'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get companies by industry
     */
    public function getByIndustry(int $industryId, int $limit = null): Collection
    {
        $query = Company::where('industry_id', $industryId)
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->with(['user', 'industry', 'companySize']);

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get companies with most jobs
     */
    public function getTopEmployers(int $limit = 10): Collection
    {
        return Company::withCount(['jobs' => fn($q) => $q->where('status', 1)]) // Active jobs
            ->whereHas('user', fn($q) => $q->where('is_active', true))
            ->orderBy('jobs_count', 'desc')
            ->with(['user', 'industry'])
            ->limit($limit)
            ->get();
    }

    /**
     * Toggle company active status
     */
    public function toggleActiveStatus(Company $company): Company
    {
        $company->user->update(['is_active' => !$company->user->is_active]);
        return $company->fresh(['user']);
    }

    /**
     * Get company statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_companies' => Company::count(),
            'active_companies' => Company::whereHas('user', fn($q) => $q->where('is_active', true))->count(),
            'featured_companies' => Company::whereHas('featured')->count(),
            'companies_with_jobs' => Company::whereHas('jobs', fn($q) => $q->where('status', 1))->count(),
        ];
    }

    /**
     * Generate unique ID for company
     */
    private function generateUniqueId(string $ceoName): string
    {
        $base = strtolower(str_replace(' ', '-', $ceoName));
        $base = preg_replace('/[^a-z0-9-]/', '', $base);
        $base = substr($base, 0, 20);
        
        $uniqueId = $base . '-' . date('Y');
        $counter = 1;
        
        while (Company::where('unique_id', $uniqueId)->exists()) {
            $uniqueId = $base . '-' . date('Y') . '-' . $counter;
            $counter++;
        }
        
        return $uniqueId;
    }
} 
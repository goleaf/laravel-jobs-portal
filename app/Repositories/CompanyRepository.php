<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\CompanySize;
use App\Models\Industry;
use App\Models\OwnerShipType;
use App\Models\User;

class CompanyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        'company_name',
        'details',
    ];

    public function __construct()
    {
        parent::__construct(new Company);
    }

    public function model()
    {
        return Company::class;
    }

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }

    /**
     * Prepare data for company forms.
     */
    public function prepareData(): array
    {
        return [
            'industries' => Industry::active()->pluck('name', 'id'),
            'company_sizes' => CompanySize::active()->pluck('size', 'id'),
            'ownership_types' => OwnerShipType::active()->pluck('name', 'id'),
        ];
    }

    /**
     * Store company with enhanced data processing.
     */
    public function store(array $input): Company
    {
        // Create user first
        $userData = [
            'first_name' => $input['first_name'] ?? '',
            'last_name' => $input['last_name'] ?? '',
            'email' => $input['email'],
            'password' => bcrypt($input['password'] ?? 'defaultpassword'),
            'user_type' => 'employer',
            'is_active' => $input['is_active'] ?? true,
        ];

        $user = User::create($userData);
        $user->assignRole('Employer');

        // Create company
        $companyData = array_merge($input, ['user_id' => $user->id]);

        return $this->create($companyData);
    }

    /**
     * Update company with enhanced data processing using Collection forget().
     */
    public function updateCompany(array $input, Company $company): Company
    {
        // Update user data
        if (isset($input['email']) || isset($input['first_name']) || isset($input['last_name'])) {
            $userData = array_filter([
                'first_name' => $input['first_name'] ?? null,
                'last_name' => $input['last_name'] ?? null,
                'email' => $input['email'] ?? null,
                'is_active' => $input['is_active'] ?? null,
            ]);

            if (! empty($userData)) {
                $company->user->update($userData);
            }
        }

        // Enhanced company data processing with dynamic field removal
        $companyData = collect($input);

        // Core fields that should never be in company data
        $coreUserFields = ['first_name', 'last_name', 'email', 'password', 'password_confirmation'];
        $companyData->forget($coreUserFields);

        // Role-based field removal
        $currentUser = auth()->user();
        if ($currentUser && ! $currentUser->hasRole('admin')) {
            $adminOnlyFields = ['is_featured', 'priority_score', 'admin_notes', 'internal_rating'];
            $companyData->forget($adminOnlyFields);
        }

        // Subscription-based field removal
        if ($currentUser && ! $currentUser->hasActiveSubscription()) {
            $premiumFields = ['premium_branding', 'advanced_analytics', 'priority_support'];
            $companyData->forget($premiumFields);
        }

        // Remove temporary/deprecated fields
        $temporaryFields = $this->getTemporaryFields();
        $companyData->forget($temporaryFields);

        // Log data changes for audit trail
        $this->logCompanyDataChanges($company, $companyData->toArray());

        $company->update($companyData->toArray());

        return $company->fresh();
    }

    /**
     * Get temporary fields that should be removed.
     */
    protected function getTemporaryFields(): array
    {
        return [
            'temp_logo_url',
            'draft_description',
            'legacy_company_id',
            'import_source',
            'session_data',
            'cache_key',
        ];
    }

    /**
     * Log company data changes for audit trail.
     */
    protected function logCompanyDataChanges(Company $company, array $newData): void
    {
        try {
            $originalData = $company->toArray();
            $changes = [];

            foreach ($newData as $key => $value) {
                if (isset($originalData[$key]) && $originalData[$key] !== $value) {
                    $changes[$key] = [
                        'old' => $originalData[$key],
                        'new' => $value,
                    ];
                }
            }

            if (! empty($changes)) {
                \Log::info('Company data updated', [
                    'company_id' => $company->id,
                    'user_id' => auth()->id(),
                    'changes' => $changes,
                ]);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to log company changes', [
                'company_id' => $company->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

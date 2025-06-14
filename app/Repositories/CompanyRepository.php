<?php

namespace App\Repositories;

use App\Models\Company;
use Illuminate\Container\Container as Application;

class CompanyRepository extends BaseRepository
{
    protected $fieldSearchable = [
        "company_name",
        "details"
    ];

    public function __construct()
    {
        parent::__construct(new Company());
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
     * Prepare data for company forms
     */
    public function prepareData(): array
    {
        return [
            'industries' => \App\Models\Industry::active()->pluck('name', 'id'),
            'company_sizes' => \App\Models\CompanySize::active()->pluck('size', 'id'),
            'ownership_types' => \App\Models\OwnerShipType::active()->pluck('name', 'id'),
        ];
    }

    /**
     * Store company with enhanced data processing
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

        $user = \App\Models\User::create($userData);
        $user->assignRole('Employer');

        // Create company
        $companyData = array_merge($input, ['user_id' => $user->id]);
        return $this->create($companyData);
    }

    /**
     * Update company with enhanced data processing
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

            if (!empty($userData)) {
                $company->user->update($userData);
            }
        }

        // Update company data
        $companyData = collect($input)->except(['first_name', 'last_name', 'email', 'password'])->toArray();
        $company->update($companyData);

        return $company->fresh();
    }
}

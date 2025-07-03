<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Repositories\CompanyRepository;
use App\Services\CollectionForgetUtility;
use App\Services\JobSearchService;
use App\Services\TwoFactorAuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\BaseTestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CollectionForgetIntegrationTest extends BaseTestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_collection_forget_utility_sanitizes_user_input()
    {
        $input = [
            'name' => 'Test Company',
            'email' => 'test@example.com',
            '_token' => 'csrf_token_here',
            '_method' => 'POST',
            'password_confirmation' => 'secret',
            'is_featured' => true,
            'admin_notes' => 'Internal note',
        ];

        // Test admin user (should keep all valid fields)
        $result = CollectionForgetUtility::sanitizeUserInput($input, 'admin', true);

        $this->assertArrayNotHasKey('_token', $result);
        $this->assertArrayNotHasKey('_method', $result);
        $this->assertArrayNotHasKey('password_confirmation', $result);
        $this->assertArrayHasKey('is_featured', $result);
        $this->assertArrayHasKey('admin_notes', $result);

        // Test regular user (should remove admin fields)
        $result = CollectionForgetUtility::sanitizeUserInput($input, 'user', false);

        $this->assertArrayNotHasKey('is_featured', $result);
        $this->assertArrayNotHasKey('admin_notes', $result);
    }

    /** @test */
    public function test_job_search_service_processes_filters()
    {
        $request = Request::create('/search', 'GET', [
            'keyword' => 'developer',
            'location' => 'New York',
            '_token' => 'csrf_token',
            'page' => 1,
            'submit' => 'Search',
            'salary_range_advanced' => '100000-150000',
            'remote_work_options' => true,
            '' => '', // empty value
        ]);

        $searchService = new JobSearchService;
        $filters = $searchService->processAdvancedFilters($request);

        // Should remove meta fields
        $this->assertFalse($filters->has('_token'));
        $this->assertFalse($filters->has('page'));
        $this->assertFalse($filters->has('submit'));

        // Should keep valid search filters
        $this->assertTrue($filters->has('keyword'));
        $this->assertTrue($filters->has('location'));

        // Should remove empty values
        $this->assertFalse($filters->has(''));
    }

    /** @test */
    public function test_company_repository_update_with_forget()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        $input = [
            'company_name' => 'Updated Company',
            'description' => 'New description',
            'first_name' => 'John', // Should be removed
            'last_name' => 'Doe', // Should be removed
            'email' => 'john@example.com', // Should be removed
            'password' => 'secret', // Should be removed
            'temp_logo_url' => 'temp_logo.jpg', // Should be removed
            'is_featured' => true, // Should be removed for non-admin
        ];

        $this->actingAs($user);

        // Simulate the update method
        $companyRepository = new CompanyRepository;
        $updatedCompany = $companyRepository->updateCompany($input, $company);

        // Verify user fields were not updated in company
        $this->assertEquals('Updated Company', $updatedCompany->company_name);
        $this->assertEquals('New description', $updatedCompany->description);

        // Verify company doesn't have user-specific fields
        $this->assertNull($updatedCompany->first_name);
        $this->assertNull($updatedCompany->last_name);
    }

    /** @test */
    public function test_base_test_case_helper_methods()
    {
        $testData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'secret',
            'admin_fields' => 'admin data',
            'premium_features' => 'premium data',
        ];

        // Test removing specific fields
        $result = $this->removeTestFields($testData, ['password', 'admin_fields']);

        $this->assertArrayNotHasKey('password', $result);
        $this->assertArrayNotHasKey('admin_fields', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('email', $result);

        // Test role-based field removal
        $result = $this->removeFieldsForRole($testData, 'basic_user');

        $this->assertArrayNotHasKey('premium_features', $result);
        $this->assertArrayNotHasKey('admin_fields', $result);
        $this->assertArrayHasKey('name', $result);
    }

    /** @test */
    public function test_advanced_cleanup_functionality()
    {
        $data = [
            'name' => 'Test',
            'email' => '',
            'description' => null,
            'temp_field' => 'temporary',
            'cache_data' => 'cached',
            'valid_field' => 'valid data',
        ];

        $options = [
            'remove_empty' => true,
            'remove_null' => true,
            'remove_patterns' => ['temp_', 'cache_'],
        ];

        $result = CollectionForgetUtility::advancedCleanup($data, $options);

        $this->assertArrayNotHasKey('email', $result); // empty
        $this->assertArrayNotHasKey('description', $result); // null
        $this->assertArrayNotHasKey('temp_field', $result); // pattern match
        $this->assertArrayNotHasKey('cache_data', $result); // pattern match
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('valid_field', $result);
    }

    /** @test */
    public function test_two_factor_auth_service_backup_code_removal()
    {
        $user = User::factory()->create([
            'two_factor_enabled' => true,
            'two_factor_backup_codes' => encrypt(json_encode([
                password_hash('code1', PASSWORD_DEFAULT),
                password_hash('code2', PASSWORD_DEFAULT),
                password_hash('code3', PASSWORD_DEFAULT),
            ])),
        ]);

        $twoFactorService = new TwoFactorAuthService;

        // The backup code verification should use forget() internally
        $remainingBefore = $twoFactorService->getRemainingBackupCodesCount($user);
        $this->assertEquals(3, $remainingBefore);

        // Verify a backup code (this should remove it using forget())
        $verified = $twoFactorService->verifyLogin($user, 'code1');

        // Check that the code was removed
        $remainingAfter = $twoFactorService->getRemainingBackupCodesCount($user->fresh());
        $this->assertEquals(2, $remainingAfter);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanyProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function company_profile_page_can_be_viewed()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->get(route('company.edit.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('employer.company.edit_profile');
    }

    /** @test */
    public function company_profile_can_be_updated()
    {
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);

        $updatedData = [
            'name' => 'Updated Company Name',
            'email' => 'updated@example.com',
            'phone' => '1234567890',
            'website' => 'https://updatedcompany.com',
            'ceo' => 'Updated CEO',
            'industry_id' => 1,
            'ownership_type_id' => 1,
            'company_size_id' => 1,
            'established_in' => '2010',
            'details' => 'Updated company details',
        ];

        $response = $this->actingAs($user)
            ->put(route('company.profile.update'), $updatedData);

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', ['name' => 'Updated Company Name']);
    }

    /** @test */
    public function company_logo_can_be_uploaded()
    {
        Storage::fake('public');
        
        $user = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $user->id]);
        $file = UploadedFile::fake()->image('company_logo.jpg');

        $response = $this->actingAs($user)
            ->post(route('company.update-image'), [
                'logo' => $file
            ]);

        $response->assertRedirect();
        Storage::disk('public')->assertExists('companies/logo/' . $file->hashName());
    }

    /** @test */
    public function company_details_page_can_be_viewed_by_public()
    {
        $company = Company::factory()->create(['is_active' => true]);

        $response = $this->get(route('front.company.details', $company->id));

        $response->assertStatus(200);
        $response->assertViewIs('front_web.company.company_details');
        $response->assertSee($company->name);
    }

    /** @test */
    public function inactive_company_cannot_be_viewed_by_public()
    {
        $company = Company::factory()->create(['is_active' => false]);

        $response = $this->get(route('front.company.details', $company->id));

        $response->assertStatus(404);
    }

    /** @test */
    public function company_jobs_can_be_viewed()
    {
        $company = Company::factory()->create(['is_active' => true]);

        $response = $this->get(route('front.company.details', $company->id));

        $response->assertStatus(200);
        $response->assertSee('Jobs');
    }
} 
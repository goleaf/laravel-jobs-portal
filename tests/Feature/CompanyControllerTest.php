<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function it_can_display_index_page()
    {
        $response = $this->actingAs($this->user)->get('/company');

        $response->assertStatus(200);
        $response->assertViewIs('company.index');
    }

    /** @test */
    public function it_can_create_company()
    {
        $data = Company::factory()->make()->toArray();

        $response = $this->actingAs($this->user)->post('/company', $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('companys', array_slice($data, 0, 3));
    }

    /** @test */
    public function it_can_update_company()
    {
        $company = Company::factory()->create();
        $data = Company::factory()->make()->toArray();

        $response = $this->actingAs($this->user)->put('/company/{$company->id}', $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('companys', ['id' => $company->id]);
    }

    /** @test */
    public function it_can_delete_company()
    {
        $company = Company::factory()->create();

        $response = $this->actingAs($this->user)->delete('/company/{$company->id}');

        $response->assertStatus(302);
        $this->assertSoftDeleted($company);
    }
}

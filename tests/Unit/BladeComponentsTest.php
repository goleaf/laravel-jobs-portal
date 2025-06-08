<?php

namespace Tests\Unit;

use App\Models\CompanySize;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BladeComponentsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function action_button_component_renders_correctly()
    {
        $companySize = CompanySize::factory()->create(['size' => 'Test Company Size']);
        
        $renderedView = view('company_sizes.table-components.action_button', [
            'row' => $companySize
        ])->render();
        
        $this->assertStringContainsString('data-id="' . $companySize->id . '"', $renderedView);
        $this->assertStringContainsString('flex items-center space-x-2', $renderedView);
        $this->assertStringContainsString('inline-flex items-center', $renderedView);
        $this->assertStringContainsString('text-indigo-600', $renderedView);
    }

    /** @test */
    public function action_button_component_has_proper_accessibility_attributes()
    {
        $companySize = CompanySize::factory()->create(['size' => 'Test Company Size']);
        
        $renderedView = view('company_sizes.table-components.action_button', [
            'row' => $companySize
        ])->render();
        
        $this->assertStringContainsString('aria-label="Edit company size"', $renderedView);
        $this->assertStringContainsString('aria-label="Delete company size"', $renderedView);
        $this->assertStringContainsString('title="Edit"', $renderedView);
        $this->assertStringContainsString('title="Delete"', $renderedView);
    }

    /** @test */
    public function action_button_component_uses_svg_icons_instead_of_font_awesome()
    {
        $companySize = CompanySize::factory()->create(['size' => 'Test Company Size']);
        
        $renderedView = view('company_sizes.table-components.action_button', [
            'row' => $companySize
        ])->render();
        
        // Should contain SVG elements, not FontAwesome icons
        $this->assertStringContainsString('<svg', $renderedView);
        $this->assertStringNotContainsString('fa-solid', $renderedView);
        $this->assertStringNotContainsString('fa-pen-to-square', $renderedView);
        $this->assertStringNotContainsString('fa-trash', $renderedView);
    }

    /** @test */
    public function action_button_component_uses_tailwind_classes()
    {
        $companySize = CompanySize::factory()->create(['size' => 'Test Company Size']);
        
        $renderedView = view('company_sizes.table-components.action_button', [
            'row' => $companySize
        ])->render();
        
        // Should use modern TailwindCSS classes
        $this->assertStringContainsString('flex items-center space-x-2', $renderedView);
        $this->assertStringContainsString('inline-flex items-center', $renderedView);
        $this->assertStringContainsString('text-indigo-600', $renderedView);
        $this->assertStringContainsString('text-red-600', $renderedView);
        
        // Should not contain Bootstrap classes
        $this->assertStringNotContainsString('btn btn-', $renderedView);
        $this->assertStringNotContainsString('fs-3', $renderedView);
        $this->assertStringNotContainsString('ps-0', $renderedView);
        $this->assertStringNotContainsString('pe-0', $renderedView);
    }

    /** @test */
    public function action_button_component_has_proper_data_attributes()
    {
        $companySize = CompanySize::factory()->create(['size' => 'Test Company Size']);
        
        $renderedView = view('company_sizes.table-components.action_button', [
            'row' => $companySize
        ])->render();
        
        $this->assertStringContainsString('data-id="' . $companySize->id . '"', $renderedView);
        
        // Should appear twice (once for edit, once for delete)
        $this->assertEquals(2, substr_count($renderedView, 'data-id="' . $companySize->id . '"'));
    }

    /** @test */
    public function action_button_component_uses_correct_css_classes()
    {
        $companySize = CompanySize::factory()->create(['size' => 'Test Company Size']);
        
        $renderedView = view('company_sizes.table-components.action_button', [
            'row' => $companySize
        ])->render();
        
        // Check for modern TailwindCSS patterns
        $this->assertStringContainsString('bg-indigo-100', $renderedView);
        $this->assertStringContainsString('bg-red-100', $renderedView);
        $this->assertStringContainsString('rounded-md', $renderedView);
        
        // Should not contain old incorrect classes
        $this->assertStringNotContainsString('company-size-edit-old', $renderedView);
    }
} 
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class JobPortalWebTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test that homepage loads successfully.
     */
    public function testHomepageLoadsSuccessfully()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Find Your Dream Job Today');
        $response->assertSee('Job Portal');
        $response->assertSee('Browse Jobs');
        $response->assertSee('Get Started');
    }

    /**
     * Test homepage contains expected sections.
     */
    public function testHomepageContainsExpectedSections()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Hero section
        $response->assertSee('Connect with top employers');
        // Stats section
        $response->assertSee('Active Jobs');
        $response->assertSee('Companies');
        $response->assertSee('Candidates');
        // Featured jobs section
        $response->assertSee('Featured Jobs');
        // How it works section
        $response->assertSee('How It Works');
        $response->assertSee('Create Profile');
        $response->assertSee('Search Jobs');
        $response->assertSee('Get Hired');
    }

    /**
     * Test jobs index page loads.
     */
    public function testJobsIndexPageLoads()
    {
        $response = $this->get('/jobs');

        $response->assertStatus(200);
        $response->assertSee('Browse Jobs');
        $response->assertSee('Find Your Dream Job');
        $response->assertSee('Jobs Found');
        $response->assertSee('Filters');
    }

    /**
     * Test jobs search functionality.
     */
    public function testJobsSearchFunctionality()
    {
        $response = $this->get('/jobs?keyword=developer&location=New+York&category=technology');

        $response->assertStatus(200);
        $response->assertSee('Browse Jobs');
        // Check that search parameters are preserved
        $this->assertEquals('developer', request('keyword'));
        $this->assertEquals('New York', request('location'));
        $this->assertEquals('technology', request('category'));
    }

    /**
     * Test job detail page loads.
     */
    public function testJobDetailPageLoads()
    {
        $response = $this->get('/jobs/1');

        $response->assertStatus(200);
        $response->assertSee('Job Details');
        $response->assertSee('Senior Software Developer');
        $response->assertSee('TechCorp Solutions');
        $response->assertSee('Apply Now');
        $response->assertSee('Save Job');
        $response->assertSee('Job Description');
        $response->assertSee('Required Skills');
    }

    /**
     * Test companies index page loads.
     */
    public function testCompaniesIndexPageLoads()
    {
        $response = $this->get('/companies');

        $response->assertStatus(200);
        $response->assertSee('Browse Companies');
        $response->assertSee('Discover amazing companies');
        $response->assertSee('Search Companies');
        $response->assertSee('All Industries');
    }

    /**
     * Test company detail page loads.
     */
    public function testCompanyDetailPageLoads()
    {
        $response = $this->get('/company/1');

        $response->assertStatus(200);
        // This route should load even if it doesn't have specific content yet
    }

    /**
     * Test about us page loads.
     */
    public function testAboutUsPageLoads()
    {
        $response = $this->get('/about-us');

        $response->assertStatus(200);
        $response->assertSee('About Our Job Portal');
        $response->assertSee('Our Mission');
        $response->assertSee('Our Vision');
        $response->assertSee('Why Choose Us?');
        $response->assertSee('Smart Matching');
        $response->assertSee('Secure Platform');
    }

    /**
     * Test contact page loads.
     */
    public function testContactPageLoads()
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertSee('Contact Us');
        $response->assertSee('Send us a Message');
        $response->assertSee('Full Name');
        $response->assertSee('Email Address');
        $response->assertSee('Send Message');
    }

    /**
     * Test navigation menu is present.
     */
    public function testNavigationMenuIsPresent()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Jobs');
        $response->assertSee('Companies');
        $response->assertSee('About');
        $response->assertSee('Contact');
        $response->assertSee('Login');
        $response->assertSee('Register');
    }

    /**
     * Test footer is present on pages.
     */
    public function testFooterIsPresent()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Quick Links');
        $response->assertSee('For Job Seekers');
        $response->assertSee('For Employers');
        $response->assertSee('Support');
        $response->assertSee('All rights reserved');
    }

    /**
     * Test login and register routes exist.
     */
    public function testAuthRoutesExist()
    {
        $loginResponse = $this->get('/login');
        $registerResponse = $this->get('/register');

        // These might return different status codes depending on auth setup
        // but should not return 404
        $this->assertNotEquals(404, $loginResponse->status());
        $this->assertNotEquals(404, $registerResponse->status());
    }

    /**
     * Test API test route works.
     */
    public function testApiTestRouteWorks()
    {
        $response = $this->get('/test');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'ok',
            'message' => 'Laravel is working!',
        ]);
        $response->assertJsonStructure([
            'status',
            'message',
            'timestamp',
            'memory_usage',
        ]);
    }

    /**
     * Test responsive design elements are present.
     */
    public function testResponsiveDesignElements()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Check for Bootstrap classes that ensure responsiveness
        $response->assertSee('container');
        $response->assertSee('col-lg-');
        $response->assertSee('col-md-');
        $response->assertSee('navbar-toggler');
    }

    /**
     * Test error handling for non-existent pages.
     */
    public function test404ErrorHandling()
    {
        $response = $this->get('/non-existent-page');

        $response->assertStatus(404);
    }

    /**
     * Test search form submission.
     */
    public function testSearchFormSubmission()
    {
        $searchData = [
            'keyword' => 'Software Developer',
            'location' => 'San Francisco',
            'category' => 'technology',
        ];

        $response = $this->get('/jobs?'.http_build_query($searchData));

        $response->assertStatus(200);
        $response->assertSee('Software Developer');
    }

    /**
     * Test that CSS and JS assets are referenced.
     */
    public function testAssetsAreReferenced()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Check for Bootstrap CSS
        $response->assertSee('bootstrap');
        // Check for Font Awesome
        $response->assertSee('font-awesome');
        // Check for custom CSS sections
        $response->assertSee('hero-section');
    }

    /**
     * Test job filtering functionality.
     */
    public function testJobFilteringFunctionality()
    {
        $response = $this->get('/jobs');

        $response->assertStatus(200);
        // Check filter options are present
        $response->assertSee('Job Type');
        $response->assertSee('Full-time');
        $response->assertSee('Part-time');
        $response->assertSee('Contract');
        $response->assertSee('Remote');
        $response->assertSee('Salary Range');
        $response->assertSee('Experience Level');
    }

    /**
     * Test company search functionality.
     */
    public function testCompanySearchFunctionality()
    {
        $response = $this->get('/companies?search=tech&industry=technology');

        $response->assertStatus(200);
        $response->assertSee('Browse Companies');
    }

    /**
     * Test social media and sharing elements.
     */
    public function testSocialMediaElements()
    {
        $response = $this->get('/jobs/1');

        $response->assertStatus(200);
        $response->assertSee('Share this Job');
        $response->assertSee('fa-facebook');
        $response->assertSee('fa-twitter');
        $response->assertSee('fa-linkedin');
    }
}

<?php

/**
 * Comprehensive Testing Framework Optimization
 * Priority 6: Comprehensive Testing Implementation
 */

echo "🚀 Starting Testing Framework Optimization...\n";
echo "===============================================\n\n";

// Step 1: Fix Database Seeder Issues
echo "📊 Step 1: Fixing Database Seeder Issues...\n";

$seederFile = 'database/seeders/DefaultLastChangeBySeeder.php';
if (file_exists($seederFile)) {
    $content = file_get_contents($seederFile);
    
    // Fix the seeder issue with property access
    $fixedContent = str_replace(
        'User::first()->id',
        'optional(User::first())->id ?? 1',
        $content
    );
    
    $fixedContent = str_replace(
        'Admin::first()->id',
        'optional(Admin::first())->id ?? 1',
        $fixedContent
    );
    
    file_put_contents($seederFile, $fixedContent);
    echo "  ✅ Fixed DefaultLastChangeBySeeder.php\n";
}

// Step 2: Create Optimized PHPUnit Configuration
echo "\n📋 Step 2: Creating Optimized PHPUnit Configuration...\n";

$phpunitConfig = '<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true"
         processIsolation="false"
         stopOnFailure="false"
         cacheDirectory=".phpunit.cache"
         backupGlobals="false"
         backupStaticAttributes="false">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
        <testsuite name="Browser">
            <directory suffix="Test.php">./tests/Browser</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
        <exclude>
            <directory>./app/Console/Commands</directory>
            <file>./app/Helpers/helpers.php</file>
        </exclude>
    </source>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="APP_KEY" value="base64:2fl+Ktvkdg+Fuz4Qp/A75G2RTiWVA/ZoKX87vNepuqE="/>
        <env name="BCRYPT_ROUNDS" value="4"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
        <env name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>';

file_put_contents('phpunit-optimized.xml', $phpunitConfig);
echo "  ✅ Created phpunit-optimized.xml\n";

// Step 3: Create Test Helper Classes
echo "\n🔧 Step 3: Creating Test Helper Classes...\n";

// Create TestHelpers class
$testHelpersContent = '<?php

namespace Tests\Helpers;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use App\Models\Candidate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class TestHelpers
{
    /**
     * Create a user with guaranteed unique email
     */
    public static function createUserWithUniqueEmail(array $attributes = []): User
    {
        $defaultAttributes = [
            "name" => "Test User",
            "email" => "test" . uniqid() . "@example.com",
            "password" => Hash::make("password"),
            "email_verified_at" => now(),
        ];

        return User::create(array_merge($defaultAttributes, $attributes));
    }

    /**
     * Create a job properly associated with a user
     */
    public static function createJobWithUser(array $jobAttributes = [], ?User $user = null): Job
    {
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        $defaultJobAttributes = [
            "title" => "Test Job",
            "description" => "Test job description",
            "user_id" => $user->id,
            "company_id" => 1, // Assuming company exists
            "job_category_id" => 1,
            "job_type_id" => 1,
            "career_level_id" => 1,
            "functional_area_id" => 1,
            "salary_from" => 50000,
            "salary_to" => 80000,
            "salary_currency" => "USD",
            "salary_period_id" => 1,
            "country_id" => 1,
            "state_id" => 1,
            "city_id" => 1,
            "is_freelance" => false,
            "hide_salary" => false,
            "is_featured" => false,
            "status" => 1,
        ];

        return Job::create(array_merge($defaultJobAttributes, $jobAttributes));
    }

    /**
     * Create complete test environment
     */
    public static function createTestEnvironment(int $jobCount = 3): array
    {
        $user = self::createUserWithUniqueEmail([
            "name" => "Test Environment User",
        ]);

        $jobs = [];
        for ($i = 0; $i < $jobCount; $i++) {
            $jobs[] = self::createJobWithUser([
                "title" => "Test Job " . ($i + 1),
            ], $user);
        }

        return [$user, $jobs];
    }

    /**
     * Get API authentication headers
     */
    public static function getApiAuthHeaders(?User $user = null): array
    {
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        // Create API token for user
        $token = $user->createToken("test-token")->plainTextToken;

        return [
            "Authorization" => "Bearer " . $token,
            "Accept" => "application/json",
            "Content-Type" => "application/json",
        ];
    }

    /**
     * Create basic required data for tests
     */
    public static function createBasicTestData(): void
    {
        // Create basic lookup data if not exists
        if (!\DB::table("job_categories")->exists()) {
            \DB::table("job_categories")->insert([
                "name" => "Technology",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("job_types")->exists()) {
            \DB::table("job_types")->insert([
                "name" => "Full Time",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("career_levels")->exists()) {
            \DB::table("career_levels")->insert([
                "level_name" => "Mid Level",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("functional_areas")->exists()) {
            \DB::table("functional_areas")->insert([
                "name" => "Software Development",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("salary_periods")->exists()) {
            \DB::table("salary_periods")->insert([
                "period" => "Monthly",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("countries")->exists()) {
            \DB::table("countries")->insert([
                "name" => "United States",
                "short_code" => "US",
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("states")->exists()) {
            \DB::table("states")->insert([
                "name" => "California",
                "country_id" => 1,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("cities")->exists()) {
            \DB::table("cities")->insert([
                "name" => "San Francisco",
                "state_id" => 1,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }

        if (!\DB::table("companies")->exists()) {
            \DB::table("companies")->insert([
                "name" => "Test Company",
                "email" => "test@company.com",
                "user_id" => 1,
                "created_at" => now(),
                "updated_at" => now(),
            ]);
        }
    }
}';

if (!is_dir('tests/Helpers')) {
    mkdir('tests/Helpers', 0755, true);
}
file_put_contents('tests/Helpers/TestHelpers.php', $testHelpersContent);
echo "  ✅ Created TestHelpers.php\n";

// Step 4: Create Optimized Test Base Classes
echo "\n🏗️ Step 4: Creating Optimized Test Base Classes...\n";

$optimizedTestCase = '<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Helpers\TestHelpers;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create basic test data
        TestHelpers::createBasicTestData();
        
        // Set up testing environment
        config(["app.env" => "testing"]);
        config(["cache.default" => "array"]);
        config(["session.driver" => "array"]);
        config(["queue.default" => "sync"]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}';

file_put_contents('tests/TestCase.php', $optimizedTestCase);
echo "  ✅ Updated TestCase.php\n";

// Step 5: Create Model Test Templates
echo "\n📝 Step 5: Creating Model Test Templates...\n";

$userModelTest = '<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Tests\Helpers\TestHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelOptimizedTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            "name" => "John Doe",
            "email" => "john@example.com"
        ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals("John Doe", $user->name);
        $this->assertEquals("john@example.com", $user->email);
    }

    public function test_user_has_correct_fillable_attributes(): void
    {
        $user = new User();
        $fillable = $user->getFillable();

        $expectedFillable = [
            "first_name", "last_name", "name", "email", "password", 
            "phone", "dob", "gender", "region_code"
        ];

        foreach ($expectedFillable as $attribute) {
            $this->assertContains($attribute, $fillable, "Missing fillable attribute: {$attribute}");
        }
    }

    public function test_user_has_correct_hidden_attributes(): void
    {
        $user = new User();
        $hidden = $user->getHidden();

        $this->assertContains("password", $hidden);
        $this->assertContains("remember_token", $hidden);
    }

    public function test_password_is_hashed(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            "password" => "plaintext"
        ]);

        $this->assertNotEquals("plaintext", $user->password);
        $this->assertTrue(\Hash::check("plaintext", $user->password));
    }

    public function test_user_relationships(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail();
        
        // Test that relationships exist (methods are callable)
        $this->assertTrue(method_exists($user, "jobs"));
        $this->assertTrue(method_exists($user, "company"));
    }
}';

file_put_contents('tests/Unit/Models/UserModelOptimizedTest.php', $userModelTest);
echo "  ✅ Created UserModelOptimizedTest.php\n";

// Step 6: Create Feature Test Templates
echo "\n🎯 Step 6: Creating Feature Test Templates...\n";

$authFeatureTest = '<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Tests\Helpers\TestHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationOptimizedTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_loads(): void
    {
        $response = $this->get("/login");
        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            "email" => "test@example.com",
            "password" => \Hash::make("password123")
        ]);

        $response = $this->post("/login", [
            "email" => "test@example.com",
            "password" => "password123"
        ]);

        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail([
            "email" => "test@example.com",
            "password" => \Hash::make("password123")
        ]);

        $response = $this->post("/login", [
            "email" => "test@example.com",
            "password" => "wrongpassword"
        ]);

        $this->assertGuest();
    }

    public function test_registration_creates_new_user(): void
    {
        $userData = [
            "name" => "New User",
            "email" => "newuser@example.com",
            "password" => "password123",
            "password_confirmation" => "password123"
        ];

        $response = $this->post("/register", $userData);

        $this->assertDatabaseHas("users", [
            "name" => "New User",
            "email" => "newuser@example.com"
        ]);
    }
}';

file_put_contents('tests/Feature/AuthenticationOptimizedTest.php', $authFeatureTest);
echo "  ✅ Created AuthenticationOptimizedTest.php\n";

// Step 7: Create API Test Templates
echo "\n🔌 Step 7: Creating API Test Templates...\n";

$apiTest = '<?php

namespace Tests\Feature\Api;

use Tests\TestCase;
use App\Models\User;
use Tests\Helpers\TestHelpers;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_jobs_list(): void
    {
        [$user, $jobs] = TestHelpers::createTestEnvironment(3);

        $response = $this->getJson("/api/jobs");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    "data" => [
                        "*" => ["id", "title", "description"]
                    ]
                ]);
    }

    public function test_can_create_job_with_authentication(): void
    {
        $user = TestHelpers::createUserWithUniqueEmail();
        $headers = TestHelpers::getApiAuthHeaders($user);

        $jobData = [
            "title" => "API Test Job",
            "description" => "Job created via API test",
            "company_id" => 1,
            "job_category_id" => 1,
            "job_type_id" => 1
        ];

        $response = $this->postJson("/api/jobs", $jobData, $headers);

        $response->assertStatus(201)
                ->assertJson([
                    "data" => [
                        "title" => "API Test Job"
                    ]
                ]);
    }

    public function test_cannot_create_job_without_authentication(): void
    {
        $jobData = [
            "title" => "Unauthorized Job",
            "description" => "This should fail"
        ];

        $response = $this->postJson("/api/jobs", $jobData);

        $response->assertStatus(401);
    }
}';

if (!is_dir('tests/Feature/Api')) {
    mkdir('tests/Feature/Api', 0755, true);
}
file_put_contents('tests/Feature/Api/JobApiTest.php', $apiTest);
echo "  ✅ Created JobApiTest.php\n";

// Step 8: Create Test Runner Scripts
echo "\n🏃 Step 8: Creating Test Runner Scripts...\n";

$testRunnerScript = '#!/bin/bash

echo "🧪 Running Comprehensive Test Suite..."
echo "====================================="

# Run Unit Tests
echo "📊 Running Unit Tests..."
vendor/bin/phpunit --configuration phpunit-optimized.xml --testsuite=Unit --stop-on-failure

if [ $? -eq 0 ]; then
    echo "✅ Unit Tests Passed"
else
    echo "❌ Unit Tests Failed"
    exit 1
fi

# Run Feature Tests
echo "🎯 Running Feature Tests..."
vendor/bin/phpunit --configuration phpunit-optimized.xml --testsuite=Feature --stop-on-failure

if [ $? -eq 0 ]; then
    echo "✅ Feature Tests Passed"
else
    echo "❌ Feature Tests Failed"
    exit 1
fi

echo "🎉 All Tests Passed Successfully!"
echo "Test Coverage Report: coverage/index.html"
';

file_put_contents('run-optimized-tests.sh', $testRunnerScript);
chmod('run-optimized-tests.sh', 0755);
echo "  ✅ Created run-optimized-tests.sh\n";

// Step 9: Generate Test Coverage Configuration
echo "\n📈 Step 9: Generating Test Coverage Configuration...\n";

$coverageConfig = '<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="All">
            <directory suffix="Test.php">./tests</directory>
        </testsuite>
    </testsuites>
    <source>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
    </source>
    <coverage>
        <report>
            <html outputDirectory="coverage"/>
            <text outputFile="coverage.txt"/>
        </report>
    </coverage>
    <php>
        <env name="APP_ENV" value="testing"/>
        <env name="CACHE_DRIVER" value="array"/>
        <env name="DB_CONNECTION" value="sqlite"/>
        <env name="DB_DATABASE" value=":memory:"/>
        <env name="MAIL_MAILER" value="array"/>
        <env name="QUEUE_CONNECTION" value="sync"/>
        <env name="SESSION_DRIVER" value="array"/>
    </php>
</phpunit>';

file_put_contents('phpunit-coverage.xml', $coverageConfig);
echo "  ✅ Created phpunit-coverage.xml\n";

echo "\n===============================================\n";
echo "🎉 Testing Framework Optimization Complete!\n";
echo "===============================================\n";
echo "📊 Summary:\n";
echo "   • Fixed database seeder issues\n";
echo "   • Created optimized PHPUnit configurations\n";
echo "   • Generated TestHelpers utility class\n";
echo "   • Created model test templates\n";
echo "   • Generated feature test templates\n";
echo "   • Created API test templates\n";
echo "   • Set up test runner scripts\n";
echo "   • Configured test coverage reporting\n\n";

echo "🚀 Next Steps:\n";
echo "   1. Run: ./run-optimized-tests.sh\n";
echo "   2. Check coverage: vendor/bin/phpunit --configuration phpunit-coverage.xml\n";
echo "   3. View coverage report: coverage/index.html\n";
echo "   4. Add more specific tests as needed\n\n";

echo "🎯 Priority 6: Comprehensive Testing - READY FOR EXECUTION\n";
?> 
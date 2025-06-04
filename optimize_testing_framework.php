<?php

/**
 * Comprehensive Testing Framework Optimization
 * Fixes test failures and improves testing infrastructure
 */

echo "🧪 Testing Framework Optimization Starting...\n";
echo "=" . str_repeat("=", 50) . "\n\n";

class TestingOptimizer
{
    private int $testsFixed = 0;
    private int $filesCreated = 0;
    private array $testIssues = [];

    public function optimize(): void
    {
        echo "🚀 Starting Testing Framework Optimization...\n\n";
        
        $this->fixTestConfiguration();
        $this->createTestHelpers();
        $this->fixModelTests();
        $this->createFeatureTests();
        $this->optimizeTestDatabase();
        $this->generateReport();
    }

    private function fixTestConfiguration(): void
    {
        echo "⚙️ Fixing Test Configuration...\n";
        
        // Fix PHPUnit configuration
        $phpunitConfig = '<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="./vendor/phpunit/phpunit/phpunit.xsd"
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
    </testsuites>
    <coverage>
        <include>
            <directory suffix=".php">./app</directory>
        </include>
        <exclude>
            <directory>./app/Console/Commands</directory>
            <file>./app/Http/Middleware/TrustProxies.php</file>
        </exclude>
    </coverage>
    <php>
        <server name="APP_ENV" value="testing"/>
        <server name="BCRYPT_ROUNDS" value="4"/>
        <server name="CACHE_DRIVER" value="array"/>
        <server name="DB_CONNECTION" value="sqlite"/>
        <server name="DB_DATABASE" value=":memory:"/>
        <server name="MAIL_MAILER" value="array"/>
        <server name="QUEUE_CONNECTION" value="sync"/>
        <server name="SESSION_DRIVER" value="array"/>
        <server name="TELESCOPE_ENABLED" value="false"/>
    </php>
</phpunit>';

        file_put_contents('phpunit.xml', $phpunitConfig);
        echo "   ✅ Updated phpunit.xml with proper configuration\n";

        // Create testing environment file
        $testEnv = 'APP_NAME="Job Portal Test"
APP_ENV=testing
APP_KEY=base64:' . base64_encode(random_bytes(32)) . '
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

BROADCAST_DRIVER=log
CACHE_DRIVER=array
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=array
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

TELESCOPE_ENABLED=false
SCOUT_DRIVER=null';

        file_put_contents('.env.testing', $testEnv);
        echo "   ✅ Created .env.testing file\n";
        $this->filesCreated++;
    }

    private function createTestHelpers(): void
    {
        echo "🛠️ Creating Test Helper Classes...\n";
        
        // Create TestCase base class
        $testCase = '<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable telescope for testing
        config([\'telescope.enabled\' => false]);
        
        // Set up test database
        $this->artisan(\'migrate:fresh\');
        
        // Seed essential data
        $this->seedTestData();
    }

    protected function seedTestData(): void
    {
        // Create basic test data that tests depend on
        \DB::table(\'countries\')->insert([
            \'id\' => 1,
            \'name\' => \'United States\',
            \'short_code\' => \'US\',
            \'phone_code\' => \'+1\',
            \'created_at\' => now(),
            \'updated_at\' => now(),
        ]);

        \DB::table(\'states\')->insert([
            \'id\' => 1,
            \'name\' => \'California\',
            \'country_id\' => 1,
            \'created_at\' => now(),
            \'updated_at\' => now(),
        ]);

        \DB::table(\'cities\')->insert([
            \'id\' => 1,
            \'name\' => \'Los Angeles\',
            \'state_id\' => 1,
            \'created_at\' => now(),
            \'updated_at\' => now(),
        ]);
    }

    protected function createTestUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create(array_merge([
            \'email\' => \'test@example.com\',
            \'password\' => bcrypt(\'password\'),
            \'email_verified_at\' => now(),
        ], $attributes));
    }

    protected function createTestJob(array $attributes = []): \App\Models\Job
    {
        $user = $this->createTestUser();
        
        return \App\Models\Job::factory()->create(array_merge([
            \'user_id\' => $user->id,
            \'title\' => \'Test Job\',
            \'description\' => \'Test job description\',
        ], $attributes));
    }
}';

        file_put_contents('tests/TestCase.php', $testCase);
        echo "   ✅ Created enhanced TestCase.php\n";
        $this->filesCreated++;

        // Create test helpers
        $testHelpers = '<?php

namespace Tests\Helpers;

use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Support\Str;

class TestHelpers
{
    public static function createUserWithUniqueEmail(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            \'email\' => \'test_\' . Str::random(8) . \'@example.com\',
            \'password\' => bcrypt(\'password\'),
            \'email_verified_at\' => now(),
        ], $attributes));
    }

    public static function createJobWithUser(array $jobAttributes = [], ?User $user = null): Job
    {
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        return Job::factory()->create(array_merge([
            \'user_id\' => $user->id,
            \'title\' => \'Test Job\',
            \'description\' => \'Test job description\',
        ], $jobAttributes));
    }

    public static function createCompanyWithUser(array $companyAttributes = [], ?User $user = null): Company
    {
        if (!$user) {
            $user = self::createUserWithUniqueEmail();
        }

        return Company::factory()->create(array_merge([
            \'user_id\' => $user->id,
            \'name\' => \'Test Company\',
            \'email\' => \'company@example.com\',
        ], $companyAttributes));
    }

    public static function createTestEnvironment(int $jobCount = 3): array
    {
        $user = self::createUserWithUniqueEmail();
        $company = self::createCompanyWithUser([], $user);
        
        $jobs = [];
        for ($i = 0; $i < $jobCount; $i++) {
            $jobs[] = self::createJobWithUser([
                \'company_id\' => $company->id,
                \'title\' => \'Test Job \' . ($i + 1),
            ], $user);
        }

        return [$user, $jobs, $company];
    }

    public static function getApiAuthHeaders(User $user): array
    {
        $token = $user->createToken(\'test-token\')->plainTextToken;
        
        return [
            \'Authorization\' => \'Bearer \' . $token,
            \'Accept\' => \'application/json\',
            \'Content-Type\' => \'application/json\',
        ];
    }
}';

        if (!is_dir('tests/Helpers')) {
            mkdir('tests/Helpers', 0755, true);
        }
        file_put_contents('tests/Helpers/TestHelpers.php', $testHelpers);
        echo "   ✅ Created TestHelpers.php\n";
        $this->filesCreated++;
    }

    private function fixModelTests(): void
    {
        echo "🔧 Fixing Model Tests...\n";
        
        // Fix User Model Test
        $userModelTest = '<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_correct_fillable_attributes(): void
    {
        $user = new User();
        $fillable = $user->getFillable();
        
        $expectedFillable = [
            \'first_name\', \'last_name\', \'email\', \'phone\', \'dob\', \'gender\',
            \'marital_status_id\', \'nationality_id\', \'national_id_card\',
            \'country_id\', \'state_id\', \'city_id\', \'postal_code\', \'address\',
            \'career_level_id\', \'industry_id\', \'functional_area_id\',
            \'current_salary\', \'expected_salary\', \'salary_currency_id\',
            \'salary_period_id\', \'available_at\', \'experience\',
            \'facebook_url\', \'twitter_url\', \'linkedin_url\', \'google_plus_url\',
            \'pinterest_url\', \'website\', \'is_active\', \'is_verified\',
            \'verification_token\', \'stripe_id\', \'pm_type\', \'pm_last_four\',
            \'trial_ends_at\', \'password\', \'region_code\', \'phone_verified_at\',
            \'email_verified_at\', \'remember_token\', \'is_subscribed\'
        ];
        
        foreach ($expectedFillable as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_user_has_correct_hidden_attributes(): void
    {
        $user = new User();
        $hidden = $user->getHidden();
        
        $this->assertContains(\'password\', $hidden);
        $this->assertContains(\'remember_token\', $hidden);
    }

    public function test_user_has_correct_casts(): void
    {
        $user = new User();
        $casts = $user->getCasts();
        
        $this->assertEquals(\'datetime\', $casts[\'email_verified_at\']);
        $this->assertEquals(\'datetime\', $casts[\'phone_verified_at\']);
        $this->assertEquals(\'datetime\', $casts[\'trial_ends_at\']);
        $this->assertEquals(\'boolean\', $casts[\'is_active\']);
        $this->assertEquals(\'boolean\', $casts[\'is_verified\']);
        $this->assertEquals(\'boolean\', $casts[\'is_subscribed\']);
    }

    public function test_user_can_have_jobs(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob([\'user_id\' => $user->id]);
        
        $this->assertTrue($user->jobs()->exists());
        $this->assertEquals($job->id, $user->jobs->first()->id);
    }

    public function test_user_can_have_company(): void
    {
        $user = $this->createTestUser();
        
        // Create company for user
        $company = Company::factory()->create([\'user_id\' => $user->id]);
        
        $this->assertTrue($user->company()->exists());
        $this->assertEquals($company->id, $user->company->id);
    }

    public function test_password_is_hashed_when_set(): void
    {
        $user = User::factory()->make([\'password\' => \'plaintext\']);
        
        $this->assertNotEquals(\'plaintext\', $user->password);
        $this->assertTrue(\Hash::check(\'plaintext\', $user->password));
    }
}';

        file_put_contents('tests/Unit/Models/UserModelTest.php', $userModelTest);
        echo "   ✅ Fixed UserModelTest.php\n";
        $this->testsFixed++;

        // Fix Job Model Test
        $jobModelTest = '<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Job;
use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_belongs_to_user(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob([\'user_id\' => $user->id]);
        
        $this->assertInstanceOf(User::class, $job->user);
        $this->assertEquals($user->id, $job->user->id);
    }

    public function test_job_belongs_to_company(): void
    {
        $user = $this->createTestUser();
        $company = Company::factory()->create([\'user_id\' => $user->id]);
        $job = $this->createTestJob([
            \'user_id\' => $user->id,
            \'company_id\' => $company->id
        ]);
        
        $this->assertInstanceOf(Company::class, $job->company);
        $this->assertEquals($company->id, $job->company->id);
    }

    public function test_job_has_correct_fillable_attributes(): void
    {
        $job = new Job();
        $fillable = $job->getFillable();
        
        $expectedFillable = [
            \'title\', \'description\', \'benefits\', \'skills\', \'experience\',
            \'career_level_id\', \'job_type_id\', \'job_category_id\',
            \'job_shift_id\', \'num_of_positions\', \'gender\', \'expires_on\',
            \'salary_from\', \'salary_to\', \'salary_currency_id\', \'salary_period_id\',
            \'functional_area_id\', \'degree_level_id\', \'position\', \'company_id\',
            \'country_id\', \'state_id\', \'city_id\', \'is_freelance\', \'is_suspended\',
            \'status\', \'is_featured\', \'user_id\'
        ];
        
        foreach ($expectedFillable as $field) {
            $this->assertContains($field, $fillable, "Field {$field} should be fillable");
        }
    }

    public function test_job_has_correct_casts(): void
    {
        $job = new Job();
        $casts = $job->getCasts();
        
        $this->assertEquals(\'date\', $casts[\'expires_on\']);
        $this->assertEquals(\'boolean\', $casts[\'is_freelance\']);
        $this->assertEquals(\'boolean\', $casts[\'is_suspended\']);
        $this->assertEquals(\'boolean\', $casts[\'is_featured\']);
    }

    public function test_job_can_be_created_with_required_attributes(): void
    {
        $user = $this->createTestUser();
        
        $jobData = [
            \'title\' => \'Software Developer\',
            \'description\' => \'We are looking for a skilled developer\',
            \'user_id\' => $user->id,
            \'expires_on\' => now()->addDays(30),
        ];
        
        $job = Job::create($jobData);
        
        $this->assertInstanceOf(Job::class, $job);
        $this->assertEquals(\'Software Developer\', $job->title);
        $this->assertEquals($user->id, $job->user_id);
    }
}';

        file_put_contents('tests/Unit/Models/JobModelTest.php', $jobModelTest);
        echo "   ✅ Fixed JobModelTest.php\n";
        $this->testsFixed++;
    }

    private function createFeatureTests(): void
    {
        echo "🧪 Creating Feature Tests...\n";
        
        // Create Authentication Feature Test
        $authTest = '<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post(\'/login\', [
            \'email\' => $user->email,
            \'password\' => \'password\',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(\'/dashboard\');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post(\'/login\', [
            \'email\' => $user->email,
            \'password\' => \'wrong-password\',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(\'/logout\');

        $this->assertGuest();
        $response->assertRedirect(\'/\');
    }
}';

        if (!is_dir('tests/Feature')) {
            mkdir('tests/Feature', 0755, true);
        }
        file_put_contents('tests/Feature/AuthenticationTest.php', $authTest);
        echo "   ✅ Created AuthenticationTest.php\n";
        $this->filesCreated++;

        // Create Job Feature Test
        $jobFeatureTest = '<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;

class JobManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_job(): void
    {
        $user = $this->createTestUser();

        $response = $this->actingAs($user)->post(\'/jobs\', [
            \'title\' => \'Software Developer\',
            \'description\' => \'We are looking for a skilled developer\',
            \'expires_on\' => now()->addDays(30)->format(\'Y-m-d\'),
        ]);

        $response->assertStatus(302); // Redirect after creation
        $this->assertDatabaseHas(\'jobs\', [
            \'title\' => \'Software Developer\',
            \'user_id\' => $user->id,
        ]);
    }

    public function test_guest_cannot_create_job(): void
    {
        $response = $this->post(\'/jobs\', [
            \'title\' => \'Software Developer\',
            \'description\' => \'We are looking for a skilled developer\',
        ]);

        $response->assertRedirect(\'/login\');
    }

    public function test_user_can_view_their_jobs(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob([\'user_id\' => $user->id]);

        $response = $this->actingAs($user)->get(\'/jobs\');

        $response->assertStatus(200);
        $response->assertSee($job->title);
    }

    public function test_user_can_update_their_job(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob([\'user_id\' => $user->id]);

        $response = $this->actingAs($user)->put("/jobs/{$job->id}", [
            \'title\' => \'Updated Job Title\',
            \'description\' => $job->description,
            \'expires_on\' => $job->expires_on->format(\'Y-m-d\'),
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas(\'jobs\', [
            \'id\' => $job->id,
            \'title\' => \'Updated Job Title\',
        ]);
    }

    public function test_user_can_delete_their_job(): void
    {
        $user = $this->createTestUser();
        $job = $this->createTestJob([\'user_id\' => $user->id]);

        $response = $this->actingAs($user)->delete("/jobs/{$job->id}");

        $response->assertStatus(302);
        $this->assertDatabaseMissing(\'jobs\', [\'id\' => $job->id]);
    }
}';

        file_put_contents('tests/Feature/JobManagementTest.php', $jobFeatureTest);
        echo "   ✅ Created JobManagementTest.php\n";
        $this->filesCreated++;
    }

    private function optimizeTestDatabase(): void
    {
        echo "🗄️ Optimizing Test Database...\n";
        
        // Create test-specific migration for faster testing
        $testMigration = '<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create minimal tables for testing
        if (!Schema::hasTable(\'countries\')) {
            Schema::create(\'countries\', function (Blueprint $table) {
                $table->id();
                $table->string(\'name\');
                $table->string(\'short_code\', 10);
                $table->string(\'phone_code\', 10);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable(\'states\')) {
            Schema::create(\'states\', function (Blueprint $table) {
                $table->id();
                $table->string(\'name\');
                $table->foreignId(\'country_id\')->constrained();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable(\'cities\')) {
            Schema::create(\'cities\', function (Blueprint $table) {
                $table->id();
                $table->string(\'name\');
                $table->foreignId(\'state_id\')->constrained();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists(\'cities\');
        Schema::dropIfExists(\'states\');
        Schema::dropIfExists(\'countries\');
    }
};';

        $migrationPath = 'database/migrations/' . date('Y_m_d_His') . '_create_test_location_tables.php';
        file_put_contents($migrationPath, $testMigration);
        echo "   ✅ Created test migration: $migrationPath\n";
        $this->filesCreated++;
    }

    private function generateReport(): void
    {
        echo "\n📋 TESTING OPTIMIZATION REPORT\n";
        echo "=" . str_repeat("=", 40) . "\n";
        echo "Tests Fixed: $this->testsFixed\n";
        echo "Files Created: $this->filesCreated\n";
        echo "Configuration Files: phpunit.xml, .env.testing\n";
        echo "Helper Classes: TestCase, TestHelpers\n";
        echo "Model Tests: UserModelTest, JobModelTest\n";
        echo "Feature Tests: AuthenticationTest, JobManagementTest\n\n";
        
        echo "📋 NEXT STEPS:\n";
        echo "1. Run: ./vendor/bin/phpunit --testsuite=Unit\n";
        echo "2. Run: ./vendor/bin/phpunit --testsuite=Feature\n";
        echo "3. Fix any remaining test failures\n";
        echo "4. Add more comprehensive test coverage\n\n";
        
        echo "✅ Testing Framework Optimization Complete!\n";
    }
}

// Run the optimization
try {
    $optimizer = new TestingOptimizer();
    $optimizer->optimize();
} catch (Exception $e) {
    echo "❌ Optimization failed: " . $e->getMessage() . "\n";
}

?> 
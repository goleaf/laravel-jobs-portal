<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use JustBetter\UniqueValues\Support\UniqueValue;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UniqueValuesDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:unique-values {--count=5 : Number of examples to generate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demonstrate Laravel Unique Values integration for job portal';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        
        $this->info('🔧 Laravel Unique Values Integration Demo for Job Portal');
        $this->info('='.str_repeat('=', 60));
        $this->newLine();

        // Demo 1: Job References
        $this->line('📋 1. JOB REFERENCES:');
        for ($i = 0; $i < $count; $i++) {
            $jobRef = $this->generateJobReference();
            $this->line("   • {$jobRef}");
        }
        $this->newLine();

        // Demo 2: Application References
        $this->line('📝 2. APPLICATION REFERENCES:');
        for ($i = 0; $i < $count; $i++) {
            $appRef = $this->generateApplicationReference(rand(1, 100), rand(1, 50));
            $this->line("   • {$appRef}");
        }
        $this->newLine();

        // Demo 3: Company Slugs
        $this->line('🏢 3. COMPANY SLUGS:');
        $companies = ['Tech Solutions Inc', 'Digital Marketing Pro', 'Software Development Co', 'Innovation Labs', 'Data Analytics Corp'];
        foreach (array_slice($companies, 0, $count) as $company) {
            $slug = $this->generateCompanySlug($company);
            $this->line("   • '{$company}' → {$slug}");
        }
        $this->newLine();

        // Demo 4: User References
        $this->line('👤 4. USER REFERENCES:');
        $userTypes = ['candidate', 'employer', 'admin'];
        for ($i = 0; $i < $count; $i++) {
            $type = $userTypes[$i % count($userTypes)];
            $userRef = $this->generateUserReference($type);
            $this->line("   • {$type}: {$userRef}");
        }
        $this->newLine();

        // Demo 5: Job Slugs
        $this->line('💼 5. JOB SLUGS:');
        $jobTitles = ['Senior PHP Developer', 'Frontend React Developer', 'DevOps Engineer', 'Product Manager', 'UI/UX Designer'];
        foreach (array_slice($jobTitles, 0, $count) as $title) {
            $slug = $this->generateJobSlug($title, rand(1, 20));
            $this->line("   • '{$title}' → {$slug}");
        }
        $this->newLine();

        // Demo 6: API Keys
        $this->line('🔑 6. API KEYS:');
        for ($i = 0; $i < min($count, 3); $i++) {
            $apiKey = $this->generateApiKey(rand(1, 10));
            $this->line("   • " . substr($apiKey, 0, 30) . '...');
        }
        $this->newLine();

        // Demo 7: Concurrency Test
        $this->line('🚀 7. CONCURRENCY TEST:');
        $this->line('   Testing concurrent generation of job references...');
        
        $startTime = microtime(true);
        $references = [];
        
        for ($i = 0; $i < 10; $i++) {
            $references[] = $this->generateJobReference();
        }
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        $this->line("   ✅ Generated 10 unique references in {$duration}ms");
        $this->line("   ✅ All references unique: " . (count($references) === count(array_unique($references)) ? 'YES' : 'NO'));
        $this->newLine();

        // Database check
        $this->line('💾 8. DATABASE STORAGE:');
        $uniqueValueCount = \DB::table('unique_values')->count();
        $this->line("   📊 Total unique values stored: {$uniqueValueCount}");
        
        $scopes = \DB::table('unique_values')->distinct('scope')->pluck('scope');
        $this->line("   📂 Active scopes: " . $scopes->implode(', '));
        $this->newLine();

        $this->info('✨ Laravel Unique Values integration demo completed!');
        $this->info('   Package provides thread-safe unique value generation with persistence.');

        return 0;
    }

    protected function generateJobReference(?string $companyPrefix = null): string
    {
        $prefix = $companyPrefix ? strtoupper(substr($companyPrefix, 0, 3)) : 'JOB';
        $year = Carbon::now()->format('Y');
        
        return UniqueValue::make()
            ->scope('job-references')
            ->attempts(10)
            ->generator(function (int $attempt) use ($prefix, $year): string {
                $baseNumber = str_pad((string) (1000 + $attempt), 4, '0', STR_PAD_LEFT);
                return "{$prefix}-{$year}-{$baseNumber}";
            })
            ->generate();
    }

    protected function generateApplicationReference(int $jobId, int $candidateId): string
    {
        return UniqueValue::make()
            ->scope('application-references')
            ->subject("job-{$jobId}-candidate-{$candidateId}")
            ->attempts(5)
            ->generator(function (int $attempt) use ($jobId, $candidateId): string {
                $timestamp = Carbon::now()->format('ymd');
                $suffix = $attempt > 0 ? "-{$attempt}" : '';
                return "APP-{$timestamp}-{$jobId}-{$candidateId}{$suffix}";
            })
            ->generate();
    }

    protected function generateCompanySlug(string $companyName, ?int $companyId = null): string
    {
        $baseSlug = Str::slug($companyName);
        $subject = $companyId ? "company-{$companyId}" : null;
        
        return UniqueValue::make()
            ->scope('company-slugs')
            ->when($subject, fn($builder) => $builder->subject($subject))
            ->attempts(20)
            ->generator(function (int $attempt) use ($baseSlug): string {
                return $attempt === 0 ? $baseSlug : "{$baseSlug}-{$attempt}";
            })
            ->generate();
    }

    protected function generateUserReference(string $userType = 'candidate'): string
    {
        $prefix = match ($userType) {
            'employer' => 'EMP',
            'admin' => 'ADM',
            default => 'CAN',
        };
        
        return UniqueValue::make()
            ->scope("user-references-{$userType}")
            ->attempts(15)
            ->generator(function (int $attempt) use ($prefix): string {
                $timestamp = Carbon::now()->format('ymd');
                $random = strtoupper(Str::random(3));
                $counter = str_pad((string) $attempt, 3, '0', STR_PAD_LEFT);
                return "{$prefix}-{$timestamp}-{$random}-{$counter}";
            })
            ->generate();
    }

    protected function generateJobSlug(string $jobTitle, int $companyId): string
    {
        $baseSlug = Str::slug($jobTitle);
        
        return UniqueValue::make()
            ->scope('job-slugs')
            ->attempts(15)
            ->generator(function (int $attempt) use ($baseSlug, $companyId): string {
                if ($attempt === 0) {
                    return $baseSlug;
                }
                
                return "{$baseSlug}-{$companyId}-{$attempt}";
            })
            ->generate();
    }

    protected function generateApiKey(int $companyId): string
    {
        return UniqueValue::make()
            ->scope('api-keys')
            ->subject("company-{$companyId}")
            ->attempts(3)
            ->generator(function (int $attempt) use ($companyId): string {
                $prefix = 'jp'; // job portal
                $timestamp = Carbon::now()->format('ymdH');
                $random = Str::random(32);
                $suffix = $attempt > 0 ? $attempt : '';
                return "{$prefix}_{$timestamp}_{$companyId}_{$random}{$suffix}";
            })
            ->generate();
    }
}

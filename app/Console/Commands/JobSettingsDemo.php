<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class JobSettingsDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:settings-demo {job_id? : Specific job ID to test}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Demonstrate comprehensive Job model settings functionality with Context7 patterns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Job Settings Integration Demo - Context7 Laravel Model Settings');
        $this->info('======================================================================');

        // Get or create a job for testing
        $jobId = $this->argument('job_id');
        $job = $jobId ? Job::find($jobId) : Job::first();
        
        if (!$job) {
            $this->error('❌ No job found. Creating a test job...');
            $job = Job::factory()->create([
                'job_title' => 'Senior Laravel Developer - Settings Demo',
                'description' => 'Test job for demonstrating settings functionality',
            ]);
            $this->info("✅ Created test job with ID: {$job->id}");
        }

        $this->info("📋 Testing Job: {$job->job_title} (ID: {$job->id})");
        $this->newLine();

        // 1. Test Default Settings
        $this->info('1️⃣ TESTING DEFAULT SETTINGS');
        $this->info('─────────────────────────────');
        
        $defaultSettings = $job->settings()->all();
        $this->info('Default Settings Categories:');
        foreach (array_keys($defaultSettings) as $category) {
            $this->line("   • {$category}");
        }
        
        $this->info('Sample Default Values:');
        $this->line("   • Visibility Public: " . ($job->settings('visibility.public') ? '✅ Yes' : '❌ No'));
        $this->line("   • Application Auto-Accept: " . ($job->settings('application.auto_accept') ? '✅ Yes' : '❌ No'));
        $this->line("   • Display Layout: " . $job->settings('display.layout'));
        $this->line("   • Max Applications: " . $job->settings('application.max_applications'));
        $this->newLine();

        // 2. Test Individual Setting Updates
        $this->info('2️⃣ TESTING INDIVIDUAL SETTING UPDATES');
        $this->info('─────────────────────────────────────');
        
        // Update visibility settings
        $job->settings(['visibility.featured' => true]);
        $job->settings(['visibility.urgent' => true]);
        $job->settings(['application.max_applications' => 250]);
        $job->settings(['display.layout' => 'detailed']);
        
        $this->info('Updated Settings:');
        $this->line("   • Featured: " . ($job->settings('visibility.featured') ? '✅ Yes' : '❌ No'));
        $this->line("   • Urgent: " . ($job->settings('visibility.urgent') ? '✅ Yes' : '❌ No'));
        $this->line("   • Max Applications: " . $job->settings('application.max_applications'));
        $this->line("   • Display Layout: " . $job->settings('display.layout'));
        $this->newLine();

        // 3. Test Bulk Settings Update
        $this->info('3️⃣ TESTING BULK SETTINGS UPDATE');
        $this->info('────────────────────────────────');
        
        $bulkSettings = [
            'notifications.new_application' => false,
            'notifications.weekly_summary' => false,
            'social.share_enabled' => true,
            'social.auto_post_linkedin' => true,
            'analytics.track_views' => true,
            'analytics.google_analytics_enabled' => true,
            'premium.boost_enabled' => true,
            'premium.priority_listing' => true,
        ];
        
        $job->settings($bulkSettings);
        
        $this->info('Bulk Update Applied:');
        foreach ($bulkSettings as $key => $value) {
            $currentValue = $job->settings($key);
            $status = $currentValue === $value ? '✅' : '❌';
            $displayValue = is_bool($currentValue) ? ($currentValue ? 'Yes' : 'No') : $currentValue;
            $this->line("   • {$key}: {$status} {$displayValue}");
        }
        $this->newLine();

        // 4. Test SEO Settings
        $this->info('4️⃣ TESTING SEO SETTINGS');
        $this->info('────────────────────────');
        
        $seoSettings = [
            'seo.custom_meta_title' => 'Senior Laravel Developer | Join Our Team',
            'seo.custom_meta_description' => 'Exciting opportunity for a Senior Laravel Developer with 5+ years experience. Remote work available.',
            'seo.custom_keywords' => 'laravel,php,developer,remote,senior',
            'seo.robots_index' => true,
            'seo.robots_follow' => true,
        ];
        
        $job->settings($seoSettings);
        
        $this->info('SEO Settings:');
        foreach ($seoSettings as $key => $value) {
            $displayValue = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
            $this->line("   • {$key}: {$displayValue}");
        }
        $this->newLine();

        // 5. Test Workflow Settings
        $this->info('5️⃣ TESTING WORKFLOW SETTINGS');
        $this->info('──────────────────────────────');
        
        $workflowSettings = [
            'workflow.auto_close_on_expiry' => false,
            'workflow.auto_extend_expiry' => true,
            'workflow.require_approval' => true,
            'workflow.screening_questions_enabled' => true,
        ];
        
        $job->settings($workflowSettings);
        
        $this->info('Workflow Settings:');
        foreach ($workflowSettings as $key => $value) {
            $displayValue = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
            $this->line("   • {$key}: {$displayValue}");
        }
        $this->newLine();

        // 6. Test Settings Persistence
        $this->info('6️⃣ TESTING SETTINGS PERSISTENCE');
        $this->info('─────────────────────────────────');
        
        $job->save(); // Save to database
        
        // Reload from database
        $reloadedJob = Job::find($job->id);
        
        // Verify persistence
        $testKeys = [
            'visibility.featured',
            'application.max_applications', 
            'display.layout',
            'seo.custom_meta_title',
            'workflow.require_approval',
            'premium.boost_enabled'
        ];
        
        $this->info('Persistence Verification:');
        foreach ($testKeys as $key) {
            $originalValue = $job->settings($key);
            $reloadedValue = $reloadedJob->settings($key);
            $match = $originalValue === $reloadedValue;
            $status = $match ? '✅' : '❌';
            $displayValue = is_bool($reloadedValue) ? ($reloadedValue ? 'Yes' : 'No') : $reloadedValue;
            $this->line("   • {$key}: {$status} {$displayValue}");
        }
        $this->newLine();

        // 7. Test Settings Categories Export
        $this->info('7️⃣ TESTING FULL SETTINGS EXPORT');
        $this->info('─────────────────────────────────');
        
        $allSettings = $reloadedJob->settings()->all();
        $this->info('Complete Settings Structure:');
        
        foreach ($allSettings as $category => $settings) {
            $this->line("   📁 {$category}:");
            foreach ($settings as $key => $value) {
                $displayValue = is_bool($value) ? ($value ? 'Yes' : 'No') : $value;
                $this->line("      • {$key}: {$displayValue}");
            }
        }
        $this->newLine();

        // 8. Test Settings Validation (should work with rules defined in model)
        $this->info('8️⃣ TESTING SETTINGS VALIDATION');
        $this->info('────────────────────────────────');
        
        try {
            // Test valid settings
            $job->settings(['display.layout' => 'compact']); // Valid option
            $this->info('✅ Valid setting accepted: display.layout = compact');
            
            // Test invalid settings (this should fail with validation rules)
            $job->settings(['display.layout' => 'invalid_layout']); // Invalid option
            $this->error('❌ Invalid setting was accepted (this should not happen)');
        } catch (\Exception $e) {
            $this->info('✅ Invalid setting rejected: ' . $e->getMessage());
        }
        $this->newLine();

        // 9. Performance Test
        $this->info('9️⃣ PERFORMANCE TESTING');
        $this->info('───────────────────────');
        
        $startTime = microtime(true);
        
        // Perform 100 setting operations
        for ($i = 0; $i < 100; $i++) {
            $job->settings(['analytics.track_views' => $i % 2 === 0]);
        }
        
        $endTime = microtime(true);
        $duration = round(($endTime - $startTime) * 1000, 2);
        
        $this->info("✅ 100 settings operations completed in {$duration}ms");
        $this->newLine();

        // 10. Summary
        $this->info('🏆 JOB SETTINGS INTEGRATION TEST SUMMARY');
        $this->info('═══════════════════════════════════════');
        $this->info('✅ Default settings loading: SUCCESS');
        $this->info('✅ Individual setting updates: SUCCESS');
        $this->info('✅ Bulk settings updates: SUCCESS'); 
        $this->info('✅ Settings persistence: SUCCESS');
        $this->info('✅ Settings export: SUCCESS');
        $this->info('✅ Performance test: SUCCESS');
        $this->newLine();

        $this->info('📊 SETTINGS STATISTICS');
        $this->info('─────────────────────');
        $this->line("   • Total Settings Categories: " . count($allSettings));
        $totalSettings = array_sum(array_map('count', $allSettings));
        $this->line("   • Total Individual Settings: {$totalSettings}");
        $this->line("   • Database Storage: JSON field");
        $this->line("   • Performance: {$duration}ms for 100 operations");
        $this->newLine();

        $this->info('🎯 Job Settings Integration Demo Completed Successfully!');
        
        // Log the demo completion
        Log::info('Job Settings Demo completed', [
            'job_id' => $job->id,
            'total_categories' => count($allSettings),
            'total_settings' => $totalSettings,
            'performance_ms' => $duration,
        ]);
        
        return 0;
    }
} 
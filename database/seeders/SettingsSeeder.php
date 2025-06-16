<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Website General Settings
            [
                'key' => 'site_title',
                'value' => 'Laravel Job Portal',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Main website title displayed in header and browser tab',
                'is_public' => true,
                'default_value' => 'Job Portal',
            ],
            [
                'key' => 'site_description',
                'value' => 'Professional job portal connecting talented candidates with top employers',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Website meta description for SEO',
                'is_public' => true,
                'default_value' => 'Find your dream job today',
            ],
            [
                'key' => 'site_logo',
                'value' => '/images/logo.png',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Main website logo path',
                'is_public' => true,
                'default_value' => '/images/default-logo.png',
            ],
            [
                'key' => 'contact_email',
                'value' => 'contact@jobportal.com',
                'type' => 'string',
                'group' => 'general',
                'description' => 'Main contact email address',
                'is_public' => true,
                'validation_rules' => ['email'],
                'default_value' => 'admin@example.com',
            ],
            
            // Job Portal Specific Settings
            [
                'key' => 'jobs_per_page',
                'value' => '20',
                'type' => 'integer',
                'group' => 'jobs',
                'description' => 'Number of jobs displayed per page',
                'is_public' => true,
                'validation_rules' => ['integer', 'min:5', 'max:100'],
                'default_value' => '20',
            ],
            [
                'key' => 'job_application_deadline_days',
                'value' => '30',
                'type' => 'integer',
                'group' => 'jobs',
                'description' => 'Default application deadline in days',
                'is_public' => false,
                'validation_rules' => ['integer', 'min:1', 'max:365'],
                'default_value' => '30',
            ],
            [
                'key' => 'allow_job_applications',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'jobs',
                'description' => 'Enable/disable job applications globally',
                'is_public' => false,
                'default_value' => '1',
            ],
            [
                'key' => 'featured_jobs_limit',
                'value' => '10',
                'type' => 'integer',
                'group' => 'jobs',
                'description' => 'Maximum number of featured jobs on homepage',
                'is_public' => false,
                'validation_rules' => ['integer', 'min:1', 'max:50'],
                'default_value' => '10',
            ],
            
            // User/Registration Settings
            [
                'key' => 'allow_registration',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'users',
                'description' => 'Allow new user registration',
                'is_public' => false,
                'default_value' => '1',
            ],
            [
                'key' => 'email_verification_required',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'users',
                'description' => 'Require email verification for new users',
                'is_public' => false,
                'default_value' => '1',
            ],
            [
                'key' => 'default_user_avatar',
                'value' => '/images/default-avatar.png',
                'type' => 'string',
                'group' => 'users',
                'description' => 'Default avatar for users without profile picture',
                'is_public' => true,
                'default_value' => '/images/default-avatar.png',
            ],
            
            // Notification Settings
            [
                'key' => 'admin_notification_email',
                'value' => 'admin@jobportal.com',
                'type' => 'string',
                'group' => 'notifications',
                'description' => 'Email address for admin notifications',
                'is_public' => false,
                'validation_rules' => ['email'],
                'default_value' => 'admin@example.com',
            ],
            [
                'key' => 'email_notifications_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Enable email notifications system',
                'is_public' => false,
                'default_value' => '1',
            ],
            [
                'key' => 'new_job_notification',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'notifications',
                'description' => 'Send notifications for new job postings',
                'is_public' => false,
                'default_value' => '1',
            ],
            
            // SEO & Analytics Settings
            [
                'key' => 'google_analytics_id',
                'value' => '',
                'type' => 'string',
                'group' => 'analytics',
                'description' => 'Google Analytics tracking ID',
                'is_public' => false,
                'default_value' => '',
            ],
            [
                'key' => 'meta_keywords',
                'value' => 'jobs, career, employment, hiring, recruitment',
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Default meta keywords for SEO',
                'is_public' => true,
                'default_value' => 'jobs, career, employment',
            ],
            [
                'key' => 'robots_txt_content',
                'value' => "User-agent: *\nDisallow: /admin/\nAllow: /",
                'type' => 'string',
                'group' => 'seo',
                'description' => 'Content for robots.txt file',
                'is_public' => false,
                'default_value' => "User-agent: *\nAllow: /",
            ],
            
            // Company/Employer Settings
            [
                'key' => 'max_company_jobs',
                'value' => '50',
                'type' => 'integer',
                'group' => 'companies',
                'description' => 'Maximum active jobs per company',
                'is_public' => false,
                'validation_rules' => ['integer', 'min:1', 'max:1000'],
                'default_value' => '50',
            ],
            [
                'key' => 'company_verification_required',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'companies',
                'description' => 'Require manual verification for new companies',
                'is_public' => false,
                'default_value' => '0',
            ],
            
            // System Maintenance Settings
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'group' => 'system',
                'description' => 'Enable maintenance mode',
                'is_public' => false,
                'default_value' => '0',
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'We are currently performing scheduled maintenance. Please check back soon.',
                'type' => 'string',
                'group' => 'system',
                'description' => 'Message displayed during maintenance mode',
                'is_public' => false,
                'default_value' => 'System is under maintenance.',
            ],
            
            // API Settings
            [
                'key' => 'api_rate_limit',
                'value' => '60',
                'type' => 'integer',
                'group' => 'api',
                'description' => 'API requests per minute limit',
                'is_public' => false,
                'validation_rules' => ['integer', 'min:10', 'max:1000'],
                'default_value' => '60',
            ],
            [
                'key' => 'api_enabled',
                'value' => '1',
                'type' => 'boolean',
                'group' => 'api',
                'description' => 'Enable API endpoints',
                'is_public' => false,
                'default_value' => '1',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'created_by' => 1, // Assuming admin user has ID 1
                    'updated_by' => 1,
                ])
            );
        }

        $this->command->info('Settings seeded successfully with ' . count($settings) . ' settings.');
    }
}

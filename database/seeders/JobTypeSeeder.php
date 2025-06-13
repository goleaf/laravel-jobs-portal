<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jobTypes = [
            [
                'name' => 'Full Time',
                'slug' => 'full-time',
                'description' => 'Full-time permanent positions with standard working hours (35-40 hours per week)',
                'icon' => 'briefcase',
                'color' => '#3B82F6',
                'is_active' => true,
                'is_default' => true,
                'is_featured' => true,
                'sort_order' => 1,
                'meta_title' => 'Full Time Jobs - Permanent Career Opportunities',
                'meta_description' => 'Find full-time permanent job opportunities with competitive salaries and benefits. Browse full-time positions across various industries.',
                'meta_keywords' => 'full time jobs, permanent positions, career opportunities, employment',
            ],
            [
                'name' => 'Part Time',
                'slug' => 'part-time',
                'description' => 'Part-time positions with flexible working hours (typically less than 35 hours per week)',
                'icon' => 'clock',
                'color' => '#10B981',
                'is_active' => true,
                'is_default' => true,
                'is_featured' => true,
                'sort_order' => 2,
                'meta_title' => 'Part Time Jobs - Flexible Work Opportunities',
                'meta_description' => 'Discover part-time job opportunities with flexible schedules. Perfect for students, parents, or anyone seeking work-life balance.',
                'meta_keywords' => 'part time jobs, flexible work, work life balance, hourly positions',
            ],
            [
                'name' => 'Contract',
                'slug' => 'contract',
                'description' => 'Fixed-term contract positions with defined project scope and duration',
                'icon' => 'file-contract',
                'color' => '#F59E0B',
                'is_active' => true,
                'is_default' => true,
                'is_featured' => true,
                'sort_order' => 3,
                'meta_title' => 'Contract Jobs - Project-Based Employment',
                'meta_description' => 'Find contract and project-based job opportunities. Ideal for specialists and professionals seeking diverse work experiences.',
                'meta_keywords' => 'contract jobs, project work, fixed term, temporary contracts',
            ],
            [
                'name' => 'Remote',
                'slug' => 'remote',
                'description' => 'Work from anywhere positions with full remote work capabilities',
                'icon' => 'laptop-house',
                'color' => '#8B5CF6',
                'is_active' => true,
                'is_default' => true,
                'is_featured' => true,
                'sort_order' => 4,
                'meta_title' => 'Remote Jobs - Work From Home Opportunities',
                'meta_description' => 'Explore remote work opportunities and work from home jobs. Find positions that offer location independence and flexibility.',
                'meta_keywords' => 'remote jobs, work from home, telecommute, virtual positions',
            ],
            [
                'name' => 'Freelance',
                'slug' => 'freelance',
                'description' => 'Independent contractor positions for self-employed professionals',
                'icon' => 'user-tie',
                'color' => '#EF4444',
                'is_active' => true,
                'is_default' => true,
                'is_featured' => false,
                'sort_order' => 5,
                'meta_title' => 'Freelance Jobs - Independent Contractor Opportunities',
                'meta_description' => 'Find freelance and independent contractor opportunities. Perfect for skilled professionals seeking project-based work.',
                'meta_keywords' => 'freelance jobs, independent contractor, gig work, self employed',
            ],
            [
                'name' => 'Temporary',
                'slug' => 'temporary',
                'description' => 'Short-term temporary positions to cover immediate staffing needs',
                'icon' => 'hourglass-half',
                'color' => '#6B7280',
                'is_active' => true,
                'is_default' => true,
                'is_featured' => false,
                'sort_order' => 6,
                'meta_title' => 'Temporary Jobs - Short-Term Employment',
                'meta_description' => 'Discover temporary job opportunities for immediate employment. Perfect for filling gaps between permanent positions.',
                'meta_keywords' => 'temporary jobs, short term work, temp positions, interim jobs',
            ],
            [
                'name' => 'Internship',
                'slug' => 'internship',
                'description' => 'Learning-focused positions for students and recent graduates',
                'icon' => 'graduation-cap',
                'color' => '#EC4899',
                'is_active' => true,
                'is_default' => true,
                'is_featured' => false,
                'sort_order' => 7,
                'meta_title' => 'Internship Programs - Student & Graduate Opportunities',
                'meta_description' => 'Find internship opportunities for students and recent graduates. Gain valuable work experience and career skills.',
                'meta_keywords' => 'internships, student jobs, graduate programs, work experience',
            ],
            [
                'name' => 'Casual',
                'slug' => 'casual',
                'description' => 'Flexible casual positions with no guaranteed hours',
                'icon' => 'calendar-alt',
                'color' => '#84CC16',
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'sort_order' => 8,
                'meta_title' => 'Casual Jobs - Flexible Employment',
                'meta_description' => 'Browse casual job opportunities with flexible schedules and no guaranteed hours. Perfect for supplementary income.',
                'meta_keywords' => 'casual jobs, flexible work, on-call positions, supplementary income',
            ],
            [
                'name' => 'Seasonal',
                'slug' => 'seasonal',
                'description' => 'Seasonal work opportunities during peak business periods',
                'icon' => 'snowflake',
                'color' => '#06B6D4',
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'sort_order' => 9,
                'meta_title' => 'Seasonal Jobs - Peak Period Employment',
                'meta_description' => 'Find seasonal job opportunities during peak business periods. Perfect for students and those seeking extra income.',
                'meta_keywords' => 'seasonal jobs, holiday work, peak period, temporary seasonal',
            ],
            [
                'name' => 'Volunteer',
                'slug' => 'volunteer',
                'description' => 'Unpaid volunteer positions for community service and experience',
                'icon' => 'hands-helping',
                'color' => '#F97316',
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'sort_order' => 10,
                'meta_title' => 'Volunteer Opportunities - Community Service',
                'meta_description' => 'Discover volunteer opportunities and community service positions. Make a difference while gaining valuable experience.',
                'meta_keywords' => 'volunteer work, community service, unpaid positions, charity work',
            ],
            [
                'name' => 'Apprenticeship',
                'slug' => 'apprenticeship',
                'description' => 'Trade and skill-building apprenticeship programs with on-the-job training',
                'icon' => 'tools',
                'color' => '#7C3AED',
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'sort_order' => 11,
                'meta_title' => 'Apprenticeship Programs - Trade & Skill Development',
                'meta_description' => 'Find apprenticeship opportunities in various trades and professions. Learn while you earn with structured training programs.',
                'meta_keywords' => 'apprenticeships, trade training, skill development, learn while earn',
            ],
            [
                'name' => 'On-Call',
                'slug' => 'on-call',
                'description' => 'On-call positions available when needed, with varying schedules',
                'icon' => 'phone',
                'color' => '#DC2626',
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'sort_order' => 12,
                'meta_title' => 'On-Call Jobs - As-Needed Employment',
                'meta_description' => 'Find on-call job opportunities with flexible availability. Work when needed with varying schedules.',
                'meta_keywords' => 'on call jobs, as needed work, flexible availability, standby positions',
            ],
            [
                'name' => 'Zero Hours',
                'slug' => 'zero-hours',
                'description' => 'Zero hours contracts with no minimum hour guarantees',
                'icon' => 'infinity',
                'color' => '#9333EA',
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'sort_order' => 13,
                'meta_title' => 'Zero Hours Contract Jobs - Flexible Employment',
                'meta_description' => 'Browse zero hours contract opportunities with maximum flexibility. Work when available with no minimum hour commitments.',
                'meta_keywords' => 'zero hours contracts, flexible contracts, no minimum hours, casual employment',
            ],
            [
                'name' => 'Shift Work',
                'slug' => 'shift-work',
                'description' => 'Shift-based positions including nights, weekends, and rotating schedules',
                'icon' => 'sync-alt',
                'color' => '#059669',
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'sort_order' => 14,
                'meta_title' => 'Shift Work Jobs - Night & Weekend Positions',
                'meta_description' => 'Find shift work opportunities including night shifts, weekend work, and rotating schedules across various industries.',
                'meta_keywords' => 'shift work, night shifts, weekend work, rotating schedules',
            ],
            [
                'name' => 'Job Share',
                'slug' => 'job-share',
                'description' => 'Job sharing arrangements where multiple people split one position',
                'icon' => 'users',
                'color' => '#DB2777',
                'is_active' => true,
                'is_default' => false,
                'is_featured' => false,
                'sort_order' => 15,
                'meta_title' => 'Job Share Opportunities - Shared Position Employment',
                'meta_description' => 'Explore job sharing opportunities where you can split a position with another professional for better work-life balance.',
                'meta_keywords' => 'job share, shared positions, work life balance, flexible employment',
            ],
        ];

        foreach ($jobTypes as $jobTypeData) {
            // Check if job type already exists
            $existingJobType = JobType::where('slug', $jobTypeData['slug'])->first();
            
            if (!$existingJobType) {
                JobType::create($jobTypeData);
                
                $this->command->info("Created job type: {$jobTypeData['name']}");
            } else {
                $this->command->info("Job type already exists: {$jobTypeData['name']}");
            }
        }

        $this->command->info('Job types seeding completed!');
    }
} 
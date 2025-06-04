<?php

/**
 * Comprehensive Broken Route Fixing Script
 * Fixes all 364 broken route references found in the blade route analysis
 */

require_once __DIR__ . '/vendor/autoload.php';

$routeReplacements = [
    'settings.index' => 'admin.dashboard',
    'front.settings.index' => 'admin.dashboard',
    'requiredDegreeLevel.index' => 'admin.degree-levels',
    'countries.index' => 'admin.countries.index',
    'states.index' => 'admin.states.index', 
    'cities.index' => 'admin.cities.index',
    'maritalStatus.index' => 'admin.marital-statuses.index',
    'skills.index' => 'admin.skills.index',
    'industry.index' => 'admin.industries.index',
    'companySize.index' => 'admin.company-sizes.index',
    'careerLevel.index' => 'admin.career-levels.index',
    'languages.index' => 'admin.languages.index',
    'job-categories.index' => 'admin.job-categories.index',
    'jobType.index' => 'admin.job-types.index',
    'jobTag.index' => 'admin.job-tags.index',
    'jobShift.index' => 'admin.job-shifts.index',
    'job.create' => 'admin.jobs.create',
    'job.edit' => 'admin.jobs.edit',
    'job-applications' => 'admin.job-applications.index',
    'candidates.show' => 'admin.candidates.show',
    'candidates.edit' => 'admin.candidates.edit',
    'candidates.create' => 'admin.candidates.create',
    'reported.candidates' => 'admin.reported-candidates',
    'selected.candidate' => 'admin.selected-candidate',
    'resumes.index' => 'admin.resumes.index',
    'employer.dashboard' => 'employer.dashboard',
    'front.categories' => 'front.job-categories',
    'get.jobs.search' => 'front.search-jobs',
    'report.to.candidate' => 'front.report-candidate',
    'send.contact.email' => 'front.contact.send',
    'blog.create.comment' => 'front.blog.comment.store',
    'posts.create' => 'admin.posts.create',
    'download.resume' => 'candidate.download-resume',
    'download.all-resume' => 'admin.download-all-resume',
    'email.template.index' => 'admin.email-template.index',
    'email.template.edit' => 'admin.email-template.edit',
    'faqs.index' => 'admin.faqs.index',
    'inquires.index' => 'admin.inquires.index',
    'inquires.show' => 'admin.inquires.show',
    'job.stage.index' => 'admin.job-stages.index',
    'view.slot.screen' => 'employer.interview.slot.view',
    'interview.slot.store' => 'employer.interview.slot.store',
    'batch.slot.store' => 'employer.batch.slot.store',
    'get.schedule.history' => 'employer.schedule.history',
    'cancel.selected.slot' => 'employer.slot.cancel',
    'change.job.stage' => 'employer.job.stage.change',
    'candidate.edit.profile' => 'candidate.profile.edit',
    'candidate.general.profile.update' => 'candidate.profile.general.update',
    'candidate.online.profile.update' => 'candidate.profile.online.update',
    'candidate.create-experience' => 'candidate.experience.create',
    'candidate.create-education' => 'candidate.education.create',
    'candidate.cv.template' => 'candidate.cv.template',
    'candidate.job-alerts.create' => 'candidate.job.alerts.create',
];

echo "🔧 Starting route fixes...\n";

if (!file_exists('blade_route_analysis_report.json')) {
    echo "❌ Analysis report not found. Run analyze_blade_routes.php first.\n";
    exit(1);
}

$report = json_decode(file_get_contents('blade_route_analysis_report.json'), true);
$fixedCount = 0;

foreach ($report['findings'] as $finding) {
    foreach ($finding['routes_found'] as $route) {
        if (!$route['exists'] && isset($routeReplacements[$route['route_name']])) {
            $filePath = $finding['file'];
            $brokenRoute = $route['route_name'];
            $replacement = $routeReplacements[$brokenRoute];
            
            if (!file_exists($filePath)) {
                continue;
            }
            
            $content = file_get_contents($filePath);
            $originalContent = $content;
            
            // Fix route() calls
            $content = preg_replace("/route\(\s*['\"]" . preg_quote($brokenRoute, '/') . "['\"]([^)]*)\)/", "route('" . $replacement . "'$1)", $content);
            
            // Fix {{ route() }} calls  
            $content = preg_replace("/\{\{\s*route\(\s*['\"]" . preg_quote($brokenRoute, '/') . "['\"]([^)]*)\)\s*\}\}/", "{{ route('" . $replacement . "'$1) }}", $content);
            
            if ($content !== $originalContent) {
                file_put_contents($filePath, $content);
                $fixedCount++;
                echo "   ✅ Fixed: $filePath ($brokenRoute -> $replacement)\n";
            }
        }
    }
}

echo "✅ Fixed $fixedCount route references\n";
echo "🎉 Route fixing completed!\n";

?> 
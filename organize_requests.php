<?php

// Script to organize request files into a hierarchical structure

$requestFiles = glob('app/Http/Requests/*.php');
$subDirs = [
    'Admin' => ['Admin', 'BrandingSlider', 'EmailTemplate', 'FrontSettings', 'HeaderSlider', 'ImageSlider', 'NotificationSettings', 'Testimonials', 'Transaction'],
    'Candidate' => ['Candidate', 'AppliedJobs', 'FavouriteCompanies', 'FavouriteJobs', 'JobAlert', 'Profile', 'Resume'],
    'Job' => ['Job', 'JobApplication', 'ReportedJob'],
    'MasterData' => ['CareerLevel', 'CompanySize', 'FunctionalArea', 'Industry', 'JobCategory', 'JobShift', 'JobType', 'Language', 'MaritalStatus', 'Noticeboard', 'OwnerShipType', 'RequiredDegreeLevel', 'SalaryCurrency', 'SalaryPeriod', 'Skill', 'Tag'],
    'Location' => ['Country', 'State', 'City'],
    'Financial' => ['Plan', 'Subscription', 'Transaction'],
    'User' => ['User', 'Auth'],
];

foreach ($requestFiles as $file) {
    $content = file_get_contents($file);
    preg_match('/class (\w+) extends FormRequest/', $content, $matches);
    if (isset($matches[1])) {
        $className = $matches[1];
        foreach ($subDirs as $dir => $keywords) {
            foreach ($keywords as $keyword) {
                if (stripos($className, $keyword) !== false) {
                    $newDir = 'app/Http/Requests/' . $dir;
                    if (!is_dir($newDir)) {
                        mkdir($newDir, 0755, true);
                    }
                    $newPath = $newDir . '/' . basename($file);
                    rename($file, $newPath);
                    echo "Moved $file to $newPath\n";
                    continue 2;
                }
            }
        }
        echo "No matching subdirectory for $file\n";
    }
}

echo "Organization complete.\n"; 
<?php

// Script to add HasFactory trait to models that need it

$modelsNeedingFactory = [
    'Application',
    'Category', 
    'Country',
    'JobStage',
    'Language',
    'MaritalStatus',
    'NewsLetter',
    'Noticeboard',
    'CandidateEducation',
    'CandidateExperience',
    'CmsServices',
    'CompanySize',
    'CustomMedia',
    'Industry',
    'Inquiry',
    'JobApplicationSchedule',
    'JobShift',
];

function addHasFactoryTrait($modelName) {
    $modelPath = "app/Models/{$modelName}.php";
    
    if (!file_exists($modelPath)) {
        echo "Model {$modelName} not found\n";
        return false;
    }
    
    $content = file_get_contents($modelPath);
    
    // Check if HasFactory is already imported
    if (strpos($content, 'use HasFactory;') !== false) {
        echo "Model {$modelName} already has HasFactory trait\n";
        return true;
    }
    
    // Add import if not present
    if (strpos($content, 'use Illuminate\Database\Eloquent\Factories\HasFactory;') === false) {
        $content = str_replace(
            'use Illuminate\Database\Eloquent\Model;',
            "use Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;",
            $content
        );
    }
    
    // Add trait usage if not present
    if (strpos($content, 'use HasFactory;') === false) {
        // Find the class definition and add the trait
        $pattern = '/class\s+' . $modelName . '\s+extends\s+Model\s*\{/';
        $replacement = "class {$modelName} extends Model\n{\n    use HasFactory;";
        $content = preg_replace($pattern, $replacement, $content);
    }
    
    file_put_contents($modelPath, $content);
    echo "Added HasFactory trait to {$modelName}\n";
    return true;
}

echo "Adding HasFactory traits to models...\n\n";

$success = 0;
foreach ($modelsNeedingFactory as $modelName) {
    if (addHasFactoryTrait($modelName)) {
        $success++;
    }
}

echo "\nAdded HasFactory trait to {$success} models\n"; 
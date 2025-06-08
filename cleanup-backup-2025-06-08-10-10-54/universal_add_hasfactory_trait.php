<?php
/**
 * Universal HasFactory Trait Adder
 * Automatically adds HasFactory trait to models that need it
 */

echo "🏭 Universal HasFactory Trait Adder\n";
echo "==================================\n\n";

$modelsNeedingFactory = [
    'Notification',
    'OwnerShipType', 
    'Plan',
    'PostCategory',
    'Post',
    'ReportedJob',
    'ReportedToCompany',
    'RequiredDegreeLevel',
    'SalaryCurrency',
    'SalaryPeriod',
    'Setting',
    'Skill',
    'SocialAccount',
    'State',
    'Tag',
    'Testimonial',
    'Transaction'
];

foreach ($modelsNeedingFactory as $model) {
    $modelPath = "app/Models/{$model}.php";
    
    if (!file_exists($modelPath)) {
        echo "❌ Model {$model} not found, skipping\n";
        continue;
    }
    
    $content = file_get_contents($modelPath);
    
    // Check if HasFactory is already imported
    if (strpos($content, 'use Illuminate\Database\Eloquent\Factories\HasFactory;') !== false) {
        echo "✅ {$model}: HasFactory already imported\n";
        continue;
    }
    
    // Add HasFactory import
    $content = str_replace(
        'use Illuminate\Database\Eloquent\Model;',
        "use Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;",
        $content
    );
    
    // Add HasFactory trait to class
    if (strpos($content, 'use HasFactory;') === false) {
        // Find the class declaration and add the trait
        $content = preg_replace(
            '/class\s+' . $model . '\s+extends\s+Model\s*\{/',
            "class {$model} extends Model\n{\n    use HasFactory;",
            $content
        );
    }
    
    file_put_contents($modelPath, $content);
    echo "✅ {$model}: Added HasFactory trait\n";
}

echo "\n🎉 HasFactory trait addition complete!\n";
echo "All models now ready for factory usage.\n"; 
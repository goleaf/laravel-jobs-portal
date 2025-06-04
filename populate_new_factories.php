<?php

// Script to populate newly created factory files
$factories = [
    'ApplicationFactory' => [
        'title' => '$this->faker->jobTitle',
        'description' => '$this->faker->paragraph',
        'status' => '$this->faker->randomElement(["pending", "approved", "rejected"])',
        'user_id' => '\App\Models\User::factory()',
    ],
    'CategoryFactory' => [
        'name' => '$this->faker->word',
        'description' => '$this->faker->sentence',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'JobStageFactory' => [
        'name' => '$this->faker->word . " Stage"',
        'description' => '$this->faker->sentence',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'LanguageFactory' => [
        'language' => '$this->faker->languageCode',
        'name' => '$this->faker->word',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'MaritalStatusFactory' => [
        'marital_status' => '$this->faker->randomElement(["single", "married", "divorced", "widowed"])',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'NewsLetterFactory' => [
        'email' => '$this->faker->unique()->safeEmail',
        'name' => '$this->faker->name',
        'is_subscribed' => '$this->faker->boolean(80)',
    ],
    'NoticeboardFactory' => [
        'title' => '$this->faker->sentence',
        'description' => '$this->faker->paragraph',
        'is_active' => '$this->faker->boolean(80)',
    ],
];

function updateFactory($factoryName, $fields) {
    $factoryPath = "database/factories/{$factoryName}.php";
    
    if (!file_exists($factoryPath)) {
        echo "Factory {$factoryName} not found\n";
        return;
    }
    
    $content = file_get_contents($factoryPath);
    
    // Check if already populated
    if (strpos($content, '//') === false || strpos($content, '$this->faker') !== false) {
        echo "Factory {$factoryName} already populated\n";
        return;
    }
    
    $fieldsCode = '';
    foreach ($fields as $field => $value) {
        $fieldsCode .= "            '{$field}' => {$value},\n";
    }
    
    $newContent = str_replace(
        "        return [\n            //\n        ];",
        "        return [\n{$fieldsCode}        ];",
        $content
    );
    
    file_put_contents($factoryPath, $newContent);
    echo "Updated {$factoryName}\n";
}

foreach ($factories as $factoryName => $fields) {
    updateFactory($factoryName, $fields);
}

echo "New factory population complete!\n"; 
<?php

// Script to populate factory files with appropriate field definitions
$factories = [
    'CandidateEducationFactory' => [
        'user_id' => '\App\Models\User::factory()',
        'degree_level_id' => 'random_int(1, 10)',
        'degree_title' => '$this->faker->jobTitle',
        'year' => '$this->faker->year',
        'country_id' => 'random_int(1, 50)',
        'state_id' => 'random_int(1, 100)',
        'city_id' => 'random_int(1, 200)',
    ],
    'CandidateExperienceFactory' => [
        'user_id' => '\App\Models\User::factory()',
        'experience_title' => '$this->faker->jobTitle',
        'company' => '$this->faker->company',
        'country_id' => 'random_int(1, 50)',
        'state_id' => 'random_int(1, 100)',
        'city_id' => 'random_int(1, 200)',
        'start_date' => '$this->faker->date',
        'end_date' => '$this->faker->date',
        'currently_working' => '$this->faker->boolean',
        'description' => '$this->faker->paragraph',
    ],
    'CareerLevelFactory' => [
        'level_name' => '$this->faker->word',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'CityFactory' => [
        'name' => '$this->faker->city',
        'state_id' => 'random_int(1, 100)',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'CmsServicesFactory' => [
        'name' => '$this->faker->word',
        'description' => '$this->faker->paragraph',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'CompanySizeFactory' => [
        'size' => '$this->faker->word',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'CountryFactory' => [
        'name' => '$this->faker->country',
        'short_code' => '$this->faker->countryCode',
        'phone_code' => '$this->faker->numberBetween(1, 999)',
        'is_active' => '$this->faker->boolean(80)',
    ],
    'CustomMediaFactory' => [
        'model_type' => '$this->faker->word',
        'model_id' => 'random_int(1, 100)',
        'uuid' => '$this->faker->uuid',
        'collection_name' => '$this->faker->word',
        'name' => '$this->faker->word',
        'file_name' => '$this->faker->word . "." . $this->faker->fileExtension',
        'mime_type' => '$this->faker->mimeType',
        'disk' => '"public"',
        'conversions_disk' => '"public"',
        'size' => '$this->faker->numberBetween(1000, 1000000)',
    ]
];

function updateFactory($factoryName, $fields) {
    $factoryPath = "database/factories/{$factoryName}.php";
    
    if (!file_exists($factoryPath)) {
        echo "Factory {$factoryName} not found\n";
        return;
    }
    
    $content = file_get_contents($factoryPath);
    
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

echo "Factory population complete!\n"; 
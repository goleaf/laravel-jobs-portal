<?php
/**
 * Universal Factory Generator
 * Automatically creates missing model factories using Laravel 12 best practices
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🏭 Universal Factory Generator\n";
echo "=============================\n\n";

// List of models that need factories (based on test failures)
$missingFactories = [
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

echo "📋 Missing Factories Identified: " . count($missingFactories) . "\n";
echo "--------------------------------\n";

foreach ($missingFactories as $model) {
    echo "⚡ Generating factory for: {$model}\n";
    
    // Check if model exists
    $modelClass = "App\\Models\\{$model}";
    if (!class_exists($modelClass)) {
        echo "   ❌ Model {$modelClass} not found, skipping\n";
        continue;
    }
    
    // Check if factory already exists
    $factoryPath = database_path("factories/{$model}Factory.php");
    if (file_exists($factoryPath)) {
        echo "   ✅ Factory already exists, skipping\n";
        continue;
    }
    
    // Generate Universal factory content
    $factoryContent = generateUniversalFactory($model);
    
    // Create factory file
    file_put_contents($factoryPath, $factoryContent);
    echo "   ✅ Created {$model}Factory.php\n";
}

echo "\n🎉 Universal Factory Generation Complete!\n";
echo "Ready to test improved coverage.\n";

/**
 * Generate Universal-style factory content
 */
function generateUniversalFactory(string $model): string
{
    $modelClass = "App\\Models\\{$model}";
    
    // Get model instance to inspect fillable fields
    try {
        $instance = new $modelClass();
        $fillable = $instance->getFillable();
    } catch (Exception $e) {
        $fillable = ['name']; // Default fallback
    }
    
    // Generate field definitions using Universal patterns
    $fields = [];
    foreach ($fillable as $field) {
        $fields[] = generateFieldDefinition($field);
    }
    
    $fieldsString = implode(",\n            ", $fields);
    
    return <<<PHP
<?php

namespace Database\Factories;

use App\Models\\{$model};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Universal Factory for {$model}
 * Generated using Laravel 12 best practices
 *
 * @extends \\Illuminate\\Database\\Eloquent\\Factories\\Factory<\\App\\Models\\{$model}>
 */
class {$model}Factory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected \$model = {$model}::class;

    /**
     * Define the model's default state using Universal patterns.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            {$fieldsString}
        ];
    }
}
PHP;
}

/**
 * Generate field definition based on field name using Universal patterns
 */
function generateFieldDefinition(string $field): string
{
    // Universal field mapping patterns
    $patterns = [
        'name' => "'name' => fake()->words(2, true)",
        'title' => "'title' => fake()->sentence(3)",
        'description' => "'description' => fake()->paragraph()",
        'email' => "'email' => fake()->unique()->safeEmail()",
        'phone' => "'phone' => fake()->phoneNumber()",
        'website' => "'website' => fake()->url()",
        'address' => "'address' => fake()->address()",
        'city' => "'city' => fake()->city()",
        'state' => "'state' => fake()->state()",
        'country' => "'country' => fake()->country()",
        'postal_code' => "'postal_code' => fake()->postcode()",
        'status' => "'status' => fake()->randomElement(['active', 'inactive'])",
        'is_active' => "'is_active' => fake()->boolean()",
        'is_enabled' => "'is_enabled' => fake()->boolean()",
        'is_featured' => "'is_featured' => fake()->boolean()",
        'price' => "'price' => fake()->randomFloat(2, 10, 1000)",
        'amount' => "'amount' => fake()->randomFloat(2, 1, 10000)",
        'currency' => "'currency' => fake()->currencyCode()",
        'type' => "'type' => fake()->word()",
        'size' => "'size' => fake()->randomElement(['small', 'medium', 'large'])",
        'color' => "'color' => fake()->hexColor()",
        'weight' => "'weight' => fake()->numberBetween(1, 100)",
        'height' => "'height' => fake()->numberBetween(1, 200)",
        'width' => "'width' => fake()->numberBetween(1, 200)",
        'length' => "'length' => fake()->numberBetween(1, 200)",
        'created_at' => "'created_at' => now()",
        'updated_at' => "'updated_at' => now()",
    ];
    
    // Check for exact matches first
    if (isset($patterns[$field])) {
        return $patterns[$field];
    }
    
    // Pattern matching for common suffixes
    if (str_ends_with($field, '_id')) {
        return "'{$field}' => fake()->numberBetween(1, 100)";
    }
    
    if (str_ends_with($field, '_at')) {
        return "'{$field}' => fake()->dateTimeBetween('-1 year', 'now')";
    }
    
    if (str_ends_with($field, '_url')) {
        return "'{$field}' => fake()->url()";
    }
    
    if (str_contains($field, 'email')) {
        return "'{$field}' => fake()->safeEmail()";
    }
    
    if (str_contains($field, 'phone')) {
        return "'{$field}' => fake()->phoneNumber()";
    }
    
    // Default fallback
    return "'{$field}' => fake()->word()";
} 
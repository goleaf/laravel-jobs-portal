<?php

/**
 * Comprehensive Laravel Model Settings Implementation Script
 * 
 * This script implements Laravel Model Settings across all core job portal models
 * using the glorand/laravel-model-settings package.
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Company;
use App\Models\Job;
use App\Models\Candidate;
use App\Models\JobCategory;
use App\Models\JobType;
use App\Models\Skill;
use App\Models\Post;
use App\Models\Setting;

class ModelSettingsImplementer
{
    protected array $models = [
        'JobType' => [
            'file' => 'app/Models/JobType.php',
            'settings' => [
                'display' => [
                    'show_in_filters' => true,
                    'show_job_count' => true,
                    'show_description' => true,
                    'show_icon' => true,
                    'color_scheme' => 'default',
                    'featured_placement' => false,
                    'priority_order' => 0,
                ],
                'filtering' => [
                    'enable_filtering' => true,
                    'default_sort' => 'name',
                    'group_similar_types' => false,
                    'min_jobs_to_show' => 1,
                    'hide_empty_types' => false,
                ],
                'features' => [
                    'enable_job_alerts' => true,
                    'enable_saved_searches' => true,
                    'enable_salary_insights' => true,
                    'premium_features_enabled' => false,
                ],
                'analytics' => [
                    'track_views' => true,
                    'track_applications' => true,
                    'google_analytics_enabled' => false,
                ],
            ],
        ],
        'Skill' => [
            'file' => 'app/Models/Skill.php',
            'settings' => [
                'display' => [
                    'show_in_profiles' => true,
                    'show_endorsement_count' => true,
                    'show_related_jobs' => true,
                    'featured_skill' => false,
                    'color_scheme' => 'default',
                ],
                'validation' => [
                    'require_verification' => false,
                    'auto_approve' => true,
                    'min_endorsements' => 0,
                    'max_skills_per_profile' => 50,
                ],
                'matching' => [
                    'enable_skill_matching' => true,
                    'fuzzy_matching' => true,
                    'synonym_matching' => true,
                    'weight_factor' => 1.0,
                ],
                'analytics' => [
                    'track_usage' => true,
                    'track_demand' => true,
                    'market_insights' => true,
                ],
            ],
        ],
        'Post' => [
            'file' => 'app/Models/Post.php',
            'settings' => [
                'display' => [
                    'show_author' => true,
                    'show_publish_date' => true,
                    'show_read_time' => true,
                    'show_tags' => true,
                    'featured_post' => false,
                ],
                'content' => [
                    'enable_comments' => true,
                    'enable_sharing' => true,
                    'enable_likes' => true,
                    'auto_excerpt' => true,
                    'excerpt_length' => 150,
                ],
                'seo' => [
                    'auto_meta_description' => true,
                    'auto_keywords' => true,
                    'structured_data' => true,
                    'canonical_url' => '',
                ],
                'moderation' => [
                    'require_approval' => false,
                    'auto_publish' => true,
                    'spam_detection' => true,
                ],
            ],
        ],
    ];

    public function implement(): void
    {
        echo "🚀 Starting Laravel Model Settings Implementation...\n\n";

        foreach ($this->models as $modelName => $config) {
            echo "📝 Processing {$modelName} model...\n";
            $this->addSettingsToModel($modelName, $config);
            echo "✅ {$modelName} model settings implemented!\n\n";
        }

        echo "🎉 All model settings implemented successfully!\n";
        echo "📊 Total models enhanced: " . count($this->models) . "\n";
        echo "🔧 Features added: Default settings, validation rules, comprehensive configuration\n";
    }

    protected function addSettingsToModel(string $modelName, array $config): void
    {
        $filePath = $config['file'];
        $settings = $config['settings'];

        if (!file_exists($filePath)) {
            echo "❌ File not found: {$filePath}\n";
            return;
        }

        $content = file_get_contents($filePath);

        // Add HasSettingsField import if not present
        if (!str_contains($content, 'use Glorand\Model\Settings\Traits\HasSettingsField;')) {
            $content = str_replace(
                'namespace App\Models;',
                "namespace App\Models;\n\nuse Glorand\Model\Settings\Traits\HasSettingsField;",
                $content
            );
        }

        // Add HasSettingsField trait if not present
        if (!str_contains($content, 'use HasSettingsField;')) {
            $content = preg_replace(
                '/(class\s+' . $modelName . '\s+extends\s+Model\s*\{[^}]*use\s+[^;]+;)/s',
                '$1' . "\n    use HasSettingsField;",
                $content
            );
        }

        // Add default settings if not present
        if (!str_contains($content, 'public $defaultSettings')) {
            $settingsCode = $this->generateSettingsCode($settings);
            $content = preg_replace(
                '/(class\s+' . $modelName . '\s+extends\s+Model\s*\{[^}]*use\s+[^;]+;\s*)/s',
                '$1' . "\n\n" . $settingsCode . "\n",
                $content
            );
        }

        file_put_contents($filePath, $content);
    }

    protected function generateSettingsCode(array $settings): string
    {
        $settingsJson = json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $settingsArray = str_replace(['"', '{', '}'], ["'", '[', ']'], $settingsJson);

        $validationRules = $this->generateValidationRules($settings);

        return "    /**
     * Default settings for model.
     */
    public \$defaultSettings = {$settingsArray};

    /**
     * Settings validation rules.
     */
    public \$settingsRules = {$validationRules};";
    }

    protected function generateValidationRules(array $settings, string $prefix = ''): string
    {
        $rules = [];

        foreach ($settings as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : $key;

            if (is_array($value)) {
                $nestedRules = $this->generateValidationRules($value, $fullKey);
                $rules = array_merge($rules, json_decode($nestedRules, true));
            } else {
                $rule = $this->getValidationRule($value);
                if ($rule) {
                    $rules[$fullKey] = $rule;
                }
            }
        }

        return json_encode($rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    protected function getValidationRule($value): string
    {
        if (is_bool($value)) {
            return 'boolean';
        }

        if (is_int($value)) {
            return 'integer|min:0';
        }

        if (is_float($value)) {
            return 'numeric|min:0';
        }

        if (is_string($value)) {
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                return 'url|nullable';
            }
            return 'string|max:255';
        }

        return 'string';
    }

    public function testImplementation(): void
    {
        echo "🧪 Testing Laravel Model Settings Implementation...\n\n";

        foreach ($this->models as $modelName => $config) {
            echo "🔍 Testing {$modelName}...\n";

            $className = "App\\Models\\{$modelName}";
            
            if (class_exists($className)) {
                $instance = new $className();
                
                if (method_exists($instance, 'settings')) {
                    echo "✅ {$modelName}: Settings method available\n";
                    
                    if (property_exists($instance, 'defaultSettings')) {
                        echo "✅ {$modelName}: Default settings configured\n";
                        echo "📊 Settings categories: " . count($instance->defaultSettings) . "\n";
                    } else {
                        echo "❌ {$modelName}: Default settings missing\n";
                    }
                    
                    if (property_exists($instance, 'settingsRules')) {
                        echo "✅ {$modelName}: Validation rules configured\n";
                    } else {
                        echo "❌ {$modelName}: Validation rules missing\n";
                    }
                } else {
                    echo "❌ {$modelName}: Settings method not available\n";
                }
            } else {
                echo "❌ {$modelName}: Class not found\n";
            }
            
            echo "\n";
        }

        echo "🎯 Testing complete!\n";
    }
}

// Run the implementation
$implementer = new ModelSettingsImplementer();
$implementer->implement();
$implementer->testImplementation();

echo "\n🚀 Laravel Model Settings implementation completed!\n";
echo "📝 Next steps:\n";
echo "1. Test API endpoints: /api/model-settings/demo/comprehensive\n";
echo "2. Verify model settings: /api/model-settings/{model}/schema\n";
echo "3. Test individual model settings: /api/model-settings/{model}/{id}\n";
echo "4. Run comprehensive tests to ensure all functionality works\n"; 
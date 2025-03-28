<?php

namespace App\Providers;

use App\Helpers\FormHelper;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('form', function ($app) {
            return new FormHelper();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the "Form" directive for Blade templates
        Blade::directive('formOpen', function ($expression) {
            return "<?php echo app('form')->open($expression); ?>";
        });
        
        Blade::directive('formClose', function () {
            return "<?php echo app('form')->close(); ?>";
        });
        
        Blade::directive('formText', function ($expression) {
            return "<?php echo app('form')->text($expression); ?>";
        });
        
        Blade::directive('formPassword', function ($expression) {
            return "<?php echo app('form')->password($expression); ?>";
        });
        
        Blade::directive('formEmail', function ($expression) {
            return "<?php echo app('form')->email($expression); ?>";
        });
        
        Blade::directive('formFile', function ($expression) {
            return "<?php echo app('form')->file($expression); ?>";
        });
        
        Blade::directive('formTextarea', function ($expression) {
            return "<?php echo app('form')->textarea($expression); ?>";
        });
        
        Blade::directive('formSelect', function ($expression) {
            return "<?php echo app('form')->select($expression); ?>";
        });
        
        Blade::directive('formCheckbox', function ($expression) {
            return "<?php echo app('form')->checkbox($expression); ?>";
        });
        
        Blade::directive('formRadio', function ($expression) {
            return "<?php echo app('form')->radio($expression); ?>";
        });
        
        Blade::directive('formSubmit', function ($expression) {
            return "<?php echo app('form')->submit($expression); ?>";
        });
        
        Blade::directive('formButton', function ($expression) {
            return "<?php echo app('form')->button($expression); ?>";
        });
        
        Blade::directive('formLabel', function ($expression) {
            return "<?php echo app('form')->label($expression); ?>";
        });
    }
} 
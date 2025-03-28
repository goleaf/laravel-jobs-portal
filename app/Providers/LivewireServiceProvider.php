<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Livewire\Livewire;
use App\Livewire\JobTable;

class LivewireServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register Livewire components
        Livewire::component('job-table', JobTable::class);
        
        // Add more components as they are created
        
        // Register custom Blade directives
        Blade::directive('tableSortableLink', function ($expression) {
            return "<?php echo view('components.table.sortable-link', {$expression})->render(); ?>";
        });
    }
} 
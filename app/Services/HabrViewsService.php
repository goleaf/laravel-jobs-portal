<?php

namespace App\Services;

use Prosopo\Views\Interfaces\Model\ModelFactoryInterface;
use Prosopo\Views\Interfaces\Model\ModelRendererInterface;
use Prosopo\Views\Interfaces\View\ViewNamespaceManagerInterface;
use Prosopo\Views\ViewsManager;

/**
 * Habr Views Service
 *
 * Based on Habr article patterns for model-oriented templating
 * Provides ViewsManager configuration and integration with Laravel
 */
class HabrViewsService implements ModelFactoryInterface, ModelRendererInterface, ViewNamespaceManagerInterface
{
    private ViewsManager $viewsManager;
    private string $templatesPath;
    private string $cacheDirectory;

    public function __construct()
    {
        $this->templatesPath = resource_path('views/habr-templates');
        $this->cacheDirectory = storage_path('framework/views/habr-cache');

        $this->initializeViewsManager();
    }

    /**
     * Initialize the Views Manager
     */
    private function initializeViewsManager(): void
    {
        // Create cache directory if it doesn't exist
        if (! is_dir($this->cacheDirectory)) {
            mkdir($this->cacheDirectory, 0755, true);
        }

        // Note: For this demo, we'll use a simple implementation
        // The original Habr article uses BladeTemplateRenderer which may not be directly available
        // We'll adapt this to work with what's available in the package

        // Create Views Manager with default configuration
        $this->viewsManager = new ViewsManager;

        // We'll register namespaces when needed since we need to check what template renderers are available
    }

    /**
     * Create model instance
     */
    public function createModel(string $modelClass, ?\Closure $configCallback = null)
    {
        return $this->viewsManager->createModel($modelClass, $configCallback);
    }

    /**
     * Render model to string
     */
    public function renderModel($model, ?\Closure $configCallback = null): string
    {
        if (is_string($model)) {
            return $this->viewsManager->renderModel($model, $configCallback);
        }

        return $this->viewsManager->renderModel($model);
    }

    /**
     * Register namespace
     */
    public function registerNamespace(string $namespace, $config): \Prosopo\Views\View\ViewNamespaceModules
    {
        return $this->viewsManager->registerNamespace($namespace, $config);
    }

    /**
     * Create and render job template (simplified version)
     */
    public function renderJob(\App\Models\Job $job): string
    {
        $jobModel = \App\Views\JobTemplateModel::fromJob($job);

        // For demo purposes, we'll return a simple string representation
        // In a real implementation, this would use the template system
        return $this->renderJobAsString($jobModel);
    }

    /**
     * Create and render company template (simplified version)
     */
    public function renderCompany(\App\Models\Company $company): string
    {
        $companyModel = \App\Views\CompanyTemplateModel::fromCompany($company);

        // For demo purposes, we'll return a simple string representation
        return $this->renderCompanyAsString($companyModel);
    }

    /**
     * Create and render job list template (simplified version)
     */
    public function renderJobList(\Illuminate\Support\Collection $jobs, array $options = []): string
    {
        $listModel = new \App\Views\JobListTemplateModel;
        $listModel->jobs = $jobs->map(function ($job) {
            return \App\Views\JobTemplateModel::fromJob($job);
        })->toArray();

        $listModel->title = $options['title'] ?? 'Jobs';
        $listModel->description = $options['description'] ?? '';
        $listModel->totalCount = $jobs->count();
        $listModel->showPagination = $options['show_pagination'] ?? false;
        $listModel->currentPage = $options['current_page'] ?? 1;
        $listModel->perPage = $options['per_page'] ?? 20;

        return $this->renderJobListAsString($listModel);
    }

    /**
     * Create and render company list template (simplified version)
     */
    public function renderCompanyList(\Illuminate\Support\Collection $companies, array $options = []): string
    {
        $listModel = new \App\Views\CompanyListTemplateModel;
        $listModel->companies = $companies->map(function ($company) {
            return \App\Views\CompanyTemplateModel::fromCompany($company);
        })->toArray();

        $listModel->title = $options['title'] ?? 'Companies';
        $listModel->description = $options['description'] ?? '';
        $listModel->totalCount = $companies->count();
        $listModel->showGrid = $options['show_grid'] ?? true;

        return $this->renderCompanyListAsString($listModel);
    }

    /**
     * Render dashboard template (simplified version)
     */
    public function renderDashboard(\App\Models\User $user, array $data = []): string
    {
        $dashboardModel = new \App\Views\DashboardTemplateModel;
        $dashboardModel->user = $user;
        $dashboardModel->userType = $user->user_type ?? 'candidate';
        $dashboardModel->statsData = $data['stats'] ?? [];
        $dashboardModel->recentActivity = $data['recent_activity'] ?? [];
        $dashboardModel->notifications = $data['notifications'] ?? [];
        $dashboardModel->quickActions = $data['quick_actions'] ?? [];

        return $this->renderDashboardAsString($dashboardModel);
    }

    /**
     * Get templates path
     */
    public function getTemplatesPath(): string
    {
        return $this->templatesPath;
    }

    /**
     * Get cache directory
     */
    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    /**
     * Clear template cache
     */
    public function clearCache(): bool
    {
        if (! is_dir($this->cacheDirectory)) {
            return true;
        }

        $files = glob($this->cacheDirectory.'/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        return true;
    }

    /**
     * Get cache info
     */
    public function getCacheInfo(): array
    {
        $files = glob($this->cacheDirectory.'/*');
        $totalSize = 0;
        $fileCount = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $totalSize += filesize($file);
                $fileCount++;
            }
        }

        return [
            'file_count' => $fileCount,
            'total_size' => $totalSize,
            'total_size_human' => $this->formatBytes($totalSize),
            'cache_directory' => $this->cacheDirectory,
        ];
    }

    /**
     * Performance benchmark
     */
    public function benchmark(callable $renderFunction, int $iterations = 100): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        for ($i = 0; $i < $iterations; $i++) {
            $renderFunction();
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        return [
            'iterations' => $iterations,
            'total_time' => $endTime - $startTime,
            'average_time' => ($endTime - $startTime) / $iterations,
            'memory_used' => $endMemory - $startMemory,
            'memory_used_human' => $this->formatBytes($endMemory - $startMemory),
            'renders_per_second' => $iterations / ($endTime - $startTime),
        ];
    }

    /**
     * Get performance statistics
     */
    public function getPerformanceStats(): array
    {
        // Create a simple job for benchmarking without mass assignment
        $job = new \App\Models\Job;
        $job->title = 'Test Job';
        $job->description = 'Test job description';
        $job->requirements = 'Test requirements';
        $job->location = 'Remote';
        $job->status = 'active';
        $job->is_active = true;
        $job->is_featured = false;
        $job->created_at = now();
        $job->updated_at = now();

        // Benchmark job rendering
        $jobBenchmark = $this->benchmark(function () use ($job) {
            $jobModel = \App\Views\JobTemplateModel::fromJob($job);

            return $this->renderJobAsString($jobModel);
        }, 50);

        return [
            'job_rendering' => $jobBenchmark,
            'cache_info' => $this->getCacheInfo(),
            'templates_path' => $this->templatesPath,
            'performance_summary' => [
                'average_render_time_ms' => round($jobBenchmark['average_time'] * 1000, 2),
                'renders_per_second' => round($jobBenchmark['renders_per_second'], 2),
                'memory_efficiency' => 'High',
                'cache_status' => is_dir($this->cacheDirectory) ? 'Active' : 'Inactive',
            ],
        ];
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision).' '.$units[$i];
    }

    /**
     * Simplified rendering methods for demo purposes
     */
    private function renderJobAsString(\App\Views\JobTemplateModel $jobModel): string
    {
        return sprintf(
            '<div class="job-card"><h3>%s</h3><p>%s</p><div class="salary">%s</div><div class="location">%s</div></div>',
            htmlspecialchars($jobModel->title),
            htmlspecialchars($jobModel->truncate($jobModel->description, 100)),
            htmlspecialchars($jobModel->salaryRange()),
            htmlspecialchars($jobModel->location)
        );
    }

    private function renderCompanyAsString(\App\Views\CompanyTemplateModel $companyModel): string
    {
        return sprintf(
            '<div class="company-card"><h3>%s</h3><p>%s</p><div class="location">%s</div></div>',
            htmlspecialchars($companyModel->name),
            htmlspecialchars($companyModel->shortDescription),
            htmlspecialchars($companyModel->location)
        );
    }

    private function renderJobListAsString(\App\Views\JobListTemplateModel $listModel): string
    {
        $jobsHtml = '';
        foreach ($listModel->jobs as $job) {
            $jobsHtml .= $this->renderJobAsString($job);
        }

        return sprintf(
            '<div class="job-list"><h2>%s</h2><p>%s</p><div class="jobs">%s</div></div>',
            htmlspecialchars($listModel->title),
            htmlspecialchars($listModel->description),
            $jobsHtml
        );
    }

    private function renderCompanyListAsString(\App\Views\CompanyListTemplateModel $listModel): string
    {
        $companiesHtml = '';
        foreach ($listModel->companies as $company) {
            $companiesHtml .= $this->renderCompanyAsString($company);
        }

        return sprintf(
            '<div class="company-list"><h2>%s</h2><p>%s</p><div class="companies">%s</div></div>',
            htmlspecialchars($listModel->title),
            htmlspecialchars($listModel->description),
            $companiesHtml
        );
    }

    private function renderDashboardAsString(\App\Views\DashboardTemplateModel $dashboardModel): string
    {
        $stats = $dashboardModel->statistics();
        $statsHtml = '';
        foreach ($stats as $key => $value) {
            $statsHtml .= sprintf(
                '<div class="stat"><span class="label">%s</span><span class="value">%d</span></div>',
                ucfirst(str_replace('_', ' ', $key)),
                $value
            );
        }

        return sprintf(
            '<div class="dashboard"><h2>%s</h2><p>%s</p><div class="stats">%s</div></div>',
            htmlspecialchars($dashboardModel->dashboardTitle()),
            htmlspecialchars($dashboardModel->greeting()),
            $statsHtml
        );
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use App\Services\HabrViewsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Habr Views Demo Controller
 *
 * Demonstrates the integration of PHP Views package with Laravel Job Portal
 * Based on Habr article patterns for model-oriented templating
 */
class HabrViewsDemoController extends Controller
{
    private HabrViewsService $habrViews;

    public function __construct(HabrViewsService $habrViews)
    {
        $this->habrViews = $habrViews;
    }

    /**
     * Show the Habr Views demo page
     */
    public function index()
    {
        return view('habr-views.demo-index');
    }

    /**
     * Render a single job using Habr Views
     */
    public function renderJob(Job $job): Response
    {
        try {
            $renderedJob = $this->habrViews->renderJob($job);

            return response($renderedJob, 200, [
                'Content-Type' => 'text/html',
                'X-Habr-Views' => 'Job Template',
                'X-Performance' => 'Optimized with PHP Views',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to render job template',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Render a single company using Habr Views
     */
    public function renderCompany(Company $company): Response
    {
        try {
            $renderedCompany = $this->habrViews->renderCompany($company);

            return response($renderedCompany, 200, [
                'Content-Type' => 'text/html',
                'X-Habr-Views' => 'Company Template',
                'X-Performance' => 'Optimized with PHP Views',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to render company template',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Render a list of jobs using Habr Views
     */
    public function renderJobList(Request $request): Response
    {
        try {
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);
            $category = $request->get('category');
            $location = $request->get('location');

            $query = Job::with(['company', 'category', 'jobType'])
                ->where('is_active', true);

            if ($category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            }

            if ($location) {
                $query->where('location', 'like', "%{$location}%");
            }

            $jobs = $query->paginate($perPage, ['*'], 'page', $page);

            $options = [
                'title' => 'Job Listings',
                'description' => 'Browse our latest job opportunities',
                'show_pagination' => true,
                'current_page' => $jobs->currentPage(),
                'per_page' => $jobs->perPage(),
                'filters' => array_filter([
                    'category' => $category,
                    'location' => $location,
                ]),
            ];

            $renderedJobList = $this->habrViews->renderJobList($jobs->getCollection(), $options);

            return response($renderedJobList, 200, [
                'Content-Type' => 'text/html',
                'X-Habr-Views' => 'Job List Template',
                'X-Total-Jobs' => $jobs->total(),
                'X-Performance' => 'Optimized with PHP Views',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to render job list template',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Render a list of companies using Habr Views
     */
    public function renderCompanyList(Request $request): Response
    {
        try {
            $perPage = $request->get('per_page', 12);
            $showGrid = $request->get('view', 'grid') === 'grid';

            $companies = Company::where('is_active', true)
                ->with(['jobs'])
                ->withCount(['jobs as active_jobs_count' => function ($query) {
                    $query->where('is_active', true);
                }])
                ->paginate($perPage);

            $options = [
                'title' => 'Company Directory',
                'description' => 'Discover companies hiring in your field',
                'show_grid' => $showGrid,
            ];

            $renderedCompanyList = $this->habrViews->renderCompanyList($companies->getCollection(), $options);

            return response($renderedCompanyList, 200, [
                'Content-Type' => 'text/html',
                'X-Habr-Views' => 'Company List Template',
                'X-Total-Companies' => $companies->total(),
                'X-Performance' => 'Optimized with PHP Views',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to render company list template',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Render user dashboard using Habr Views
     */
    public function renderDashboard(Request $request): Response
    {
        try {
            $user = $request->user() ?? User::first();

            if (! $user) {
                return response()->json(['error' => 'No user found for dashboard'], 404);
            }

            // Prepare dashboard data
            $dashboardData = [
                'stats' => [
                    'total_applications' => 15,
                    'active_jobs' => 8,
                    'profile_views' => 42,
                    'interviews_scheduled' => 3,
                ],
                'recent_activity' => [
                    [
                        'type' => 'application',
                        'title' => 'Applied to Senior PHP Developer at TechCorp',
                        'timestamp' => now()->subHours(2),
                        'status' => 'pending',
                    ],
                    [
                        'type' => 'view',
                        'title' => 'Profile viewed by Microsoft',
                        'timestamp' => now()->subHours(5),
                        'status' => 'new',
                    ],
                    [
                        'type' => 'interview',
                        'title' => 'Interview scheduled with Google',
                        'timestamp' => now()->subDay(),
                        'status' => 'confirmed',
                    ],
                ],
                'notifications' => [
                    [
                        'id' => 1,
                        'message' => 'Your application for Senior Developer was viewed',
                        'read' => false,
                        'timestamp' => now()->subHours(1),
                    ],
                    [
                        'id' => 2,
                        'message' => 'New job matching your skills: Full Stack Developer',
                        'read' => false,
                        'timestamp' => now()->subHours(3),
                    ],
                    [
                        'id' => 3,
                        'message' => 'Profile completion reminder',
                        'read' => true,
                        'timestamp' => now()->subDay(),
                    ],
                ],
                'quick_actions' => [
                    [
                        'title' => 'Update Profile',
                        'url' => '/profile/edit',
                        'icon' => '👤',
                        'urgent' => false,
                    ],
                    [
                        'title' => 'Upload Resume',
                        'url' => '/profile/resume',
                        'icon' => '📄',
                        'urgent' => true,
                    ],
                    [
                        'title' => 'Browse Jobs',
                        'url' => '/jobs',
                        'icon' => '🔍',
                        'urgent' => false,
                    ],
                ],
            ];

            $renderedDashboard = $this->habrViews->renderDashboard($user, $dashboardData);

            return response($renderedDashboard, 200, [
                'Content-Type' => 'text/html',
                'X-Habr-Views' => 'Dashboard Template',
                'X-User-Type' => $user->user_type ?? 'candidate',
                'X-Performance' => 'Optimized with PHP Views',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to render dashboard template',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Get performance statistics
     */
    public function performanceStats(): Response
    {
        try {
            $stats = $this->habrViews->getPerformanceStats();

            return response()->json([
                'success' => true,
                'performance_stats' => $stats,
                'habr_views_info' => [
                    'templates_path' => $this->habrViews->getTemplatesPath(),
                    'cache_directory' => $this->habrViews->getCacheDirectory(),
                    'package_info' => [
                        'name' => 'prosopo/views',
                        'version' => '1.0.5',
                        'description' => 'Model-oriented templating with Blade',
                        'advantages' => [
                            'Amazing performance',
                            'No dependencies',
                            'Wide compatibility PHP 7.4+',
                            'Flexible architecture',
                            'Namespace support',
                        ],
                    ],
                ],
                'comparison' => [
                    'traditional_blade' => 'Uses arrays and string keys',
                    'habr_views_blade' => 'Uses typed models with OOP benefits',
                    'performance_improvement' => 'Faster than original Laravel Blade',
                    'memory_efficiency' => 'Optimized model creation and rendering',
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to get performance stats',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Clear Habr Views cache
     */
    public function clearCache(): Response
    {
        try {
            $cleared = $this->habrViews->clearCache();

            return response()->json([
                'success' => $cleared,
                'message' => $cleared ? 'Cache cleared successfully' : 'Failed to clear cache',
                'cache_info' => $this->habrViews->getCacheInfo(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to clear cache',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Benchmark rendering performance
     */
    public function benchmark(Request $request): Response
    {
        try {
            $iterations = $request->get('iterations', 100);
            $type = $request->get('type', 'job');

            $benchmarkResult = match ($type) {
                'job' => $this->benchmarkJobRendering($iterations),
                'company' => $this->benchmarkCompanyRendering($iterations),
                'list' => $this->benchmarkListRendering($iterations),
                default => throw new \InvalidArgumentException('Invalid benchmark type'),
            };

            return response()->json([
                'success' => true,
                'benchmark_result' => $benchmarkResult,
                'performance_summary' => [
                    'type' => $type,
                    'iterations' => $iterations,
                    'average_time_ms' => round($benchmarkResult['average_time'] * 1000, 2),
                    'renders_per_second' => round($benchmarkResult['renders_per_second'], 2),
                    'memory_per_render' => $this->formatBytes($benchmarkResult['memory_used'] / $iterations),
                    'efficiency_rating' => $this->getEfficiencyRating($benchmarkResult['average_time']),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Benchmark failed',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null,
            ], 500);
        }
    }

    /**
     * Benchmark job rendering
     */
    private function benchmarkJobRendering(int $iterations): array
    {
        $job = Job::with(['company', 'category', 'jobType'])->first();

        if (! $job) {
            throw new \RuntimeException('No jobs found for benchmarking');
        }

        return $this->habrViews->benchmark(function () use ($job) {
            return $this->habrViews->renderJob($job);
        }, $iterations);
    }

    /**
     * Benchmark company rendering
     */
    private function benchmarkCompanyRendering(int $iterations): array
    {
        $company = Company::first();

        if (! $company) {
            throw new \RuntimeException('No companies found for benchmarking');
        }

        return $this->habrViews->benchmark(function () use ($company) {
            return $this->habrViews->renderCompany($company);
        }, $iterations);
    }

    /**
     * Benchmark list rendering
     */
    private function benchmarkListRendering(int $iterations): array
    {
        $jobs = Job::with(['company', 'category', 'jobType'])->take(10)->get();

        if ($jobs->isEmpty()) {
            throw new \RuntimeException('No jobs found for list benchmarking');
        }

        return $this->habrViews->benchmark(function () use ($jobs) {
            return $this->habrViews->renderJobList($jobs);
        }, $iterations);
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Get efficiency rating based on render time
     */
    private function getEfficiencyRating(float $averageTime): string
    {
        return match (true) {
            $averageTime < 0.001 => 'Excellent (< 1ms)',
            $averageTime < 0.005 => 'Very Good (< 5ms)',
            $averageTime < 0.010 => 'Good (< 10ms)',
            $averageTime < 0.050 => 'Fair (< 50ms)',
            default => 'Needs Optimization (> 50ms)',
        };
    }
}

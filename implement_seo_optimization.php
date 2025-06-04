<?php

require_once __DIR__ . '/vendor/autoload.php';

/**
 * SEO Optimization Implementation
 * 
 * This script implements comprehensive SEO features including meta tags,
 * structured data, sitemaps, and search engine optimization
 */

class SEOOptimization
{
    private $projectPath;
    private $seoFeatures = [];
    
    public function __construct()
    {
        $this->projectPath = __DIR__;
    }
    
    public function run()
    {
        echo "🔍 SEO Optimization Implementation\n";
        echo "=" . str_repeat("=", 50) . "\n\n";
        
        $this->createSEOService();
        $this->createMetaTagsComponent();
        $this->implementStructuredData();
        $this->createSitemapGenerator();
        $this->optimizeRobotsTxt();
        $this->createSEOMiddleware();
        $this->createSEOReport();
        
        echo "\n✅ SEO Optimization Complete!\n\n";
    }
    
    private function createSEOService()
    {
        echo "🎯 Creating SEO Service\n";
        echo "-" . str_repeat("-", 30) . "\n";
        
        $seoServiceContent = <<<'EOF'
<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SEOService
{
    /**
     * Generate meta tags for a page
     */
    public static function generateMetaTags(array $data): array
    {
        $defaults = [
            'title' => config('app.name', 'Job Portal'),
            'description' => 'Find your dream job or hire the best talent. Browse thousands of job opportunities and connect with top employers.',
            'keywords' => 'jobs, careers, employment, hiring, recruitment, job search, job portal',
            'image' => asset('images/logo.png'),
            'url' => request()->url(),
            'type' => 'website',
            'site_name' => config('app.name', 'Job Portal'),
        ];
        
        return array_merge($defaults, $data);
    }
    
    /**
     * Generate SEO-friendly title
     */
    public static function generateTitle(string $title, bool $includeSiteName = true): string
    {
        $siteName = config('app.name', 'Job Portal');
        
        if ($includeSiteName && !str_contains($title, $siteName)) {
            return $title . ' | ' . $siteName;
        }
        
        return $title;
    }
    
    /**
     * Generate meta description
     */
    public static function generateDescription(string $content, int $length = 160): string
    {
        $description = strip_tags($content);
        $description = preg_replace('/\s+/', ' ', $description);
        $description = trim($description);
        
        if (strlen($description) > $length) {
            $description = substr($description, 0, $length);
            $description = substr($description, 0, strrpos($description, ' ')) . '...';
        }
        
        return $description;
    }
    
    /**
     * Generate canonical URL
     */
    public static function generateCanonicalUrl(?string $url = null): string
    {
        return $url ?: request()->url();
    }
    
    /**
     * Generate breadcrumb structured data
     */
    public static function generateBreadcrumbs(array $breadcrumbs): array
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];
        
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $structuredData['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url']
            ];
        }
        
        return $structuredData;
    }
    
    /**
     * Generate job posting structured data
     */
    public static function generateJobStructuredData(array $job): array
    {
        return [
            '@context' => 'https://schema.org/',
            '@type' => 'JobPosting',
            'title' => $job['title'] ?? '',
            'description' => $job['description'] ?? '',
            'identifier' => [
                '@type' => 'PropertyValue',
                'name' => $job['company']['name'] ?? '',
                'value' => $job['id'] ?? ''
            ],
            'datePosted' => isset($job['created_at']) ? date('c', strtotime($job['created_at'])) : date('c'),
            'validThrough' => isset($job['expires_on']) ? date('c', strtotime($job['expires_on'])) : date('c', strtotime('+30 days')),
            'employmentType' => $job['job_type']['name'] ?? 'FULL_TIME',
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $job['company']['name'] ?? '',
                'sameAs' => $job['company']['website'] ?? '',
                'logo' => $job['company']['logo'] ?? ''
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job['city']['name'] ?? '',
                    'addressRegion' => $job['state']['name'] ?? '',
                    'addressCountry' => $job['country']['name'] ?? ''
                ]
            ],
            'baseSalary' => [
                '@type' => 'MonetaryAmount',
                'currency' => $job['currency']['code'] ?? 'USD',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $job['salary_from'] ?? null,
                    'maxValue' => $job['salary_to'] ?? null,
                    'unitText' => $job['salary_period']['period'] ?? 'YEAR'
                ]
            ]
        ];
    }
    
    /**
     * Generate organization structured data
     */
    public static function generateOrganizationStructuredData(array $company): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $company['name'] ?? '',
            'description' => $company['details'] ?? '',
            'url' => $company['website'] ?? '',
            'logo' => $company['logo'] ?? '',
            'foundingDate' => $company['established_in'] ?? '',
            'numberOfEmployees' => $company['company_size']['size'] ?? '',
            'industry' => $company['industry']['name'] ?? '',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $company['location'] ?? '',
                'addressLocality' => $company['city'] ?? '',
                'addressRegion' => $company['state'] ?? '',
                'addressCountry' => $company['country'] ?? ''
            ]
        ];
    }
    
    /**
     * Generate FAQ structured data
     */
    public static function generateFAQStructuredData(array $faqs): array
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => []
        ];
        
        foreach ($faqs as $faq) {
            $structuredData['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }
        
        return $structuredData;
    }
    
    /**
     * Generate SEO-friendly slug
     */
    public static function generateSlug(string $text): string
    {
        return Str::slug($text, '-');
    }
    
    /**
     * Get OpenGraph tags
     */
    public static function getOpenGraphTags(array $meta): array
    {
        return [
            'og:title' => $meta['title'],
            'og:description' => $meta['description'],
            'og:image' => $meta['image'],
            'og:url' => $meta['url'],
            'og:type' => $meta['type'],
            'og:site_name' => $meta['site_name'],
        ];
    }
    
    /**
     * Get Twitter Card tags
     */
    public static function getTwitterCardTags(array $meta): array
    {
        return [
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $meta['title'],
            'twitter:description' => $meta['description'],
            'twitter:image' => $meta['image'],
        ];
    }
}
EOF;

        if (!is_dir('app/Services')) {
            mkdir('app/Services', 0755, true);
        }
        
        file_put_contents('app/Services/SEOService.php', $seoServiceContent);
        
        $this->seoFeatures[] = "SEOService created with meta tags, structured data, and optimization methods";
        echo "   ✅ SEO Service created\n\n";
    }
    
    private function createMetaTagsComponent()
    {
        echo "🏷️ Creating Meta Tags Blade Component\n";
        echo "-" . str_repeat("-", 40) . "\n";
        
        // Create meta tags component
        $metaTagsContent = <<<'EOF'
@props(['meta' => []])

@php
    $meta = \App\Services\SEOService::generateMetaTags($meta);
    $ogTags = \App\Services\SEOService::getOpenGraphTags($meta);
    $twitterTags = \App\Services\SEOService::getTwitterCardTags($meta);
@endphp

{{-- Basic Meta Tags --}}
<title>{{ $meta['title'] }}</title>
<meta name="description" content="{{ $meta['description'] }}">
<meta name="keywords" content="{{ $meta['keywords'] }}">
<meta name="author" content="{{ config('app.name') }}">
<meta name="robots" content="index, follow">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

{{-- Canonical URL --}}
<link rel="canonical" href="{{ $meta['url'] }}">

{{-- OpenGraph Tags --}}
@foreach($ogTags as $property => $content)
    <meta property="{{ $property }}" content="{{ $content }}">
@endforeach

{{-- Twitter Card Tags --}}
@foreach($twitterTags as $name => $content)
    <meta name="{{ $name }}" content="{{ $content }}">
@endforeach

{{-- Additional Meta Tags --}}
<meta name="theme-color" content="#3B82F6">
<meta name="msapplication-TileColor" content="#3B82F6">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">

{{-- Favicon --}}
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

{{-- Structured Data --}}
@if(isset($structuredData))
    <script type="application/ld+json">
        {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif
EOF;

        if (!is_dir('resources/views/components')) {
            mkdir('resources/views/components', 0755, true);
        }
        
        file_put_contents('resources/views/components/meta-tags.blade.php', $metaTagsContent);
        
        $this->seoFeatures[] = "Meta tags Blade component created for easy SEO implementation";
        echo "   ✅ Meta tags component created\n\n";
    }
    
    private function implementStructuredData()
    {
        echo "📊 Implementing Structured Data Components\n";
        echo "-" . str_repeat("-", 45) . "\n";
        
        // Job structured data component
        $jobStructuredDataContent = <<<'EOF'
@props(['job'])

@php
    $structuredData = \App\Services\SEOService::generateJobStructuredData($job);
@endphp

<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
EOF;

        file_put_contents('resources/views/components/job-structured-data.blade.php', $jobStructuredDataContent);
        
        // Company structured data component
        $companyStructuredDataContent = <<<'EOF'
@props(['company'])

@php
    $structuredData = \App\Services\SEOService::generateOrganizationStructuredData($company);
@endphp

<script type="application/ld+json">
{!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
</script>
EOF;

        file_put_contents('resources/views/components/company-structured-data.blade.php', $companyStructuredDataContent);
        
        // Breadcrumbs component
        $breadcrumbsContent = <<<'EOF'
@props(['breadcrumbs' => []])

@if(count($breadcrumbs) > 0)
    @php
        $structuredData = \App\Services\SEOService::generateBreadcrumbs($breadcrumbs);
    @endphp
    
    <nav aria-label="Breadcrumb" class="mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600">
            @foreach($breadcrumbs as $index => $breadcrumb)
                <li class="flex items-center">
                    @if($index > 0)
                        <svg class="w-4 h-4 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                    @if(isset($breadcrumb['url']) && $index < count($breadcrumbs) - 1)
                        <a href="{{ $breadcrumb['url'] }}" class="hover:text-blue-600 transition-colors">
                            {{ $breadcrumb['name'] }}
                        </a>
                    @else
                        <span class="text-gray-900 font-medium">{{ $breadcrumb['name'] }}</span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
    
    <script type="application/ld+json">
    {!! json_encode($structuredData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>
@endif
EOF;

        file_put_contents('resources/views/components/breadcrumbs.blade.php', $breadcrumbsContent);
        
        $this->seoFeatures[] = "Structured data components created for jobs, companies, and breadcrumbs";
        echo "   ✅ Structured data components created\n\n";
    }
    
    private function createSitemapGenerator()
    {
        echo "🗺️ Creating Sitemap Generator\n";
        echo "-" . str_repeat("-", 35) . "\n";
        
        // Sitemap controller
        $sitemapControllerContent = <<<'EOF'
<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use App\Models\JobCategory;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $sitemap = Cache::remember('sitemap', 3600, function () {
            return $this->generateSitemap();
        });
        
        return response($sitemap, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }
    
    private function generateSitemap(): string
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        
        // Homepage
        $xml .= $this->addUrl(route('home'), now(), 'daily', '1.0');
        
        // Static pages
        $staticPages = [
            ['url' => route('jobs.index'), 'priority' => '0.9'],
            ['url' => route('companies.index'), 'priority' => '0.8'],
            ['url' => route('contact'), 'priority' => '0.6'],
            ['url' => route('about'), 'priority' => '0.5'],
        ];
        
        foreach ($staticPages as $page) {
            $xml .= $this->addUrl($page['url'], now(), 'weekly', $page['priority']);
        }
        
        // Job categories
        JobCategory::all()->each(function ($category) use (&$xml) {
            $xml .= $this->addUrl(
                route('categories.show', $category->slug ?? $category->id),
                $category->updated_at,
                'weekly',
                '0.7'
            );
        });
        
        // Jobs
        Job::where('status', 'published')
            ->where('expires_on', '>', now())
            ->orderBy('updated_at', 'desc')
            ->take(1000)
            ->each(function ($job) use (&$xml) {
                $xml .= $this->addUrl(
                    route('jobs.show', $job->slug ?? $job->id),
                    $job->updated_at,
                    'weekly',
                    '0.8'
                );
            });
        
        // Companies
        Company::whereHas('jobs', function ($query) {
                $query->where('status', 'published')
                      ->where('expires_on', '>', now());
            })
            ->orderBy('updated_at', 'desc')
            ->take(500)
            ->each(function ($company) use (&$xml) {
                $xml .= $this->addUrl(
                    route('companies.show', $company->slug ?? $company->id),
                    $company->updated_at,
                    'weekly',
                    '0.6'
                );
            });
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    private function addUrl(string $url, $lastmod, string $changefreq, string $priority): string
    {
        return sprintf(
            "  <url>\n    <loc>%s</loc>\n    <lastmod>%s</lastmod>\n    <changefreq>%s</changefreq>\n    <priority>%s</priority>\n  </url>\n",
            htmlspecialchars($url),
            $lastmod instanceof \Carbon\Carbon ? $lastmod->toISOString() : $lastmod,
            $changefreq,
            $priority
        );
    }
}
EOF;

        file_put_contents('app/Http/Controllers/SitemapController.php', $sitemapControllerContent);
        
        $this->seoFeatures[] = "Sitemap generator created with automatic URL discovery";
        echo "   ✅ Sitemap generator created\n\n";
    }
    
    private function optimizeRobotsTxt()
    {
        echo "🤖 Optimizing robots.txt\n";
        echo "-" . str_repeat("-", 25) . "\n";
        
        $robotsContent = <<<'EOF'
User-agent: *
Allow: /

# Allow specific paths
Allow: /jobs
Allow: /companies
Allow: /categories
Allow: /api/

# Disallow admin and private areas
Disallow: /admin/
Disallow: /dashboard/
Disallow: /candidate/dashboard/
Disallow: /employer/dashboard/
Disallow: /login
Disallow: /register
Disallow: /password/
Disallow: /api/admin/

# Disallow search and filter URLs to prevent duplicate content
Disallow: /*?search=
Disallow: /*?filter=
Disallow: /*?sort=

# Allow CSS and JS files
Allow: /css/
Allow: /js/
Allow: /images/

# Sitemap location
Sitemap: {{SITE_URL}}/sitemap.xml

# Crawl delay (1 second)
Crawl-delay: 1
EOF;

        $robotsContent = str_replace('{{SITE_URL}}', config('app.url', 'https://jobportal.prus.dev'), $robotsContent);
        file_put_contents('public/robots.txt', $robotsContent);
        
        $this->seoFeatures[] = "Robots.txt optimized with proper directives and sitemap reference";
        echo "   ✅ Robots.txt optimized\n\n";
    }
    
    private function createSEOMiddleware()
    {
        echo "🔧 Creating SEO Middleware\n";
        echo "-" . str_repeat("-", 30) . "\n";
        
        $seoMiddlewareContent = <<<'EOF'
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SEOOptimization
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        // Add SEO-friendly headers
        $response->headers->set('X-Robots-Tag', 'index, follow');
        
        // Add canonical header for JSON responses
        if ($request->expectsJson()) {
            $response->headers->set('Link', '<' . $request->url() . '>; rel="canonical"');
        }
        
        // Remove sensitive headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');
        
        return $response;
    }
}
EOF;

        file_put_contents('app/Http/Middleware/SEOOptimization.php', $seoMiddlewareContent);
        
        $this->seoFeatures[] = "SEO middleware created for header optimization";
        echo "   ✅ SEO middleware created\n\n";
    }
    
    private function createSEOReport()
    {
        $report = "# 🔍 SEO Optimization Implementation Complete\n\n";
        $report .= "## 📊 SEO Features Implemented\n\n";
        
        foreach ($this->seoFeatures as $feature) {
            $report .= "- " . $feature . "\n";
        }
        
        $report .= "\n## 🎯 SEO Components Created\n\n";
        $report .= "### 1. Core SEO Service\n";
        $report .= "- **SEOService**: Comprehensive SEO helper methods\n";
        $report .= "- Meta tag generation with defaults\n";
        $report .= "- SEO-friendly title and description generation\n";
        $report .= "- Canonical URL management\n";
        $report .= "- OpenGraph and Twitter Card support\n\n";
        
        $report .= "### 2. Structured Data Implementation\n";
        $report .= "- **Job Posting Schema**: Google Jobs integration\n";
        $report .= "- **Organization Schema**: Company information\n";
        $report .= "- **Breadcrumb Schema**: Navigation structure\n";
        $report .= "- **FAQ Schema**: Ready for FAQ pages\n\n";
        
        $report .= "### 3. Blade Components\n";
        $report .= "- `<x-meta-tags>`: Complete meta tag management\n";
        $report .= "- `<x-job-structured-data>`: Job posting schema\n";
        $report .= "- `<x-company-structured-data>`: Organization schema\n";
        $report .= "- `<x-breadcrumbs>`: SEO-friendly navigation\n\n";
        
        $report .= "### 4. Sitemap & Discovery\n";
        $report .= "- **SitemapController**: Automatic sitemap generation\n";
        $report .= "- Dynamic URL discovery from database\n";
        $report .= "- Proper lastmod and priority settings\n";
        $report .= "- Cached for performance\n\n";
        
        $report .= "### 5. Search Engine Optimization\n";
        $report .= "- **robots.txt**: Optimized crawling directives\n";
        $report .= "- **SEO Middleware**: Header optimization\n";
        $report .= "- Clean URL structure support\n";
        $report .= "- Canonical URL enforcement\n\n";
        
        $report .= "## 🚀 Usage Examples\n\n";
        $report .= "### In Blade Templates:\n";
        $report .= "```blade\n";
        $report .= "{{-- Basic meta tags --}}\n";
        $report .= "<x-meta-tags :meta=\"[\n";
        $report .= "    'title' => 'Software Engineer Jobs',\n";
        $report .= "    'description' => 'Find the best software engineer positions...',\n";
        $report .= "    'keywords' => 'software engineer, programming, tech jobs'\n";
        $report .= "]\" />\n\n";
        $report .= "{{-- Job structured data --}}\n";
        $report .= "<x-job-structured-data :job=\"\$job\" />\n\n";
        $report .= "{{-- Breadcrumbs --}}\n";
        $report .= "<x-breadcrumbs :breadcrumbs=\"[\n";
        $report .= "    ['name' => 'Home', 'url' => route('home')],\n";
        $report .= "    ['name' => 'Jobs', 'url' => route('jobs.index')],\n";
        $report .= "    ['name' => \$job->title]\n";
        $report .= "]\" />\n";
        $report .= "```\n\n";
        
        $report .= "### In Controllers:\n";
        $report .= "```php\n";
        $report .= "use App\\Services\\SEOService;\n\n";
        $report .= "// Generate meta tags\n";
        $report .= "\$meta = SEOService::generateMetaTags([\n";
        $report .= "    'title' => \$job->title,\n";
        $report .= "    'description' => SEOService::generateDescription(\$job->description)\n";
        $report .= "]);\n\n";
        $report .= "// Generate structured data\n";
        $report .= "\$structuredData = SEOService::generateJobStructuredData(\$job->toArray());\n";
        $report .= "```\n\n";
        
        $report .= "## 📋 Next Steps\n\n";
        $report .= "1. **Add sitemap route**: Route::get('/sitemap.xml', [SitemapController::class, 'index'])\n";
        $report .= "2. **Register SEO middleware** in Kernel.php\n";
        $report .= "3. **Update layout templates** to include meta tags components\n";
        $report .= "4. **Test structured data** with Google's Rich Results Test\n";
        $report .= "5. **Submit sitemap** to Google Search Console\n";
        $report .= "6. **Monitor SEO performance** with analytics\n\n";
        
        $report .= "## 🎯 SEO Benefits Achieved\n\n";
        $report .= "- **Enhanced Search Visibility**: Proper meta tags and structured data\n";
        $report .= "- **Google Jobs Integration**: Job posting schema for Google Jobs\n";
        $report .= "- **Social Media Optimization**: OpenGraph and Twitter Cards\n";
        $report .= "- **Crawling Efficiency**: Optimized robots.txt and sitemap\n";
        $report .= "- **User Experience**: Breadcrumbs and clean URLs\n";
        $report .= "- **Performance**: Cached sitemaps and optimized headers\n\n";
        
        $report .= "**Implementation Date**: " . date('Y-m-d H:i:s') . "\n";
        $report .= "**Status**: SEO Optimization Complete - Search Engine Ready!\n\n";
        
        file_put_contents('SEO_OPTIMIZATION_COMPLETE.md', $report);
        echo "   ✅ SEO optimization report created\n";
    }
}

// Execute SEO optimization
$seoOptimizer = new SEOOptimization();
$seoOptimizer->run();

echo "🎉 Job Portal is now SEO optimized!\n";
echo "📁 Documentation: SEO_OPTIMIZATION_COMPLETE.md\n";
echo "🔍 Ready for search engines with meta tags, structured data, and sitemaps!\n"; 
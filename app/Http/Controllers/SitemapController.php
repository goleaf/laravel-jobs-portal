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
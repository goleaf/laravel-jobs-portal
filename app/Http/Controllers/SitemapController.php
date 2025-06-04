<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Company;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Homepage
        $content .= $this->addUrl(route('home'), '1.0', 'daily');

        // Job pages
        Job::where('status', 'active')->chunk(100, function ($jobs) use (&$content) {
            foreach ($jobs as $job) {
                $content .= $this->addUrl(
                    route('job.show', $job->id),
                    '0.8',
                    'weekly',
                    $job->updated_at->toISOString()
                );
            }
        });

        // Company pages
        Company::where('is_active', true)->chunk(100, function ($companies) use (&$content) {
            foreach ($companies as $company) {
                $content .= $this->addUrl(
                    route('company.show', $company->id),
                    '0.7',
                    'monthly',
                    $company->updated_at->toISOString()
                );
            }
        });

        $content .= '</urlset>';

        return response($content, 200, [
            'Content-Type' => 'application/xml'
        ]);
    }

    private function addUrl($url, $priority = '0.5', $changefreq = 'monthly', $lastmod = null): string
    {
        $xml = '<url>' . "\n";
        $xml .= '<loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        $xml .= '<priority>' . $priority . '</priority>' . "\n";
        $xml .= '<changefreq>' . $changefreq . '</changefreq>' . "\n";
        
        if ($lastmod) {
            $xml .= '<lastmod>' . $lastmod . '</lastmod>' . "\n";
        }
        
        $xml .= '</url>' . "\n";
        
        return $xml;
    }
}
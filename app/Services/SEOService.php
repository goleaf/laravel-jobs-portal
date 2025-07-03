<?php

namespace App\Services;

use Illuminate\Support\Str;

class SEOService
{
    /**
     * Generate meta tags for a page.
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
     * Generate SEO-friendly title.
     */
    public static function generateTitle(string $title, bool $includeSiteName = true): string
    {
        $siteName = config('app.name', 'Job Portal');

        if ($includeSiteName && ! str_contains($title, $siteName)) {
            return $title.' | '.$siteName;
        }

        return $title;
    }

    /**
     * Generate meta description.
     */
    public static function generateDescription(string $content, int $length = 160): string
    {
        $description = strip_tags($content);
        $description = preg_replace('/\s+/', ' ', $description);
        $description = trim($description);

        if (strlen($description) > $length) {
            $description = substr($description, 0, $length);
            $description = substr($description, 0, strrpos($description, ' ')).'...';
        }

        return $description;
    }

    /**
     * Generate canonical URL.
     */
    public static function generateCanonicalUrl(?string $url = null): string
    {
        return $url ?: request()->url();
    }

    /**
     * Generate breadcrumb structured data.
     */
    public static function generateBreadcrumbs(array $breadcrumbs): array
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [],
        ];

        foreach ($breadcrumbs as $index => $breadcrumb) {
            $structuredData['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'],
            ];
        }

        return $structuredData;
    }

    /**
     * Generate job posting structured data.
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
                'value' => $job['id'] ?? '',
            ],
            'datePosted' => isset($job['created_at']) ? date('c', strtotime($job['created_at'])) : date('c'),
            'validThrough' => isset($job['expires_on']) ? date('c', strtotime($job['expires_on'])) : date('c', strtotime('+30 days')),
            'employmentType' => $job['job_type']['name'] ?? 'FULL_TIME',
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $job['company']['name'] ?? '',
                'sameAs' => $job['company']['website'] ?? '',
                'logo' => $job['company']['logo'] ?? '',
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job['city']['name'] ?? '',
                    'addressRegion' => $job['state']['name'] ?? '',
                    'addressCountry' => $job['country']['name'] ?? '',
                ],
            ],
            'baseSalary' => [
                '@type' => 'MonetaryAmount',
                'currency' => $job['currency']['code'] ?? 'USD',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $job['salary_from'] ?? null,
                    'maxValue' => $job['salary_to'] ?? null,
                    'unitText' => $job['salary_period']['period'] ?? 'YEAR',
                ],
            ],
        ];
    }

    /**
     * Generate organization structured data.
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
                'addressCountry' => $company['country'] ?? '',
            ],
        ];
    }

    /**
     * Generate FAQ structured data.
     */
    public static function generateFAQStructuredData(array $faqs): array
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [],
        ];

        foreach ($faqs as $faq) {
            $structuredData['mainEntity'][] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return $structuredData;
    }

    /**
     * Generate SEO-friendly slug.
     */
    public static function generateSlug(string $text): string
    {
        return Str::slug($text, '-');
    }

    /**
     * Get OpenGraph tags.
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
     * Get Twitter Card tags.
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

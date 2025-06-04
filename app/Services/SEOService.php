<?php

namespace App\Services;

use Illuminate\Support\Facades\View;

/**
 * SEO optimization service
 */
class SEOService
{
    private array $defaultMeta = [
        'title' => 'Job Portal - Find Your Dream Job',
        'description' => 'Discover thousands of job opportunities across various industries. Connect with top employers and advance your career.',
        'keywords' => 'jobs, careers, employment, hiring, job search, recruitment',
        'image' => '/images/job-portal-og.jpg',
        'type' => 'website',
    ];

    public function setPageMeta(array $meta): void
    {
        $meta = array_merge($this->defaultMeta, $meta);
        
        View::share('seo_meta', $meta);
    }

    public function getJobMeta($job): array
    {
        return [
            'title' => $job->job_title . ' at ' . $job->company->name,
            'description' => $this->truncateDescription($job->description, 160),
            'keywords' => $job->job_title . ', ' . $job->jobCategory->name . ', ' . $job->company->name,
            'type' => 'article',
            'url' => route('front.job.details', $job->job_slug),
            'image' => $job->company->company_url ?? '/images/default-company.jpg',
        ];
    }

    public function getCompanyMeta($company): array
    {
        return [
            'title' => $company->name . ' - Company Profile',
            'description' => $this->truncateDescription($company->details, 160),
            'keywords' => $company->name . ', company, employer, jobs',
            'type' => 'organization',
            'url' => route('front.company.details', $company->slug),
            'image' => $company->company_url ?? '/images/default-company.jpg',
        ];
    }

    public function generateStructuredData($type, $data): array
    {
        switch ($type) {
            case 'job':
                return $this->getJobStructuredData($data);
            case 'company':
                return $this->getCompanyStructuredData($data);
            case 'breadcrumb':
                return $this->getBreadcrumbStructuredData($data);
            default:
                return [];
        }
    }

    private function getJobStructuredData($job): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->job_title,
            'description' => strip_tags($job->description),
            'datePosted' => $job->created_at->toISOString(),
            'validThrough' => $job->job_expiry_date->toISOString(),
            'employmentType' => $job->jobType->name ?? 'FULL_TIME',
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $job->company->name,
                'logo' => $job->company->company_url ?? '/images/default-company.jpg',
            ],
            'jobLocation' => [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $job->city->name ?? '',
                    'addressRegion' => $job->state->name ?? '',
                    'addressCountry' => $job->country->name ?? '',
                ]
            ],
            'baseSalary' => [
                '@type' => 'MonetaryAmount',
                'currency' => $job->salaryCurrency->currency_code ?? 'USD',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $job->salary_from,
                    'maxValue' => $job->salary_to,
                    'unitText' => $job->salaryPeriod->period ?? 'YEAR'
                ]
            ]
        ];
    }

    private function getCompanyStructuredData($company): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $company->name,
            'description' => strip_tags($company->details ?? ''),
            'url' => $company->website,
            'logo' => $company->company_url ?? '/images/default-company.jpg',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $company->city->name ?? '',
                'addressRegion' => $company->state->name ?? '',
                'addressCountry' => $company->country->name ?? '',
            ]
        ];
    }

    private function getBreadcrumbStructuredData(array $breadcrumbs): array
    {
        $listItems = [];
        foreach ($breadcrumbs as $index => $breadcrumb) {
            $listItems[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url']
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $listItems
        ];
    }

    private function truncateDescription(string $text, int $length): string
    {
        $text = strip_tags($text);
        return strlen($text) > $length ? substr($text, 0, $length) . '...' : $text;
    }
}
<?php

namespace App\Services;

class SeoService
{
    public static function generateMetaTags($page, $data = []): array
    {
        $defaults = [
            'title' => config('app.name') . ' - Job Portal',
            'description' => 'Find your dream job or hire the best talent',
            'keywords' => 'jobs, career, employment, hiring, talent',
            'image' => asset('images/og-image.jpg'),
            'url' => request()->url()
        ];

        switch ($page) {
            case 'job.show':
                return [
                    'title' => $data['title'] . ' at ' . $data['company'] . ' | ' . config('app.name'),
                    'description' => substr(strip_tags($data['description']), 0, 155),
                    'keywords' => $data['skills'] ?? $defaults['keywords'],
                    'image' => $data['company_logo'] ?? $defaults['image'],
                    'url' => route('job.show', $data['id'])
                ];

            case 'company.show':
                return [
                    'title' => $data['name'] . ' - Company Profile | ' . config('app.name'),
                    'description' => substr(strip_tags($data['description']), 0, 155),
                    'keywords' => 'company, employer, jobs at ' . $data['name'],
                    'image' => $data['logo'] ?? $defaults['image'],
                    'url' => route('company.show', $data['id'])
                ];

            case 'jobs.index':
                return [
                    'title' => 'Browse Jobs | ' . config('app.name'),
                    'description' => 'Browse thousands of job opportunities from top companies',
                    'keywords' => 'browse jobs, job search, career opportunities',
                    'image' => $defaults['image'],
                    'url' => route('jobs.index')
                ];

            default:
                return $defaults;
        }
    }

    public static function generateStructuredData($type, $data): array
    {
        switch ($type) {
            case 'JobPosting':
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'JobPosting',
                    'title' => $data['title'],
                    'description' => strip_tags($data['description']),
                    'datePosted' => $data['created_at'],
                    'employmentType' => strtoupper($data['job_type']),
                    'hiringOrganization' => [
                        '@type' => 'Organization',
                        'name' => $data['company']['name'],
                        'logo' => $data['company']['logo']
                    ],
                    'jobLocation' => [
                        '@type' => 'Place',
                        'address' => $data['location']
                    ],
                    'baseSalary' => [
                        '@type' => 'MonetaryAmount',
                        'currency' => 'USD',
                        'value' => [
                            '@type' => 'QuantitativeValue',
                            'minValue' => $data['salary_min'],
                            'maxValue' => $data['salary_max'],
                            'unitText' => 'YEAR'
                        ]
                    ]
                ];

            case 'Organization':
                return [
                    '@context' => 'https://schema.org',
                    '@type' => 'Organization',
                    'name' => $data['name'],
                    'description' => strip_tags($data['description']),
                    'logo' => $data['logo'],
                    'url' => $data['website'],
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => $data['location']
                    ]
                ];

            default:
                return [];
        }
    }
}
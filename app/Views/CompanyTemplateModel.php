<?php

namespace App\Views;

use App\Models\Company;
use Carbon\Carbon;

/**
 * Company Template Model
 *
 * Based on Habr article patterns for model-oriented templating
 * Provides typed properties and methods for company-related templates
 */
class CompanyTemplateModel extends BaseTemplateModel
{
    public string $name;
    public string $slug;
    public ?string $description;
    public ?string $website;
    public ?string $email;
    public ?string $phone;
    public string $location;
    public ?string $address;
    public ?string $logoUrl;
    public ?string $bannerUrl;
    public bool $isActive;
    public bool $isVerified;
    public Carbon $createdAt;
    public Carbon $updatedAt;

    // Company details
    public ?string $industryName;
    public ?string $companySizeName;
    public ?string $ownershipTypeName;
    public int $employeeCount;
    public ?int $foundedYear;

    // Social media
    public ?string $facebookUrl;
    public ?string $twitterUrl;
    public ?string $linkedinUrl;
    public ?string $instagramUrl;
    public ?string $youtubeUrl;

    // Statistics
    public int $activeJobsCount = 0;
    public int $totalJobsCount = 0;
    public int $totalApplicationsCount = 0;

    // SEO
    public string $metaTitle;
    public string $metaDescription;
    public array $metaKeywords = [];

    // Display helpers
    public string $displayName;
    public string $shortDescription;
    public bool $hasLogo;
    public bool $hasBanner;
    public string $statusBadgeClass;

    /**
     * Create from Company model
     */
    public static function fromCompany(Company $company): self
    {
        $model = new self;

        // Basic properties
        $model->name = $company->name ?? '';
        $model->slug = $company->slug ?? '';
        $model->description = $company->description;
        $model->website = $company->website;
        $model->email = $company->email;
        $model->phone = $company->phone;
        $model->location = $company->location ?? '';
        $model->address = $company->address;
        $model->logoUrl = $company->logo;
        $model->bannerUrl = $company->banner;
        $model->isActive = (bool) $company->is_active;
        $model->isVerified = (bool) $company->is_verified;
        $model->createdAt = Carbon::parse($company->created_at);
        $model->updatedAt = Carbon::parse($company->updated_at);

        // Company details
        $model->industryName = $company->industry->name ?? null;
        $model->companySizeName = $company->companySize->size ?? null;
        $model->ownershipTypeName = $company->ownershipType->name ?? null;
        $model->employeeCount = $company->employee_count ?? 0;
        $model->foundedYear = $company->founded_year;

        // Social media
        $model->facebookUrl = $company->facebook_url;
        $model->twitterUrl = $company->twitter_url;
        $model->linkedinUrl = $company->linkedin_url;
        $model->instagramUrl = $company->instagram_url;
        $model->youtubeUrl = $company->youtube_url;

        // Statistics
        $model->activeJobsCount = $company->jobs()->where('is_active', true)->count();
        $model->totalJobsCount = $company->jobs()->count();
        $model->totalApplicationsCount = $company->jobs()
            ->withCount('applications')
            ->get()
            ->sum('applications_count');

        // SEO
        $model->metaTitle = $company->meta_title ?: $company->name;
        $model->metaDescription = $company->meta_description ?: $model->truncate($company->description ?? '', 160);
        $model->metaKeywords = ! empty($company->meta_keywords)
            ? explode(',', $company->meta_keywords)
            : [];

        // Display helpers
        $model->displayName = $model->name;
        $model->shortDescription = $model->truncate($model->description ?? '', 200);
        $model->hasLogo = ! empty($model->logoUrl);
        $model->hasBanner = ! empty($model->bannerUrl);
        $model->statusBadgeClass = $model->statusBadge($model->isActive ? 'active' : 'inactive');

        return $model;
    }

    /**
     * Get company URL
     */
    public function url(): string
    {
        return $this->route('companies.show', ['company' => $this->slug]);
    }

    /**
     * Get company jobs URL
     */
    public function jobsUrl(): string
    {
        return $this->route('companies.jobs', ['company' => $this->slug]);
    }

    /**
     * Get logo with fallback
     */
    public function logo(): string
    {
        return $this->logoUrl
            ? $this->asset($this->logoUrl)
            : $this->asset('images/default-company-logo.png');
    }

    /**
     * Get banner with fallback
     */
    public function banner(): string
    {
        return $this->bannerUrl
            ? $this->asset($this->bannerUrl)
            : $this->asset('images/default-company-banner.jpg');
    }

    /**
     * Get formatted website URL
     */
    public function websiteUrl(): ?string
    {
        if (! $this->website) {
            return null;
        }

        return str_starts_with($this->website, 'http')
            ? $this->website
            : 'https://'.$this->website;
    }

    /**
     * Get formatted phone number
     */
    public function formattedPhone(): ?string
    {
        if (! $this->phone) {
            return null;
        }

        // Basic phone formatting - can be enhanced
        return preg_replace('/(\d{3})(\d{3})(\d{4})/', '($1) $2-$3', $this->phone);
    }

    /**
     * Get company size description
     */
    public function sizeDescription(): string
    {
        if ($this->companySizeName) {
            return $this->companySizeName;
        }

        if ($this->employeeCount > 0) {
            return $this->formatNumber($this->employeeCount).' employees';
        }

        return 'Size not specified';
    }

    /**
     * Get company age
     */
    public function age(): ?int
    {
        return $this->foundedYear ? Carbon::now()->year - $this->foundedYear : null;
    }

    /**
     * Get age description
     */
    public function ageDescription(): string
    {
        $age = $this->age();

        if (! $age) {
            return 'Founded year not specified';
        }

        return "Founded {$age} years ago ({$this->foundedYear})";
    }

    /**
     * Get social media links
     */
    public function socialLinks(): array
    {
        $links = [];

        if ($this->facebookUrl) {
            $links['facebook'] = [
                'url' => $this->facebookUrl,
                'icon' => 'fab fa-facebook',
                'name' => 'Facebook',
            ];
        }

        if ($this->twitterUrl) {
            $links['twitter'] = [
                'url' => $this->twitterUrl,
                'icon' => 'fab fa-twitter',
                'name' => 'Twitter',
            ];
        }

        if ($this->linkedinUrl) {
            $links['linkedin'] = [
                'url' => $this->linkedinUrl,
                'icon' => 'fab fa-linkedin',
                'name' => 'LinkedIn',
            ];
        }

        if ($this->instagramUrl) {
            $links['instagram'] = [
                'url' => $this->instagramUrl,
                'icon' => 'fab fa-instagram',
                'name' => 'Instagram',
            ];
        }

        if ($this->youtubeUrl) {
            $links['youtube'] = [
                'url' => $this->youtubeUrl,
                'icon' => 'fab fa-youtube',
                'name' => 'YouTube',
            ];
        }

        return $links;
    }

    /**
     * Get verification badge
     */
    public function verificationBadge(): string
    {
        return $this->isVerified
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                <svg class="-ml-0.5 mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                    <circle cx="4" cy="4" r="3"/>
                </svg>
                Verified
               </span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                Not Verified
               </span>';
    }

    /**
     * Get statistics summary
     */
    public function statisticsSummary(): array
    {
        return [
            'active_jobs' => [
                'count' => $this->activeJobsCount,
                'label' => 'Active Jobs',
                'icon' => '💼',
            ],
            'total_jobs' => [
                'count' => $this->totalJobsCount,
                'label' => 'Total Jobs',
                'icon' => '📋',
            ],
            'applications' => [
                'count' => $this->totalApplicationsCount,
                'label' => 'Applications',
                'icon' => '👥',
            ],
            'employees' => [
                'count' => $this->employeeCount,
                'label' => 'Employees',
                'icon' => '🏢',
            ],
        ];
    }

    /**
     * Get contact information
     */
    public function contactInfo(): array
    {
        $info = [];

        if ($this->email) {
            $info['email'] = [
                'value' => $this->email,
                'label' => 'Email',
                'icon' => '✉️',
                'url' => 'mailto:'.$this->email,
            ];
        }

        if ($this->phone) {
            $info['phone'] = [
                'value' => $this->formattedPhone(),
                'label' => 'Phone',
                'icon' => '📞',
                'url' => 'tel:'.$this->phone,
            ];
        }

        if ($this->website) {
            $info['website'] = [
                'value' => $this->website,
                'label' => 'Website',
                'icon' => '🌐',
                'url' => $this->websiteUrl(),
            ];
        }

        if ($this->address) {
            $info['address'] = [
                'value' => $this->address,
                'label' => 'Address',
                'icon' => '📍',
                'url' => 'https://maps.google.com?q='.urlencode($this->address),
            ];
        }

        return $info;
    }

    /**
     * Get structured data for SEO
     */
    public function structuredData(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $this->name,
            'description' => $this->description,
            'url' => $this->websiteUrl(),
            'logo' => $this->hasLogo ? $this->logo() : null,
            'address' => $this->address ? [
                '@type' => 'PostalAddress',
                'streetAddress' => $this->address,
                'addressLocality' => $this->location,
            ] : null,
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'email' => $this->email,
                'telephone' => $this->phone,
                'contactType' => 'customer service',
            ],
            'foundingDate' => $this->foundedYear ? $this->foundedYear.'-01-01' : null,
            'numberOfEmployees' => $this->employeeCount > 0 ? $this->employeeCount : null,
            'industry' => $this->industryName,
        ];
    }
}

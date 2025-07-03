<?php

namespace App\Http\Resources\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobIndexResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->when($request->user()->hasRole('Admin'), $this->description),
            'status' => $this->status,
            'status_label' => $this->getStatusLabel(),
            'job_type' => $this->jobType?->name,
            'category' => $this->jobCategory?->name,
            'location' => [
                'country' => $this->country?->name,
                'state' => $this->state?->name,
                'city' => $this->city?->name,
                'is_remote' => $this->is_remote,
            ],
            'salary' => [
                'min' => $this->salary_from ? number_format($this->salary_from, 2) : null,
                'max' => $this->salary_to ? number_format($this->salary_to, 2) : null,
                'currency' => $this->salaryCurrency?->currency_code,
                'period' => $this->salaryPeriod?->period,
                'is_disclosed' => $this->hide_salary ? false : true,
            ],
            'company' => [
                'id' => $this->company?->id,
                'name' => $this->company?->name,
                'logo' => $this->company?->logo_url,
                'is_featured' => $this->company?->is_featured,
            ],
            'dates' => [
                'created' => $this->created_at?->format('Y-m-d H:i:s'),
                'expires' => $this->expires_at?->format('Y-m-d'),
                'updated' => $this->updated_at?->format('Y-m-d H:i:s'),
                'days_until_expiry' => $this->expires_at ? $this->expires_at->diffInDays(now()) : null,
                'is_expired' => $this->expires_at ? $this->expires_at->isPast() : false,
            ],
            'statistics' => [
                'applications_count' => $this->when($request->user()->hasRole(['Admin', 'Employer']), $this->job_applications_count ?? 0),
                'views_count' => $this->when($request->user()->hasRole(['Admin', 'Employer']), $this->views_count ?? 0),
            ],
            'flags' => [
                'is_featured' => $this->is_featured,
                'is_suspended' => $this->is_suspended,
                'is_freelance' => $this->is_freelance,
                'is_remote' => $this->is_remote,
                'hide_salary' => $this->hide_salary,
            ],
            'skills' => $this->whenLoaded('jobsSkill', function () {
                return $this->jobsSkill->pluck('name');
            }),
            'tags' => $this->whenLoaded('jobsTag', function () {
                return $this->jobsTag->pluck('name');
            }),
            'actions' => [
                'can_edit' => $this->canUserEdit($request->user()),
                'can_delete' => $this->canUserDelete($request->user()),
                'can_feature' => $this->canUserFeature($request->user()),
                'can_suspend' => $this->canUserSuspend($request->user()),
            ],
            'urls' => [
                'public' => route('front.job.details', $this->slug),
                'edit' => $this->when($this->canUserEdit($request->user()), route('jobs.edit', $this->id)),
                'show' => route('jobs.show', $this->id),
            ],
            'meta' => [
                'resource_type' => 'job_index',
                'generated_at' => now()->toISOString(),
                'locale' => app()->getLocale(),
            ],
        ];
    }

    /**
     * Get the status label for display.
     */
    private function getStatusLabel(): string
    {
        return match ($this->status) {
            'open' => __('jobs.status.open'),
            'closed' => __('jobs.status.closed'),
            'drafted' => __('jobs.status.drafted'),
            'paused' => __('jobs.status.paused'),
            default => __('jobs.status.unknown'),
        };
    }

    /**
     * Check if user can edit this job.
     *
     * @param  mixed  $user
     */
    private function canUserEdit($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $this->company?->user_id === $user->id);
    }

    /**
     * Check if user can delete this job.
     *
     * @param  mixed  $user
     */
    private function canUserDelete($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $this->company?->user_id === $user->id && $this->job_applications_count === 0);
    }

    /**
     * Check if user can feature this job.
     *
     * @param  mixed  $user
     */
    private function canUserFeature($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin')
               || ($user->hasRole('Employer') && $this->company?->user_id === $user->id);
    }

    /**
     * Check if user can suspend this job.
     *
     * @param  mixed  $user
     */
    private function canUserSuspend($user): bool
    {
        if (! $user) {
            return false;
        }

        return $user->hasRole('Admin');
    }
}

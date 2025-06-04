<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class QueryOptimizer
{
    /**
     * Optimize job search queries
     */
    public static function optimizeJobSearch(Builder $query, array $filters = []): Builder
    {
        // Select only necessary columns
        $query->select([
            'id', 'title', 'description', 'company_id', 'location',
            'salary_min', 'salary_max', 'job_type', 'created_at'
        ]);

        // Eager load relationships
        $query->with(['company:id,name,logo', 'category:id,name']);

        // Apply filters efficiently
        if (!empty($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        if (!empty($filters['category_id'])) {
            $query->where('job_category_id', $filters['category_id']);
        }

        if (!empty($filters['job_type'])) {
            $query->where('job_type', $filters['job_type']);
        }

        if (!empty($filters['salary_min'])) {
            $query->where('salary_min', '>=', $filters['salary_min']);
        }

        // Use index for status
        $query->where('status', 'active');

        return $query;
    }

    /**
     * Optimize user dashboard queries
     */
    public static function optimizeUserDashboard($userId, $userType): array
    {
        $stats = [];

        if ($userType === 'candidate') {
            $stats = DB::select("
                SELECT 
                    COUNT(CASE WHEN status = 'applied' THEN 1 END) as applications_sent,
                    COUNT(CASE WHEN status = 'interview' THEN 1 END) as interviews_scheduled,
                    COUNT(CASE WHEN status = 'hired' THEN 1 END) as jobs_offered
                FROM job_applications 
                WHERE candidate_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            ", [$userId]);
        } else {
            $stats = DB::select("
                SELECT 
                    COUNT(j.id) as active_jobs,
                    COUNT(ja.id) as total_applications,
                    COUNT(CASE WHEN ja.status = 'pending' THEN 1 END) as pending_applications
                FROM jobs j
                LEFT JOIN job_applications ja ON j.id = ja.job_id
                WHERE j.user_id = ? AND j.status = 'active'
            ", [$userId]);
        }

        return $stats[0] ?? [];
    }

    /**
     * Optimize company search
     */
    public static function optimizeCompanySearch(Builder $query, array $filters = []): Builder
    {
        $query->select(['id', 'name', 'logo', 'location', 'industry_id', 'created_at'])
               ->where('is_active', true);

        if (!empty($filters['industry_id'])) {
            $query->where('industry_id', $filters['industry_id']);
        }

        if (!empty($filters['location'])) {
            $query->where('location', 'like', '%' . $filters['location'] . '%');
        }

        return $query;
    }
}
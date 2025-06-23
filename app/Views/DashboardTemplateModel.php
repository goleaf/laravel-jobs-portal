<?php

namespace App\Views;

use App\Models\User;

/**
 * Dashboard Template Model
 * 
 * Based on Habr article patterns for model-oriented templating
 * Handles user dashboard with statistics and activities
 */
class DashboardTemplateModel extends BaseTemplateModel
{
    public User $user;
    public string $userType;
    public array $statsData = [];
    public array $recentActivity = [];
    public array $notifications = [];
    public array $quickActions = [];

    /**
     * Get formatted stats
     */
    public function statistics(): array
    {
        $defaultStats = [
            'total_applications' => 0,
            'active_jobs' => 0,
            'profile_views' => 0,
            'interviews_scheduled' => 0,
        ];

        return array_merge($defaultStats, $this->statsData);
    }

    /**
     * Get greeting message
     */
    public function greeting(): string
    {
        $hour = (int) date('H');
        $timeGreeting = match(true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening'
        };

        return $timeGreeting . ', ' . $this->user->name;
    }

    /**
     * Get dashboard title based on user type
     */
    public function dashboardTitle(): string
    {
        return match($this->userType) {
            'employer' => 'Employer Dashboard',
            'admin' => 'Admin Dashboard',
            default => 'Candidate Dashboard'
        };
    }

    /**
     * Get unread notifications count
     */
    public function unreadNotificationsCount(): int
    {
        return count(array_filter($this->notifications, function ($notification) {
            return !($notification['read'] ?? false);
        }));
    }

    /**
     * Get urgent actions
     */
    public function urgentActions(): array
    {
        return array_filter($this->quickActions, function ($action) {
            return $action['urgent'] ?? false;
        });
    }
} 
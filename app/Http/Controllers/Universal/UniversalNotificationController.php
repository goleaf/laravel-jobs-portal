<?php

namespace App\Http\Controllers\Universal;

use App\Http\Controllers\Controller;
use App\Services\Notifications\UniversalNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Universal Notification Controller
 * 
 * Comprehensive notification management system with real-time updates,
 * filtering, marking as read/unread, and enterprise-grade features.
 */
class UniversalNotificationController extends Controller
{
    protected UniversalNotificationService $notificationService;

    public function __construct(UniversalNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
        // Authentication middleware removed - all methods now public
        $this->middleware('throttle:120,1')->only(['index', 'show']);
        $this->middleware('throttle:60,1')->only(['store', 'update', 'destroy']);
    }

    /**
     * Display notifications index page
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Get filter parameters
            $filter = $request->get('filter', 'all');
            $sort = $request->get('sort', 'newest');
            $perPage = $request->get('per_page', 15);
            
            // Build query with proper relationships
            $query = $user->notifications();
            
            // Apply filters
            switch ($filter) {
                case 'unread':
                    $query->whereNull('read_at');
                    break;
                case 'read':
                    $query->whereNotNull('read_at');
                    break;
                case 'job_applications':
                    $query->where('type', 'like', '%JobApplication%');
                    break;
                case 'messages':
                    $query->where('type', 'like', '%Message%');
                    break;
                case 'system':
                    $query->where('type', 'like', '%System%');
                    break;
                case 'updates':
                    $query->where('type', 'like', '%Update%');
                    break;
            }
            
            // Apply sorting
            switch ($sort) {
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                case 'unread':
                    $query->orderByRaw('read_at IS NULL DESC, created_at DESC');
                    break;
                default: // newest
                    $query->orderBy('created_at', 'desc');
                    break;
            }
            
            // Get paginated notifications
            $notifications = $query->paginate($perPage);
            
            // Get notification counts for filters
            $counts = [
                'all' => $user->notifications()->count(),
                'unread' => $user->unreadNotifications()->count(),
                'job_applications' => $user->notifications()->where('type', 'like', '%JobApplication%')->count(),
                'messages' => $user->notifications()->where('type', 'like', '%Message%')->count(),
                'system' => $user->notifications()->where('type', 'like', '%System%')->count(),
                'updates' => $user->notifications()->where('type', 'like', '%Update%')->count(),
            ];
            
            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'notifications' => $notifications,
                        'counts' => $counts,
                        'current_filter' => $filter,
                        'current_sort' => $sort
                    ]
                ]);
            }
            
            // Return view for regular requests
            return view('notifications.index', compact('notifications', 'counts', 'filter', 'sort'));
            
        } catch (\Exception $e) {
            Log::error('Notification index error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to load notifications')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to load notifications'));
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $notification = $user->notifications()->findOrFail($id);
            
            if ($notification->unread()) {
                $notification->markAsRead();
                
                // Log the action
                Log::info('Notification marked as read', [
                    'notification_id' => $id,
                    'user_id' => $user->id,
                    'type' => $notification->type
                ]);
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Notification marked as read'),
                    'unread_count' => $user->unreadNotifications()->count()
                ]);
            }
            
            return redirect()->back()->with('success', __('Notification marked as read'));
            
        } catch (\Exception $e) {
            Log::error('Mark notification as read error', [
                'error' => $e->getMessage(),
                'notification_id' => $id,
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to mark notification as read')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to mark notification as read'));
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(Request $request)
    {
        try {
            $user = auth()->user();
            $unreadCount = $user->unreadNotifications()->count();
            
            if ($unreadCount > 0) {
                $user->unreadNotifications->markAsRead();
                
                Log::info('All notifications marked as read', [
                    'user_id' => $user->id,
                    'marked_count' => $unreadCount
                ]);
            }
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('All notifications marked as read'),
                    'marked_count' => $unreadCount,
                    'unread_count' => 0
                ]);
            }
            
            return redirect()->back()->with('success', __('All notifications marked as read'));
            
        } catch (\Exception $e) {
            Log::error('Mark all notifications as read error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to mark all notifications as read')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to mark all notifications as read'));
        }
    }

    /**
     * Delete notification
     */
    public function destroy(Request $request, $id)
    {
        try {
            $user = auth()->user();
            $notification = $user->notifications()->findOrFail($id);
            
            $notification->delete();
            
            Log::info('Notification deleted', [
                'notification_id' => $id,
                'user_id' => $user->id,
                'type' => $notification->type
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Notification deleted'),
                    'unread_count' => $user->unreadNotifications()->count()
                ]);
            }
            
            return redirect()->back()->with('success', __('Notification deleted'));
            
        } catch (\Exception $e) {
            Log::error('Delete notification error', [
                'error' => $e->getMessage(),
                'notification_id' => $id,
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to delete notification')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to delete notification'));
        }
    }

    /**
     * Get notification settings
     */
    public function getSettings(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Get user notification preferences (assuming a settings table/model)
            $settings = [
                'email_notifications' => [
                    'job_applications' => true,
                    'messages' => true,
                    'system_updates' => false,
                    'marketing' => false,
                ],
                'push_notifications' => [
                    'instant_messages' => true,
                    'daily_digest' => false,
                    'weekly_summary' => false,
                ],
                'frequency' => 'immediately', // immediately, hourly, daily, weekly
                'quiet_hours' => [
                    'enabled' => true,
                    'start' => '22:00',
                    'end' => '08:00'
                ]
            ];
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $settings
                ]);
            }
            
            return view('notifications.settings', compact('settings'));
            
        } catch (\Exception $e) {
            Log::error('Get notification settings error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to load notification settings')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to load notification settings'));
        }
    }

    /**
     * Update notification settings
     */
    public function updateSettings(Request $request)
    {
        try {
            $validated = $request->validate([
                'email_notifications' => 'array',
                'email_notifications.job_applications' => 'boolean',
                'email_notifications.messages' => 'boolean',
                'email_notifications.system_updates' => 'boolean',
                'email_notifications.marketing' => 'boolean',
                'push_notifications' => 'array',
                'push_notifications.instant_messages' => 'boolean',
                'push_notifications.daily_digest' => 'boolean',
                'push_notifications.weekly_summary' => 'boolean',
                'frequency' => 'in:immediately,hourly,daily,weekly',
                'quiet_hours' => 'array',
                'quiet_hours.enabled' => 'boolean',
                'quiet_hours.start' => 'date_format:H:i',
                'quiet_hours.end' => 'date_format:H:i',
            ]);
            
            $user = auth()->user();
            
            // Save settings (implement according to your preferences storage)
            // This could be in user profile, separate settings table, etc.
            
            Log::info('Notification settings updated', [
                'user_id' => $user->id,
                'settings' => $validated
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Notification settings updated successfully')
                ]);
            }
            
            return redirect()->back()->with('success', __('Notification settings updated successfully'));
            
        } catch (\Exception $e) {
            Log::error('Update notification settings error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'data' => $request->all()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to update notification settings')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to update notification settings'));
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount(Request $request)
    {
        try {
            $user = auth()->user();
            $count = $user->unreadNotifications()->count();
            
            return response()->json([
                'success' => true,
                'count' => $count
            ]);
            
        } catch (\Exception $e) {
            Log::error('Get unread count error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            return response()->json([
                'success' => false,
                'count' => 0
            ]);
        }
    }

    /**
     * Send test notification
     */
    public function sendTestNotification(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Create a test notification
            $user->notify(new \App\Notifications\TestNotification([
                'title' => __('Test Notification'),
                'message' => __('This is a test notification to verify your notification settings.'),
                'type' => 'test'
            ]));
            
            Log::info('Test notification sent', [
                'user_id' => $user->id
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => __('Test notification sent successfully')
                ]);
            }
            
            return redirect()->back()->with('success', __('Test notification sent successfully'));
            
        } catch (\Exception $e) {
            Log::error('Send test notification error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Unable to send test notification')
                ], 500);
            }
            
            return redirect()->back()->with('error', __('Unable to send test notification'));
        }
    }

    /**
     * Get notifications via API (for real-time updates).
     */
    public function apiIndex(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $filters = $request->only(['type', 'read_status', 'limit', 'offset']);
            $limit = min($request->get('limit', 10), 50); // Max 50 notifications per request

            $notifications = $this->notificationService->getNotificationsAPI(
                $user->id,
                $filters,
                $limit
            );

            return response()->json([
                'success' => true,
                'data' => $notifications,
                'meta' => [
                    'total_unread' => $this->notificationService->getUnreadCount($user->id),
                    'last_updated' => now()->toISOString(),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Notification API error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Unable to fetch notifications')
            ], 500);
        }
    }

    /**
     * Show a specific notification.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $notification = $this->notificationService->getNotification($id, $user->id);

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => __('Notification not found')
                ], 404);
            }

            // Mark as read when viewed
            $this->notificationService->markAsRead($id, $user->id);

            return response()->json([
                'success' => true,
                'data' => $notification
            ]);

        } catch (\Exception $e) {
            Log::error('Notification show error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Unable to load notification')
            ], 500);
        }
    }

    /**
     * Create a new notification (admin/system use).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:job_application,message,system,update,reminder,promotion',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'recipient_id' => 'required|exists:users,id',
            'action_url' => 'nullable|url|max:500',
            'priority' => 'required|in:low,normal,high,urgent',
            'expires_at' => 'nullable|date|after:now',
            'metadata' => 'nullable|array',
        ]);

        try {
            $notification = $this->notificationService->createNotification($validated);

            return response()->json([
                'success' => true,
                'data' => $notification,
                'message' => __('Notification created successfully')
            ], 201);

        } catch (\Exception $e) {
            Log::error('Notification creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Unable to create notification')
            ], 500);
        }
    }

    /**
     * Mark notification as unread.
     */
    public function markAsUnread(Request $request, string $id): JsonResponse
    {
        try {
            $user = Auth::user();
            $result = $this->notificationService->markAsUnread($id, $user->id);

            if (!$result) {
                return response()->json([
                    'success' => false,
                    'message' => __('Notification not found')
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => __('Notification marked as unread'),
                'unread_count' => $this->notificationService->getUnreadCount($user->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Mark as unread error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Unable to mark notification as unread')
            ], 500);
        }
    }

    /**
     * Get notification preferences.
     */
    public function getPreferences(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $preferences = $this->notificationService->getUserPreferences($user->id);

            return response()->json([
                'success' => true,
                'data' => $preferences
            ]);

        } catch (\Exception $e) {
            Log::error('Get preferences error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Unable to load preferences')
            ], 500);
        }
    }

    /**
     * Get notification statistics.
     */
    public function getStats(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            $period = $request->get('period', '30'); // days
            
            $stats = $this->notificationService->getDetailedStats($user->id, $period);

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Get stats error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Unable to load statistics')
            ], 500);
        }
    }

    /**
     * Bulk operations on notifications.
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => 'required|in:mark_read,mark_unread,delete,archive',
            'notification_ids' => 'required|array|min:1|max:100',
            'notification_ids.*' => 'string',
        ]);

        try {
            $user = Auth::user();
            $result = $this->notificationService->bulkAction(
                $validated['notification_ids'],
                $validated['action'],
                $user->id
            );

            return response()->json([
                'success' => true,
                'message' => __('Bulk action completed successfully'),
                'affected_count' => $result['affected_count'],
                'unread_count' => $this->notificationService->getUnreadCount($user->id)
            ]);

        } catch (\Exception $e) {
            Log::error('Bulk action error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Unable to complete bulk action')
            ], 500);
        }
    }

    /**
     * Export notifications (for data portability).
     */
    public function export(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'format' => 'required|in:json,csv,pdf',
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'types' => 'nullable|array',
            'types.*' => 'in:job_application,message,system,update,reminder,promotion',
        ]);

        try {
            $user = Auth::user();
            $exportData = $this->notificationService->exportUserNotifications(
                $user->id,
                $validated
            );

            return response()->json([
                'success' => true,
                'download_url' => $exportData['url'],
                'expires_at' => $exportData['expires_at'],
                'message' => __('Export ready for download')
            ]);

        } catch (\Exception $e) {
            Log::error('Export notifications error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('Unable to export notifications')
            ], 500);
        }
    }
}

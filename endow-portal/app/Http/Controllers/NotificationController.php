<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get filter parameter (default: 3 days)
        $days = $request->input('days', 3);
        $filter = $request->input('filter', 'all'); // all, unread, read
        
        // Get notifications from the last X days
        $query = $user->notifications()
            ->where('created_at', '>=', Carbon::now()->subDays($days));
        
        // Apply filter
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }
        
        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get counts for filter badges
        $unreadCount = $user->unreadNotifications()->count();
        $readCount = $user->readNotifications()
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->count();
        $totalCount = $user->notifications()
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->count();
        
        return view('notifications.index', compact(
            'notifications',
            'unreadCount',
            'readCount',
            'totalCount',
            'days',
            'filter'
        ));
    }
    
    /**
     * Mark a single notification as read (without redirect)
     */
    public function markAsRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if (!$notification) {
            return redirect()->back()
                ->with('error', 'Notification not found');
        }
        
        // Mark as read
        $notification->markAsRead();
        
        return redirect()->back()
            ->with('success', 'Notification marked as read');
    }
    
    /**
     * Mark a single notification as read and redirect to its URL
     */
    public function markAsReadAndRedirect($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if (!$notification) {
            return redirect()->route('notifications.index')
                ->with('error', 'Notification not found');
        }
        
        // Mark as read
        $notification->markAsRead();
        
        // Get the URL from notification data
        $url = $notification->data['url'] ?? route('dashboard');
        
        return redirect($url);
    }
    
    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        
        return redirect()->back()
            ->with('success', 'All notifications marked as read');
    }
    
    /**
     * Delete a notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);
        
        if (!$notification) {
            return redirect()->back()
                ->with('error', 'Notification not found');
        }
        
        $notification->delete();
        
        return redirect()->back()
            ->with('success', 'Notification deleted');
    }
    
    /**
     * Clear all read notifications
     */
    public function clearRead()
    {
        $user = Auth::user();
        $user->readNotifications()->delete();
        
        return redirect()->back()
            ->with('success', 'All read notifications cleared');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of the user's notifications.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = $request->get('filter', 'all');

        $query = $user->notifications();

        if ($filter === 'unread') {
            $query = $user->unreadNotifications();
        } elseif ($filter === 'read') {
            $query = $user->readNotifications();
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();
        $unreadCount = $user->unreadNotifications()->count();

        return view('notifications.index', compact('notifications', 'filter', 'unreadCount'));
    }

    /**
     * Get the count of unread notifications for live polling/badge update.
     */
    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'unread_count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Get recent notifications formatted for navbar bell dropdown.
     */
    public function dropdown(Request $request): JsonResponse
    {
        $user = $request->user();
        $notifications = $user->notifications()->latest()->take(6)->get()->map(function ($n) {
            $data = $n->data;
            return [
                'id' => $n->id,
                'title' => $data['title'] ?? 'सूचना',
                'message' => $data['message'] ?? '',
                'link' => route('notifications.read', $n->id),
                'icon' => $data['icon'] ?? 'bell',
                'color' => $data['color'] ?? 'primary',
                'read' => !is_null($n->read_at),
                'time_ago' => $n->created_at->diffForHumans(),
            ];
        });

        return response()->json([
            'unread_count' => $user->unreadNotifications()->count(),
            'notifications' => $notifications,
        ]);
    }

    /**
     * Mark a specific notification as read and redirect to its target link.
     */
    public function markAsRead(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        $targetUrl = $notification->data['link'] ?? '#';
        if ($targetUrl && $targetUrl !== '#') {
            return redirect()->to($targetUrl);
        }

        return redirect()->back()->with('success', 'सूचना पढिएको भनी चिन्ह लगाइयो।');
    }

    /**
     * Mark all unread notifications as read.
     */
    public function markAllAsRead(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'सबै सूचनाहरू पढिएको भनी चिन्ह लगाइयो।');
    }

    /**
     * Delete a specific notification.
     */
    public function destroy(Request $request, string $id): RedirectResponse|JsonResponse
    {
        $notification = $request->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'unread_count' => $request->user()->unreadNotifications()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'सूचना हटाइयो।');
    }
}

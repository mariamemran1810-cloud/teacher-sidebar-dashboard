<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display user notifications.
     */
    public function index(Request $request): View
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->when($request->filled('read'), fn ($q) => $q->where('read', $request->read === 'true'))
            ->latest()
            ->paginate(20);

        $unreadCount = Notification::where('user_id', auth()->id())
            ->where('read', false)
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Mark a notification as read.
     */
    public function markRead(Request $request, Notification $notification): RedirectResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $notification->update(['read' => true, 'read_at' => now()]);

        return redirect()->route('notifications.index')
            ->with('success', 'Notification marked as read.');
    }
}

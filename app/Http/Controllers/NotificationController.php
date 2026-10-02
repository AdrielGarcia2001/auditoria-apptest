<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Notifications/Index', [
            'notifications' => auth()->user()->notifications()->paginate(20),
        ]);
    }

    public function unread(): JsonResponse
    {
        return response()->json([
            'notifications' => auth()->user()->unreadNotifications,
            'count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        return redirect()->back();
    }

    public function markAllAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return redirect()->back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(string $id): JsonResponse
    {
        $notification = Auth::user()->unreadNotifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(): RedirectResponse
    {
        Auth::user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }
}

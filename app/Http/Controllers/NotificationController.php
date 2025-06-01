<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\Props\NotificationProps;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index()
    {
        return Inertia::render('notifications/Index', NotificationProps::index(request()->user()));
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return redirect($notification->data['url']);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return redirect()->back();
    }
}

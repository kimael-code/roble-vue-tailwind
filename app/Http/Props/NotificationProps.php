<?php

namespace App\Http\Props;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Inertia\Inertia;

class NotificationProps
{
    public static function index(Authenticatable|User $user): array
    {
        $page = request()->input('page', 1);
        $perPage = request()->input('per_page', 10);
        $notifications = $user->notifications()->paginate($perPage, page: $page);

        return [
            'notifications' => Inertia::merge(fn() => $notifications->items()),
            'pagination' => $notifications->toArray(),
        ];
    }
}

<?php

use App\Http\Controllers\{
    BulkDeletionController,
    Security\PermissionController,
};
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::middleware(['auth', 'verified', 'password.set',])->group(function ()
{
    Route::get('dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::post('/bulk-deletion/{resource}', BulkDeletionController::class)->name('bulk-deletion');

    Route::resources([
        'permissions' => PermissionController::class,
    ]);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

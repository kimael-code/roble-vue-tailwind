<?php

use App\Http\Controllers\{
    BatchDeletionController,
    Organization\OrganizationController,
    Security\PermissionController,
    Security\RoleController,
    Security\UserController,
};
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Welcome'))->name('home');

Route::middleware(['auth', 'verified', 'password.set',])->group(function ()
{
    Route::get('dashboard', fn () => Inertia::render('Dashboard'))->name('dashboard');
    Route::post('/batch-deletion/{resource}', BatchDeletionController::class)->name('batch-deletion');

    Route::resources([
        'permissions' => PermissionController::class,
        'roles'       => RoleController::class,
        'users'       => UserController::class,
        'organizations' => OrganizationController::class,
    ], [
        'middleware' => [HandlePrecognitiveRequests::class],
    ]);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

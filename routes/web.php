<?php

use App\Http\Controllers\{
    BatchDeletionController,
    DashboardController,
    Debugging\ActivityLogController,
    Debugging\LogFileController,
    Organization\OrganizationalUnitController,
    Organization\OrganizationController,
    Security\PermissionController,
    Security\RoleController,
    Security\UserController,
};
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn() => Inertia::render('Welcome'))->name('home');

Route::middleware(['auth', 'verified', 'password.set',])->group(function ()
{
    Route::get('dashboard', DashboardController::class)->name('dashboard');
    Route::post('/batch-deletion/{resource}', BatchDeletionController::class)->name('batch-deletion');

    Route::controller(LogFileController::class)->group(function ()
    {
        Route::get('log-files', 'index')->middleware('can:read any system log')->name('log-files.index');
        Route::get('log-files/{file}', 'export')->middleware('can:export system logs')->name('log-files.export');
        Route::delete('log-files/{file}', 'delete')->middleware('can:delete system logs')->name('log-files.destroy');
    });

    Route::resource('activity-logs', ActivityLogController::class)->only(['index', 'show',]);

    Route::resources([
        'permissions' => PermissionController::class,
        'roles' => RoleController::class,
        'users' => UserController::class,
        'organizations' => OrganizationController::class,
        'organizational-units' => OrganizationalUnitController::class,
    ], [
        'middleware' => [HandlePrecognitiveRequests::class],
    ]);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';

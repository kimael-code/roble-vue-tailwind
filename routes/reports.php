<?php

use App\Http\Controllers\Reports\ActivityLogReportController;
use App\Http\Controllers\Reports\PermissionReportController;
use App\Http\Controllers\Reports\RoleReportController;
use App\Http\Controllers\Reports\UserReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'password.set'])->group(function ()
{
    Route::get('export/activity-logs/pdf', [ActivityLogReportController::class, 'indexToPdf'])
        ->name('export-activity-logs-pdf.index');

    Route::get('export/permissions/pdf', [PermissionReportController::class, 'indexToPdf'])
        ->name('export-permissions-pdf.index');

    Route::get('export/users/pdf', [UserReportController::class, 'indexToPdf'])
        ->name('export-users-pdf.index');

    Route::get('export/roles/pdf', [RoleReportController::class, 'indexToPdf'])
        ->name('export-roles-pdf.index');
});
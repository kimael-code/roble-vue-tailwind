<?php

use App\Http\Controllers\Exporters\ActivityLogExporterController;
use App\Http\Controllers\Exporters\PermissionExporterController;
use App\Http\Controllers\Exporters\RoleExporterController;
use App\Http\Controllers\Exporters\UserExporterController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'password.set'])->group(function ()
{
    Route::get('export/activity-logs/pdf', [ActivityLogExporterController::class, 'indexToPdf'])
        ->name('export-activity-logs-pdf.index');

    Route::get('export/permissions/pdf', [PermissionExporterController::class, 'indexToPdf'])
        ->name('export-permissions-pdf.index');

    Route::get('export/users/pdf', [UserExporterController::class, 'indexToPdf'])
        ->name('export-users-pdf.index');

    Route::get('export/roles/pdf', [RoleExporterController::class, 'indexToPdf'])
        ->name('export-roles-pdf.index');
});
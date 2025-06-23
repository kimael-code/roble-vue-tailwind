<?php

use App\Http\Controllers\Reports\ActivityLogReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'password.set'])->group(function ()
{
    Route::get('export/activity-logs/pdf', [ActivityLogReportController::class, 'indexToPdf'])
        ->name('export-activity-logs-pdf.index');
});
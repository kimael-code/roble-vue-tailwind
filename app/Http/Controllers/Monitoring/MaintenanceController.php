<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use App\Http\Requests\Monitoring\ToggleMaintenanceModeRequest;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    public function index()
    {
        return Inertia::render('monitoring/maintenance-mode/Index', [
            'status' => app()->isDownForMaintenance(),
        ]);
    }

    public function toggle(ToggleMaintenanceModeRequest $request)
    {
        if (app()->isDownForMaintenance())
        {
            Artisan::call('up');

            return back()->with('message', [
                'message' => __('Maintenance mode disabled'),
                'title' => __('PROCESSED!'),
                'type' => 'success',
            ]);
        }

        Artisan::call('down', [
            '--secret' => $request->safe()->secret ?: null,
        ]);

        return back()->with('message', [
            'message' => __('Maintenance mode enabled'),
            'title' => __('PROCESSED!'),
            'type' => 'success',
        ]);
    }
}

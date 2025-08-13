<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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

    public function toggle(Request $request)
    {
        if ($request->user()->cannot('manage maintenance mode'))
        {
            abort(403);
        }

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
            '--secret' => $request->input('secret') ?: null,
            '--refresh' => $request->input('refresh') ?: 25,
            '--retry' => $request->input('retry') ?: 120,
        ]);

        return back()->with('message', [
            'message' => __('Maintenance mode enabled'),
            'title' => __('PROCESSED!'),
            'type' => 'success',
        ]);
    }
}

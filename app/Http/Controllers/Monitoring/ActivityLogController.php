<?php

namespace App\Http\Controllers\Monitoring;

use App\Http\Controllers\Controller;
use App\Http\Props\Monitoring\ActivityLogProps;
use App\Models\Monitoring\ActivityLog;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', ActivityLog::class);

        return Inertia::render('monitoring/activity-logs/Index', ActivityLogProps::index());
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $activityLog)
    {
        Gate::authorize('view', $activityLog);

        return Inertia::render('monitoring/activity-logs/Show', ActivityLogProps::show($activityLog));
    }
}

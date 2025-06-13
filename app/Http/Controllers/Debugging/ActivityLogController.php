<?php

namespace App\Http\Controllers\Debugging;

use App\Http\Controllers\Controller;
use App\Http\Props\Debugging\ActivityLogProps;
use App\Models\Debugging\ActivityLog;
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

        return Inertia::render('debugging/activity-logs/Index', ActivityLogProps::index());
    }

    /**
     * Display the specified resource.
     */
    public function show(ActivityLog $activityLog)
    {
        Gate::authorize('view', $activityLog);

        return Inertia::render('debugging/activity-logs/Show', ActivityLogProps::show($activityLog));
    }
}

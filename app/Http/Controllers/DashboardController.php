<?php

namespace App\Http\Controllers;

use App\Http\Props\DashboardProps;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        return Inertia::render('Dashboard', DashboardProps::all($request->user()));
    }
}

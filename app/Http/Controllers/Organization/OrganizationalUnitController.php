<?php

namespace App\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use App\Http\Requests\Organization\StoreOrganizationalUnitRequest;
use App\Http\Requests\Organization\UpdateOrganizationalUnitRequest;
use App\Models\Organization\OrganizationalUnit;
use App\Support\Props\Organization\OrganizationalUnitProps;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class OrganizationalUnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', OrganizationalUnit::class);

        return Inertia::render('organization/organizational-units/Index', OrganizationalUnitProps::index());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', OrganizationalUnit::class);

        return Inertia::render('organization/organizational-units/Create', OrganizationalUnitProps::create());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrganizationalUnitRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(OrganizationalUnit $organizationalUnit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OrganizationalUnit $organizationalUnit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrganizationalUnitRequest $request, OrganizationalUnit $organizationalUnit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OrganizationalUnit $organizationalUnit)
    {
        //
    }
}

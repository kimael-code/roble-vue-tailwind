<?php

namespace App\Http\Controllers\Security;

use App\Actions\Security\CreateUser;
use App\Actions\Security\DisableUser;
use App\Actions\Security\EnableUser;
use App\Actions\Security\UpdateUser;
use App\Http\Controllers\Controller;
use App\Http\Props\Security\UserProps;
use App\Http\Requests\Security\StoreUserRequest;
use App\Http\Requests\Security\UpdateUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('viewAny', User::class);

        return Inertia::render('security/users/Index', UserProps::index());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create', User::class);

        return Inertia::render('security/users/Create', UserProps::create());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        event(new Registered(CreateUser::handle($request->validated())));

        return redirect(route('users.index'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return Inertia::render('security/users/Show', UserProps::show($user));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        return Inertia::render('security/users/Edit', UserProps::edit($user));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        UpdateUser::handle($user, $request->validated());

        return redirect(route('users.index'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);

        $user->delete();

        return redirect(route('users.index'));
    }

    public function forceDestroy(User $user)
    {
        Gate::authorize('forceDelete', $user);

        $user->forceDelete();

        return redirect(route('users.index'));
    }

    public function restore(User $user)
    {
        Gate::authorize('restore', $user);

        $user->restore();

        return redirect()->back();
    }

    public function enable(User $user)
    {
        Gate::authorize('enable', $user);

        EnableUser::handle($user);

        return redirect()->back();
    }

    public function disable(User $user)
    {
        Gate::authorize('disable', $user);

        DisableUser::handle($user);

        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Events\Auth\PasswordSet;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class SetPasswordController extends Controller
{
    public function index(): \Inertia\Response
    {
        abort_if(!request()->user(), 403, 'Forbidden');

        return Inertia::render('auth/SetPassword');
    }

    public function update(SetPasswordRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->password = Hash::make($request->password);
        $user->is_password_set = true;
        $user->save();

        PasswordSet::dispatch($user);
        sleep(1);

        Auth::guard('web')->logout();
        sleep(1);

        Session::flush();
        Auth::guard('web')->login($user);

        return redirect('/dashboard');
    }
}

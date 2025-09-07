<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class PasswordController extends Controller
{
    /**
     * Show the user's password settings page.
     */
    public function edit(): Response
    {
        return Inertia::render('settings/Password');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        activity(__('Settings/Password'))
            ->causedBy(auth()->user())
            ->performedOn($request->user())
            ->event('updated')
            ->withProperties([
                'request' => [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->header('user-agent'),
                    'user_agent_lang' => $request->header('accept-language'),
                    'referer' => $request->header('referer'),
                    'http_method' => $request->method(),
                    'request_url' => $request->fullUrl(),
                ],
                'causer' => User::with('person')->find(auth()->user()->id)->toArray(),
            ])
            ->log(__('updated their own password'));

        return back();
    }
}

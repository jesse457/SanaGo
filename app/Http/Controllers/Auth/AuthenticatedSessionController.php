<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Redirect based on role — super admin vs hospital roles
        if ($user->hasRole('super-admin')) {
            return redirect()->route('dashboard.super-admin');
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('dashboard.admin');
        }

        if ($user->hasRole('doctor')) {
            return redirect()->route('dashboard.doctor');
        }

        if ($user->hasRole('reception')) {
            return redirect()->route('dashboard.reception');
        }

        if ($user->hasRole('lab_tech')) {
            return redirect()->route('dashboard.lab');
        }

        if ($user->hasRole('pharma')) {
            return redirect()->route('dashboard.pharma');
        }

        return redirect()->route('dashboard.admin');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user()->role);
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'This account has been deactivated. Please contact administration.',
                ]);
            }

            // Sync user locale with session
            if ($user->locale) {
                session(['locale' => $user->locale]);
            }

            return $this->redirectBasedOnRole($user->role)
                ->with('success', __('messages.welcome_back') . ', ' . $user->name);
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our institutional records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'You have been logged out securely.');
    }

    public function switchLocale(Request $request, string $locale): RedirectResponse
    {
        if (in_array($locale, ['en', 'bn'])) {
            session(['locale' => $locale]);
            if (Auth::check()) {
                Auth::user()->update(['locale' => $locale]);
            }
        }

        return back();
    }

    private function redirectBasedOnRole(string $role): RedirectResponse
    {
        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            'teacher' => redirect()->route('teacher.dashboard'),
            'parent' => redirect()->route('parent.dashboard'),
            'student' => redirect()->route('student.dashboard'),
            'accountant' => redirect()->route('accountant.dashboard'),
            default => redirect()->route('home'),
        };
    }
}

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->ensureIsNotRateLimited();

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (\Illuminate\Support\Facades\Auth::attempt($credentials, $remember)) {
            // Block login for users not yet approved (non-admins)
            $logged = \Illuminate\Support\Facades\Auth::user();
            $status = strtolower((string) ($logged->status ?? ''));
            $role = $logged->role ?? null;
            $isAdmin = in_array($role, ['admin', 'hr'], true) || (bool) ($logged->is_kepala_kepegawaian ?? false);
            if (! $isAdmin && $status !== 'active') {
                \Illuminate\Support\Facades\Auth::logout();
                // Reset session + CSRF to avoid 419 on next request
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('login')->with('status', 'Login belum bisa: akun Anda belum disetujui admin.');
            }

            $request->session()->regenerate();
            RateLimiter::clear($request->throttleKey());
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $user = User::where('email', $request->string('email'))->first();

        if ($user) {
            $inputPassword = (string) $request->string('password');
            $storedHash = (string) $user->password;

            // Fallback: legacy sha256 hash string stored in users.password
            $sha256 = hash('sha256', $inputPassword);
            if (hash_equals(strtolower($storedHash), strtolower($sha256))) {
                // Rehash with Laravel's current hasher and update immediately
                $user->forceFill(['password' => Hash::make($inputPassword)])->save();

                // Block login for users not yet approved (non-admins)
                $status = strtolower((string) ($user->status ?? ''));
                $role = $user->role ?? null;
                $isAdmin = in_array($role, ['admin', 'hr'], true) || (bool) ($user->is_kepala_kepegawaian ?? false);
                if (! $isAdmin && $status !== 'active') {
                    // Reset CSRF/session before redirect to prevent 419
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->route('login')->with('status', 'Login belum bisa: akun Anda belum disetujui admin.');
                }

                \Illuminate\Support\Facades\Auth::login($user, $remember);
                $request->session()->regenerate();
                RateLimiter::clear($request->throttleKey());
                return redirect()->intended(route('dashboard', absolute: false));
            }
        }

        RateLimiter::hit($request->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.failed'),
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

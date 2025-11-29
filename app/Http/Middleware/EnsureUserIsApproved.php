<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $status = strtolower((string) ($user->status ?? ''));
        $role = $user->role ?? null;
        $isAdmin = in_array($role, ['admin', 'hr'], true) || (bool) ($user->is_kepala_kepegawaian ?? false);

        // Admins/kepala bypass approval; regular users require status=active
        if (!$isAdmin && $status !== 'active') {
            if ($request->expectsJson()) {
                abort(403, 'Akun Anda belum disetujui oleh admin.');
            }
            return redirect()->route('profile.edit')
                ->with('status', 'Akun Anda belum disetujui oleh admin.');
        }

        return $next($request);
    }
}

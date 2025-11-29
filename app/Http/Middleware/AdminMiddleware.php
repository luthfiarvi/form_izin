<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Normalisasi role ke lowercase agar nilai seperti "Admin" atau "HR" tetap diterima
        $role = $user ? strtolower(trim((string) ($user->role ?? ''))) : null;
        if ($user && (in_array($role, ['admin', 'hr'], true) || (bool) $user->is_kepala_kepegawaian === true)) {
            return $next($request);
        }

        abort(403);
    }
}

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

        if ($user && (($user->role ?? null) === 'admin' || (bool) $user->is_kepala_kepegawaian === true)) {
            return $next($request);
        }

        abort(403);
    }
}


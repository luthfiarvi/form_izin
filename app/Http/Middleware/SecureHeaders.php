<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Allow Vite dev server (localhost:5173) and CSP-friendly Alpine/Tailwind usage in dev.
        // These localhost origins are still safe in production because they only point to the visitor's own machine.
        $viteHttp = "http://127.0.0.1:5173 http://localhost:5173";
        $viteWs   = "ws://127.0.0.1:5173 ws://localhost:5173";

        $csp = "default-src 'self'; ".
               "base-uri 'self'; frame-ancestors 'none'; form-action 'self'; object-src 'none'; ".
               "img-src 'self' data: blob:; ".
               "style-src 'self' 'unsafe-inline' $viteHttp https://fonts.bunny.net; ".
               "script-src 'self' 'unsafe-inline' 'unsafe-eval' $viteHttp; ".
               "connect-src 'self' $viteHttp $viteWs; upgrade-insecure-requests";

        $headers = [
            'X-Frame-Options' => 'DENY',
            'X-Content-Type-Options' => 'nosniff',
            'Referrer-Policy' => 'no-referrer',
            'Permissions-Policy' => "camera=(), microphone=(), geolocation=()",
            'Content-Security-Policy' => $csp,
        ];

        if ($request->isSecure()) {
            $headers['Strict-Transport-Security'] = 'max-age=63072000; includeSubDomains; preload';
        }

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value, false);
        }

        return $response;
    }
}

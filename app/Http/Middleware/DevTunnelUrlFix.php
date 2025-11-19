<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class DevTunnelUrlFix
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $forwardedHost = $request->header('X-Forwarded-Host');

        // Prefer the forwarded host when present (Dev Tunnels / reverse proxies)
        $effectiveHost = $forwardedHost ?: $host;

        // When accessed via VS Code Dev Tunnels, ensure all generated URLs
        // (redirects, route(), asset(), etc.) use the tunnel host without an
        // extra :8000 so shared links work correctly.
        if (is_string($effectiveHost) && str_ends_with($effectiveHost, '.devtunnels.ms')) {
            URL::forceRootUrl('https://'.$effectiveHost);
            URL::forceScheme('https');
        }

        return $next($request);
    }
}


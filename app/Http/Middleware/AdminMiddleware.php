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

        // Jika sudah login tapi bukan admin, paksa logout lalu arahkan ke login
        // supaya bisa masuk dengan akun admin dan tetap menuju halaman yang dimaksud.
        if ($user) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->guest(route('login'))
                ->with('status', 'Silakan login sebagai admin untuk membuka halaman tersebut.');
        }

        // Belum login: arahkan ke login agar setelah autentikasi diarahkan ke halaman ini.
        return redirect()->guest(route('login'));
    }
}

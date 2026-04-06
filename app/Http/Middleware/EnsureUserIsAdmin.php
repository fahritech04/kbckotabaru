<?php

namespace App\Http\Middleware;

use App\Services\SessionAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionUser = $request->session()->get(SessionAuthService::SESSION_KEY_ADMIN);

        if ($sessionUser === null) {
            return redirect()->route('admin.login')->with('error', 'Silakan login sebagai admin.');
        }

        if (($sessionUser['role'] ?? 'user') !== 'admin') {
            return redirect()->route('admin.login')->with('error', 'Akses ditolak. Halaman ini hanya untuk admin.');
        }

        return $next($request);
    }
}

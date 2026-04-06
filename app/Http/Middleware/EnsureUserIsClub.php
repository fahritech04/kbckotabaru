<?php

namespace App\Http\Middleware;

use App\Services\SessionAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsClub
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $sessionUser = $request->session()->get(SessionAuthService::SESSION_KEY_CLUB);

        if ($sessionUser === null) {
            return redirect()->route('club.login')->with('error', 'Silakan login sebagai klub.');
        }

        if (($sessionUser['role'] ?? 'user') !== 'club') {
            return redirect()->route('club.login')->with('error', 'Akses ini hanya untuk akun klub.');
        }

        return $next($request);
    }
}

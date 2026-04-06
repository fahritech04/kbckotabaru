<?php

namespace App\Http\Middleware;

use App\Services\SessionAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isClubRoute = $request->routeIs('club.*');
        $sessionKey = $isClubRoute
            ? SessionAuthService::SESSION_KEY_CLUB
            : SessionAuthService::SESSION_KEY_ADMIN;

        if (! $request->session()->has($sessionKey)) {
            $loginRoute = $isClubRoute ? 'club.login' : 'admin.login';
            $message = $isClubRoute ? 'Silakan login klub untuk melanjutkan.' : 'Silakan login admin untuk melanjutkan.';

            return redirect()->route($loginRoute)->with('error', $message);
        }

        return $next($request);
    }
}

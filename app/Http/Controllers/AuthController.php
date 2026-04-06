<?php

namespace App\Http\Controllers;

use App\Services\KbcRepository;
use App\Services\SessionAuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly SessionAuthService $authService,
        private readonly KbcRepository $repository
    ) {}

    public function showAdminLogin()
    {
        if ($this->authService->checkAdmin() && $this->authService->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login', [
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = $this->authService->attemptAdmin($credentials['email'], $credentials['password']);

        if ($user === null) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau password tidak valid.');
        }

        return redirect()->route('admin.dashboard')->with('success', 'Login admin berhasil.');
    }

    public function logoutAdmin()
    {
        $this->authService->logoutAdmin();

        return redirect()->route('admin.login')->with('success', 'Anda berhasil logout admin.');
    }
}

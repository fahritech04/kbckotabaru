<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Services\KbcRepository;
use App\Services\SessionAuthService;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    public function __construct(
        private readonly SessionAuthService $authService,
        private readonly KbcRepository $repository
    ) {}

    public function showLogin()
    {
        if ($this->authService->checkClub() && $this->authService->isClub()) {
            return redirect()->route('club.dashboard');
        }

        return view('club.auth.login', [
            'googleConfigured' => $this->isGoogleConfigured(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function showRegister()
    {
        if ($this->authService->checkClub() && $this->authService->isClub()) {
            return redirect()->route('club.dashboard');
        }

        return view('club.auth.register', [
            'googleConfigured' => $this->isGoogleConfigured(),
            'firebaseReady' => $this->repository->isFirebaseReady(),
            'firebaseError' => $this->repository->firebaseError(),
        ]);
    }

    public function redirectToGoogle(): RedirectResponse
    {
        if (! $this->isGoogleConfigured()) {
            return redirect()->route('club.login')->with('error', 'Google Login belum dikonfigurasi di file .env.');
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable) {
            return redirect()->route('club.login')->with('error', 'Proses login Google gagal. Silakan coba lagi.');
        }

        $email = strtolower((string) $googleUser->getEmail());
        $googleId = (string) $googleUser->getId();

        if ($email === '' || $googleId === '') {
            return redirect()->route('club.login')->with('error', 'Akun Google tidak menyediakan email/ID yang valid.');
        }

        $name = trim((string) $googleUser->getName()) !== '' ? trim((string) $googleUser->getName()) : $email;
        $avatarUrl = $googleUser->getAvatar();

        $user = $this->repository->findUserByGoogleId($googleId);

        if ($user === null) {
            $existingByEmail = $this->repository->findUserByEmail($email);

            if ($existingByEmail !== null && ($existingByEmail['role'] ?? 'user') !== 'club') {
                return redirect()->route('club.login')->with('error', 'Email ini sudah dipakai akun non-klub.');
            }

            if ($existingByEmail !== null) {
                $user = $this->repository->updateUser($existingByEmail['id'], [
                    'name' => $name,
                    'google_id' => $googleId,
                    'avatar_url' => $avatarUrl,
                    'auth_provider' => 'google',
                    'email_verified_at' => now()->toIso8601String(),
                ]);
            } else {
                $user = $this->repository->createUser([
                    'name' => $name,
                    'email' => $email,
                    'role' => 'club',
                    'google_id' => $googleId,
                    'avatar_url' => $avatarUrl,
                    'auth_provider' => 'google',
                    'email_verified_at' => now()->toIso8601String(),
                ]);
            }
        } else {
            if (($user['role'] ?? 'user') !== 'club') {
                return redirect()->route('club.login')->with('error', 'Akun Google ini bukan role klub.');
            }

            $user = $this->repository->updateUser($user['id'], [
                'name' => $name,
                'email' => $email,
                'avatar_url' => $avatarUrl,
                'auth_provider' => 'google',
                'email_verified_at' => now()->toIso8601String(),
            ]);
        }

        if ($user === null) {
            return redirect()->route('club.login')->with('error', 'Gagal memproses akun klub dari Google.');
        }

        if (($user['role'] ?? 'user') !== 'club') {
            return redirect()->route('club.login')->with('error', 'Akun ini bukan role klub.');
        }

        $this->authService->loginClubUsingUserRecord($user);

        $clubId = $user['club_id'] ?? null;
        if ($clubId === null) {
            $club = $this->repository->findClubByOwnerUserId($user['id'], true);
            $clubId = $club['id'] ?? null;
        }

        if ($clubId === null) {
            return redirect()->route('club.onboarding')->with('success', 'Login Google berhasil. Lengkapi data klub Anda.');
        }

        return redirect()->route('club.dashboard')->with('success', 'Login klub berhasil.');
    }

    public function logout()
    {
        $this->authService->logoutClub();

        return redirect()->route('club.login')->with('success', 'Logout klub berhasil.');
    }

    private function isGoogleConfigured(): bool
    {
        return config('services.google.client_id') !== null
            && config('services.google.client_id') !== ''
            && config('services.google.client_secret') !== null
            && config('services.google.client_secret') !== ''
            && config('services.google.redirect') !== null
            && config('services.google.redirect') !== '';
    }
}

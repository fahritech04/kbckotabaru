<?php

namespace App\Services;

use Illuminate\Support\Facades\Hash;

class SessionAuthService
{
    public const SESSION_KEY_ADMIN = 'kbc_auth_admin';

    public const SESSION_KEY_CLUB = 'kbc_auth_club';

    public function __construct(private readonly KbcRepository $repository) {}

    public function attemptAdmin(string $email, string $password): ?array
    {
        $user = $this->repository->findUserByEmail($email);

        if (
            $user === null
            || ($user['role'] ?? 'user') !== 'admin'
            || ! Hash::check($password, (string) ($user['password'] ?? ''))
        ) {
            return null;
        }

        $this->storeSession(self::SESSION_KEY_ADMIN, $user);

        return $this->adminUser();
    }

    public function loginClubUsingUserRecord(array $user): array
    {
        if (($user['role'] ?? 'user') !== 'club') {
            return [];
        }

        $this->storeSession(self::SESSION_KEY_CLUB, $user);

        return $this->clubUser() ?? [];
    }

    public function logoutAdmin(): void
    {
        session()->forget(self::SESSION_KEY_ADMIN);
        session()->regenerateToken();
    }

    public function logoutClub(): void
    {
        session()->forget(self::SESSION_KEY_CLUB);
        session()->regenerateToken();
    }

    public function adminUser(): ?array
    {
        return session(self::SESSION_KEY_ADMIN);
    }

    public function clubUser(): ?array
    {
        return session(self::SESSION_KEY_CLUB);
    }

    public function checkAdmin(): bool
    {
        return $this->adminUser() !== null;
    }

    public function checkClub(): bool
    {
        return $this->clubUser() !== null;
    }

    public function isAdmin(): bool
    {
        return ($this->adminUser()['role'] ?? null) === 'admin';
    }

    public function isClub(): bool
    {
        return ($this->clubUser()['role'] ?? null) === 'club';
    }

    private function storeSession(string $key, array $user): void
    {
        session()->put($key, [
            'id' => $user['id'] ?? null,
            'name' => $user['name'] ?? 'Pengguna',
            'email' => $user['email'] ?? null,
            'role' => $user['role'] ?? 'user',
            'club_id' => $user['club_id'] ?? null,
        ]);

        session()->regenerate();
    }
}

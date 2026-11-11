<?php

namespace App\Repositories\v1\Auth;

use App\Models\User;

class AuthRepository implements AuthRepositoryInterface
{
    public function findUserByDni(string $dni): ?User
    {
        return User::where('dni', $dni)->first();
    }

    public function createUser(array $data): User
    {
        return User::create($data);
    }

    public function createToken(User $user, string $deviceName): string
    {
        return $user->createToken($deviceName)->plainTextToken;
    }

    public function revokeCurrentToken(User $user): void
    {
        $user->currentAccessToken()?->delete();
    }

    public function revokeAllTokens(User $user): void
    {
        $user->tokens()->delete();
    }
}

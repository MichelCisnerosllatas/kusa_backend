<?php

namespace App\Repositories\v1\Auth;

use App\Models\User;

interface AuthRepositoryInterface
{
    public function findUserByDni(string $dni): ?User;

    public function createUser(array $data): User;

    public function createToken(User $user, string $deviceName): string;

    public function revokeCurrentToken(User $user): void;

    public function revokeAllTokens(User $user): void;
}

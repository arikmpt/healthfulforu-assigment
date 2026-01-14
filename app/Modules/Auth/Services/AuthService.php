<?php

namespace Modules\Auth\Services;

use Modules\Auth\Actions\AuthAction;
use Modules\Auth\Models\User;
use Modules\Auth\Http\Resources\AuthResource;

class AuthService
{
    public function __construct(
        private readonly AuthAction $action,
    ) {}


    public function register(array $data): array
    {
        $user = $this->action->executeRegister($data);
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => new AuthResource($user),
            'token' => $token,
        ];
    }

    public function login(string $email, string $password): array
    {
        $result = $this->action->executeLogin($email, $password);

        return [
            'user' => new AuthResource($result['user']),
            'token' => $result['token'],
        ];
    }

    public function logout(User $user): void
    {
        $user->tokens()->delete();
    }

    public function logoutFromDevice(User $user, string $tokenId): void
    {
        $user->tokens()->where('id', $tokenId)->delete();
    }

    public function refreshToken(User $user): string
    {

        $user->tokens()->delete();
    
        return $user->createToken('auth_token')->plainTextToken;
    }
}
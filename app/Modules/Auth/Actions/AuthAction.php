<?php

namespace Modules\Auth\Actions;

use Modules\Auth\Models\User;
use Modules\User\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use DB;

class AuthAction
{
    public function executeLogin(string $email, string $password): array
    {
        $key = 'login:' . $email;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw new TooManyRequestsHttpException(
                $seconds,
                "Too many login attempts. Please try again in {$seconds} seconds."
            );
        }

        $user = User::where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            RateLimiter::hit($key, 60);
            throw new AuthenticationException('Invalid credentials');
        }

        if ($user->status !== 'active') {
            throw new HttpException(403, 'Your account has been deactivated');
        }

        RateLimiter::clear($key);

        $user->updateLastLogin();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user,
            'token' => $token,
        ];
    }

    public function executeRegister(array $data): User
    {
        DB::beginTransaction();

        $user = User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        if(!$user) {
            DB::rollback();
            throw new HttpException(500, 'Cannot save user');
        }

        UserProfile::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone' => $data['phone'] ?? null,
            'user_id' => $user->id
        ]);

        DB::commit();

        //need to handle with enum later
        $user->assignRole('user');

        return $user;
    }
}

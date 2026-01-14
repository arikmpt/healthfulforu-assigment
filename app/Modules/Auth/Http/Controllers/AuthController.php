<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user.
     *
     * @group Authentication
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->created([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 'Registration successful. Please verify your email.');
    }

    /**
     * Login user.
     *
     * @group Authentication
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->email,
            $request->password
        );

        return $this->success([
            'user' => $result['user'],
            'token' => $result['token'],
        ], 'Login successful');
    }

    /**
     * Logout user (revoke all tokens).
     *
     * @group Authentication
     * @authenticated
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * Logout from current device only.
     *
     * @group Authentication
     * @authenticated
     */
    public function logoutFromDevice(Request $request): JsonResponse
    {
        $currentToken = $request->user()->currentAccessToken();
        
        $this->authService->logoutFromDevice(
            $request->user(),
            $currentToken->id
        );

        return $this->success(null, 'Logged out from this device');
    }
    
    /**
     * Refresh authentication token.
     *
     * @group Authentication
     * @authenticated
     */
    public function refresh(Request $request): JsonResponse
    {
        $token = $this->authService->refreshToken($request->user());

        return $this->success([
            'token' => $token,
        ], 'Token refreshed successfully');
    }
}

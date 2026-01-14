<?php

namespace Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\User\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function __construct(
        private readonly UserService $service
    ) {}

    /**
     * Get logined profile
     * 
     * @group User
     */
    public function me(Request $request): JsonResponse
    {
        $profile = $this->service->getProfile($request->user());

        return $this->success($profile);
    }
}

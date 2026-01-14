<?php

namespace Modules\User\Services;

use Modules\Auth\Models\User;
use Modules\Auth\Http\Resources\AuthResource;

class UserService
{

    public function getProfile(User $user): AuthResource
    {
        return new AuthResource($user);
    }
}
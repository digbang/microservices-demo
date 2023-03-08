<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class DisableUserController extends Controller
{
    public function __invoke(User $user): JsonResource
    {
        $user->disable();

        return UserResource::make($user);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class EnableUserController extends Controller
{
    public function __invoke(User $user): JsonResource
    {
        $user->enable();

        return UserResource::make($user);
    }
}

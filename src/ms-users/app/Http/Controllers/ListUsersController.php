<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class ListUsersController extends Controller
{
    public function __invoke(): JsonResource
    {
        $users = User::query()->paginate();

        return UserResource::collection($users);
    }
}

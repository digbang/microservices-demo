<?php

namespace App\Http\Controllers;

use App\Actions\RegisterUser;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterUserController extends Controller
{
    public function __invoke(RegisterUserRequest $request, RegisterUser $registerUser): JsonResource
    {
        $aNewUser = $registerUser->handle($request->data());

        return UserResource::make($aNewUser);
    }
}

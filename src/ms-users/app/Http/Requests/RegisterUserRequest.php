<?php

namespace App\Http\Requests;

use App\DataTransferObjects\RegisterUserData;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', Password::default()],
        ];
    }

    public function data(): RegisterUserData
    {
        return new RegisterUserData(
            $this->name,
            $this->email,
            $this->password,
        );
    }
}

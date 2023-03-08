<?php

namespace App\Actions;

use App\DataTransferObjects\RegisterUserData;
use App\Exceptions\NonUniqueEmailException;
use App\Models\User;
use Illuminate\Validation\Rule;

class RegisterUser
{
    public function handle(RegisterUserData $data): User
    {
        $this->assertThatEmailIsUnique($data->email);

        return User::query()->create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => bcrypt($data->password),
            'enabled_at' => now(),
        ]);
    }

    private function assertThatEmailIsUnique(string $email): void
    {
        $validator = validator(
            data: [
                'email' => $email,
            ],
            rules: [
                'email' => Rule::unique('users'),
            ],
        );

        if ($validator->fails()) {
            throw new NonUniqueEmailException();
        }
    }
}

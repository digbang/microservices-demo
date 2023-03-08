<?php

namespace App\DataTransferObjects;

class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
        //
    }
}

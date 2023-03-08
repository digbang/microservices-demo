<?php

namespace App\RabbitMQ\Handlers;

use App\DataTransferObjects\ReceivedMessage;
use App\Models\User;

class PaymentRegisteredHandler
{
    public function handle(ReceivedMessage $message): void
    {
        $userId = $message->payload['user_id'];

        /** @var User $user */
        $user = User::query()->find($userId);

        if ($user) {
            $user->enable();
        }
    }
}

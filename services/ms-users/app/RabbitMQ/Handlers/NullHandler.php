<?php

namespace App\RabbitMQ\Handlers;

use App\DataTransferObjects\ReceivedMessage;


class NullHandler
{
    public function handle(ReceivedMessage $message): void
    {
        //
    }
}

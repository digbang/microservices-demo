<?php

namespace App\Actions\Contracts;

interface EventPublisher
{
    public function publish(string $eventType, array $data = [], string $queue = 'default'): void;
}

<?php

namespace App\Actions;

use App\Actions\Contracts\EventPublisher;
use Illuminate\Config\Repository as Config;
use Illuminate\Support\Str;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQPublisher implements EventPublisher
{
    private readonly AMQPStreamConnection $connection;

    public function __construct(private readonly Config $config)
    {
        $this->connection = new AMQPStreamConnection(
            host: $config->get('services.rabbitmq.host'),
            port: $config->get('services.rabbitmq.port'),
            user: $config->get('services.rabbitmq.user'),
            password: $config->get('services.rabbitmq.password'),
        );
    }

    public function publish(string $type, array $data = [], string $queue = 'default'): void
    {
        $channel = $this->connection->channel();

        $message = new AMQPMessage(body: json_encode([
            'id' => Str::uuid(),
            'type' => $type,
            'createdAt' => now()->format('Y-m-d H:i:s'),
            'source' => $this->config->get('app.name'),
            'payload' => $data,
        ]));

        $channel->basic_publish($message, exchange: '', routing_key: $queue);

        $channel->publish_batch();
    }
}

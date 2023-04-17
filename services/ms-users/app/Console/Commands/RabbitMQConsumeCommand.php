<?php

namespace App\Console\Commands;

use Ramsey\Uuid\Uuid;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use PhpAmqpLib\Message\AMQPMessage;
use Illuminate\Config\Repository as Config;
use App\DataTransferObjects\ReceivedMessage;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConsumeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume {handler=App\RabbitMQ\Handlers\NullHandler} {queue=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume a RabbitMQ queue';

    public function handle(Config $config): int
    {
        $handler = $this->argument('handler');

        if (! class_exists($handler)) {
            $this->error('Handler not found');

            return 0;
        }

        $connection = new AMQPStreamConnection(
            host: $config->get('services.rabbitmq.host'),
            port: $config->get('services.rabbitmq.port'),
            user: $config->get('services.rabbitmq.user'),
            password: $config->get('services.rabbitmq.password'),
        );

        $channel = $connection->channel();

        // $this->trap([SIGINT, SIGTERM], fn () => $channel->stopConsume());

        $queue = $this->argument('queue');

        $channel->queue_declare(
            queue: $queue,
            durable: true,
            auto_delete: false
        );

        try {
            $this->info('Waiting for incoming messages...');

            $channel->basic_consume(
                queue: $queue,
                no_ack: true,
                callback: function (AMQPMessage $message) use ($handler): void {
                    app($handler)->handle($this->buildMessage($message));
                },
            );

            $channel->consume();
        } catch (\Throwable $e) {
            $this->error("¡Whoops! {$e->getMessage()}");

            logger()->error($e);
        }

        return Command::SUCCESS;
    }

    private function buildMessage(AMQPMessage $incomingMessage): ReceivedMessage
    {
        $body = validator(data: json_decode($incomingMessage->getBody(), true) ?? [], rules: [
            'id' => 'required|uuid',
            'type' => 'required|string',
            'createdAt' => 'required|date_format:Y-m-d H:i:s',
            'payload' => 'nullable|array',
            'source' => 'nullable|string',
            'upstreamId' => 'nullable|uuid',
        ])->validated();

        return new ReceivedMessage(
            id: Uuid::fromString($body['id']),
            type: $body['type'],
            createdAt: CarbonImmutable::createFromFormat('Y-m-d H:i:s', $body['createdAt']),
            payload: $body['payload'] ?? [],
            source: $body['source'] ?? null,
            upstreamId: $body['upstreamId'] ?? null,
        );
    }
}

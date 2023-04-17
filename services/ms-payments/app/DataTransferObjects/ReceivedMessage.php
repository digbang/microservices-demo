<?php

namespace App\DataTransferObjects;

use Carbon\CarbonInterface;
use Illuminate\Contracts\Support\Arrayable;
use Ramsey\Uuid\UuidInterface;

class ReceivedMessage implements Arrayable
{
    public function __construct(
        public readonly UuidInterface $id,
        public readonly string $type,
        public readonly CarbonInterface $createdAt,
        public readonly array $payload,
        public readonly ?string $source,
        public readonly ?UuidInterface $upstreamId,
    ) {
        //
    }

    public function toArray()
    {
        return [
            'id' => $this->id->toString(),
            'type' => $this->type,
            'createdAt' => optional($this->createdAt)->format('Y-m-d H:i:s'),
            'payload' => $this->payload,
            'source' => $this->source,
            'upstreamId' => $this->upstreamId,
        ];
    }
}

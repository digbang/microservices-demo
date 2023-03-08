<?php

namespace App\DataTransferObjects;

use Carbon\CarbonImmutable;
use Ramsey\Uuid\UuidInterface;

class RegisterPaymentData
{
    public function __construct(
        public UuidInterface $userId,
        public CarbonImmutable $paidAt,
    ) {
        //
    }
}

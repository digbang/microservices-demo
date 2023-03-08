<?php

namespace App\Actions;

use App\DataTransferObjects\RegisterPaymentData;
use App\Events\PaymentRegistered;
use App\Models\Payment;

class RegisterPayment
{
    public function handle(RegisterPaymentData $data): Payment
    {
        $payment = Payment::query()->create([
            'user_id' => $data->userId,
            'paid_at' => $data->paidAt,
        ]);

        PaymentRegistered::dispatch($payment);

        return $payment;
    }
}

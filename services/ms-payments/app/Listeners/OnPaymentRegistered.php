<?php

namespace App\Listeners;

use App\Actions\Contracts\EventPublisher;
use App\Events\PaymentRegistered;

class OnPaymentRegistered
{
    public function __construct(
        private readonly EventPublisher $publisher,
    ) {
        //
    }
    
    public function handle(PaymentRegistered $event): void
    {
        // Http::baseUrl(config('services.users.base_url'))
        //     ->post(sprintf('%s/enable', $event->payment->user_id));

        $this->publisher->publish('paymentRegistered', [
            'user_id' => $event->payment->user_id,
        ]);
    }
}

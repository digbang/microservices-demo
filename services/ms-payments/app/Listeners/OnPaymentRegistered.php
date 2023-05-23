<?php

namespace App\Listeners;

use App\Events\PaymentRegistered;
use Illuminate\Support\Facades\Http;

class OnPaymentRegistered
{
    public function handle(PaymentRegistered $event): void
    {
        Http::baseUrl(config('services.users.base_url'))
            ->patch(sprintf('%s/enable', $event->payment->user_id));
    }
}

<?php

namespace App\Http\Controllers;

use App\Actions\RegisterPayment;
use App\Http\Requests\RegisterPaymentRequest;
use App\Http\Resources\PaymentResource;

class RegisterPaymentController extends Controller
{
    public function __invoke(RegisterPaymentRequest $request, RegisterPayment $registerPayment)
    {
        $aNewPayment = $registerPayment->handle($request->data());

        return PaymentResource::make($aNewPayment);
    }
}

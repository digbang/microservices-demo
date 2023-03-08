<?php

namespace App\Http\Requests;

use App\DataTransferObjects\RegisterPaymentData;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Http\FormRequest;
use Ramsey\Uuid\Uuid;

class RegisterPaymentRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'user_id' => 'required|uuid',
            'paid_at' => 'required|date',
        ];
    }

    public function data(): RegisterPaymentData
    {
        return new RegisterPaymentData(
            Uuid::fromString($this->user_id),
            CarbonImmutable::createFromFormat('Y-m-d', $this->paid_at),
        );
    }
}

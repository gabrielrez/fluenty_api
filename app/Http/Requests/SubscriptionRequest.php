<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'price_id' => ['required', 'string', 'in:' . config('services.stripe.price_monthly') . ',' . config('services.stripe.price_yearly')],
            'success_url' => ['required', 'url'],
            'cancel_url' => ['required', 'url'],
        ];
    }
}

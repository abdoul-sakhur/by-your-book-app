<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'delivery_address' => 'required|string|max:500',
            'delivery_phone'   => 'required|string|max:20',
            'payment_method'   => 'required|in:cash,mobile_money',
            'delivery_notes'   => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'delivery_address.required' => 'Veuillez indiquer votre adresse de livraison.',
            'delivery_phone.required'   => 'Veuillez indiquer un numéro de téléphone.',
            'payment_method.required'   => 'Veuillez sélectionner un mode de paiement.',
            'payment_method.in'         => 'Mode de paiement invalide.',
        ];
    }
}

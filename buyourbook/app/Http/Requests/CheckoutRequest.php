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
            'relay_point_id' => 'required|exists:relay_points,id',
            'delivery_notes' => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'relay_point_id.required' => 'Veuillez sélectionner un point de retrait.',
            'relay_point_id.exists' => 'Le point de retrait sélectionné est invalide.',
        ];
    }
}

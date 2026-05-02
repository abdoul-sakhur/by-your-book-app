<?php

namespace App\Http\Requests\Seller;

use Illuminate\Foundation\Http\FormRequest;

class SellerBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'official_book_id' => ['required', 'exists:official_books,id'],
            'condition' => ['required', 'string', 'in:new,good,acceptable'],
            'price' => ['required', 'integer', 'min:500', 'max:100000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'purchase_price' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

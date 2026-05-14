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
            'author'      => ['nullable', 'string', 'max:255'],
            'isbn'        => ['nullable', 'string', 'max:30'],
            'publisher'   => ['nullable', 'string', 'max:255'],
            'condition' => ['required', 'string', 'in:new,good,acceptable'],
            'price' => ['required', 'integer', 'min:500', 'max:100000'],
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
            'images'      => ['nullable', 'array', 'max:6'],
            'images.*'    => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'keep_images'   => ['nullable', 'array'],
            'keep_images.*' => ['nullable', 'string', 'max:500'],
            'purchase_price' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

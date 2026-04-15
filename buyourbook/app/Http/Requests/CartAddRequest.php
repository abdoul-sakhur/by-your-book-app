<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartAddRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'seller_book_id' => 'required|exists:seller_books,id',
            'quantity' => 'required|integer|min:1',
        ];
    }
}

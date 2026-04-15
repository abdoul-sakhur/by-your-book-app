<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string', 'max:255', 'unique:settings,key', 'regex:/^[a-z0-9_.]+$/'],
            'value' => ['nullable', 'string', 'max:2000'],
        ];
    }
}

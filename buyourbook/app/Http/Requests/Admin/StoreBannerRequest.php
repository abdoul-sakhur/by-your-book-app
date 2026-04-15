<?php

namespace App\Http\Requests\Admin;

use App\Enums\BannerPosition;
use App\Enums\BannerTarget;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'image' => ['required', 'image', 'max:2048'],
            'link_url' => ['nullable', 'url', 'max:500'],
            'position' => ['required', new Enum(BannerPosition::class)],
            'target_type' => ['required', new Enum(BannerTarget::class)],
            'school_id' => ['nullable', 'required_if:target_type,school', 'exists:schools,id'],
            'is_active' => ['boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
        ];
    }
}

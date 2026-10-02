<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChecklistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_completed' => ['sometimes', 'boolean'],
            'notes' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}

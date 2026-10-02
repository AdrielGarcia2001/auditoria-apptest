<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFindingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'severity' => ['sometimes', 'required', 'string', 'in:low,medium,high'],
            'status' => ['sometimes', 'required', 'string', 'in:open,in_progress,resolved,closed'],
            'recommendation' => ['nullable', 'string'],
        ];
    }
}

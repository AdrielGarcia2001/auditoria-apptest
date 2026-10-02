<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEvidenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240', 'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx'],
            'description' => ['nullable', 'string', 'max:500'],
            'evidencable_id' => ['required', 'integer'],
            'evidencable_type' => ['required', 'string', 'in:visit,finding'],
        ];
    }
}

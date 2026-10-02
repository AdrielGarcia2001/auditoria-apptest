<?php

namespace App\Http\Requests;

use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Visit::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'ticket_id' => ['required', 'exists:tickets,id'],
            'type' => ['required', 'string', 'in:preventive,corrective'],
            'scheduled_at' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}

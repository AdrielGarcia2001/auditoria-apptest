<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Ticket::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'priority' => ['required', 'string', 'in:low,medium,high,critical'],
            'asset_id' => ['required', 'exists:assets,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'crew_id' => ['nullable', 'exists:crews,id'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}

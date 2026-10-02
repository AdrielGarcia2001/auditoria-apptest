<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        return $ticket
            ? $this->user()?->can('update', $ticket) ?? false
            : false;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['sometimes', 'required', 'string'],
            'priority' => ['sometimes', 'required', 'string', 'in:low,medium,high,critical'],
            'status' => ['sometimes', 'required', 'string', 'in:pending,assigned,in_progress,completed,verified,reopened,cancelled'],
            'asset_id' => ['sometimes', 'required', 'exists:assets,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'crew_id' => ['nullable', 'exists:crews,id'],
            'due_date' => ['nullable', 'date'],
            'completed_at' => ['nullable', 'date'],
        ];
    }
}

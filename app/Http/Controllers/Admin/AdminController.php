<?php

namespace App\Http\Controllers\Admin;

use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Events\TicketStatusChanged;
use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->orderByRaw('CASE WHEN approved_at IS NULL THEN 0 ELSE 1 END')
            ->latest('created_at')
            ->take(50)
            ->get();

        return Inertia::render('Admin/Index', [
            'users' => $users,
            'stats' => [
                'total' => User::count(),
                'pending' => User::whereNull('approved_at')->count(),
                'byRole' => [
                    UserRole::Admin->value => User::where('role', UserRole::Admin->value)->count(),
                    UserRole::Supervisor->value => User::where('role', UserRole::Supervisor->value)->count(),
                    UserRole::Technician->value => User::where('role', UserRole::Technician->value)->count(),
                ],
            ],
            'tickets' => Ticket::with(['asset', 'creator', 'assignee'])
                ->latest()
                ->paginate(10)
                ->withQueryString(),
            'statuses' => TicketStatus::options(),
            'types' => TicketType::options(),
        ]);
    }

    public function approveUser(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'role' => ['sometimes', 'string', Rule::enum(UserRole::class)->only([
                UserRole::Supervisor,
                UserRole::Technician,
            ])],
        ]);

        $user->update([
            'approved_at' => now(),
            'is_active' => true,
            'role' => $request->input('role', $user->role->value),
        ]);

        return back()->with('success', "La cuenta de {$user->name} fue aprobada.");
    }

    public function updateTicket(Request $request, Ticket $ticket): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::enum(TicketType::class)],
            'status' => ['required', Rule::enum(TicketStatus::class)],
        ]);

        $oldStatus = $ticket->status->value;

        $ticket->update($validated);

        if ($ticket->status->value !== $oldStatus) {
            TicketStatusChanged::dispatch($ticket, $oldStatus, $ticket->status->value);
        }

        return back()->with('success', "El ticket {$ticket->ticket_number} fue actualizado.");
    }
}

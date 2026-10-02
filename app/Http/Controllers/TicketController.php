<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Events\TicketAssigned;
use App\Events\TicketStatusChanged;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Asset;
use App\Models\Crew;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    public function index(Request $request): Response
    {
        $tickets = Ticket::with(['asset', 'creator', 'assignee', 'crew'])
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->priority, fn ($query, $priority) => $query->where('priority', $priority))
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('ticket_number', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Tickets/Index', [
            'tickets' => $tickets,
            'filters' => $request->only(['status', 'priority', 'search']),
            'statuses' => TicketStatus::options(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Ticket::class);

        return Inertia::render('Tickets/Create', [
            'assets' => Asset::where('status', 'active')->get(),
            'crews' => Crew::where('is_active', true)->with('members')->get(),
            'technicians' => User::where('role', 'technician')->where('is_active', true)->get(),
        ]);
    }

    public function store(StoreTicketRequest $request): RedirectResponse
    {
        $ticket = Ticket::create([
            ...$request->validated(),
            'ticket_number' => 'TKT-'.strtoupper(uniqid()),
            'created_by' => $request->user()->id,
            'status' => TicketStatus::Pending,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket creado exitosamente.');
    }

    public function show(Ticket $ticket): Response
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'asset',
            'creator',
            'assignee',
            'crew',
            'visits.technician',
            'visits.checklists',
            'visits.findings',
        ]);

        return Inertia::render('Tickets/Show', [
            'ticket' => $ticket,
        ]);
    }

    public function edit(Ticket $ticket): Response
    {
        $this->authorize('update', $ticket);

        return Inertia::render('Tickets/Edit', [
            'ticket' => $ticket->load(['asset', 'crew', 'assignee']),
            'assets' => Asset::where('status', 'active')->get(),
            'crews' => Crew::where('is_active', true)->with('members')->get(),
            'technicians' => User::where('role', 'technician')->where('is_active', true)->get(),
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $oldStatus = $ticket->status->value;
        $oldAssignedTo = $ticket->assigned_to;

        $ticket->update($request->validated());

        if ($ticket->status->value !== $oldStatus) {
            TicketStatusChanged::dispatch($ticket, $oldStatus, $ticket->status->value);
        }

        if ($ticket->assigned_to && $ticket->assigned_to !== $oldAssignedTo) {
            TicketAssigned::dispatch($ticket, $ticket->assigned_to);
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Ticket actualizado exitosamente.');
    }

    public function destroy(Ticket $ticket): RedirectResponse
    {
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Ticket eliminado exitosamente.');
    }
}

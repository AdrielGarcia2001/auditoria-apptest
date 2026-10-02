<?php

namespace App\Http\Controllers;

use App\Enums\VisitStatus;
use App\Events\VisitStatusChanged;
use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VisitController extends Controller
{
    public function index(Request $request): Response
    {
        $visits = Visit::with(['ticket', 'technician'])
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->type, fn ($query, $type) => $query->where('type', $type))
            ->when($request->technician_id, fn ($query, $id) => $query->where('technician_id', $id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Visits/Index', [
            'visits' => $visits,
            'filters' => $request->only(['status', 'type', 'technician_id']),
            'statuses' => VisitStatus::options(),
            'technicians' => User::where('role', 'technician')->where('is_active', true)->get(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Visit::class);

        return Inertia::render('Visits/Create', [
            'tickets' => Ticket::whereIn('status', ['pending', 'assigned', 'in_progress'])->get(),
            'technicians' => User::where('role', 'technician')->where('is_active', true)->get(),
        ]);
    }

    public function store(StoreVisitRequest $request): RedirectResponse
    {
        $visit = Visit::create([
            ...$request->validated(),
            'technician_id' => $request->user()->id,
            'status' => VisitStatus::Scheduled,
        ]);

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Visita creada exitosamente.');
    }

    public function show(Visit $visit): Response
    {
        $this->authorize('view', $visit);

        $visit->load([
            'ticket',
            'technician',
            'checklists',
            'findings',
            'evidence',
        ]);

        return Inertia::render('Visits/Show', [
            'visit' => $visit,
        ]);
    }

    public function edit(Visit $visit): Response
    {
        $this->authorize('update', $visit);

        return Inertia::render('Visits/Edit', [
            'visit' => $visit->load(['ticket', 'technician']),
            'tickets' => Ticket::whereIn('status', ['pending', 'assigned', 'in_progress'])->get(),
            'technicians' => User::where('role', 'technician')->where('is_active', true)->get(),
        ]);
    }

    public function update(UpdateVisitRequest $request, Visit $visit): RedirectResponse
    {
        $oldStatus = $visit->status->value;

        $visit->update($request->validated());

        if ($visit->status->value !== $oldStatus) {
            VisitStatusChanged::dispatch($visit, $oldStatus, $visit->status->value);
        }

        return redirect()->route('visits.show', $visit)
            ->with('success', 'Visita actualizada exitosamente.');
    }

    public function destroy(Visit $visit): RedirectResponse
    {
        $this->authorize('delete', $visit);

        $visit->delete();

        return redirect()->route('visits.index')
            ->with('success', 'Visita eliminada exitosamente.');
    }
}

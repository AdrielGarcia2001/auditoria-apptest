<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\Visit;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Reports/Index');
    }

    public function tickets(Request $request): Response
    {
        $query = Ticket::with(['asset', 'assignee', 'creator', 'crew']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $tickets = $query->latest()->get();

        return Inertia::render('Reports/Tickets', [
            'tickets' => $tickets,
            'filters' => $request->all(),
            'statuses' => TicketStatus::options(),
        ]);
    }

    public function visits(Request $request): Response
    {
        $query = Visit::with(['ticket', 'technician']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $visits = $query->latest()->get();

        return Inertia::render('Reports/Visits', [
            'visits' => $visits,
            'filters' => $request->all(),
        ]);
    }

    public function exportTicketsPdf(Request $request)
    {
        $query = Ticket::with(['asset', 'assignee', 'creator']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        $tickets = $query->latest()->get();

        $pdf = Pdf::loadView('reports.tickets-pdf', [
            'tickets' => $tickets,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->download('tickets-'.now()->format('Y-m-d').'.pdf');
    }

    public function exportVisitsPdf(Request $request)
    {
        $query = Visit::with(['ticket', 'technician']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $visits = $query->latest()->get();

        $pdf = Pdf::loadView('reports.visits-pdf', [
            'visits' => $visits,
            'generatedAt' => now()->format('d/m/Y H:i'),
        ]);

        return $pdf->download('visits-'.now()->format('Y-m-d').'.pdf');
    }
}

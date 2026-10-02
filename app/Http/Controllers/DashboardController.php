<?php

namespace App\Http\Controllers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use App\Models\Visit;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $totalTickets = Ticket::count();
        $pendingTickets = Ticket::where('status', TicketStatus::Pending)->count();
        $inProgressTickets = Ticket::where('status', TicketStatus::InProgress)->count();
        $completedTickets = Ticket::where('status', TicketStatus::Completed)->count();
        $verifiedTickets = Ticket::where('status', TicketStatus::Verified)->count();

        $totalVisits = Visit::count();
        $scheduledVisits = Visit::where('status', 'scheduled')->count();
        $completedVisits = Visit::where('status', 'completed')->count();

        $ticketsByPriority = Ticket::selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        $ticketsByStatus = Ticket::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $recentTickets = Ticket::with(['asset', 'assignee'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingVisits = Visit::with(['ticket', 'technician'])
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->take(5)
            ->get();

        $overdueTickets = Ticket::where('due_date', '<', now())
            ->whereNotIn('status', [TicketStatus::Completed, TicketStatus::Verified, TicketStatus::Cancelled])
            ->count();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalTickets' => $totalTickets,
                'pendingTickets' => $pendingTickets,
                'inProgressTickets' => $inProgressTickets,
                'completedTickets' => $completedTickets,
                'verifiedTickets' => $verifiedTickets,
                'totalVisits' => $totalVisits,
                'scheduledVisits' => $scheduledVisits,
                'completedVisits' => $completedVisits,
                'overdueTickets' => $overdueTickets,
            ],
            'ticketsByPriority' => $ticketsByPriority,
            'ticketsByStatus' => $ticketsByStatus,
            'recentTickets' => $recentTickets,
            'upcomingVisits' => $upcomingVisits,
        ]);
    }
}

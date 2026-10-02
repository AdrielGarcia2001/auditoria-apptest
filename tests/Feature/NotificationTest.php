<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Enums\VisitStatus;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Visit;
use App\Notifications\TicketNotification;
use App\Notifications\VisitNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $supervisor;

    protected User $technician;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->supervisor = User::factory()->supervisor()->create();
        $this->technician = User::factory()->technician()->create();
    }

    public function test_notification_sent_when_ticket_status_changes(): void
    {
        Notification::fake();

        $ticket = Ticket::factory()->create([
            'created_by' => $this->supervisor->id,
            'assigned_to' => $this->technician->id,
        ]);

        $this->actingAs($this->admin)
            ->put(route('tickets.update', $ticket->id), [
                'status' => TicketStatus::InProgress->value,
            ]);

        Notification::assertSentTo($this->technician, TicketNotification::class);
    }

    public function test_notification_sent_when_visit_completed(): void
    {
        Notification::fake();

        $visit = Visit::factory()->create([
            'technician_id' => $this->technician->id,
        ]);

        $this->actingAs($this->admin)
            ->put(route('visits.update', $visit->id), [
                'status' => VisitStatus::Completed->value,
                'completed_at' => now()->format('Y-m-d H:i:s'),
            ]);

        Notification::assertSentTo($this->technician, VisitNotification::class);
    }

    public function test_user_can_view_notifications(): void
    {
        $this->technician->notify(new TicketNotification(
            Ticket::factory()->create(),
            'Test notification'
        ));

        $response = $this->actingAs($this->technician)
            ->get(route('notifications.index'));

        $response->assertOk();
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $ticket = Ticket::factory()->create();
        $this->technician->notify(new TicketNotification($ticket, 'Test notification'));

        $notification = $this->technician->notifications()->first();

        $response = $this->actingAs($this->technician)
            ->post(route('notifications.read', $notification->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'read_at' => now()->toDateTimeString(),
        ]);
    }
}

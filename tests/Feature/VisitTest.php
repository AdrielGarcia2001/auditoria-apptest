<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Enums\VisitStatus;
use App\Enums\VisitType;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $supervisor;

    protected User $technician;

    protected Ticket $ticket;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->supervisor = User::factory()->supervisor()->create();
        $this->technician = User::factory()->technician()->create();
        $this->ticket = Ticket::factory()->create([
            'status' => TicketStatus::Assigned,
        ]);
    }

    public function test_admin_can_create_visit(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('visits.store'), [
                'ticket_id' => $this->ticket->id,
                'type' => VisitType::Corrective->value,
                'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('visits', [
            'ticket_id' => $this->ticket->id,
            'status' => VisitStatus::Scheduled->value,
        ]);
    }

    public function test_technician_can_view_own_visit(): void
    {
        $visit = Visit::factory()->create([
            'technician_id' => $this->technician->id,
        ]);

        $response = $this->actingAs($this->technician)
            ->get(route('visits.show', $visit->id));

        $response->assertOk();
    }

    public function test_technician_cannot_view_other_visit(): void
    {
        $otherTechnician = User::factory()->technician()->create();
        $visit = Visit::factory()->create([
            'technician_id' => $otherTechnician->id,
        ]);

        $response = $this->actingAs($this->technician)
            ->get(route('visits.show', $visit->id));

        $response->assertForbidden();
    }

    public function test_admin_can_update_visit_status(): void
    {
        $visit = Visit::factory()->create([
            'technician_id' => $this->technician->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('visits.update', $visit->id), [
                'status' => VisitStatus::Completed->value,
                'completed_at' => now()->format('Y-m-d H:i:s'),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('visits', [
            'id' => $visit->id,
            'status' => VisitStatus::Completed->value,
        ]);
    }

    public function test_visit_requires_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('visits.store'), [
                'ticket_id' => '',
                'type' => '',
                'scheduled_at' => '',
            ]);

        $response->assertSessionHasErrors(['ticket_id', 'type', 'scheduled_at']);
    }
}

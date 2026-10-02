<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Models\Asset;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $supervisor;

    protected User $technician;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->supervisor = User::factory()->supervisor()->create();
        $this->technician = User::factory()->technician()->create();
        $this->asset = Asset::factory()->create();
    }

    public function test_admin_can_create_ticket(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('tickets.store'), [
                'title' => 'Test Ticket',
                'description' => 'Test Description',
                'priority' => 'high',
                'asset_id' => $this->asset->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'title' => 'Test Ticket',
            'status' => TicketStatus::Pending->value,
        ]);
    }

    public function test_supervisor_can_create_ticket(): void
    {
        $response = $this->actingAs($this->supervisor)
            ->post(route('tickets.store'), [
                'title' => 'Supervisor Ticket',
                'description' => 'Test Description',
                'priority' => 'medium',
                'asset_id' => $this->asset->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'title' => 'Supervisor Ticket',
        ]);
    }

    public function test_technician_cannot_create_ticket(): void
    {
        $response = $this->actingAs($this->technician)
            ->post(route('tickets.store'), [
                'title' => 'Technician Ticket',
                'description' => 'Test Description',
                'priority' => 'low',
                'asset_id' => $this->asset->id,
            ]);

        $response->assertForbidden();
    }

    public function test_admin_can_update_ticket_status(): void
    {
        $ticket = Ticket::factory()->create([
            'created_by' => $this->supervisor->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('tickets.update', $ticket->id), [
                'status' => TicketStatus::InProgress->value,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'status' => TicketStatus::InProgress->value,
        ]);
    }

    public function test_admin_can_delete_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete(route('tickets.destroy', $ticket->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('tickets', [
            'id' => $ticket->id,
        ]);
    }

    public function test_technician_cannot_delete_ticket(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($this->technician)
            ->delete(route('tickets.destroy', $ticket->id));

        $response->assertForbidden();
    }

    public function test_ticket_requires_valid_data(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('tickets.store'), [
                'title' => '',
                'description' => '',
                'asset_id' => '',
            ]);

        $response->assertSessionHasErrors(['title', 'description', 'asset_id']);
    }
}

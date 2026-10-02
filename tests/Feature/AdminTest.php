<?php

namespace Tests\Feature;

use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Enums\UserRole;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_admin_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.index'));

        $response->assertOk();
    }

    public function test_guest_can_not_view_admin_page(): void
    {
        $response = $this->get(route('admin.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_non_admin_can_not_view_admin_page(): void
    {
        $technician = User::factory()->technician()->create();

        $response = $this->actingAs($technician)->get(route('admin.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_approve_registered_user(): void
    {
        $user = User::factory()->pending()->create();

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.approve', $user), ['role' => 'supervisor']);

        $response->assertRedirect();

        $user->refresh();

        $this->assertNotNull($user->approved_at);
        $this->assertTrue($user->is_active);
        $this->assertSame(UserRole::Supervisor, $user->role);
    }

    public function test_approval_role_must_be_supervisor_or_technician(): void
    {
        $user = User::factory()->pending()->create();

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.users.approve', $user), ['role' => 'admin']);

        $response->assertSessionHasErrors('role');
        $this->assertNull($user->fresh()->approved_at);
    }

    public function test_admin_can_update_ticket_type_and_status(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.tickets.update', $ticket), [
                'type' => TicketType::Preventive->value,
                'status' => TicketStatus::InProgress->value,
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'type' => TicketType::Preventive->value,
            'status' => TicketStatus::InProgress->value,
        ]);
    }

    public function test_ticket_update_requires_valid_type_and_status(): void
    {
        $ticket = Ticket::factory()->create();

        $response = $this->actingAs($this->admin)
            ->patch(route('admin.tickets.update', $ticket), [
                'type' => 'invalid-type',
                'status' => 'invalid-status',
            ]);

        $response->assertSessionHasErrors(['type', 'status']);
    }
}

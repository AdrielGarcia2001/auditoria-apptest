<?php

namespace Database\Factories;

use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\Models\Asset;
use App\Models\Crew;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Ticket>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'ticket_number' => 'TKT-'.strtoupper(fake()->unique()->bothify('####-####')),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'status' => TicketStatus::Pending,
            'priority' => TicketPriority::Medium,
            'type' => TicketType::Corrective,
            'asset_id' => Asset::factory(),
            'created_by' => User::factory()->supervisor(),
            'assigned_to' => null,
            'crew_id' => null,
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'completed_at' => null,
        ];
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Assigned,
            'assigned_to' => User::factory()->technician(),
            'crew_id' => Crew::factory(),
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::InProgress,
            'assigned_to' => User::factory()->technician(),
            'crew_id' => Crew::factory(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Completed,
            'assigned_to' => User::factory()->technician(),
            'crew_id' => Crew::factory(),
            'completed_at' => now(),
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Verified,
            'assigned_to' => User::factory()->technician(),
            'crew_id' => Crew::factory(),
            'completed_at' => now()->subDays(2),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TicketStatus::Cancelled,
        ]);
    }

    public function highPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::High,
        ]);
    }

    public function criticalPriority(): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => TicketPriority::Critical,
        ]);
    }
}

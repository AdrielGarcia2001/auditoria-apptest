<?php

namespace Database\Factories;

use App\Enums\VisitStatus;
use App\Enums\VisitType;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visit>
 */
class VisitFactory extends Factory
{
    protected $model = Visit::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'technician_id' => User::factory()->technician(),
            'type' => VisitType::Corrective,
            'status' => VisitStatus::Scheduled,
            'scheduled_at' => fake()->dateTimeBetween('now', '+7 days'),
            'started_at' => null,
            'completed_at' => null,
            'start_latitude' => null,
            'start_longitude' => null,
            'end_latitude' => null,
            'end_longitude' => null,
            'notes' => null,
        ];
    }

    public function preventive(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => VisitType::Preventive,
        ]);
    }

    public function inRoute(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VisitStatus::InRoute,
            'started_at' => now(),
            'start_latitude' => fake()->latitude(),
            'start_longitude' => fake()->longitude(),
        ]);
    }

    public function onSite(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VisitStatus::OnSite,
            'started_at' => now()->subHour(),
            'start_latitude' => fake()->latitude(),
            'start_longitude' => fake()->longitude(),
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VisitStatus::Completed,
            'started_at' => now()->subHours(2),
            'completed_at' => now(),
            'start_latitude' => fake()->latitude(),
            'start_longitude' => fake()->longitude(),
            'end_latitude' => fake()->latitude(),
            'end_longitude' => fake()->longitude(),
            'notes' => fake()->sentence(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => VisitStatus::Cancelled,
        ]);
    }
}

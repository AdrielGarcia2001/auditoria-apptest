<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'code' => 'AST-'.strtoupper(fake()->unique()->bothify('####')),
            'model' => fake()->word(),
            'serial_number' => strtoupper(fake()->bothify('SN-####-####')),
            'location' => fake()->address(),
            'description' => fake()->sentence(),
            'installation_date' => fake()->dateTimeBetween('-5 years', 'now'),
            'status' => 'active',
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'maintenance',
        ]);
    }
}

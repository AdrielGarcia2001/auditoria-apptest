<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Asset;
use App\Models\Crew;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Visit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@auditoria.com',
            'role' => UserRole::Admin,
        ]);

        $supervisors = User::factory()->supervisor()->count(3)->create();

        $technicians = User::factory()->technician()->count(10)->create();

        $crews = Crew::factory()->count(4)->create([
            'supervisor_id' => $supervisors->random()->id,
        ]);

        foreach ($crews as $crew) {
            $crew->members()->attach(
                $technicians->random(rand(2, 4))->pluck('id')
            );
        }

        Asset::factory()->count(20)->create();

        Ticket::factory()
            ->count(30)
            ->sequence(
                fn ($sequence) => ['created_by' => $supervisors->random()->id]
            )
            ->create();

        Visit::factory()
            ->count(20)
            ->sequence(
                fn ($sequence) => ['technician_id' => $technicians->random()->id]
            )
            ->create();
    }
}

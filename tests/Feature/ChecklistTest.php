<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChecklistTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $technician;

    protected Visit $visit;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
        $this->technician = User::factory()->technician()->create();
        $this->visit = Visit::factory()->create([
            'technician_id' => $this->technician->id,
        ]);
    }

    public function test_admin_can_add_checklist_item(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('visits.checklists.store', $this->visit->id), [
                'item_name' => 'Verificar conexiones',
                'description' => 'Revisar todas las conexiones eléctricas',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('checklists', [
            'visit_id' => $this->visit->id,
            'item_name' => 'Verificar conexiones',
        ]);
    }

    public function test_technician_can_add_checklist_item(): void
    {
        $response = $this->actingAs($this->technician)
            ->post(route('visits.checklists.store', $this->visit->id), [
                'item_name' => 'Limpiar equipo',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('checklists', [
            'visit_id' => $this->visit->id,
            'item_name' => 'Limpiar equipo',
        ]);
    }

    public function test_admin_can_update_checklist_item(): void
    {
        $checklist = $this->visit->checklists()->create([
            'item_name' => 'Test Item',
            'is_completed' => false,
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('visits.checklists.update', [$this->visit->id, $checklist->id]), [
                'is_completed' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('checklists', [
            'id' => $checklist->id,
            'is_completed' => true,
        ]);
    }

    public function test_admin_can_delete_checklist_item(): void
    {
        $checklist = $this->visit->checklists()->create([
            'item_name' => 'Test Item',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('visits.checklists.destroy', [$this->visit->id, $checklist->id]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('checklists', [
            'id' => $checklist->id,
        ]);
    }
}

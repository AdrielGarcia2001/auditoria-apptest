<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChecklistRequest;
use App\Http\Requests\UpdateChecklistRequest;
use App\Models\Checklist;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;

class ChecklistController extends Controller
{
    public function store(StoreChecklistRequest $request, Visit $visit): RedirectResponse
    {
        $visit->checklists()->create($request->validated());

        return redirect()->back()
            ->with('success', 'Ítem agregado al checklist.');
    }

    public function update(UpdateChecklistRequest $request, Visit $visit, Checklist $checklist): RedirectResponse
    {
        $checklist->update($request->validated());

        return redirect()->back()
            ->with('success', 'Ítem actualizado.');
    }

    public function destroy(Visit $visit, Checklist $checklist): RedirectResponse
    {
        $checklist->delete();

        return redirect()->back()
            ->with('success', 'Ítem eliminado.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFindingRequest;
use App\Http\Requests\UpdateFindingRequest;
use App\Models\Finding;
use App\Models\Visit;
use Illuminate\Http\RedirectResponse;

class FindingController extends Controller
{
    public function store(StoreFindingRequest $request, Visit $visit): RedirectResponse
    {
        $visit->findings()->create($request->validated());

        return redirect()->back()
            ->with('success', 'Hallazgo registrado.');
    }

    public function update(UpdateFindingRequest $request, Visit $visit, Finding $finding): RedirectResponse
    {
        $finding->update($request->validated());

        return redirect()->back()
            ->with('success', 'Hallazgo actualizado.');
    }

    public function destroy(Visit $visit, Finding $finding): RedirectResponse
    {
        $finding->delete();

        return redirect()->back()
            ->with('success', 'Hallazgo eliminado.');
    }
}

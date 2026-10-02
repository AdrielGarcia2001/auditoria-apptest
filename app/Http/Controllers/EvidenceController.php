<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEvidenceRequest;
use App\Models\Evidence;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class EvidenceController extends Controller
{
    public function store(StoreEvidenceRequest $request): RedirectResponse
    {
        $path = $request->file('file')->store('evidence', 'public');

        Evidence::create([
            'file_path' => $path,
            'file_name' => $request->file('file')->hashName(),
            'file_type' => $request->file('file')->getMimeType(),
            'file_size' => $request->file('file')->getSize(),
            'description' => $request->description,
            'uploaded_by' => $request->user()->id,
            'evidencable_id' => $request->evidencable_id,
            'evidencable_type' => $request->evidencable_type,
        ]);

        return redirect()->back()
            ->with('success', 'Evidencia subida exitosamente.');
    }

    public function destroy(Evidence $evidence): RedirectResponse
    {
        Storage::disk('public')->delete($evidence->file_path);
        $evidence->delete();

        return redirect()->back()
            ->with('success', 'Evidencia eliminada.');
    }
}

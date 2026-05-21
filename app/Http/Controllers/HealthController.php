<?php

namespace App\Http\Controllers;

use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Vaccination;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HealthController extends Controller
{
    public function index(): View
    {
        $pets = Pet::where('user_id', auth()->id())->with(['medicalRecords', 'vaccinations'])->get();
        return view('health.index', compact('pets'));
    }

    public function petHealth(Pet $pet): View
    {
        $this->authorize('view', $pet);
        $pet->load(['medicalRecords' => fn($q) => $q->orderBy('record_date', 'desc'), 'vaccinations' => fn($q) => $q->orderBy('administered_date', 'desc')]);

        $weightHistory = $pet->medicalRecords()
            ->whereNotNull('weight')
            ->orderBy('record_date')
            ->get(['record_date', 'weight']);

        return view('health.pet', compact('pet', 'weightHistory'));
    }

    public function storeMedicalRecord(Request $request, Pet $pet): RedirectResponse
    {
        $this->authorize('update', $pet);

        $validated = $request->validate([
            'type' => 'required|in:checkup,illness,surgery,prescription,weight_log,symptom,other',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'record_date' => 'required|date',
            'weight' => 'nullable|numeric|min:0|max:999',
            'vet_name' => 'nullable|string|max:100',
            'diagnosis' => 'nullable|string',
            'treatment' => 'nullable|string',
            'medications' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            $validated['attachment_path'] = $request->file('attachment')->store('medical/records', 'public');
        }

        $pet->medicalRecords()->create($validated);

        // Update pet weight if provided
        if (!empty($validated['weight'])) {
            $pet->update(['weight' => $validated['weight']]);
        }

        return back()->with('success', 'Medical record added successfully!');
    }

    public function storeVaccination(Request $request, Pet $pet): RedirectResponse
    {
        $this->authorize('update', $pet);

        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:100',
            'administered_date' => 'required|date',
            'next_due_date' => 'nullable|date|after:administered_date',
            'administered_by' => 'nullable|string|max:100',
            'batch_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('certificate')) {
            $validated['certificate_path'] = $request->file('certificate')->store('medical/vaccinations', 'public');
        }

        $pet->vaccinations()->create($validated);

        return back()->with('success', 'Vaccination record added successfully!');
    }

    public function destroyMedicalRecord(MedicalRecord $record): RedirectResponse
    {
        $this->authorize('update', $record->pet);
        if ($record->attachment_path) {
            Storage::disk('public')->delete($record->attachment_path);
        }
        $record->delete();
        return back()->with('success', 'Record deleted.');
    }

    public function destroyVaccination(Vaccination $vaccination): RedirectResponse
    {
        $this->authorize('update', $vaccination->pet);
        $vaccination->delete();
        return back()->with('success', 'Vaccination record deleted.');
    }
}

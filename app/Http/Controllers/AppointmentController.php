<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Appointment::where('user_id', auth()->id())->with('pet');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        $appointments = $query->orderBy('appointment_datetime', 'desc')->paginate(15);
        $pets = Pet::where('user_id', auth()->id())->get();

        return view('appointments.index', compact('appointments', 'pets'));
    }

    public function create(): View
    {
        $pets = Pet::where('user_id', auth()->id())->get();
        return view('appointments.create', compact('pets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'type' => 'required|in:checkup,vaccination,grooming,surgery,dental,other',
            'appointment_datetime' => 'required|date|after:now',
            'vet_name' => 'nullable|string|max:100',
            'clinic_name' => 'nullable|string|max:100',
            'clinic_address' => 'nullable|string|max:200',
            'clinic_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();
        $appointment = Appointment::create($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment scheduled successfully!');
    }

    public function show(Appointment $appointment): View
    {
        $this->authorize('view', $appointment);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment): View
    {
        $this->authorize('update', $appointment);
        $pets = Pet::where('user_id', auth()->id())->get();
        return view('appointments.edit', compact('appointment', 'pets'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorize('update', $appointment);

        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'type' => 'required|in:checkup,vaccination,grooming,surgery,dental,other',
            'appointment_datetime' => 'required|date',
            'vet_name' => 'nullable|string|max:100',
            'clinic_name' => 'nullable|string|max:100',
            'clinic_address' => 'nullable|string|max:200',
            'clinic_phone' => 'nullable|string|max:20',
            'status' => 'required|in:scheduled,completed,cancelled,missed',
            'notes' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return redirect()->route('appointments.index')->with('success', 'Appointment updated successfully!');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $this->authorize('delete', $appointment);
        $appointment->delete();
        return redirect()->route('appointments.index')->with('success', 'Appointment deleted.');
    }
}

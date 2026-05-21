@extends('layouts.app')

@section('title', 'Edit Appointment')
@section('page-title', 'Edit Appointment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-900">Edit Appointment</h2>
        </div>
        <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="p-6 space-y-4">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Pet *</label>
                    <select name="pet_id" required class="form-input">
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', $appointment->pet_id) == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $appointment->title) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label">Type *</label>
                    <select name="type" required class="form-input">
                        @foreach(['checkup' => 'Checkup', 'vaccination' => 'Vaccination', 'grooming' => 'Grooming', 'surgery' => 'Surgery', 'dental' => 'Dental', 'other' => 'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $appointment->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Date & Time *</label>
                    <input type="datetime-local" name="appointment_datetime"
                           value="{{ old('appointment_datetime', $appointment->appointment_datetime->format('Y-m-d\TH:i')) }}"
                           required class="form-input">
                </div>

                <div>
                    <label class="form-label">Status *</label>
                    <select name="status" required class="form-input">
                        @foreach(['scheduled', 'completed', 'cancelled', 'missed'] as $s)
                            <option value="{{ $s }}" {{ old('status', $appointment->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Vet Name</label>
                    <input type="text" name="vet_name" value="{{ old('vet_name', $appointment->vet_name) }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Clinic Name</label>
                    <input type="text" name="clinic_name" value="{{ old('clinic_name', $appointment->clinic_name) }}" class="form-input">
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-input">{{ old('notes', $appointment->notes) }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary btn-lg">Save Changes</button>
                <a href="{{ route('appointments.index') }}" class="btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

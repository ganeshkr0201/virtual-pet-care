@extends('layouts.app')

@section('title', 'New Appointment')
@section('page-title', 'Schedule Appointment')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-900">New Appointment</h2>
        </div>
        <form method="POST" action="{{ route('appointments.store') }}" class="p-6 space-y-4">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Pet *</label>
                    <select name="pet_id" required class="form-input">
                        <option value="">Select a pet</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', request('pet_id')) == $pet->id ? 'selected' : '' }}>{{ $pet->name }}</option>
                        @endforeach
                    </select>
                    @error('pet_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-input" placeholder="e.g. Annual checkup">
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Type *</label>
                    <select name="type" required class="form-input">
                        @foreach(['checkup' => 'Checkup', 'vaccination' => 'Vaccination', 'grooming' => 'Grooming', 'surgery' => 'Surgery', 'dental' => 'Dental', 'other' => 'Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Date & Time *</label>
                    <input type="datetime-local" name="appointment_datetime" value="{{ old('appointment_datetime') }}" required class="form-input">
                    @error('appointment_datetime') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Vet Name</label>
                    <input type="text" name="vet_name" value="{{ old('vet_name') }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Clinic Name</label>
                    <input type="text" name="clinic_name" value="{{ old('clinic_name') }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Clinic Phone</label>
                    <input type="text" name="clinic_phone" value="{{ old('clinic_phone') }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Clinic Address</label>
                    <input type="text" name="clinic_address" value="{{ old('clinic_address') }}" class="form-input">
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" rows="3" class="form-input" placeholder="Any additional notes...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary btn-lg">Schedule Appointment</button>
                <a href="{{ route('appointments.index') }}" class="btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

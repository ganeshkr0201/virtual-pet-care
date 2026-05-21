@extends('layouts.app')

@section('title', 'Edit ' . $pet->name)
@section('page-title', 'Edit ' . $pet->name)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-900">Edit Pet Information</h2>
        </div>

        <form method="POST" action="{{ route('pets.update', $pet) }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf @method('PUT')

            <!-- Avatar -->
            <div class="flex items-center gap-6" x-data="{ preview: '{{ $pet->avatar_url }}' }">
                <div class="relative">
                    <img :src="preview" class="w-24 h-24 rounded-2xl object-cover border-4 border-white shadow-lg">
                    <label class="absolute -bottom-2 -right-2 w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-primary-700 transition-colors">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <input type="file" name="avatar" accept="image/*" class="hidden"
                               @change="preview = URL.createObjectURL($event.target.files[0])">
                    </label>
                </div>
                <div>
                    <p class="font-medium text-slate-900">{{ $pet->name }}'s Photo</p>
                    <p class="text-sm text-slate-400">Upload a new photo to replace the current one</p>
                </div>
            </div>

            <!-- Basic Info -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Pet Name *</label>
                    <input type="text" name="name" value="{{ old('name', $pet->name) }}" required class="form-input">
                </div>
                <div>
                    <label class="form-label">Species *</label>
                    <select name="species" required class="form-input">
                        @foreach(['dog', 'cat', 'bird', 'rabbit', 'fish', 'hamster', 'turtle', 'guinea pig', 'other'] as $s)
                            <option value="{{ $s }}" {{ old('species', $pet->species) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Breed</label>
                    <input type="text" name="breed" value="{{ old('breed', $pet->breed) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Gender *</label>
                    <select name="gender" required class="form-input">
                        @foreach(['unknown', 'male', 'female'] as $g)
                            <option value="{{ $g }}" {{ old('gender', $pet->gender) === $g ? 'selected' : '' }}>{{ ucfirst($g) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="form-label">Date of Birth</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $pet->date_of_birth?->format('Y-m-d')) }}" class="form-input" max="{{ today()->toDateString() }}">
                </div>
                <div>
                    <label class="form-label">Weight (kg)</label>
                    <input type="number" name="weight" value="{{ old('weight', $pet->weight) }}" step="0.1" min="0" class="form-input">
                </div>
                <div>
                    <label class="form-label">Color</label>
                    <input type="text" name="color" value="{{ old('color', $pet->color) }}" class="form-input">
                </div>
                <div>
                    <label class="form-label">Activity Level *</label>
                    <select name="activity_level" required class="form-input">
                        @foreach(['low', 'moderate', 'high'] as $level)
                            <option value="{{ $level }}" {{ old('activity_level', $pet->activity_level) === $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Medical Info -->
            <div class="border-t border-slate-100 pt-6">
                <h3 class="font-medium text-slate-900 mb-4">Medical Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="form-label">Allergies</label>
                        <textarea name="allergies" rows="2" class="form-input">{{ old('allergies', $pet->allergies) }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Medical History</label>
                        <textarea name="medical_history" rows="3" class="form-input">{{ old('medical_history', $pet->medical_history) }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Emergency Notes</label>
                        <textarea name="emergency_notes" rows="2" class="form-input">{{ old('emergency_notes', $pet->emergency_notes) }}</textarea>
                    </div>
                    <div>
                        <label class="form-label">Microchip ID</label>
                        <input type="text" name="microchip_id" value="{{ old('microchip_id', $pet->microchip_id) }}" class="form-input">
                    </div>
                </div>
            </div>

            <!-- Vet Info -->
            <div class="border-t border-slate-100 pt-6">
                <h3 class="font-medium text-slate-900 mb-4">Veterinarian Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Vet Name</label>
                        <input type="text" name="vet_name" value="{{ old('vet_name', $pet->vet_name) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Vet Phone</label>
                        <input type="text" name="vet_phone" value="{{ old('vet_phone', $pet->vet_phone) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Vet Email</label>
                        <input type="email" name="vet_email" value="{{ old('vet_email', $pet->vet_email) }}" class="form-input">
                    </div>
                    <div>
                        <label class="form-label">Clinic Name</label>
                        <input type="text" name="vet_clinic" value="{{ old('vet_clinic', $pet->vet_clinic) }}" class="form-input">
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary btn-lg">Save Changes</button>
                <a href="{{ route('pets.show', $pet) }}" class="btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

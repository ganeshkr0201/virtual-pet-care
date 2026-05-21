@extends('layouts.app')

@section('title', 'New Reminder')
@section('page-title', 'Create Reminder')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-900">New Reminder</h2>
            <p class="text-sm text-slate-500 mt-1">Set up a care reminder for your pet</p>
        </div>

        <form method="POST" action="{{ route('reminders.store') }}" class="p-6 space-y-5"
              x-data="{ repeat: '{{ old('repeat', 'daily') }}' }">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Pet *</label>
                    <select name="pet_id" required class="form-input">
                        <option value="">Select a pet</option>
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', request('pet_id')) == $pet->id ? 'selected' : '' }}>
                                {{ $pet->name }} ({{ ucfirst($pet->species) }})
                            </option>
                        @endforeach
                    </select>
                    @error('pet_id') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Reminder Title *</label>
                    <input type="text" name="title" value="{{ old('title') }}" required class="form-input" placeholder="e.g. Morning feeding">
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Type *</label>
                    <select name="type" required class="form-input">
                        @foreach(['feeding' => '🍽️ Feeding', 'walking' => '🦮 Walking', 'exercise' => '🏃 Exercise', 'grooming' => '✂️ Grooming', 'medication' => '💊 Medication', 'vet_appointment' => '🏥 Vet Appointment', 'vaccination' => '💉 Vaccination', 'training' => '🎓 Training', 'water' => '💧 Water', 'other' => '📌 Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type') === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Time *</label>
                    <input type="time" name="reminder_time" value="{{ old('reminder_time', '08:00') }}" required class="form-input">
                    @error('reminder_time') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', today()->toDateString()) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-input">
                    <p class="text-xs text-slate-400 mt-1">Leave empty for no end date</p>
                </div>

                <div>
                    <label class="form-label">Repeat *</label>
                    <select name="repeat" required class="form-input" x-model="repeat">
                        <option value="none">No repeat</option>
                        <option value="daily" selected>Daily</option>
                        <option value="weekly">Weekly</option>
                        <option value="monthly">Monthly</option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Snooze Duration</label>
                    <select name="snooze_minutes" class="form-input">
                        @foreach([5, 10, 15, 30, 60] as $min)
                            <option value="{{ $min }}" {{ old('snooze_minutes', 10) == $min ? 'selected' : '' }}>{{ $min }} minutes</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Weekly days selector -->
            <div x-show="repeat === 'weekly'" class="space-y-2">
                <label class="form-label">Repeat on days</label>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $i => $day)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="repeat_days[]" value="{{ $i }}"
                                   {{ in_array($i, old('repeat_days', [])) ? 'checked' : '' }} class="sr-only peer">
                            <span class="w-10 h-10 rounded-xl border-2 border-slate-200 peer-checked:border-primary-600 peer-checked:bg-primary-600 peer-checked:text-white flex items-center justify-center text-xs font-medium text-slate-600 transition-all">
                                {{ $day }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Description -->
            <div>
                <label class="form-label">Notes (optional)</label>
                <textarea name="description" rows="2" class="form-input" placeholder="Any additional notes...">{{ old('description') }}</textarea>
            </div>

            <!-- Notifications -->
            <div class="border-t border-slate-100 pt-4">
                <p class="font-medium text-slate-900 mb-3">Notifications</p>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="email_notify" value="1" {{ old('email_notify', '1') ? 'checked' : '' }}
                               class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">Email notifications</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="push_notify" value="1" {{ old('push_notify', '1') ? 'checked' : '' }}
                               class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">Push notifications</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary btn-lg">Create Reminder</button>
                <a href="{{ route('reminders.index') }}" class="btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

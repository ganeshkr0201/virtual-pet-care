@extends('layouts.app')

@section('title', 'Edit Reminder')
@section('page-title', 'Edit Reminder')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card">
        <div class="p-6 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-900">Edit Reminder</h2>
        </div>

        <form method="POST" action="{{ route('reminders.update', $reminder) }}" class="p-6 space-y-5"
              x-data="{ repeat: '{{ old('repeat', $reminder->repeat) }}' }">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="form-label">Pet *</label>
                    <select name="pet_id" required class="form-input">
                        @foreach($pets as $pet)
                            <option value="{{ $pet->id }}" {{ old('pet_id', $reminder->pet_id) == $pet->id ? 'selected' : '' }}>
                                {{ $pet->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" value="{{ old('title', $reminder->title) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label">Type *</label>
                    <select name="type" required class="form-input">
                        @foreach(['feeding' => '🍽️ Feeding', 'walking' => '🦮 Walking', 'exercise' => '🏃 Exercise', 'grooming' => '✂️ Grooming', 'medication' => '💊 Medication', 'vet_appointment' => '🏥 Vet Appointment', 'vaccination' => '💉 Vaccination', 'training' => '🎓 Training', 'water' => '💧 Water', 'other' => '📌 Other'] as $val => $label)
                            <option value="{{ $val }}" {{ old('type', $reminder->type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Time *</label>
                    <input type="time" name="reminder_time" value="{{ old('reminder_time', substr($reminder->reminder_time, 0, 5)) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $reminder->start_date->format('Y-m-d')) }}" required class="form-input">
                </div>

                <div>
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $reminder->end_date?->format('Y-m-d')) }}" class="form-input">
                </div>

                <div>
                    <label class="form-label">Repeat *</label>
                    <select name="repeat" required class="form-input" x-model="repeat">
                        @foreach(['none' => 'No repeat', 'daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly'] as $val => $label)
                            <option value="{{ $val }}" {{ old('repeat', $reminder->repeat) === $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="form-label">Snooze Duration</label>
                    <select name="snooze_minutes" class="form-input">
                        @foreach([5, 10, 15, 30, 60] as $min)
                            <option value="{{ $min }}" {{ old('snooze_minutes', $reminder->snooze_minutes) == $min ? 'selected' : '' }}>{{ $min }} minutes</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div x-show="repeat === 'weekly'" class="space-y-2">
                <label class="form-label">Repeat on days</label>
                <div class="flex gap-2 flex-wrap">
                    @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $i => $day)
                        <label class="cursor-pointer">
                            <input type="checkbox" name="repeat_days[]" value="{{ $i }}"
                                   {{ in_array($i, old('repeat_days', $reminder->repeat_days ?? [])) ? 'checked' : '' }} class="sr-only peer">
                            <span class="w-10 h-10 rounded-xl border-2 border-slate-200 peer-checked:border-primary-600 peer-checked:bg-primary-600 peer-checked:text-white flex items-center justify-center text-xs font-medium text-slate-600 transition-all">
                                {{ $day }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label class="form-label">Notes</label>
                <textarea name="description" rows="2" class="form-input">{{ old('description', $reminder->description) }}</textarea>
            </div>

            <div class="border-t border-slate-100 pt-4">
                <p class="font-medium text-slate-900 mb-3">Notifications</p>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="email_notify" value="1" {{ old('email_notify', $reminder->email_notify) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">Email notifications</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="push_notify" value="1" {{ old('push_notify', $reminder->push_notify) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-slate-700">Push notifications</span>
                    </label>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="btn-primary btn-lg">Save Changes</button>
                <a href="{{ route('reminders.index') }}" class="btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection

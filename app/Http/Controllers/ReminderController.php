<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Reminder;
use App\Models\ReminderLog;
use App\Repositories\ReminderRepository;
use App\Services\ReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReminderController extends Controller
{
    public function __construct(
        private ReminderService $reminderService,
        private ReminderRepository $reminderRepository
    ) {}

    public function index(Request $request): View
    {
        $reminders = $this->reminderRepository->getAllForUser(auth()->id(), $request->only(['type', 'pet_id', 'is_active']));
        $pets = Pet::where('user_id', auth()->id())->get();
        $todayReminders = $this->reminderRepository->getTodayReminders(auth()->id());
        return view('reminders.index', compact('reminders', 'pets', 'todayReminders'));
    }

    public function create(): View
    {
        $pets = Pet::where('user_id', auth()->id())->get();
        return view('reminders.create', compact('pets'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'type' => 'required|in:feeding,walking,exercise,grooming,medication,vet_appointment,vaccination,training,water,other',
            'reminder_time' => 'required|date_format:H:i',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'repeat' => 'required|in:none,daily,weekly,monthly',
            'repeat_days' => 'nullable|array',
            'repeat_days.*' => 'integer|between:0,6',
            'email_notify' => 'boolean',
            'push_notify' => 'boolean',
            'snooze_minutes' => 'integer|min:1|max:60',
        ]);

        // Verify pet belongs to user
        $pet = Pet::where('id', $validated['pet_id'])->where('user_id', auth()->id())->firstOrFail();

        $reminder = $this->reminderService->createReminder(auth()->id(), $validated);

        return redirect()->route('reminders.index')->with('success', "Reminder '{$reminder->title}' created successfully!");
    }

    public function show(Reminder $reminder): View
    {
        $this->authorize('view', $reminder);
        $reminder->load(['pet', 'logs' => fn($q) => $q->latest()->limit(30)]);
        return view('reminders.show', compact('reminder'));
    }

    public function edit(Reminder $reminder): View
    {
        $this->authorize('update', $reminder);
        $pets = Pet::where('user_id', auth()->id())->get();
        return view('reminders.edit', compact('reminder', 'pets'));
    }

    public function update(Request $request, Reminder $reminder): RedirectResponse
    {
        $this->authorize('update', $reminder);

        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'title' => 'required|string|max:100',
            'description' => 'nullable|string',
            'type' => 'required|in:feeding,walking,exercise,grooming,medication,vet_appointment,vaccination,training,water,other',
            'reminder_time' => 'required|date_format:H:i',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'repeat' => 'required|in:none,daily,weekly,monthly',
            'repeat_days' => 'nullable|array',
            'email_notify' => 'boolean',
            'push_notify' => 'boolean',
            'snooze_minutes' => 'integer|min:1|max:60',
        ]);

        $this->reminderService->updateReminder($reminder, $validated);

        return redirect()->route('reminders.index')->with('success', 'Reminder updated successfully!');
    }

    public function destroy(Reminder $reminder): RedirectResponse
    {
        $this->authorize('delete', $reminder);
        $this->reminderService->deleteReminder($reminder);
        return redirect()->route('reminders.index')->with('success', 'Reminder deleted.');
    }

    public function markComplete(Request $request, Reminder $reminder): JsonResponse
    {
        $this->authorize('update', $reminder);
        $log = $this->reminderService->markComplete($reminder, $request->notes);
        return response()->json(['success' => true, 'log' => $log]);
    }

    public function snooze(Request $request, Reminder $reminder): JsonResponse
    {
        $this->authorize('update', $reminder);
        $log = $this->reminderService->snoozeReminder($reminder, $request->minutes);
        return response()->json(['success' => true, 'log' => $log]);
    }

    public function calendar(): View
    {
        $reminders = Reminder::where('user_id', auth()->id())
            ->where('is_active', true)
            ->with('pet')
            ->get();

        $appointments = \App\Models\Appointment::where('user_id', auth()->id())
            ->where('status', 'scheduled')
            ->with('pet')
            ->get();

        $events = [];

        foreach ($reminders as $reminder) {
            $events[] = [
                'id' => 'r_' . $reminder->id,
                'title' => $reminder->pet->name . ': ' . $reminder->title,
                'start' => $reminder->start_date->format('Y-m-d') . 'T' . $reminder->reminder_time,
                'end' => $reminder->end_date ? $reminder->end_date->format('Y-m-d') . 'T' . $reminder->reminder_time : null,
                'color' => '#4F46E5',
                'extendedProps' => ['type' => 'reminder', 'icon' => $reminder->type_icon],
            ];
        }

        foreach ($appointments as $appointment) {
            $events[] = [
                'id' => 'a_' . $appointment->id,
                'title' => $appointment->pet->name . ': ' . $appointment->title,
                'start' => $appointment->appointment_datetime->format('Y-m-d\TH:i:s'),
                'color' => '#14B8A6',
                'extendedProps' => ['type' => 'appointment'],
            ];
        }

        return view('reminders.calendar', compact('events'));
    }
}

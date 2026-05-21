<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reminder;
use App\Repositories\ReminderRepository;
use App\Services\ReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReminderApiController extends Controller
{
    public function __construct(
        private ReminderService $reminderService,
        private ReminderRepository $reminderRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $reminders = $this->reminderRepository->getAllForUser(
            auth()->id(),
            $request->only(['type', 'pet_id', 'is_active'])
        );
        return response()->json($reminders);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'pet_id'        => 'required|exists:pets,id',
            'title'         => 'required|string|max:100',
            'type'          => 'required|in:feeding,walking,exercise,grooming,medication,vet_appointment,vaccination,training,water,other',
            'reminder_time' => 'required|date_format:H:i',
            'start_date'    => 'required|date',
            'end_date'      => 'nullable|date|after:start_date',
            'repeat'        => 'required|in:none,daily,weekly,monthly',
        ]);

        $reminder = $this->reminderService->createReminder(auth()->id(), $validated);
        return response()->json($reminder, 201);
    }

    public function show(Reminder $reminder): JsonResponse
    {
        $this->authorize('view', $reminder);
        return response()->json($reminder->load(['pet', 'logs' => fn($q) => $q->latest()->limit(30)]));
    }

    public function update(Request $request, Reminder $reminder): JsonResponse
    {
        $this->authorize('update', $reminder);

        $validated = $request->validate([
            'title'         => 'sometimes|string|max:100',
            'type'          => 'sometimes|in:feeding,walking,exercise,grooming,medication,vet_appointment,vaccination,training,water,other',
            'reminder_time' => 'sometimes|date_format:H:i',
            'start_date'    => 'sometimes|date',
            'repeat'        => 'sometimes|in:none,daily,weekly,monthly',
            'is_active'     => 'sometimes|boolean',
        ]);

        $reminder = $this->reminderService->updateReminder($reminder, $validated);
        return response()->json($reminder);
    }

    public function destroy(Reminder $reminder): JsonResponse
    {
        $this->authorize('delete', $reminder);
        $this->reminderService->deleteReminder($reminder);
        return response()->json(['message' => 'Reminder deleted.']);
    }

    public function complete(Request $request, Reminder $reminder): JsonResponse
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
}

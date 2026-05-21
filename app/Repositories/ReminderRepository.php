<?php

namespace App\Repositories;

use App\Models\Reminder;
use App\Models\ReminderLog;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReminderRepository
{
    public function getAllForUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Reminder::where('user_id', $userId)->with(['pet']);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['pet_id'])) {
            $query->where('pet_id', $filters['pet_id']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        return $query->orderBy('reminder_time')->paginate(15);
    }

    public function getTodayReminders(int $userId): Collection
    {
        $today = today();
        $dayOfWeek = $today->dayOfWeek;

        return Reminder::where('user_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', $today);
            })
            ->where(function ($q) use ($dayOfWeek) {
                $q->where('repeat', 'daily')
                  ->orWhere('repeat', 'none')
                  ->orWhere(function ($q2) use ($dayOfWeek) {
                      $q2->where('repeat', 'weekly')
                         ->whereJsonContains('repeat_days', $dayOfWeek);
                  });
            })
            ->with(['pet', 'todayLog'])
            ->orderBy('reminder_time')
            ->get();
    }

    public function getUpcoming(int $userId, int $limit = 5): Collection
    {
        return Reminder::where('user_id', $userId)
            ->where('is_active', true)
            ->where('start_date', '<=', today())
            ->with('pet')
            ->orderBy('reminder_time')
            ->limit($limit)
            ->get();
    }

    public function create(array $data): Reminder
    {
        return Reminder::create($data);
    }

    public function update(Reminder $reminder, array $data): Reminder
    {
        $reminder->update($data);
        return $reminder->fresh();
    }

    public function delete(Reminder $reminder): bool
    {
        return $reminder->delete();
    }

    public function getCompletionStats(int $userId, int $days = 7): array
    {
        $stats = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $total = ReminderLog::where('user_id', $userId)->where('scheduled_date', $date)->count();
            $completed = ReminderLog::where('user_id', $userId)->where('scheduled_date', $date)->where('status', 'completed')->count();
            $stats[] = [
                'date' => $date,
                'total' => $total,
                'completed' => $completed,
                'missed' => ReminderLog::where('user_id', $userId)->where('scheduled_date', $date)->where('status', 'missed')->count(),
            ];
        }
        return $stats;
    }
}

<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Reminder;
use App\Models\ReminderLog;
use App\Models\Vaccination;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function getDashboardData(int $userId): array
    {
        $today = today();

        $pets = Pet::where('user_id', $userId)->with(['vaccinations', 'reminders'])->get();
        $petIds = $pets->pluck('id');

        // Today's reminders
        $todayLogs = ReminderLog::where('user_id', $userId)
            ->whereDate('scheduled_date', $today)
            ->with('reminder.pet')
            ->get();

        $completed = $todayLogs->where('status', 'completed')->count();
        $missed = $todayLogs->where('status', 'missed')->count();
        $pending = $todayLogs->where('status', 'pending')->count();
        $total = $todayLogs->count();

        // Upcoming appointments
        $upcomingAppointments = Appointment::where('user_id', $userId)
            ->where('status', 'scheduled')
            ->where('appointment_datetime', '>=', now())
            ->with('pet')
            ->orderBy('appointment_datetime')
            ->limit(5)
            ->get();

        // Upcoming vaccinations
        $upcomingVaccinations = Vaccination::whereIn('pet_id', $petIds)
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '>=', $today)
            ->where('next_due_date', '<=', $today->copy()->addDays(30))
            ->with('pet')
            ->orderBy('next_due_date')
            ->get();

        // Weekly stats
        $weeklyStats = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dayLogs = ReminderLog::where('user_id', $userId)->where('scheduled_date', $date)->get();
            $weeklyStats[] = [
                'date' => $date,
                'day' => now()->subDays($i)->format('D'),
                'total' => $dayLogs->count(),
                'completed' => $dayLogs->where('status', 'completed')->count(),
                'missed' => $dayLogs->where('status', 'missed')->count(),
            ];
        }

        // Monthly completion rate
        $monthStart = now()->startOfMonth();
        $monthLogs = ReminderLog::where('user_id', $userId)
            ->where('scheduled_date', '>=', $monthStart)
            ->get();
        $monthlyRate = $monthLogs->count() > 0
            ? round(($monthLogs->where('status', 'completed')->count() / $monthLogs->count()) * 100)
            : 0;

        return [
            'pets' => $pets,
            'pets_count' => $pets->count(),
            'today_total' => $total,
            'today_completed' => $completed,
            'today_missed' => $missed,
            'today_pending' => $pending,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100) : 0,
            'monthly_rate' => $monthlyRate,
            'upcoming_appointments' => $upcomingAppointments,
            'upcoming_vaccinations' => $upcomingVaccinations,
            'weekly_stats' => $weeklyStats,
            'today_logs' => $todayLogs,
        ];
    }
}

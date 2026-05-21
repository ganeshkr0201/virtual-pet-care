<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\Reminder;
use App\Models\ReminderLog;
use App\Models\User;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $month->format('M Y'),
                'new_users' => User::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
                'new_pets' => Pet::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->count(),
                'completed_reminders' => ReminderLog::where('status', 'completed')
                    ->whereYear('scheduled_date', $month->year)
                    ->whereMonth('scheduled_date', $month->month)
                    ->count(),
            ];
        }

        return view('admin.reports', compact('monthlyStats'));
    }
}

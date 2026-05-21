<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Pet;
use App\Models\Reminder;
use App\Models\ReminderLog;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users' => User::count(),
            'active_pets' => Pet::count(),
            'total_reminders' => Reminder::count(),
            'completed_today' => ReminderLog::whereDate('scheduled_date', today())->where('status', 'completed')->count(),
            'open_feedbacks' => Feedback::where('status', 'open')->count(),
        ];

        $recentUsers = User::latest()->limit(10)->get();

        $speciesStats = Pet::selectRaw('species, count(*) as count')
            ->groupBy('species')
            ->orderByDesc('count')
            ->get();

        $weeklyRegistrations = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $weeklyRegistrations[] = [
                'date' => $date,
                'day' => now()->subDays($i)->format('D'),
                'count' => User::whereDate('created_at', $date)->count(),
            ];
        }

        return view('admin.dashboard', compact('stats', 'recentUsers', 'speciesStats', 'weeklyRegistrations'));
    }
}

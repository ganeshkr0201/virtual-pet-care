<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('roles');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $users = $query->latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->load(['pets', 'reminders', 'activityLogs' => fn($q) => $q->latest()->limit(20)]);
        return view('admin.users.show', compact('user'));
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate your own account.');
        }

        if ($user->trashed()) {
            $user->restore();
            $message = "User {$user->name} has been reactivated.";
        } else {
            $user->delete();
            $message = "User {$user->name} has been deactivated.";
        }

        return back()->with('success', $message);
    }
}

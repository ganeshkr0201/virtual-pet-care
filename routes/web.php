<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReminderController;
use Illuminate\Support\Facades\Route;

// Public / Landing
Route::get('/', fn() => view('welcome'))->name('home');
Route::get('/features', fn() => view('pages.features'))->name('features');
Route::get('/about', fn() => view('pages.about'))->name('about');
Route::get('/contact', fn() => view('pages.contact'))->name('contact');
Route::post('/contact', function (\Illuminate\Http\Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'email' => 'required|email',
        'subject' => 'required|string|max:200',
        'message' => 'required|string',
    ]);
    \App\Models\Feedback::create(array_merge($validated, [
        'user_id' => auth()->id(),
        'type' => 'general',
    ]));
    return back()->with('success', 'Thank you! We\'ll get back to you within 24 hours.');
})->name('contact.submit');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// Email verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('dashboard')->with('success', 'Email verified!');
    })->middleware('signed')->name('verification.verify');
    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

// Authenticated User Routes
Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Pets
    Route::resource('pets', PetController::class);
    Route::post('/pets/{pet}/images', [PetController::class, 'uploadImages'])->name('pets.images.upload');
    Route::delete('/pets/{pet}/images/{image}', [PetController::class, 'deleteImage'])->name('pets.images.delete');

    // Reminders
    Route::resource('reminders', ReminderController::class);
    Route::post('/reminders/{reminder}/complete', [ReminderController::class, 'markComplete'])->name('reminders.complete');
    Route::post('/reminders/{reminder}/snooze', [ReminderController::class, 'snooze'])->name('reminders.snooze');
    Route::get('/calendar', [ReminderController::class, 'calendar'])->name('calendar');

    // Appointments
    Route::resource('appointments', AppointmentController::class);

    // Health Tracker
    Route::get('/health', [HealthController::class, 'index'])->name('health.index');
    Route::get('/health/{pet}', [HealthController::class, 'petHealth'])->name('health.pet');
    Route::post('/health/{pet}/medical-records', [HealthController::class, 'storeMedicalRecord'])->name('health.medical.store');
    Route::delete('/health/medical-records/{record}', [HealthController::class, 'destroyMedicalRecord'])->name('health.medical.destroy');
    Route::post('/health/{pet}/vaccinations', [HealthController::class, 'storeVaccination'])->name('health.vaccination.store');
    Route::delete('/health/vaccinations/{vaccination}', [HealthController::class, 'destroyVaccination'])->name('health.vaccination.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.read-all');
    Route::delete('/notifications', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::put('/profile/notifications', [ProfileController::class, 'updateNotifications'])->name('profile.notifications');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class)->only(['index', 'show']);
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
});

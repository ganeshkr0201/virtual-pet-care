<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Reminder;
use App\Models\ReminderLog;
use App\Models\User;
use App\Models\Vaccination;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('email', 'demo@virtualpetcare.com')->first();
        if (!$user) return;

        // Create demo pets
        $dog = Pet::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Buddy'],
            [
                'species' => 'dog',
                'breed' => 'Golden Retriever',
                'gender' => 'male',
                'date_of_birth' => now()->subYears(3)->subMonths(2),
                'weight' => 28.5,
                'color' => 'Golden',
                'activity_level' => 'high',
                'vet_name' => 'Dr. Sarah Johnson',
                'vet_phone' => '+1 555 0100',
                'vet_clinic' => 'Happy Paws Veterinary Clinic',
                'allergies' => 'Chicken-based foods',
                'is_active' => true,
            ]
        );

        $cat = Pet::firstOrCreate(
            ['user_id' => $user->id, 'name' => 'Luna'],
            [
                'species' => 'cat',
                'breed' => 'Persian',
                'gender' => 'female',
                'date_of_birth' => now()->subYears(2),
                'weight' => 4.2,
                'color' => 'White',
                'activity_level' => 'moderate',
                'vet_name' => 'Dr. Sarah Johnson',
                'vet_phone' => '+1 555 0100',
                'vet_clinic' => 'Happy Paws Veterinary Clinic',
                'is_active' => true,
            ]
        );

        // Reminders for Buddy
        $reminders = [
            ['title' => 'Morning Feeding', 'type' => 'feeding', 'time' => '07:00'],
            ['title' => 'Evening Walk', 'type' => 'walking', 'time' => '18:00'],
            ['title' => 'Evening Feeding', 'type' => 'feeding', 'time' => '19:00'],
            ['title' => 'Heartworm Medication', 'type' => 'medication', 'time' => '08:00'],
        ];

        foreach ($reminders as $r) {
            Reminder::firstOrCreate(
                ['user_id' => $user->id, 'pet_id' => $dog->id, 'title' => $r['title']],
                [
                    'type' => $r['type'],
                    'reminder_time' => $r['time'],
                    'start_date' => now()->subDays(30),
                    'repeat' => 'daily',
                    'is_active' => true,
                    'email_notify' => true,
                    'push_notify' => true,
                ]
            );
        }

        // Luna reminders
        Reminder::firstOrCreate(
            ['user_id' => $user->id, 'pet_id' => $cat->id, 'title' => 'Luna Morning Feeding'],
            [
                'type' => 'feeding',
                'reminder_time' => '07:30',
                'start_date' => now()->subDays(20),
                'repeat' => 'daily',
                'is_active' => true,
            ]
        );

        // Vaccinations
        Vaccination::firstOrCreate(
            ['pet_id' => $dog->id, 'vaccine_name' => 'Rabies'],
            [
                'administered_date' => now()->subYear(),
                'next_due_date' => now()->addMonths(2),
                'administered_by' => 'Dr. Sarah Johnson',
            ]
        );

        Vaccination::firstOrCreate(
            ['pet_id' => $dog->id, 'vaccine_name' => 'DHPP'],
            [
                'administered_date' => now()->subMonths(8),
                'next_due_date' => now()->addMonths(4),
                'administered_by' => 'Dr. Sarah Johnson',
            ]
        );

        // Medical Records
        MedicalRecord::firstOrCreate(
            ['pet_id' => $dog->id, 'title' => 'Annual Checkup 2025'],
            [
                'type' => 'checkup',
                'record_date' => now()->subMonths(3),
                'weight' => 28.5,
                'vet_name' => 'Dr. Sarah Johnson',
                'description' => 'Routine annual checkup. All vitals normal.',
                'diagnosis' => 'Healthy',
            ]
        );

        // Appointment
        Appointment::firstOrCreate(
            ['user_id' => $user->id, 'pet_id' => $dog->id, 'title' => 'Annual Vaccination'],
            [
                'type' => 'vaccination',
                'appointment_datetime' => now()->addDays(14)->setTime(10, 0),
                'vet_name' => 'Dr. Sarah Johnson',
                'clinic_name' => 'Happy Paws Veterinary Clinic',
                'status' => 'scheduled',
            ]
        );

        // Generate some reminder logs for the past week
        $allReminders = Reminder::where('user_id', $user->id)->get();
        for ($i = 6; $i >= 1; $i--) {
            $date = now()->subDays($i)->toDateString();
            foreach ($allReminders as $reminder) {
                $status = rand(0, 10) > 2 ? 'completed' : 'missed';
                ReminderLog::firstOrCreate(
                    ['reminder_id' => $reminder->id, 'user_id' => $user->id, 'scheduled_date' => $date],
                    [
                        'scheduled_time' => $reminder->reminder_time,
                        'status' => $status,
                        'completed_at' => $status === 'completed' ? Carbon::parse($date . ' ' . $reminder->reminder_time) : null,
                    ]
                );
            }
        }

        // Seed in-app notifications for demo user
        foreach ($allReminders->take(3) as $reminder) {
            $user->notify(new \App\Notifications\ReminderNotification($reminder));
        }

        $vax = \App\Models\Vaccination::where('pet_id', $dog->id)->first();
        if ($vax) {
            $user->notify(new \App\Notifications\VaccinationDueNotification($vax));
        }
    }
}

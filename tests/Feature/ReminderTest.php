<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\Reminder;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Pet  $pet;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->user->assignRole('pet_owner');
        $this->pet = Pet::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_reminders_index_loads(): void
    {
        $this->actingAs($this->user)->get('/reminders')->assertStatus(200);
    }

    public function test_user_can_create_reminder(): void
    {
        $this->actingAs($this->user)
             ->post('/reminders', [
                 'pet_id'         => $this->pet->id,
                 'title'          => 'Morning Feeding',
                 'type'           => 'feeding',
                 'reminder_time'  => '08:00',
                 'start_date'     => today()->toDateString(),
                 'repeat'         => 'daily',
                 'email_notify'   => 1,
                 'push_notify'    => 1,
                 'snooze_minutes' => 10,
             ])->assertRedirect('/reminders');

        $this->assertDatabaseHas('reminders', [
            'title'   => 'Morning Feeding',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_user_can_mark_reminder_complete(): void
    {
        $reminder = Reminder::factory()->create([
            'user_id' => $this->user->id,
            'pet_id'  => $this->pet->id,
        ]);

        $this->actingAs($this->user)
             ->postJson("/reminders/{$reminder->id}/complete")
             ->assertJson(['success' => true]);

        $this->assertDatabaseHas('reminder_logs', [
            'reminder_id' => $reminder->id,
            'status'      => 'completed',
        ]);
    }

    public function test_user_can_delete_reminder(): void
    {
        $reminder = Reminder::factory()->create([
            'user_id' => $this->user->id,
            'pet_id'  => $this->pet->id,
        ]);

        $this->actingAs($this->user)
             ->delete("/reminders/{$reminder->id}")
             ->assertRedirect('/reminders');

        $this->assertSoftDeleted('reminders', ['id' => $reminder->id]);
    }

    public function test_calendar_page_loads(): void
    {
        $this->actingAs($this->user)->get('/calendar')->assertStatus(200);
    }
}

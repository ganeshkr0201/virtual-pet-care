<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PetTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->withoutMiddleware(VerifyCsrfToken::class);
        $this->user = User::factory()->create(['email_verified_at' => now()]);
        $this->user->assignRole('pet_owner');
    }

    public function test_pets_index_loads(): void
    {
        $this->actingAs($this->user)->get('/pets')->assertStatus(200);
    }

    public function test_create_pet_page_loads(): void
    {
        $this->actingAs($this->user)->get('/pets/create')->assertStatus(200);
    }

    public function test_user_can_create_pet(): void
    {
        $this->actingAs($this->user)
             ->post('/pets', [
                 'name'           => 'Buddy',
                 'species'        => 'dog',
                 'breed'          => 'Golden Retriever',
                 'gender'         => 'male',
                 'activity_level' => 'high',
             ])->assertRedirect();

        $this->assertDatabaseHas('pets', ['name' => 'Buddy', 'user_id' => $this->user->id]);
    }

    public function test_user_can_view_own_pet(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
             ->get("/pets/{$pet->id}")
             ->assertStatus(200)
             ->assertSee($pet->name);
    }

    public function test_user_cannot_view_other_users_pet(): void
    {
        $other = User::factory()->create();
        $pet   = Pet::factory()->create(['user_id' => $other->id]);

        $this->actingAs($this->user)
             ->get("/pets/{$pet->id}")
             ->assertStatus(403);
    }

    public function test_user_can_update_pet(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
             ->put("/pets/{$pet->id}", [
                 'name'           => 'Max',
                 'species'        => $pet->species,
                 'gender'         => $pet->gender,
                 'activity_level' => $pet->activity_level,
             ])->assertRedirect();

        $this->assertDatabaseHas('pets', ['id' => $pet->id, 'name' => 'Max']);
    }

    public function test_user_can_delete_pet(): void
    {
        $pet = Pet::factory()->create(['user_id' => $this->user->id]);

        $this->actingAs($this->user)
             ->delete("/pets/{$pet->id}")
             ->assertRedirect('/pets');

        $this->assertSoftDeleted('pets', ['id' => $pet->id]);
    }
}

<?php


use App\Models\Role;
use App\Models\Travel;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminTourTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_public_user_cannot_access_adding_tour(): void
    {
        $travel = Travel::factory()->create();

        $response = $this->postJson('api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(401); //not authenticated
    }

    public function test_non_admin_user_cannot_access_adding_travels(): void
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();

        $user->roles()->attach(Role::where('name', 'editor')->value('id'));

        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->postJson('api/v1/admin/travels/' . $travel->id . '/tours');

        $response->assertStatus(403); //not authorized
    }

    public function test_saves_tours_data_successfully_with_valid_data(): void
    {
        $this->seed(RoleSeeder::class);

        $user = User::factory()->create();

        $user->roles()->attach(Role::where('name', 'admin')->value('id'));

        $travel = Travel::factory()->create();

        $response = $this->actingAs($user)->postJson('api/v1/admin/travels/'. $travel->id . '/tours', [
            'name' => 'X5 Amazing Tour.',
        ]);

        $response->assertStatus(422);

        $response = $this->actingAs($user)->postJson('api/v1/admin/travels/'. $travel->id . '/tours', [
            'name' => 'X5 Amazing Tour.',
            'starting_date' => '2023/07/11',
            'ending_date' => '2023/07/18',
            'price' => 2100
        ]);

        $response->assertStatus(201);

        $response->assertJsonFragment(['name' => 'X5 Amazing Tour.']);
    }
}

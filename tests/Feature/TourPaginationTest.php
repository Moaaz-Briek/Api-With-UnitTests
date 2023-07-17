<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_return_list_pagination_correctly(): void
    {
        $travels = Travel::factory()->create();

        Tour::factory(16)->create(['travel_id' => $travels->id]);

        $response = $this->get('api/v1/travels/' . $travels->slug . '/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(15, 'data');

        $response->assertJsonPath('meta.last_page', 2);
    }
}

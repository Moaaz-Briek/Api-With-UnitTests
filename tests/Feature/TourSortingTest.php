<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TourSortingTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_list_sorts_by_starting_date_correctly(): void
    {
        $travels = Travel::factory()->create();

        $laterTour = Tour::factory()->create([
            'travel_id' => $travels->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(5),
        ]);

        $earlierTour = Tour::factory()->create([
            'travel_id' => $travels->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(2),
        ]);

        $response = $this->get('api/v1/travels/' . $travels->slug . '/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(2, 'data');

        $response->assertJsonPath('data.0.id', $earlierTour->id);
        $response->assertJsonPath('data.1.id', $laterTour->id);
    }

    public function test_tours_list_sorts_by_price_correctly(): void
    {
        $travels = Travel::factory()->create();

        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travels->id,
            'price' => 5000,
        ]);

        $cheapEarlierTour = Tour::factory()->create([
            'travel_id' => $travels->id,
            'starting_date' => now(),
            'ending_date' => now()->addDays(2),
            'price' => 100,
        ]);

        $cheapLaterTour = Tour::factory()->create([
            'travel_id' => $travels->id,
            'starting_date' => now()->addDays(2),
            'ending_date' => now()->addDays(5),
            'price' => 100,
        ]);

        $response = $this->get('api/v1/travels/' . $travels->slug . '/tours?sortBy=price&sortOrder=asc');

        $response->assertStatus(200);

        $response->assertJsonCount(3, 'data');

        $response->assertJsonPath('data.0.id', $cheapEarlierTour->id);
        $response->assertJsonPath('data.1.id', $cheapLaterTour->id);
        $response->assertJsonPath('data.2.id', $expensiveTour->id);
    }
}

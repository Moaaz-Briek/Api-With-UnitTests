<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourFilteringTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_list_filters_by_price_correctly(): void
    {
        $travels = Travel::factory()->create();

        $expensiveTour = Tour::factory()->create([
            'travel_id' => $travels->id,
            'price' => 200,
        ]);

        $cheapTour = Tour::factory()->create([
            'travel_id' => $travels->id,
            'price' => 100,
        ]);

        $endPoint = 'api/v1/travels/'.$travels->slug.'/tours';

        $response = $this->get($endPoint.'?priceFrom=100');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endPoint.'?priceFrom=150');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endPoint.'?priceFrom=250');
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endPoint.'?priceTo=200');
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);

        $response = $this->get($endPoint.'?priceTo=150');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonFragment(['id' => $cheapTour->id]);
        $response->assertJsonMissing(['id' => $expensiveTour->id]);

        $response = $this->get($endPoint.'?priceTo=50');
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endPoint.'?priceFrom=150&priceTo=250');
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $cheapTour->id]);
        $response->assertJsonFragment(['id' => $expensiveTour->id]);
    }

    public function test_tours_list_filters_by_starting_date_correctly(): void
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
            'ending_date' => now()->addDays(1),
        ]);

        $endPoint = 'api/v1/travels/'.$travels->slug.'/tours';

        //        $response = $this->get($endPoint . '?dateFrom='.now());
        //        $response->assertStatus(200);
        //        $response->assertJsonCount(2, 'data');
        //        $response->assertJsonFragment(['id' => $laterTour->id]);
        //        $response->assertJsonFragment(['id' => $earlierTour->id]);

        $response = $this->get($endPoint.'?dateFrom='.now()->addDay());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

        $response = $this->get($endPoint.'?dateFrom='.now()->addDays(5));
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endPoint.'?dateTo='.now()->addDays(5));
        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);

        $response = $this->get($endPoint.'?dateTo='.now()->addDay());
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $laterTour->id]);
        $response->assertJsonFragment(['id' => $earlierTour->id]);

        $response = $this->get($endPoint.'?dateTo='.now()->subDay());
        $response->assertStatus(200);
        $response->assertJsonCount(0, 'data');

        $response = $this->get($endPoint.'?dateFrom='.now()->addDay().'&dateTo='.now()->addDays(5));
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data');
        $response->assertJsonMissing(['id' => $earlierTour->id]);
        $response->assertJsonFragment(['id' => $laterTour->id]);
    }
}

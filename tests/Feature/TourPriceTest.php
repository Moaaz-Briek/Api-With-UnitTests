<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourPriceTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_price_is_shown_correctly(): void
    {
        $travels = Travel::factory()->create();

        Tour::factory()->create([
            'travel_id' => $travels->id,
            'price' => 125.65,
        ]);

        $response = $this->get('api/v1/travels/'.$travels->slug.'/tours');

        $response->assertStatus(200);

        $response->assertJsonCount(1, 'data');

        $response->assertJsonFragment(['price' => '125.65']);
    }
}

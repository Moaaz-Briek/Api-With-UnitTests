<?php

namespace Tests\Feature;

use App\Models\Travel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TourValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_tours_list_returns_validation_errors(): void
    {
        $travels = Travel::factory()->create();

        $endPoint = 'api/v1/travels/'.$travels->slug.'/tours';

        $response = $this->getJson($endPoint.'?dateFrom=saxs');
        $response->assertStatus(422);

        $response = $this->getJson($endPoint.'?priceFrom=saxs');
        $response->assertStatus(422);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Pet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PetRepositoryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/api/v1/pets/all');

        $response->assertStatus(200);
    }

    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/api/v1/pets/cursor-paginate');

        $response->assertStatus(200);
    }

    public function test_index_returns_successful_response_with_default_pagination(): void
    {
        $response = $this->getJson('/api/v1/pets');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ])
            ->assertJson([
                'status' => 'success',
            ])->assertStatus(200);
    }

    public function test_index_returns_successful_response_with_filtered_results(): void
    {
        Pet::factory()->create(['name' => 'Bochok']);

        $response = $this->getJson('/api/v1/pets?search=Bochok');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ],
            ])
            ->assertJson([
                'status' => 'success',
                'data' => [
                    [
                        'name' => 'Bochok',
                    ],
                ],
            ]);
    }

    public function test_index_returns_empty_data_array_when_search_does_not_exist(): void
    {
        $response = $this->getJson('api/v1/pets?search=NonExistingPets');
        
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [],
            'meta' => [
                'current_page' => 1,
                'last_page' => 1,
                'per_page' => 15,
                'total' => 0,
            ],
        ]);
    }

}
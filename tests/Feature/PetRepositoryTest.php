<?php

namespace Tests\Feature;

use App\Models\Pet;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\QRCodeService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Nette\Utils\Random;
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
    // @INDEX

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

    public function test_index_returns_successful_response_with_empty_search_query(): void
    {
        $response = $this->getJson('api/v1/pets?search=');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data',
                'meta' => [
                    'current_page',
                    'last_page',
                    'per_page',
                    'total',
                ]
            ])->assertJson([
                    'status' => 'success'
                ]);
    }


    // @STORE 
    public function test_pet_can_be_created_successfully()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/pets', [
            'name' => 'Bochok',
            'species' => 'Dog',
            'breed' => 'Shiba Inu',
            'color' => 'Brown',
            'age' => 3,
            'weight' => 10.5,
            'gender' => 'male',
            'is_vaccinated' => true,
            'is_neutered' => false,
            'profile_image_url' => UploadedFile::fake()->image('pet.jpg'),
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pet information was saved successfully',
                'data' => [
                    'name' => 'Bochok',
                    'species' => 'Dog',
                    'breed' => 'Shiba Inu',
                    'color' => 'Brown',
                    'age' => 3,
                    'weight' => 10.5,
                    'gender' => 'male',
                    'is_vaccinated' => true,
                    'is_neutered' => false,
                ], // Only assert essential fields
            ]);

        // // Get the actual stored path from the response
        // $storedPath = $response->json('data.profile_image_url');

        // // Remove any leading slashes or storage path prefixes
        // $storedPath = ltrim($storedPath, '/');
        // $storedPath = str_replace('storage/', '', $storedPath);

        // Assert the file exists in the fake storage
        // Storage::disk('public')->assertExists($storedPath);

        $this->assertNotNull($response->json('data.qr_code_url'));
        $this->assertNotNull($response->json('data.qr_code_path'));
    }

    public function test_pet_creation_fails_with_invalid_data()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/pets', [
            'name' => '', // Invalid (empty)
            'species' => 'Cat',
        ]);

        $response->assertStatus(422) // Validation error
            ->assertJsonValidationErrors(['name']);
    }

    public function test_pet_creation_fails_without_authentication()
    {
        $response = $this->postJson('/api/v1/pets', [
            'name' => 'Bochok',
            'species' => 'Dog',
        ]);

        $response->assertStatus(401); // Unauthorized
    }

    public function test_pet_creation_handles_file_upload_failure()
    {
        Storage::fake('public');
        Storage::shouldReceive('putFileAs')->andReturn(false); // Force upload failure

        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson('/api/v1/pets', [
            'name' => 'Bochok',
            'species' => 'Dog',
            'breed' => 'Shiba Inu',
            'color' => 'Brown',
            'age' => 3,
            'weight' => 10.5,
            'gender' => 'male',
            'is_vaccinated' => true,
            'is_neutered' => false,
            'profile_image_url' => UploadedFile::fake()->image('pet.jpg'),
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'status' => 'error',
                'message' => 'An error occurred while saving pet information',
            ]);
    }


}
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
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (
            config('database.default') === 'sqlite' &&
            !in_array(RefreshDatabase::class, class_uses($this))
        ) {
            throw new \Exception('Define RefreshDatabase Trait');
        }
    }


    // add test env for safe access
    public function test_environment_check()
    {
        dd(
            [
                "ACTIVE ENV" => app()->environment(), // Should show "testing"
                "DB_CONNECTION" => config('database.default'), // Should show "sqlite"
                "DB_DATABASE" => config('database.connections.sqlite.database') // Should show ":memory:"]
            ]
        );
    }

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
            'name' => 'Bochoky',
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
                    'name' => 'Bochoky',
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

    // @UPDATE
    public function test_pet_can_be_updated_successfully()
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['owner_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->putJson("/api/v1/pets/{$pet->id}", [
            'name' => 'Updated Name',
            'species' => 'Cat', // Changed from Dog
            'breed' => 'Persian',
            'color' => 'White',
            'age' => 4,
            'weight' => 8.2,
            'gender' => 'female',
            'is_vaccinated' => false,
            'is_neutered' => true,
            'profile_image_url' => UploadedFile::fake()->image('new_pet.jpg'),
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pet information updated successfully',
                'data' => [
                    'name' => 'Updated Name',
                    'species' => 'Cat',
                    'breed' => 'Persian',
                    'color' => 'White',
                    'age' => 4,
                    'weight' => 8.2,
                    'gender' => 'female',
                    'is_vaccinated' => false,
                    'is_neutered' => true,
                ]
            ]);

        // Assert file was uploaded
        $storedPath = $response->json('data.profile_image_url');
        $storedPath = ltrim(str_replace('storage/', '', $storedPath), '/');
        Storage::disk('public')->assertExists($storedPath);

        // Assert old file was deleted (if your service does this)
        Storage::disk('public')->assertMissing('old/path/to/image.jpg');
    }

    public function test_pet_update_validates_required_fields()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create(['owner_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->putJson("/api/v1/pets/{$pet->id}", [
            'name' => '', // Invalid empty name
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_pet_update_fails_for_unauthorized_users()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $pet = Pet::factory()->create(['owner_id' => $owner->id]);

        $this->actingAs($otherUser);

        $response = $this->putJson("/api/v1/pets/{$pet->id}", [
            'name' => 'Should Not Work',
        ]);

        $response->assertStatus(403); // Forbidden
    }

    public function test_pet_update_handles_file_upload_failure()
    {
        Storage::fake('public');
        Storage::shouldReceive('putFileAs')->andReturn(false); // Force upload failure

        $user = User::factory()->create();
        $pet = Pet::factory()->create(['owner_id' => $user->id]);
        $this->actingAs($user);

        $response = $this->putJson("/api/v1/pets/{$pet->id}", [
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
                'message' => 'An error occurred while updating pet information',
            ]);
    }


    // @DELETE
    public function test_owner_can_soft_delete_pet()
    {
        $owner = User::factory()->create();
        $pet = Pet::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)
            ->deleteJson("/api/v1/pets/{$pet->id}");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pet deleted successfully',
            ]);

        // Assert soft delete
        $this->assertSoftDeleted($pet);
        $this->assertDatabaseHas('pets', ['id' => $pet->id]);
    }

    public function test_owner_can_force_delete_pet()
    {
        $owner = User::factory()->create();
        $pet = Pet::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($owner)
            ->deleteJson("/api/v1/pets/{$pet->id}/force-delete");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pet force deleted successfully',
            ]);

        // Assert complete removal
        $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
    }

    public function test_non_owner_cannot_delete_pet()
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $pet = Pet::factory()->create(['owner_id' => $owner->id]);

        $response = $this->actingAs($otherUser)
            ->deleteJson("/api/v1/pets/{$pet->id}");

        $response->assertStatus(403);
        $this->assertNotSoftDeleted($pet);
    }

    public function test_guest_cannot_delete_pet()
    {
        $pet = Pet::factory()->create();

        $response = $this->deleteJson("/api/v1/pets/{$pet->id}");

        $response->assertStatus(401);
    }

    public function test_deleting_nonexistent_pet_returns_404()
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)
            ->deleteJson("/api/v1/pets/99999");

        $response->assertStatus(404);
    }

    public function test_force_delete_removes_all_related_files()
    {
        Storage::fake('public');

        $owner = User::factory()->create();
        $pet = Pet::factory()->create([
            'owner_id' => $owner->id,
            'file_path' => "pets/image.jpg",
            'qr_code_path' => "pets/qrcode.png"
        ]);

        Storage::disk()->put($pet->file_path, 'dummy content');
        Storage::disk()->put($pet->qr_code_path, 'dummy content');

        $this->assertTrue(Storage::disk()->exists($pet->file_path));
        $this->assertTrue(Storage::disk()->exists($pet->qr_code_path));

        $response = $this->actingAs($owner)->deleteJson("/api/v1/pets/{$pet->id}/force-delete");

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Pet force deleted successfully',
            ]);

        $this->assertDatabaseMissing('pets', ['id' => $pet->id]);
        Storage::disk()->assertMissing($pet->file_path);
        Storage::disk()->assertMissing($pet->qr_code_path);
    }
}
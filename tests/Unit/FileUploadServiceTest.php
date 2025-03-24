<?php

namespace Tests\Unit;

use App\Services\FileUploadService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\TestCase;

class FileUploadServiceTest extends TestCase
{
    protected FileUploadService $fileUploadService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fileUploadService = new FileUploadService();
    }

    // Invokable method to assess private modifiers (emptyImageReponse)
    protected function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    // Empty Response
    public function test_empty_image_response_keys(): void
    {
        $response = $this->fileUploadService->upload(null);

        $this->assertArrayHasKey('profile_image_url', $response);
        $this->assertArrayHasKey('file_name', $response);
        $this->assertArrayHasKey('file_path', $response);

        $this->assertNull($response['profile_image_url']);
        $this->assertNull($response['file_name']);
        $this->assertNull($response['file_path']);
    }

    public function test_empty_image_response_returns_null_values(): void
    {
        $response = $this->invokeMethod($this->fileUploadService, 'emptyImageResponse');

        $this->assertIsArray($response);
        $this->assertNull($response['profile_image_url']);
        $this->assertNull($response['file_name']);
        $this->assertNull($response['file_path']);
    }

    public function test_empty_image_response_has_exactly_three_keys(): void
    {
        $response = $this->invokeMethod($this->fileUploadService, 'emptyImageResponse');

        $this->assertCount(3, $response);
        $this->assertArrayHasKey('profile_image_url', $response);
        $this->assertArrayHasKey('file_name', $response);
        $this->assertArrayHasKey('file_path', $response);
    }

    public function test_empty_image_response_consistent_structure_across_multiple_calls(): void
    {
        $response1 = $this->invokeMethod($this->fileUploadService, 'emptyImageResponse');
        $response2 = $this->invokeMethod($this->fileUploadService, 'emptyImageResponse');

        $this->assertSame($response1, $response2);
        $this->assertArrayHasKey('profile_image_url', $response1);
        $this->assertArrayHasKey('file_name', $response1);
        $this->assertArrayHasKey('file_path', $response1);
    }

    public function test_empty_image_response_return_type_is_array(): void
    {

        $response = $this->invokeMethod($this->fileUploadService, 'emptyImageResponse');

        $this->assertIsArray($response);
    }

    // UPLOAD
    public function test_upload_stores_file_in_specified_directory_and_returns_correct_file_path_and_url(): void
    {
        $mockFile = $this->createMock(UploadedFile::class);
        $mockFile->method('store')->willReturn('pet_image/test-file.jpg');
        $mockFile->method('getClientOriginalName')->willReturn('test-file.jpg');

        Storage::shouldReceive('url')
            ->once()
            ->with('pet_image/test-file.jpg')
            ->andReturn('/storage/pet_image/test-file.jpg');

        $response = $this->fileUploadService->upload($mockFile, 'pet_image');

        $this->assertIsArray($response);
        $this->assertEquals('/storage/pet_image/test-file.jpg', $response['profile_image_url']);
        $this->assertEquals('test-file.jpg', $response['file_name']);
        $this->assertEquals('pet_image/test-file.jpg', $response['file_path']);
    }

    public function test_upload_deletes_existing_file_if_file_path_is_provided_and_exists(): void
    {
        $mockFile = $this->createMock(UploadedFile::class);
        $mockFile->method('store')->willReturn('pet_image/new-file.jpg');
        $mockFile->method('getClientOriginalName')->willReturn('new-file.jpg');

        $existingFilePath = 'pet_image/existing-file.jpg';

        Storage::shouldReceive('exists')
            ->once()
            ->with($existingFilePath)
            ->andReturn(true);

        Storage::shouldReceive('delete')
            ->once()
            ->with($existingFilePath);

        Storage::shouldReceive('url')
            ->once()
            ->with('pet_image/new-file.jpg')
            ->andReturn('/storage/pet_image/new-file.jpg');

        $response = $this->fileUploadService->upload($mockFile, 'pet_image', $existingFilePath);

        $this->assertIsArray($response);
        $this->assertEquals('/storage/pet_image/new-file.jpg', $response['profile_image_url']);
        $this->assertEquals('new-file.jpg', $response['file_name']);
        $this->assertEquals('pet_image/new-file.jpg', $response['file_path']);
    }

    public function test_upload_returns_correct_original_file_name(): void
    {
        $mockFile = $this->createMock(UploadedFile::class);
        $mockFile->method('store')->willReturn('pet_image/test-file.jpg');
        $mockFile->method('getClientOriginalName')->willReturn('test-file.jpg');

        Storage::shouldReceive('url')
            ->once()
            ->with('pet_image/test-file.jpg')
            ->andReturn('/storage/pet_image/test-file.jpg');

        $response = $this->fileUploadService->upload($mockFile, 'pet_image');

        $this->assertIsArray($response);
        $this->assertEquals('test-file.jpg', $response['file_name']);
    }

    public function test_upload_handles_file_when_directory_is_empty_string(): void
    {
        $mockFile = $this->createMock(UploadedFile::class);
        $mockFile->method('store')->willReturn('test-file.jpg');
        $mockFile->method('getClientOriginalName')->willReturn('test-file.jpg');

        Storage::shouldReceive('url')
            ->once()
            ->with('test-file.jpg')
            ->andReturn('/storage/test-file.jpg');

        $response = $this->fileUploadService->upload($mockFile, '');

        $this->assertIsArray($response);
        $this->assertEquals('/storage/test-file.jpg', $response['profile_image_url']);
        $this->assertEquals('test-file.jpg', $response['file_name']);
        $this->assertEquals('test-file.jpg', $response['file_path']);
    }

    public function test_upload_handles_file_when_directory_is_custom_string(): void
    {
        $mockFile = $this->createMock(UploadedFile::class);
        $mockFile->method('store')->willReturn('custom_directory/test-file.jpg');
        $mockFile->method('getClientOriginalName')->willReturn('test-file.jpg');

        Storage::shouldReceive('url')
            ->once()
            ->with('custom_directory/test-file.jpg')
            ->andReturn('/storage/custom_directory/test-file.jpg');

        $response = $this->fileUploadService->upload($mockFile, 'custom_directory');

        $this->assertIsArray($response);
        $this->assertEquals('/storage/custom_directory/test-file.jpg', $response['profile_image_url']);
        $this->assertEquals('test-file.jpg', $response['file_name']);
        $this->assertEquals('custom_directory/test-file.jpg', $response['file_path']);
    }

    public function test_upload_handles_file_when_existing_file_path_is_null(): void
    {
        $mockFile = $this->createMock(UploadedFile::class);
        $mockFile->method('store')->willReturn('pet_image/test-file.jpg');
        $mockFile->method('getClientOriginalName')->willReturn('test-file.jpg');

        Storage::shouldReceive('url')
            ->once()
            ->with('pet_image/test-file.jpg')
            ->andReturn('/storage/pet_image/test-file.jpg');

        $response = $this->fileUploadService->upload($mockFile, 'pet_image', null);

        $this->assertIsArray($response);
        $this->assertEquals('/storage/pet_image/test-file.jpg', $response['profile_image_url']);
        $this->assertEquals('test-file.jpg', $response['file_name']);
        $this->assertEquals('pet_image/test-file.jpg', $response['file_path']);
    }

    public function test_upload_returns_valid_public_url_for_uploaded_file(): void
    {
        $mockFile = $this->createMock(UploadedFile::class);
        $mockFile->method('store')->willReturn('pet_image/test-file.jpg');
        $mockFile->method('getClientOriginalName')->willReturn('test-file.jpg');

        Storage::shouldReceive('url')
            ->once()
            ->with('pet_image/test-file.jpg')
            ->andReturn('/storage/pet_image/test-file.jpg');

        $response = $this->fileUploadService->upload($mockFile, 'pet_image');

        $this->assertIsArray($response);
        $this->assertEquals('/storage/pet_image/test-file.jpg', $response['profile_image_url']);
    }
}
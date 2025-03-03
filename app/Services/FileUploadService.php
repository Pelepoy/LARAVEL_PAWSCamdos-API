<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadService
{
	private function emptyImageResponse(): array
	{
		return [
			'profile_image_url' => null,
			'file_name' => null,
			'file_path' => null,
		];
	}

	/**
	 * This method handles the uploading of a file to the specified directory.
	 * If no file is provided, it returns an array with null values.
	 * Otherwise, it stores the file and returns an array containing the public URL, original file name, and file path.
	 * @param UploadedFile|null $file The file to be uploaded.
	 * @param string $directory The directory where the file should be stored. Defaults to 'pet_image'.
	 * @return array An array containing 'profile_image_url', 'file_name', and 'file_path'.
	 */

	public function upload(?UploadedFile $file, string $directory = 'pet_image', ?string $existingFilePath = null): array
	{
		// If no file is uploaded, return an empty|null array
		if (is_null(value: $file)) {
			return $this->emptyImageResponse();
		}

		// Delete the old file if it exists | for update compatibility
    	if ($existingFilePath && Storage::exists($existingFilePath)) {
			Storage::delete($existingFilePath);
		}

		$filePath = $file->store($directory); // Stores in storage/app/pet_images (default)
		$fileName = $file->getClientOriginalName();
		return [
			'profile_image_url' => Storage::url($filePath), // Public URL
			'file_name' => $fileName, // Original file name
			'file_path' => $filePath, // File path
		];
	}
}
<?php
namespace App\Services;

use App\Models\Pet;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Auth\User;

class PetService
{
    public function __construct(
        protected FileUploadService $fileUploadService,
        protected QRCodeService $qrcodeService
    ) {
    }

    public function createPet(array $validatedData, ?UploadedFile $profileImage, User $user): Pet
    {
        // Handle image upload
        $uploadData = $this->fileUploadService->upload($profileImage);

        // \Log::info("UPLOADED DATA" . $uploadData);
        $data = array_merge($validatedData, $uploadData);

        // Create pet record
        $pet = $user->pets()->create($data);

        // Generate and store QR code
        $qrCode = $this->qrcodeService->generateAndStore($pet->id);

        // Update pet with QR code information
        $pet->updateOrFail([
            'qr_code_url' => $qrCode['url'],
            'qr_code_path' => $qrCode['file_path']
        ]);

        return $pet;

    }


}
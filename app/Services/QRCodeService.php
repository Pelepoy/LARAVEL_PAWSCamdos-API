<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generates a QR code image, stores it, and returns its URL and file path.
     *
     * This method encodes the given data into a QR code, saves it as a PNG image in storage,
     * and returns both the public URL and the storage path of the generated QR code.
     *
     * @param int $petId The ID of the pet for which the QR code is generated.
     * @return array Contains:
     *               - 'url' (string): The publicly accessible URL of the stored QR code image.
     *               - 'file_path' (string): The internal storage path of the QR code image.
     */

    public function generateAndStore(string $petId): array
    {
        // Construct the QR code URL inside the service
        $qrCodeData = env('FRONTEND_URL') . "/pet/{$petId}";

        // Generate the QR code as a PNG image
        $qrCode = QRCode::format('png')->size(300)->generate($qrCodeData);

        // Define storage path
        // $fileName = sprintf('pet_%d.png', $petId);
        $qrPath = 'qrcodes/' . $petId . '.png';

        // Store the QR code in the default storage disk
        Storage::disk()->put($qrPath, $qrCode);

        // Return both the path and public URL
        return [
            'file_path' => $qrPath, // Storage path for deletion
            'url' => Storage::url($qrPath) // Public URL for display
        ];
    }
}
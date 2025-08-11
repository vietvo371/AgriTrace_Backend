<?php

namespace App\Services;

use App\Models\Batch;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class QrCodeService
{
    /**
     * Generate QR code for a batch
     *
     * @param Batch $batch
     * @return string QR code image path
     */
    public function generateQrCode(Batch $batch): string
    {
        // Generate QR code content URL
        $url = route('batches.show', $batch->id);

        // Generate QR code image
        $qrCode = QrCode::format('png')
            ->size(300)
            ->errorCorrection('H')
            ->generate($url);

        // Generate unique filename
        $filename = 'qrcodes/' . uniqid('qr_') . '.png';

        // Store QR code image
        Storage::disk('public')->put($filename, $qrCode);

        // Set QR code expiry date (30 days from now)
        $batch->update([
            'qr_code' => $filename,
            'qr_expiry' => Carbon::now()->addDays(30),
        ]);

        return $filename;
    }

    /**
     * Check if QR code is valid
     *
     * @param Batch $batch
     * @return bool
     */
    public function isQrCodeValid(Batch $batch): bool
    {
        if (!$batch->qr_expiry) {
            return false;
        }

        return Carbon::now()->lt($batch->qr_expiry);
    }

    /**
     * Log QR code access
     *
     * @param Batch $batch
     * @param string|null $ipAddress
     * @param string|null $deviceInfo
     * @return void
     */
    public function logQrAccess(Batch $batch, ?string $ipAddress = null, ?string $deviceInfo = null): void
    {
        $batch->accessLogs()->create([
            'access_time' => Carbon::now(),
            'ip_address' => $ipAddress,
            'device_info' => $deviceInfo,
        ]);
    }
}

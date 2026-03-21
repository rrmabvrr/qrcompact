<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeService
{
    public function toDataUri(string $data, int $size = 300): string
    {
        $result = new Builder(
            writer: new PngWriter(),
            data: trim($data),
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Medium,
            size: $size,
            margin: 8,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
        );

        return $result->build()->getDataUri();
    }
}

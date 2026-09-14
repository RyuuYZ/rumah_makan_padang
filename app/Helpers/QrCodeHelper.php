<?php

namespace App\Helpers;

use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRMarkupSVG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrCodeHelper
{
    /**
     * Generate base64 SVG QR Code string that can be used directly in <img src="">
     */
    public static function generate(string $data, int $size = 300): string
    {
        $options = new QROptions([
            'outputType' => QRMarkupSVG::class,
            'eccLevel' => EccLevel::L,
            'svgViewBoxSize' => $size,
            'imageBase64' => true, // Ensure it outputs base64 for img src
        ]);

        return (new QRCode($options))->render($data);
    }
}

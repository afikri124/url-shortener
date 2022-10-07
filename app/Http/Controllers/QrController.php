<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QrController extends Controller
{
    public function qrcode()
    {
        $writer = new PngWriter();

        // Create QR code
        $qrCode = QrCode::create(route('index'))
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create(public_path('/assets/img/logo-jgu-white.png'))
            ->setResizeToWidth(80);

        // Create generic label
        $label = Label::create(route('index'))
            ->setTextColor(new Color(255, 0, 0));

        $result = $writer->write($qrCode, $logo, $label);
        
        $dataUri = $result->getDataUri();
        echo '<img src="'.$dataUri.'" alt="Sobatcoding.com">';
    }
}

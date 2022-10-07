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
use Intervention\Image\Facades\Image;

class QrController extends Controller
{
    public function qrcode(Request $request)
    {
        $data = route('qrcode')."?data=X";
        if($request->data){
            $data = $request->data;
        }
        $writer = new PngWriter();
        
        // Create QR code
        $qrCode = QrCode::create($data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(350)
            ->setMargin(0)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        // Create generic logo
        $logo = Logo::create(public_path('/assets/img/logo-jgu-white.png'))->setResizeToWidth(80);       

        if(!$request->data){
            // Create generic label
            $label = Label::create($data)
            ->setTextColor(new Color(255, 0, 0));
            $result = $writer->write($qrCode, $logo, $label);
        } elseif($request->label) {
            $label = Label::create($request->label)
            ->setTextColor(new Color(255, 0, 0));
            $result = $writer->write($qrCode, $logo, $label);
        } else {
            $result = $writer->write($qrCode, $logo);
        }
        
        $dataUri = $result->getDataUri();

        return Image::make($dataUri)->response();
    }
}

<?php

class QRimage
{
    public static function png($frame, $filename = false, $pixelPerPoint = 4, $outerFrame = 4, $saveandprint = false)
    {
        $image = self::image($frame, $pixelPerPoint, $outerFrame);
        if ($filename === false) {
            header('Content-type: image/png');
            imagepng($image);
        } else {
            if ($saveandprint === true) {
                imagepng($image, $filename);
                header('Content-type: image/png');
                imagepng($image);
            } else {
                imagepng($image, $filename);
            }
        }
        imagedestroy($image);
    }
    public static function jpg($frame, $filename = false, $pixelPerPoint = 8, $outerFrame = 4, $q = 85)
    {
        $image = self::image($frame, $pixelPerPoint, $outerFrame);
        if ($filename === false) {
            header('Content-type: image/jpeg');
            imagejpeg($image, NULL, $q);
        } else {
            imagejpeg($image, $filename, $q);
        }
        imagedestroy($image);
    }
    private static function image($frame, $pixelPerPoint = 4, $outerFrame = 4)
    {
        $h = count($frame);
        $w = strlen($frame[0]);
        $imgW = $w + 2 * $outerFrame;
        $imgH = $h + 2 * $outerFrame;
        $base_image = imagecreate($imgW, $imgH);
        $col[0] = imagecolorallocate($base_image, 255, 255, 255);
        $col[1] = imagecolorallocate($base_image, 0, 0, 0);
        imagefill($base_image, 0, 0, $col[0]);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                if ($frame[$y][$x] == '1') {
                    imagesetpixel($base_image, $x + $outerFrame, $y + $outerFrame, $col[1]);
                }
            }
        }
        $target_image = imagecreate($imgW * $pixelPerPoint, $imgH * $pixelPerPoint);
        imagecopyresized($target_image, $base_image, 0, 0, 0, 0, $imgW * $pixelPerPoint, $imgH * $pixelPerPoint, $imgW, $imgH);
        imagedestroy($base_image);
        return $target_image;
    }
}
define('QR_IMAGE', true);
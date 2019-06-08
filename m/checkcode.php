<?php

class seccode
{
    public $code;
    public $width = 0;
    public $height = 0;
    public $shadow = 1;
    public $color = 1;
    public $fontcolor;
    public $im;
    public function display()
    {
        $bgcontent = $this->background();
        $this->im = imagecreatefromstring($bgcontent);
        $this->adulterate();
        $this->giffont();
        if (function_exists('imagepng')) {
            header('Content-type: image/png');
            imagepng($this->im);
        } else {
            header('Content-type: image/jpeg');
            imagejpeg($this->im, '', 100);
        }
        imagedestroy($this->im);
    }
    public function background()
    {
        $this->im = imagecreatetruecolor($this->width, $this->height);
        $backgroundcolor = imagecolorallocate($this->im, 255, 255, 255);
        $backgrounds = $c = array();
        for ($i = 0; $i < 3; $i++) {
            $start[$i] = mt_rand(200, 255);
            $end[$i] = mt_rand(100, 150);
            $step[$i] = ($end[$i] - $start[$i]) / $this->width;
            $c[$i] = $start[$i];
        }
        for ($i = 0; $i < $this->width; $i++) {
            $color = imagecolorallocate($this->im, $c[0], $c[1], $c[2]);
            imageline($this->im, $i, 0, $i, $this->height, $color);
            $c[0] += $step[0];
            $c[1] += $step[1];
            $c[2] += $step[2];
        }
        $c[0] -= 20;
        $c[1] -= 20;
        $c[2] -= 20;
        ob_start();
        if (function_exists('imagepng')) {
            imagepng($this->im);
        } else {
            imagejpeg($this->im, '', 100);
        }
        imagedestroy($this->im);
        $bgcontent = ob_get_contents();
        ob_end_clean();
        $this->fontcolor = $c;
        return $bgcontent;
    }
    public function adulterate()
    {
        $linenums = $this->height / 10;
        for ($i = 0; $i <= $linenums; $i++) {
            $color = imagecolorallocate($this->im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
            $x = mt_rand(0, $this->width);
            $y = mt_rand(0, $this->height);
            if (mt_rand(0, 1)) {
                imagearc($this->im, $x, $y, mt_rand(0, $this->width), mt_rand(0, $this->height), mt_rand(0, 360), mt_rand(0, 360), $color);
            } else {
                $linemaxlong = isset($linemaxlong) ? $linemaxlong : 0;
                $linex = isset($linex) ? $linex : 0;
                $liney = isset($liney) ? $liney : 0;
                imageline($this->im, $x, $y, $linex + mt_rand(0, $linemaxlong), $liney + mt_rand(0, mt_rand($this->height, $this->width)), $color);
            }
        }
    }
    public function giffont()
    {
        $seccode = $this->code;
        $widthtotal = 0;
        for ($i = 0; $i <= strlen($this->code) - 1; $i++) {
            $font[$i]['file'] = '';
            $font[$i]['width'] = 8 + mt_rand(0, $this->width / 5 - 5);
            $widthtotal += $font[$i]['width'];
        }
        $x = mt_rand(1, $this->width - $widthtotal);
        for ($i = 0; $i <= strlen($this->code) - 1; $i++) {
            $this->color && ($this->fontcolor = array(mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)));
            $y = mt_rand(0, $this->height - 20);
            if ($this->shadow) {
                $text_shadowcolor = imagecolorallocate($this->im, 255 - $this->fontcolor[0], 255 - $this->fontcolor[1], 255 - $this->fontcolor[2]);
                imagechar($this->im, 5, $x + 1, $y + 1, $seccode[$i], $text_shadowcolor);
            }
            $text_color = imagecolorallocate($this->im, $this->fontcolor[0], $this->fontcolor[1], $this->fontcolor[2]);
            imagechar($this->im, 5, $x, $y, $seccode[$i], $text_color);
            $x += $font[$i]['width'];
        }
    }
}
function jieqi_randcode($len)
{
    $str = '1234567890';
    $result = '';
    $l = strlen($str) - 1;
    srand((double) microtime() * 1000000);
    for ($i = 0; $i < $len; $i++) {
        $num = rand(0, $l);
        $result .= $str[$num];
    }
    return $result;
}
function draw_digit($x, $y, $digit)
{
    global $sx;
    global $sy;
    global $pixels;
    global $digits;
    global $lines;
    $digit = $digits[$digit];
    $m = 6;
    $b = 1;
    for ($i = 0; $i < 7; $i++, $b *= 2) {
        if (($b & $digit) == $b) {
            $j = $i * 4;
            $x0 = $lines[$j] * $m + $x;
            $y0 = $lines[$j + 1] * $m + $y;
            $x1 = $lines[$j + 2] * $m + $x;
            $y1 = $lines[$j + 3] * $m + $y;
            if ($x0 == $x1) {
                $ofs = 3 * ($sx * $y0 + $x0);
                for ($h = $y0; $h <= $y1; $h++, $ofs += 3 * $sx) {
                    $pixels[$ofs] = chr(0);
                    $pixels[$ofs + 1] = chr(0);
                    $pixels[$ofs + 2] = chr(0);
                }
            } else {
                $ofs = 3 * ($sx * $y0 + $x0);
                for ($w = $x0; $w <= $x1; $w++) {
                    $pixels[$ofs++] = chr(0);
                    $pixels[$ofs++] = chr(0);
                    $pixels[$ofs++] = chr(0);
                }
            }
        }
    }
}
function add_chunk($type)
{
    global $result;
    global $data;
    global $chunk;
    global $crc_table;
    $len = strlen($data);
    $chunk = pack('c*', $len >> 24 & 255, $len >> 16 & 255, $len >> 8 & 255, $len & 255);
    $chunk .= $type;
    $chunk .= $data;
    $z = 16777215;
    $z |= 255 << 24;
    $c = $z;
    for ($n = 4; $n < strlen($chunk); $n++) {
        $c8 = $c >> 8 & 16777215;
        $c = $crc_table[($c ^ ord($chunk[$n])) & 255] ^ $c8;
    }
    $crc = $c ^ $z;
    $chunk .= chr($crc >> 24 & 255);
    $chunk .= chr($crc >> 16 & 255);
    $chunk .= chr($crc >> 8 & 255);
    $chunk .= chr($crc & 255);
    $result .= $chunk;
}
function seccodeconvert($seccode)
{
    $s = sprintf('%04s', base_convert($seccode, 10, 20));
    $seccodeunits = 'CEFHKLMNOPQRSTUVWXYZ';
    $seccode = '';
    for ($i = 0; $i < 4; $i++) {
        $unit = ord($s[$i]);
        $seccode .= 48 <= $unit && $unit <= 57 ? $seccodeunits[$unit - 48] : $seccodeunits[$unit - 87];
    }
    return $seccode;
}
define('JIEQI_MODULE_NAME', 'system');
define('JIEQI_NOCONVERT_CHAR', '1');
define('JIEQI_NEED_SESSION', 1);
define('JIEQI_IS_OPEN', 1);
require_once 'global.php';
jieqi_getconfigs(JIEQI_MODULE_NAME, 'configs');
define('CHECK_CODE_LENGTH', 4);
define('CHECK_CODE_HEIGHT', 14);
define('CHECK_CODE_WIDTH', 10);
define('CHECK_CODE_SPACEX', 6);
define('CHECK_CODE_SPACEY', 4);
define('CHECK_CODE_SPACEM', 3);
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-type: image/png');
$digits = array(95, 5, 118, 117, 45, 121, 123, 69, 127, 125);
$lines = array(1, 1, 1, 2, 0, 1, 0, 2, 1, 0, 1, 1, 0, 0, 0, 1, 0, 2, 1, 2, 0, 1, 1, 1, 0, 0, 1, 0);
$sx = CHECK_CODE_WIDTH * CHECK_CODE_LENGTH + CHECK_CODE_SPACEM * (CHECK_CODE_LENGTH - 1) + CHECK_CODE_SPACEX + CHECK_CODE_SPACEX;
$sy = CHECK_CODE_HEIGHT + CHECK_CODE_SPACEY + CHECK_CODE_SPACEY;
$checkcode = jieqi_randcode(CHECK_CODE_LENGTH);
$_SESSION['jieqiCheckCode'] = $checkcode;
$captcha_file = JIEQI_ROOT_PATH . '/lib/kcaptcha/kcaptcha.php';
if ($_GET['type'] == 'kcaptcha' && function_exists('gd_info') && is_file($captcha_file)) {
    include_once $captcha_file;
    $captcha = new KCAPTCHA();
    $_SESSION['jieqiCheckCode'] = $captcha->getKeyString();
} else {
    if (function_exists('gd_info')) {
        $code = new seccode();
        $code->code = $checkcode;
        $code->width = $sx;
        $code->height = $sy;
        $code->display();
        $_SESSION['jieqiCheckCode'] = $code->code;
    } else {
        $pixels = '';
        for ($h = 0; $h < $sy; $h++) {
            for ($w = 0; $w < $sx; $w++) {
                $r = 100 / $sx * $w + 155;
                $g = 100 / $sy * $h + 155;
                $b = 255 - 100 / ($sx + $sy) * ($w + $h);
                $pixels .= chr($r);
                $pixels .= chr($g);
                $pixels .= chr($b);
            }
        }
        $x = CHECK_CODE_SPACEX;
        for ($i = 0; $i < CHECK_CODE_LENGTH; $i++) {
            draw_digit($x, CHECK_CODE_SPACEY, substr($checkcode, $i, 1));
            $x += CHECK_CODE_WIDTH + CHECK_CODE_SPACEM;
        }
        $z = -306674912;
        for ($n = 0; $n < 256; $n++) {
            $c = $n;
            for ($k = 0; $k < 8; $k++) {
                $c2 = $c >> 1 & 2147483647;
                if ($c & 1) {
                    $c = $z ^ $c2;
                } else {
                    $c = $c2;
                }
            }
            $crc_table[$n] = $c;
        }
        $result = pack('c*', 137, 80, 78, 71, 13, 10, 26, 10);
        $data = pack('c*', $sx >> 24 & 255, $sx >> 16 & 255, $sx >> 8 & 255, $sx & 255, $sy >> 24 & 255, $sy >> 16 & 255, $sy >> 8 & 255, $sy & 255, 8, 2, 0, 0, 0);
        add_chunk('IHDR');
        $len = ($sx * 3 + 1) * $sy;
        $data = pack('c*', 120, 1, 1, $len & 255, $len >> 8 & 255, 255 - ($len & 255), 255 - ($len >> 8 & 255));
        $start = strlen($data);
        $i2 = 0;
        for ($h = 0; $h < $sy; $h++) {
            $data .= chr(0);
            for ($w = 0; $w < $sx * 3; $w++) {
                $data .= $pixels[$i2++];
            }
        }
        $s1 = 1;
        $s2 = 0;
        for ($n = $start; $n < strlen($data); $n++) {
            $s1 = ($s1 + ord($data[$n])) % 65521;
            $s2 = ($s2 + $s1) % 65521;
        }
        $adler = $s2 << 16 | $s1;
        $data .= chr($adler >> 24 & 255);
        $data .= chr($adler >> 16 & 255);
        $data .= chr($adler >> 8 & 255);
        $data .= chr($adler & 255);
        add_chunk('IDAT');
        $data = '';
        add_chunk('IEND');
        echo $result;
    }
}
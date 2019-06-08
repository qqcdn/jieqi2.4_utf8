<?php

class ImageWater extends JieqiObject
{
    public $codepage = 'gb2312';
    public $src_image_name = '';
    public $jpeg_quality = 90;
    public $save_image_file = '';
    public $wm_image_name = '';
    public $wm_image_pos = 9;
    public $wm_image_transition = 20;
    public $wm_text = '';
    public $wm_text_size = 20;
    public $wm_text_angle = 0;
    public $wm_text_pos = 9;
    public $wm_text_font = '';
    public $wm_text_color = '#cccccc';
    public function create($filename = '')
    {
        if ($filename) {
            $this->src_image_name = trim($filename);
        }
        $src_image_type = $this->get_type($this->src_image_name);
        $src_image = $this->createImage($this->src_image_name, $src_image_type);
        if (!$src_image) {
            return NULL;
        }
        $src_image_w = imagesx($src_image);
        $src_image_h = imagesy($src_image);
        if ($this->wm_image_name) {
            $this->wm_image_name = trim($this->wm_image_name);
            $wm_image_type = $this->get_type($this->wm_image_name);
            $wm_image = $this->createImage($this->wm_image_name, $wm_image_type);
            $wm_image_w = imagesx($wm_image);
            $wm_image_h = imagesy($wm_image);
            $temp_wm_image = $this->getPos($src_image_w, $src_image_h, $this->wm_image_pos, $wm_image);
            $wm_image_x = $temp_wm_image['dest_x'];
            $wm_image_y = $temp_wm_image['dest_y'];
            imagecopymerge($src_image, $wm_image, $wm_image_x, $wm_image_y, 0, 0, $wm_image_w, $wm_image_h, $this->wm_image_transition);
        }
        if ($this->wm_text) {
            $this->wm_text = $this->txt2utf8($this->wm_text, $this->codepage);
            $temp_wm_text = $this->getPos($src_image_w, $src_image_h, $this->wm_text_pos);
            $wm_text_x = $temp_wm_text['dest_x'];
            $wm_text_y = $temp_wm_text['dest_y'];
            if (preg_match('/([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])([a-f0-9][a-f0-9])/i', $this->wm_text_color, $color)) {
                $red = hexdec($color[1]);
                $green = hexdec($color[2]);
                $blue = hexdec($color[3]);
                $wm_text_color = imagecolorallocate($src_image, $red, $green, $blue);
            } else {
                $wm_text_color = imagecolorallocate($src_image, 255, 255, 255);
            }
            imagettftext($src_image, $this->wm_text_size, $this->wm_angle, $wm_text_x, $wm_text_y, $wm_text_color, $this->wm_text_font, $this->wm_text);
        }
        if ($this->save_image_file) {
            switch ($src_image_type) {
                case 'gif':
                    $src_img = imagegif($src_image, $this->save_image_file);
                    break;
                case 'bmp':
                    include_once dirname(__FILE__) . '/gdbmp.php';
                    $src_img = imagebmp($src_image, $this->save_image_file);
                    break;
                case 'jpeg':
                    $src_img = imagejpeg($src_image, $this->save_image_file, $this->jpeg_quality);
                    break;
                case 'png':
                    $src_img = imagepng($src_image, $this->save_image_file);
                    break;
                default:
                    $src_img = imagejpeg($src_image, $this->save_image_file, $this->jpeg_quality);
                    break;
            }
        } else {
            if ($src_image_type = 'jpg') {
                $src_image_type = 'jpeg';
            }
            header('Content-type: image/' . $src_image_type);
            switch ($src_image_type) {
                case 'gif':
                    $src_img = imagegif($src_image);
                    break;
                case 'bmp':
                    include_once dirname(__FILE__) . '/gdbmp.php';
                    $src_img = imagebmp($src_image);
                    break;
                case 'jpg':
                    $src_img = imagejpeg($src_image, '', $this->jpeg_quality);
                    break;
                case 'png':
                    $src_img = imagepng($src_image);
                    break;
                default:
                    $src_img = imagejpeg($src_image, '', $this->jpeg_quality);
                    break;
            }
        }
        imagedestroy($src_image);
    }
    public function createImage($img_name, $type = NULL)
    {
        if (!$type) {
            $type = $this->get_type($img_name);
        }
        switch ($type) {
            case 'gif':
                if (function_exists('imagecreatefromgif')) {
                    $tmp_img = @imagecreatefromgif($img_name);
                }
                break;
            case 'bmp':
                include_once dirname(__FILE__) . '/gdbmp.php';
                $tmp_img = imagecreatefrombmp($img_name);
            case 'jpg':
                $tmp_img = imagecreatefromjpeg($img_name);
                break;
            case 'png':
                $tmp_img = imagecreatefrompng($img_name);
                break;
            default:
                $tmp_img = imagecreatefromstring($img_name);
                break;
        }
        return $tmp_img;
    }
    public function getPos($sourcefile_width, $sourcefile_height, $pos, $wm_image = '')
    {
        if ($wm_image) {
            $insertfile_width = imagesx($wm_image);
            $insertfile_height = imagesy($wm_image);
        } else {
            $lineCount = explode("\r\n", $this->wm_text);
            $fontSize = imagettfbbox($this->wm_text_size, $this->wm_text_angle, $this->wm_text_font, $this->wm_text);
            $insertfile_width = $fontSize[2] - $fontSize[0];
            $insertfile_height = count($lineCount) * ($fontSize[1] - $fontSize[3]);
        }
        switch ($pos) {
            case 1:
                $dest_x = 0;
                if ($this->wm_text) {
                    $dest_y = $insertfile_height;
                } else {
                    $dest_y = 0;
                }
                break;
            case 2:
                $dest_x = ($sourcefile_width - $insertfile_width) / 2;
                if ($this->wm_text) {
                    $dest_y = $insertfile_height;
                } else {
                    $dest_y = 0;
                }
                break;
            case 3:
                $dest_x = $sourcefile_width - $insertfile_width;
                if ($this->wm_text) {
                    $dest_y = $insertfile_height;
                } else {
                    $dest_y = 0;
                }
                break;
            case 4:
                $dest_x = 0;
                $dest_y = $sourcefile_height / 2 - $insertfile_height / 2;
                break;
            case 5:
                $dest_x = $sourcefile_width / 2 - $insertfile_width / 2;
                $dest_y = $sourcefile_height / 2 - $insertfile_height / 2;
                break;
            case 6:
                $dest_x = $sourcefile_width - $insertfile_width;
                $dest_y = $sourcefile_height / 2 - $insertfile_height / 2;
                break;
            case 7:
                $dest_x = 0;
                $dest_y = $sourcefile_height - $insertfile_height;
                break;
            case 8:
                $dest_x = ($sourcefile_width - $insertfile_width) / 2;
                $dest_y = $sourcefile_height - $insertfile_height;
                break;
            case 9:
                $dest_x = $sourcefile_width - $insertfile_width;
                $dest_y = $sourcefile_height - $insertfile_height;
                break;
            case 10:
                $dest_x = rand(0, $sourcefile_width - $insertfile_width);
                $dest_y = rand(0, $sourcefile_height - $insertfile_height);
                break;
            default:
                $dest_x = $sourcefile_width - $insertfile_width;
                $dest_y = $sourcefile_height - $insertfile_height;
                break;
        }
        return array('dest_x' => $dest_x, 'dest_y' => $dest_y);
    }
    public function txt2utf8($txt, $codepage = 'gb2312')
    {
        if (function_exists('iconv')) {
            return iconv($codepage, 'UTF-8', $txt);
        } else {
            include_once JIEQI_ROOT_PATH . '/include/changecode.php';
            if (strtolower($codepage) == 'gb2312' || strtolower($codepage) == 'gbk') {
                return jieqi_gb2utf8($txt);
            } else {
                if (strtolower($codepage) == 'big5') {
                    return jieqi_big52utf8($txt);
                } else {
                    return $txt;
                }
            }
        }
    }
    public function get_type($img_name, $check = true)
    {
        $type = 'string';
        if ($check && is_file($img_name)) {
            $ret = getimagesize($img_name);
            if (is_array($ret)) {
                switch ($ret[2]) {
                    case 1:
                        $type = 'gif';
                        break;
                    case 2:
                        $type = 'jpg';
                        break;
                    case 3:
                        $type = 'png';
                        break;
                    case 6:
                        $type = 'bmp';
                        break;
                }
            }
        } else {
            if (preg_match('/\\.(jpg|jpeg|gif|png|bmp)$/i', $img_name, $matches)) {
                $type = strtolower($matches[1]);
            }
        }
        return $type;
    }
}
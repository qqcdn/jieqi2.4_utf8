<?php

class ImageResize extends JieqiObject
{
    public $_img = false;
    public $_imagetype;
    public $_width;
    public $_height;
    public function load($img_name, $img_type = '')
    {
        if (!empty($img_type)) {
            $this->_imagetype = $img_type;
        } else {
            $this->_imagetype = $this->get_type($img_name);
        }
        switch ($this->_imagetype) {
            case 'gif':
                if (function_exists('imagecreatefromgif')) {
                    $this->_img = @imagecreatefromgif($img_name);
                }
                break;
            case 'jpg':
            case 'jpeg':
                $this->_img = @imagecreatefromjpeg($img_name);
                break;
            case 'bmp':
                include_once dirname(__FILE__) . '/gdbmp.php';
                $this->_img = @imagecreatefrombmp($img_name);
                break;
            case 'png':
                $this->_img = @imagecreatefrompng($img_name);
                break;
            default:
                $this->_img = @imagecreatefromstring($img_name);
                break;
        }
        $this->getxy();
    }
    public function resize($width, $height, $percent = 0)
    {
        if (!is_resource($this->_img)) {
            return false;
        }
        if (empty($width) && empty($height)) {
            if (empty($percent)) {
                return false;
            } else {
                $width = round($this->_width * $percent);
                $height = round($this->_height * $percent);
            }
        } else {
            if (empty($width) && !empty($height)) {
                $width = round($height * $this->_width / $this->_height);
            } else {
                if (empty($height) && !empty($width)) {
                    $height = round($width * $this->_height / $this->_width);
                } else {
                    if (0 <= $percent) {
                        $pw = $width / $this->_width;
                        $ph = $height / $this->_height;
                        if ($ph < $pw) {
                            $width = round($this->_width * $ph);
                        } else {
                            if ($pw < $ph) {
                                $height = round($this->_height * $pw);
                            }
                        }
                    }
                }
            }
        }
        $tmpimg = imagecreatetruecolor($width, $height);
        if (function_exists('imagecopyresampled')) {
            imagecopyresampled($tmpimg, $this->_img, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);
        } else {
            imagecopyresized($tmpimg, $this->_img, 0, 0, 0, 0, $width, $height, $this->_width, $this->_height);
        }
        $this->destroy();
        $this->_img = $tmpimg;
        $this->getxy();
    }
    public function cut($width, $height, $x = 0, $y = 0)
    {
        if (!is_resource($this->_img)) {
            return false;
        }
        if ($this->_width < $width) {
            $width = $this->_width;
        }
        if ($this->_height < $height) {
            $height = $this->_height;
        }
        if ($x < 0) {
            $x = 0;
        }
        if ($y < 0) {
            $y = 0;
        }
        $tmpimg = imagecreatetruecolor($width, $height);
        imagecopy($tmpimg, $this->_img, 0, 0, $x, $y, $width, $height);
        $this->destroy();
        $this->_img = $tmpimg;
        $this->getxy();
    }
    public function display($destroy = true)
    {
        if (!is_resource($this->_img)) {
            return false;
        }
        switch ($this->_imagetype) {
            case 'jpg':
            case 'jpeg':
                header('Content-type: image/jpeg');
                imagejpeg($this->_img);
                break;
            case 'gif':
                header('Content-type: image/gif');
                imagegif($this->_img);
                break;
            case 'bmp':
                include_once dirname(__FILE__) . '/gdbmp.php';
                header('Content-type: image/bmp');
                imagebmp($this->_img);
                break;
            case 'png':
            default:
                header('Content-type: image/png');
                imagepng($this->_img);
                break;
        }
        if ($destroy) {
            $this->destroy();
        }
    }
    public function save($fname, $destroy = false, $type = '', $quality = 90)
    {
        if (!is_resource($this->_img)) {
            return false;
        }
        if (empty($type)) {
            $type = $this->get_type($fname, false);
        }
        switch ($type) {
            case 'jpg':
            case 'jpeg':
                $ret = imagejpeg($this->_img, $fname, $quality);
                break;
            case 'gif':
                $ret = imagegif($this->_img, $fname);
                break;
            case 'bmp':
                include_once dirname(__FILE__) . '/gdbmp.php';
                $ret = imagebmp($this->_img, $fname);
                break;
            case 'png':
            default:
                $ret = imagepng($this->_img, $fname);
                break;
        }
        if ($destroy) {
            $this->destroy();
        }
        return $ret;
    }
    public function destroy()
    {
        if (is_resource($this->_img)) {
            imagedestroy($this->_img);
        }
    }
    public function getxy()
    {
        if (is_resource($this->_img)) {
            $this->_width = imagesx($this->_img);
            $this->_height = imagesy($this->_img);
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
<?php

class ImageText extends JieqiObject
{
    public $_startx = 20;
    public $_starty = 50;
    public $_imagewidth = 0;
    public $_imageheight = 0;
    public $_wordwidth = 0;
    public $_wordheight = 0;
    public $_align = 'left';
    public $_valign = 'top';
    public $_angle = 0;
    public $_imagetype = 'png';
    public $_imagecolor = '#ffffff';
    public $_textcolor = '#000000';
    public $_text;
    public $_shadowcolor = '';
    public $_shadowdeep = 0;
    public $_img;
    public $_fontsize = 10;
    public $_fontfile = '';
    public $_backimage = '';
    public $_waterimage = '';
    public $_wateriplace = 0;
    public $_wateritrans = 30;
    public $_jpegquality = 75;
    public $_watertplace = 0;
    public $_watertext = '';
    public $_watercolor = '';
    public $_waterangle = 45;
    public $_watersize = 10;
    public $_waterpct = 30;
    public function __construct($text = '')
    {
        parent::__construct();
        $this->set('text', $text);
    }
    public function set($option, $value = NULL)
    {
        if (is_array($option)) {
            foreach ($option as $opt => $val) {
                $this->{'_' . $opt} = $val;
            }
        } else {
            $this->{'_' . $option} = $value;
        }
        return true;
    }
    public function get($option)
    {
        if (isset($this->{'_' . $option})) {
            return $this->{'_' . $option};
        } else {
            return false;
        }
    }
    public function convertRGB($scolor)
    {
        if (preg_match('/^[#|]([a-f0-9]{2})?([a-f0-9]{2})([a-f0-9]{2})([a-f0-9]{2})$/i', $scolor, $matches)) {
            return array('r' => hexdec($matches[2]), 'g' => hexdec($matches[3]), 'b' => hexdec($matches[4]), 'a' => hexdec(!empty($matches[1]) ? $matches[1] : 0));
        }
        return false;
    }
    public function typeset($str = '', $width = 80, $start = 0)
    {
        $tmpstr = '';
        $strlen = strlen($str);
        $point = $start;
        for ($i = 0; $i < $strlen; $i++) {
            if ($width < $point) {
                $tmpstr .= "\n";
                $point = 0;
            }
            if (128 < ord($str[$i])) {
                $tmpstr .= $str[$i] . $str[++$i];
                $point += 2;
            } else {
                $tmpstr .= $str[$i];
                if ($str[$i] == "\n") {
                    $point = 0;
                } else {
                    $point += 1;
                }
            }
        }
        return $tmpstr;
    }
    public function getimagetype($fname)
    {
        $tmpary = explode('.', $fname);
        if (1 < count($tmpary)) {
            if ($tmpary[count($tmpary) - 1] == 'jpg') {
                return 'jpeg';
            } else {
                return $tmpary[count($tmpary) - 1];
            }
        } else {
            return '';
        }
    }
    public function createimage()
    {
        if (empty($this->_imagewidth) || empty($this->_imageheight)) {
            $boxary = imagettfbbox($this->_fontsize, $this->_angle, $this->_fontfile, $this->_text);
            $this->_imagewidth = max($boxary[0], $boxary[2], $boxary[4], $boxary[6]) - min($boxary[0], $boxary[2], $boxary[4], $boxary[6]);
            $this->_imagewidth += $this->_startx + $this->_startx;
            $this->_imageheight = max($boxary[1], $boxary[3], $boxary[5], $boxary[7]) - min($boxary[1], $boxary[3], $boxary[5], $boxary[7]);
            $this->_imageheight += $this->_starty + $this->_starty;
        }
        $this->_img = imagecreate($this->_imagewidth, $this->_imageheight);
        $colorary = $this->convertRGB($this->_imagecolor);
        $bkcolor = imagecolorallocate($this->_img, $colorary['r'], $colorary['g'], $colorary['b']);
        $colorary = $this->convertRGB($this->_textcolor);
        $ftcolor = imagecolorallocate($this->_img, $colorary['r'], $colorary['g'], $colorary['b']);
        if (!empty($this->_backimage) && file_exists($this->_backimage)) {
            $imgtype = $this->getimagetype($this->_backimage);
            $imgfun = 'imagecreatefrom' . $imgtype;
            if (function_exists($imgfun)) {
                $backimage = $imgfun($this->_backimage);
                if (is_resource($backimage)) {
                    $backwidth = imagesx($backimage);
                    $backheight = imagesy($backimage);
                    $cols = ceil($this->_imagewidth / $backwidth);
                    $rows = ceil($this->_imageheight / $backheight);
                    $startx = 0;
                    $starty = 0;
                    for ($i = 1; $i <= $rows; $i++) {
                        for ($j = 1; $j <= $cols; $j++) {
                            imagecopy($this->_img, $backimage, $startx, $starty, 0, 0, $backwidth, $backheight);
                            $startx += $backwidth;
                        }
                        $startx = 0;
                        $starty += $backheight;
                    }
                }
            }
        }
        if (!empty($this->_shadowcolor) && !empty($this->_shadowdeep)) {
            $colorary = $this->convertRGB($this->_shadowcolor);
            $sdcolor = imagecolorallocate($this->_img, $colorary['r'], $colorary['g'], $colorary['b']);
            imagettftext($this->_img, $this->_fontsize, $this->_angle, $this->_startx + $this->_shadowdeep, $this->_starty + $this->_fontsize + 2 + $this->_shadowdeep, $sdcolor, $this->_fontfile, $this->_text);
        }
        if (0 < $this->_watertplace && !empty($this->_watertext) && !empty($this->_watercolor)) {
            $boxary = imagettfbbox($this->_watersize, 0, $this->_fontfile, $this->_watertext);
            $lineheight = max($boxary[1], $boxary[3], $boxary[5], $boxary[7]) - min($boxary[1], $boxary[3], $boxary[5], $boxary[7]);
            $boxary = imagettfbbox($this->_watersize, $this->_waterangle, $this->_fontfile, $this->_watertext);
            $waterwidth = max($boxary[0], $boxary[2], $boxary[4], $boxary[6]) - min($boxary[0], $boxary[2], $boxary[4], $boxary[6]);
            $waterheight = max($boxary[1], $boxary[3], $boxary[5], $boxary[7]) - min($boxary[1], $boxary[3], $boxary[5], $boxary[7]);
            $waterwidth += $lineheight * 2;
            $waterheight += $lineheight * 2;
            $waterimage = imagecreate($waterwidth, $waterheight);
            $colorary = $this->convertRGB('#ffffff');
            $bkcolor = imagecolorallocate($waterimage, $colorary['r'], $colorary['g'], $colorary['b']);
            imagecolortransparent($waterimage, $bkcolor);
            $colorary = $this->convertRGB($this->_watercolor);
            $ftcolor = imagecolorallocate($waterimage, $colorary['r'], $colorary['g'], $colorary['b']);
            $tmprangle = $this->_waterangle;
            if ($tmprangle < 0) {
                $tmprangle += 360;
            }
            $watersx = $lineheight;
            $watersy = $waterheight - $lineheight;
            if (90 < $tmprangle && $tmprangle <= 180) {
                $watersx = $waterwidth - $lineheight;
                $watersy = $waterheight - $lineheight;
            } else {
                if (180 < $tmprangle && $tmprangle <= 270) {
                    $watersx = $waterwidth - $lineheight;
                    $watersy = $lineheight;
                }
            }
            if (270 < $tmprangle && $tmprangle <= 360) {
                $watersx = $lineheight;
                $watersy = $lineheight;
            }
            imagettftext($waterimage, $this->_watersize, $this->_waterangle, $watersx, $watersy, $ftcolor, $this->_fontfile, $this->_watertext);
            if ($this->_watertplace == 1) {
                imagecopymerge($this->_img, $waterimage, 0, 0, 0, 0, $waterwidth, $waterheight, $this->_waterpct);
                imagecopymerge($this->_img, $waterimage, $this->_imagewidth - $waterwidth, 0, 0, 0, $waterwidth, $waterheight, $this->_waterpct);
                imagecopymerge($this->_img, $waterimage, 0, $this->_imageheight - $waterheight, 0, 0, $waterwidth, $waterheight, $this->_waterpct);
                imagecopymerge($this->_img, $waterimage, $this->_imagewidth - $waterwidth, $this->_imageheight - $waterheight, 0, 0, $waterwidth, $waterheight, $this->_waterpct);
            } else {
                if ($this->_watertplace == 2) {
                    $cols = ceil($this->_imagewidth / $waterwidth);
                    $rows = ceil($this->_imageheight / $waterheight);
                    $startx = 0;
                    $starty = 0;
                    for ($i = 1; $i <= $rows; $i++) {
                        for ($j = 1; $j <= $cols; $j++) {
                            imagecopymerge($this->_img, $waterimage, $startx, $starty, 0, 0, $waterwidth, $waterheight, $this->_waterpct);
                            $startx += $waterwidth;
                        }
                        $startx = 0;
                        $starty += $waterheight;
                    }
                }
            }
        }
        imagettftext($this->_img, $this->_fontsize, $this->_angle, $this->_startx, $this->_starty + $this->_fontsize + 2, $ftcolor, $this->_fontfile, $this->_text);
        if (!empty($this->_wateriplace) && !empty($this->_waterimage) && file_exists($this->_waterimage)) {
            $imgtype = $this->getimagetype($this->_waterimage);
            $imgfun = 'imagecreatefrom' . $imgtype;
            if (function_exists($imgfun)) {
                $waterimage = $imgfun($this->_waterimage);
                if (is_resource($waterimage)) {
                    $waterwidth = imagesx($waterimage);
                    $waterheight = imagesy($waterimage);
                    $temp_wm_image = $this->getpos($this->_wateriplace, $this->_imagewidth, $this->_imageheight, $waterwidth, $waterheight);
                    $wm_image_x = $temp_wm_image['dest_x'];
                    $wm_image_y = $temp_wm_image['dest_y'];
                    imagecopymerge($this->_img, $waterimage, $wm_image_x, $wm_image_y, 0, 0, $waterwidth, $waterheight, $this->_wateritrans);
                }
            }
        }
        imagecolortransparent($this->_img, $bkcolor);
    }
    public function settransparent($color)
    {
        $colorary = $this->convertRGB($color);
        $tcolor = imagecolorallocate($this->_img, $colorary['r'], $colorary['g'], $colorary['b']);
        imagecolortransparent($this->img, $tcolor);
    }
    public function hidewaternum($num = 0, $x = 0, $y = 0, $w = 8)
    {
        $bgcolor = imagecolorat($this->_img, 0, 0);
    }
    public function destroyimage()
    {
        if (is_resource($this->_img)) {
            imagedestroy($this->_img);
        }
    }
    public function display($destroy = true)
    {
        if (!is_resource($this->_img)) {
            $this->createimage();
        }
        switch ($this->_imagetype) {
            case 'jpg':
            case 'jpeg':
                header('Content-type: image/jpeg');
                imagejpeg($this->_img, '', $this->_jpegquality);
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
            $this->destroyimage();
        }
    }
    public function save($fname, $destroy = false)
    {
        if (!is_resource($this->_img)) {
            $this->createimage();
        }
        switch ($this->_imagetype) {
            case 'jpg':
            case 'jpeg':
                $ret = imagejpeg($this->_img, $fname, $this->_jpegquality);
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
            $this->destroyimage();
        }
        return $ret;
    }
    public function getpos($pos, $sourcefile_width, $sourcefile_height, $insertfile_width, $insertfile_height)
    {
        switch ($pos) {
            case 1:
                $dest_x = 0;
                $dest_y = 0;
                break;
            case 2:
                $dest_x = ($sourcefile_width - $insertfile_width) / 2;
                $dest_y = 0;
                break;
            case 3:
                $dest_x = $sourcefile_width - $insertfile_width;
                $dest_y = 0;
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
}
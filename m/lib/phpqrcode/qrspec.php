<?php

class QRspec
{
    public static $capacity = array(array(0, 0, 0, array(0, 0, 0, 0)), array(21, 26, 0, array(7, 10, 13, 17)), array(25, 44, 7, array(10, 16, 22, 28)), array(29, 70, 7, array(15, 26, 36, 44)), array(33, 100, 7, array(20, 36, 52, 64)), array(37, 134, 7, array(26, 48, 72, 88)), array(41, 172, 7, array(36, 64, 96, 112)), array(45, 196, 0, array(40, 72, 108, 130)), array(49, 242, 0, array(48, 88, 132, 156)), array(53, 292, 0, array(60, 110, 160, 192)), array(57, 346, 0, array(72, 130, 192, 224)), array(61, 404, 0, array(80, 150, 224, 264)), array(65, 466, 0, array(96, 176, 260, 308)), array(69, 532, 0, array(104, 198, 288, 352)), array(73, 581, 3, array(120, 216, 320, 384)), array(77, 655, 3, array(132, 240, 360, 432)), array(81, 733, 3, array(144, 280, 408, 480)), array(85, 815, 3, array(168, 308, 448, 532)), array(89, 901, 3, array(180, 338, 504, 588)), array(93, 991, 3, array(196, 364, 546, 650)), array(97, 1085, 3, array(224, 416, 600, 700)), array(101, 1156, 4, array(224, 442, 644, 750)), array(105, 1258, 4, array(252, 476, 690, 816)), array(109, 1364, 4, array(270, 504, 750, 900)), array(113, 1474, 4, array(300, 560, 810, 960)), array(117, 1588, 4, array(312, 588, 870, 1050)), array(121, 1706, 4, array(336, 644, 952, 1110)), array(125, 1828, 4, array(360, 700, 1020, 1200)), array(129, 1921, 3, array(390, 728, 1050, 1260)), array(133, 2051, 3, array(420, 784, 1140, 1350)), array(137, 2185, 3, array(450, 812, 1200, 1440)), array(141, 2323, 3, array(480, 868, 1290, 1530)), array(145, 2465, 3, array(510, 924, 1350, 1620)), array(149, 2611, 3, array(540, 980, 1440, 1710)), array(153, 2761, 3, array(570, 1036, 1530, 1800)), array(157, 2876, 0, array(570, 1064, 1590, 1890)), array(161, 3034, 0, array(600, 1120, 1680, 1980)), array(165, 3196, 0, array(630, 1204, 1770, 2100)), array(169, 3362, 0, array(660, 1260, 1860, 2220)), array(173, 3532, 0, array(720, 1316, 1950, 2310)), array(177, 3706, 0, array(750, 1372, 2040, 2430)));
    public static $lengthTableBits = array(array(10, 12, 14), array(9, 11, 13), array(8, 16, 16), array(8, 10, 12));
    public static $eccTable = array(array(array(0, 0), array(0, 0), array(0, 0), array(0, 0)), array(array(1, 0), array(1, 0), array(1, 0), array(1, 0)), array(array(1, 0), array(1, 0), array(1, 0), array(1, 0)), array(array(1, 0), array(1, 0), array(2, 0), array(2, 0)), array(array(1, 0), array(2, 0), array(2, 0), array(4, 0)), array(array(1, 0), array(2, 0), array(2, 2), array(2, 2)), array(array(2, 0), array(4, 0), array(4, 0), array(4, 0)), array(array(2, 0), array(4, 0), array(2, 4), array(4, 1)), array(array(2, 0), array(2, 2), array(4, 2), array(4, 2)), array(array(2, 0), array(3, 2), array(4, 4), array(4, 4)), array(array(2, 2), array(4, 1), array(6, 2), array(6, 2)), array(array(4, 0), array(1, 4), array(4, 4), array(3, 8)), array(array(2, 2), array(6, 2), array(4, 6), array(7, 4)), array(array(4, 0), array(8, 1), array(8, 4), array(12, 4)), array(array(3, 1), array(4, 5), array(11, 5), array(11, 5)), array(array(5, 1), array(5, 5), array(5, 7), array(11, 7)), array(array(5, 1), array(7, 3), array(15, 2), array(3, 13)), array(array(1, 5), array(10, 1), array(1, 15), array(2, 17)), array(array(5, 1), array(9, 4), array(17, 1), array(2, 19)), array(array(3, 4), array(3, 11), array(17, 4), array(9, 16)), array(array(3, 5), array(3, 13), array(15, 5), array(15, 10)), array(array(4, 4), array(17, 0), array(17, 6), array(19, 6)), array(array(2, 7), array(17, 0), array(7, 16), array(34, 0)), array(array(4, 5), array(4, 14), array(11, 14), array(16, 14)), array(array(6, 4), array(6, 14), array(11, 16), array(30, 2)), array(array(8, 4), array(8, 13), array(7, 22), array(22, 13)), array(array(10, 2), array(19, 4), array(28, 6), array(33, 4)), array(array(8, 4), array(22, 3), array(8, 26), array(12, 28)), array(array(3, 10), array(3, 23), array(4, 31), array(11, 31)), array(array(7, 7), array(21, 7), array(1, 37), array(19, 26)), array(array(5, 10), array(19, 10), array(15, 25), array(23, 25)), array(array(13, 3), array(2, 29), array(42, 1), array(23, 28)), array(array(17, 0), array(10, 23), array(10, 35), array(19, 35)), array(array(17, 1), array(14, 21), array(29, 19), array(11, 46)), array(array(13, 6), array(14, 23), array(44, 7), array(59, 1)), array(array(12, 7), array(12, 26), array(39, 14), array(22, 41)), array(array(6, 14), array(6, 34), array(46, 10), array(2, 64)), array(array(17, 4), array(29, 14), array(49, 10), array(24, 46)), array(array(4, 18), array(13, 32), array(48, 14), array(42, 32)), array(array(20, 4), array(40, 7), array(43, 22), array(10, 67)), array(array(19, 6), array(18, 31), array(34, 34), array(20, 61)));
    public static $alignmentPattern = array(array(0, 0), array(0, 0), array(18, 0), array(22, 0), array(26, 0), array(30, 0), array(34, 0), array(22, 38), array(24, 42), array(26, 46), array(28, 50), array(30, 54), array(32, 58), array(34, 62), array(26, 46), array(26, 48), array(26, 50), array(30, 54), array(30, 56), array(30, 58), array(34, 62), array(28, 50), array(26, 50), array(30, 54), array(28, 54), array(32, 58), array(30, 58), array(34, 62), array(26, 50), array(30, 54), array(26, 52), array(30, 56), array(34, 60), array(30, 58), array(34, 62), array(30, 54), array(24, 50), array(28, 54), array(32, 58), array(26, 54), array(30, 58));
    public static $versionPattern = array(31892, 34236, 39577, 42195, 48118, 51042, 55367, 58893, 63784, 68472, 70749, 76311, 79154, 84390, 87683, 92361, 96236, 102084, 102881, 110507, 110734, 117786, 119615, 126325, 127568, 133589, 136944, 141498, 145311, 150283, 152622, 158308, 161089, 167017);
    public static $formatInfo = array(array(30660, 29427, 32170, 30877, 26159, 25368, 27713, 26998), array(21522, 20773, 24188, 23371, 17913, 16590, 20375, 19104), array(13663, 12392, 16177, 14854, 9396, 8579, 11994, 11245), array(5769, 5054, 7399, 6608, 1890, 597, 3340, 2107));
    public static $frames = array();
    public static function getDataLength($version, $level)
    {
        return self::$capacity[$version][QRCAP_WORDS] - self::$capacity[$version][QRCAP_EC][$level];
    }
    public static function getECCLength($version, $level)
    {
        return self::$capacity[$version][QRCAP_EC][$level];
    }
    public static function getWidth($version)
    {
        return self::$capacity[$version][QRCAP_WIDTH];
    }
    public static function getRemainder($version)
    {
        return self::$capacity[$version][QRCAP_REMINDER];
    }
    public static function getMinimumVersion($size, $level)
    {
        for ($i = 1; $i <= QRSPEC_VERSION_MAX; $i++) {
            $words = self::$capacity[$i][QRCAP_WORDS] - self::$capacity[$i][QRCAP_EC][$level];
            if ($size <= $words) {
                return $i;
            }
        }
        return -1;
    }
    public static function lengthIndicator($mode, $version)
    {
        if ($mode == QR_MODE_STRUCTURE) {
            return 0;
        }
        if ($version <= 9) {
            $l = 0;
        } else {
            if ($version <= 26) {
                $l = 1;
            } else {
                $l = 2;
            }
        }
        return self::$lengthTableBits[$mode][$l];
    }
    public static function maximumWords($mode, $version)
    {
        if ($mode == QR_MODE_STRUCTURE) {
            return 3;
        }
        if ($version <= 9) {
            $l = 0;
        } else {
            if ($version <= 26) {
                $l = 1;
            } else {
                $l = 2;
            }
        }
        $bits = self::$lengthTableBits[$mode][$l];
        $words = (1 << $bits) - 1;
        if ($mode == QR_MODE_KANJI) {
            $words *= 2;
        }
        return $words;
    }
    public static function getEccSpec($version, $level, array &$spec)
    {
        if (count($spec) < 5) {
            $spec = array(0, 0, 0, 0, 0);
        }
        $b1 = self::$eccTable[$version][$level][0];
        $b2 = self::$eccTable[$version][$level][1];
        $data = self::getDataLength($version, $level);
        $ecc = self::getECCLength($version, $level);
        if ($b2 == 0) {
            $spec[0] = $b1;
            $spec[1] = (int) $data / $b1;
            $spec[2] = (int) $ecc / $b1;
            $spec[3] = 0;
            $spec[4] = 0;
        } else {
            $spec[0] = $b1;
            $spec[1] = (int) $data / ($b1 + $b2);
            $spec[2] = (int) $ecc / ($b1 + $b2);
            $spec[3] = $b2;
            $spec[4] = $spec[1] + 1;
        }
    }
    public static function putAlignmentMarker(array &$frame, $ox, $oy)
    {
        $finder = array('　　?, '牋?, '?, '牋?, '　　?);
        $yStart = $oy - 2;
        $xStart = $ox - 2;
        for ($y = 0; $y < 5; $y++) {
            QRstr::set($frame, $xStart, $yStart + $y, $finder[$y]);
        }
    }
    public static function putAlignmentPattern($version, &$frame, $width)
    {
        if ($version < 2) {
            return NULL;
        }
        $d = self::$alignmentPattern[$version][1] - self::$alignmentPattern[$version][0];
        if ($d < 0) {
            $w = 2;
        } else {
            $w = (int) (($width - self::$alignmentPattern[$version][0]) / $d) + 2;
        }
        if ($w * $w - 3 == 1) {
            $x = self::$alignmentPattern[$version][0];
            $y = self::$alignmentPattern[$version][0];
            self::putAlignmentMarker($frame, $x, $y);
            return NULL;
        }
        $cx = self::$alignmentPattern[$version][0];
        for ($x = 1; $x < $w - 1; $x++) {
            self::putAlignmentMarker($frame, 6, $cx);
            self::putAlignmentMarker($frame, $cx, 6);
            $cx += $d;
        }
        $cy = self::$alignmentPattern[$version][0];
        for ($y = 0; $y < $w - 1; $y++) {
            $cx = self::$alignmentPattern[$version][0];
            for ($x = 0; $x < $w - 1; $x++) {
                self::putAlignmentMarker($frame, $cx, $cy);
                $cx += $d;
            }
            $cy += $d;
        }
    }
    public static function getVersionPattern($version)
    {
        if ($version < 7 || QRSPEC_VERSION_MAX < $version) {
            return 0;
        }
        return self::$versionPattern[$version - 7];
    }
    public static function getFormatInfo($mask, $level)
    {
        if ($mask < 0 || 7 < $mask) {
            return 0;
        }
        if ($level < 0 || 3 < $level) {
            return 0;
        }
        return self::$formatInfo[$level][$mask];
    }
    public static function putFinderPattern(&$frame, $ox, $oy)
    {
        $finder = array('亮亮亮?, '晾览览?, '晾亮晾?, '晾亮晾?, '晾亮晾?, '晾览览?, '亮亮亮?);
        for ($y = 0; $y < 7; $y++) {
            QRstr::set($frame, $ox, $oy + $y, $finder[$y]);
        }
    }
    public static function createFrame($version)
    {
        $width = self::$capacity[$version][QRCAP_WIDTH];
        $frameLine = str_repeat('' . "\0" . '', $width);
        $frame = array_fill(0, $width, $frameLine);
        self::putFinderPattern($frame, 0, 0);
        self::putFinderPattern($frame, $width - 7, 0);
        self::putFinderPattern($frame, 0, $width - 7);
        $yOffset = $width - 7;
        for ($y = 0; $y < 7; $y++) {
            $frame[$y][7] = '?;
            $frame[$y][$width - 8] = '?;
            $frame[$yOffset][7] = '?;
            $yOffset++;
        }
        $setPattern = str_repeat('?, 8);
        QRstr::set($frame, 0, 7, $setPattern);
        QRstr::set($frame, $width - 8, 7, $setPattern);
        QRstr::set($frame, 0, $width - 8, $setPattern);
        $setPattern = str_repeat('?, 9);
        QRstr::set($frame, 0, 8, $setPattern);
        QRstr::set($frame, $width - 8, 8, $setPattern, 8);
        $yOffset = $width - 8;
        for ($y = 0; $y < 8; $y++, $yOffset++) {
            $frame[$y][8] = '?;
            $frame[$yOffset][8] = '?;
        }
        for ($i = 1; $i < $width - 15; $i++) {
            $frame[6][7 + $i] = chr(144 | $i & 1);
            $frame[7 + $i][6] = chr(144 | $i & 1);
        }
        self::putAlignmentPattern($version, $frame, $width);
        if (7 <= $version) {
            $vinf = self::getVersionPattern($version);
            $v = $vinf;
            for ($x = 0; $x < 6; $x++) {
                for ($y = 0; $y < 3; $y++) {
                    $frame[$width - 11 + $y][$x] = chr(136 | $v & 1);
                    $v = $v >> 1;
                }
            }
            $v = $vinf;
            for ($y = 0; $y < 6; $y++) {
                for ($x = 0; $x < 3; $x++) {
                    $frame[$y][$x + ($width - 11)] = chr(136 | $v & 1);
                    $v = $v >> 1;
                }
            }
        }
        $frame[$width - 8][8] = '?;
        return $frame;
    }
    public static function debug($frame, $binary_mode = false)
    {
        if ($binary_mode) {
            foreach ($frame as &$frameLine) {
                $frameLine = join('<span class="m">&nbsp;&nbsp;</span>', explode('0', $frameLine));
                $frameLine = join('&#9608;&#9608;', explode('1', $frameLine));
            }
            echo '                <style>' . "\r\n" . '                    .m { background-color: white; }' . "\r\n" . '                </style>' . "\r\n" . '                ';
            echo '<pre><tt><br/ ><br/ ><br/ >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
            echo join('<br/ >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $frame);
            echo '</tt></pre><br/ ><br/ ><br/ ><br/ ><br/ ><br/ >';
        } else {
            foreach ($frame as &$frameLine) {
                $frameLine = join('<span class="m">&nbsp;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="m">&#9618;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="p">&nbsp;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="p">&#9618;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="s">&#9671;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="s">&#9670;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="x">&#9762;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="c">&nbsp;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="c">&#9719;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="f">&nbsp;</span>', explode('?, $frameLine));
                $frameLine = join('<span class="f">&#9618;</span>', explode('?, $frameLine));
                $frameLine = join('&#9830;', explode('', $frameLine));
                $frameLine = join('&#8901;', explode('' . "\0" . '', $frameLine));
            }
            echo '                <style>' . "\r\n" . '                    .p { background-color: yellow; }' . "\r\n" . '                    .m { background-color: #00FF00; }' . "\r\n" . '                    .s { background-color: #FF0000; }' . "\r\n" . '                    .c { background-color: aqua; }' . "\r\n" . '                    .x { background-color: pink; }' . "\r\n" . '                    .f { background-color: gold; }' . "\r\n" . '                </style>' . "\r\n" . '                ';
            echo '<pre><tt>';
            echo join('<br/ >', $frame);
            echo '</tt></pre>';
        }
    }
    public static function serial($frame)
    {
        return gzcompress(join("\n", $frame), 9);
    }
    public static function unserial($code)
    {
        return explode("\n", gzuncompress($code));
    }
    public static function newFrame($version)
    {
        if ($version < 1 || QRSPEC_VERSION_MAX < $version) {
            return NULL;
        }
        if (!isset(self::$frames[$version])) {
            $fileName = QR_CACHE_DIR . 'frame_' . $version . '.dat';
            if (QR_CACHEABLE) {
                if (file_exists($fileName)) {
                    self::$frames[$version] = self::unserial(file_get_contents($fileName));
                } else {
                    self::$frames[$version] = self::createFrame($version);
                    file_put_contents($fileName, self::serial(self::$frames[$version]));
                }
            } else {
                self::$frames[$version] = self::createFrame($version);
            }
        }
        if (is_null(self::$frames[$version])) {
            return NULL;
        }
        return self::$frames[$version];
    }
    public static function rsBlockNum($spec)
    {
        return $spec[0] + $spec[3];
    }
    public static function rsBlockNum1($spec)
    {
        return $spec[0];
    }
    public static function rsDataCodes1($spec)
    {
        return $spec[1];
    }
    public static function rsEccCodes1($spec)
    {
        return $spec[2];
    }
    public static function rsBlockNum2($spec)
    {
        return $spec[3];
    }
    public static function rsDataCodes2($spec)
    {
        return $spec[4];
    }
    public static function rsEccCodes2($spec)
    {
        return $spec[2];
    }
    public static function rsDataLength($spec)
    {
        return $spec[0] * $spec[1] + $spec[3] * $spec[4];
    }
    public static function rsEccLength($spec)
    {
        return ($spec[0] + $spec[3]) * $spec[2];
    }
}
define('QRSPEC_VERSION_MAX', 40);
define('QRSPEC_WIDTH_MAX', 177);
define('QRCAP_WIDTH', 0);
define('QRCAP_WORDS', 1);
define('QRCAP_REMINDER', 2);
define('QRCAP_EC', 3);
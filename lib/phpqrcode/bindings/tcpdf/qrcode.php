<?php

if (!defined('QRCODEDEFS')) {
    define('QRCODEDEFS', true);
    define('QR_MODE_NL', -1);
    define('QR_MODE_NM', 0);
    define('QR_MODE_AN', 1);
    define('QR_MODE_8B', 2);
    define('QR_MODE_KJ', 3);
    define('QR_MODE_ST', 4);
    define('QR_ECLEVEL_L', 0);
    define('QR_ECLEVEL_M', 1);
    define('QR_ECLEVEL_Q', 2);
    define('QR_ECLEVEL_H', 3);
    define('QRSPEC_VERSION_MAX', 40);
    define('QRSPEC_WIDTH_MAX', 177);
    define('QRCAP_WIDTH', 0);
    define('QRCAP_WORDS', 1);
    define('QRCAP_REMINDER', 2);
    define('QRCAP_EC', 3);
    define('STRUCTURE_HEADER_BITS', 20);
    define('MAX_STRUCTURED_SYMBOLS', 16);
    define('N1', 3);
    define('N2', 3);
    define('N3', 40);
    define('N4', 10);
    define('QR_FIND_BEST_MASK', true);
    define('QR_FIND_FROM_RANDOM', 2);
    define('QR_DEFAULT_MASK', 2);
}
if (!class_exists('QRcode', false)) {
    if (!function_exists('str_split')) {
        function str_split($string, $split_length = 1)
        {
            if ($split_length < strlen($string) || !$split_length) {
                do {
                    $c = strlen($string);
                    $parts[] = substr($string, 0, $split_length);
                    $string = substr($string, $split_length);
                } while ($string !== false);
            } else {
                $parts = array($string);
            }
            return $parts;
        }
    }
    class QRcode
    {
        /**
         * @var barcode array to be returned which is readable by TCPDF
         * @access protected
         */
        protected $barcode_array = array();
        /**
         * @var QR code version. Size of QRcode is defined as version. Version is from 1 to 40. Version 1 is 21*21 matrix. And 4 modules increases whenever 1 version increases. So version 40 is 177*177 matrix.
         * @access protected
         */
        protected $version = 0;
        /**
         * @var Levels of error correction. See definitions for possible values.
         * @access protected
         */
        protected $level = QR_ECLEVEL_L;
        /**
         * @var Encoding mode
         * @access protected
         */
        protected $hint = QR_MODE_8B;
        /**
         * @var if true the input string will be converted to uppercase
         * @access protected
         */
        protected $casesensitive = true;
        /**
         * @var structured QR code (not supported yet)
         * @access protected
         */
        protected $structured = 0;
        /**
         * @var mask data
         * @access protected
         */
        protected $data;
        /**
         * @var width
         * @access protected
         */
        protected $width;
        /**
         * @var frame
         * @access protected
         */
        protected $frame;
        /**
         * @var X position of bit
         * @access protected
         */
        protected $x;
        /**
         * @var Y position of bit
         * @access protected
         */
        protected $y;
        /**
         * @var direction
         * @access protected
         */
        protected $dir;
        /**
         * @var single bit
         * @access protected
         */
        protected $bit;
        /**
         * @var data code
         * @access protected
         */
        protected $datacode = array();
        /**
         * @var error correction code
         * @access protected
         */
        protected $ecccode = array();
        /**
         * @var blocks
         * @access protected
         */
        protected $blocks;
        /**
         * @var Reed-Solomon blocks
         * @access protected
         */
        protected $rsblocks = array();
        /**
         * @var counter
         * @access protected
         */
        protected $count;
        /**
         * @var data length
         * @access protected
         */
        protected $dataLength;
        /**
         * @var error correction length
         * @access protected
         */
        protected $eccLength;
        /**
         * @var b1
         * @access protected
         */
        protected $b1;
        /**
         * @var run length
         * @access protected
         */
        protected $runLength = array();
        /**
         * @var input data string
         * @access protected
         */
        protected $dataStr = '';
        /**
         * @var input items
         * @access protected
         */
        protected $items;
        /**
         * @var Reed-Solomon items
         * @access protected
         */
        protected $rsitems = array();
        /**
         * @var array of frames
         * @access protected
         */
        protected $frames = array();
        /**
         * @var alphabet-numeric convesion table
         * @access protected
         */
        protected $anTable = array(-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, 36, -1, -1, -1, 37, 38, -1, -1, -1, -1, 39, 40, -1, 41, 42, 43, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 44, -1, -1, -1, -1, -1, -1, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1);
        /**
         * @var array Table of the capacity of symbols
         * See Table 1 (pp.13) and Table 12-16 (pp.30-36), JIS X0510:2004.
         * @access protected
         */
        protected $capacity = array(array(0, 0, 0, array(0, 0, 0, 0)), array(21, 26, 0, array(7, 10, 13, 17)), array(25, 44, 7, array(10, 16, 22, 28)), array(29, 70, 7, array(15, 26, 36, 44)), array(33, 100, 7, array(20, 36, 52, 64)), array(37, 134, 7, array(26, 48, 72, 88)), array(41, 172, 7, array(36, 64, 96, 112)), array(45, 196, 0, array(40, 72, 108, 130)), array(49, 242, 0, array(48, 88, 132, 156)), array(53, 292, 0, array(60, 110, 160, 192)), array(57, 346, 0, array(72, 130, 192, 224)), array(61, 404, 0, array(80, 150, 224, 264)), array(65, 466, 0, array(96, 176, 260, 308)), array(69, 532, 0, array(104, 198, 288, 352)), array(73, 581, 3, array(120, 216, 320, 384)), array(77, 655, 3, array(132, 240, 360, 432)), array(81, 733, 3, array(144, 280, 408, 480)), array(85, 815, 3, array(168, 308, 448, 532)), array(89, 901, 3, array(180, 338, 504, 588)), array(93, 991, 3, array(196, 364, 546, 650)), array(97, 1085, 3, array(224, 416, 600, 700)), array(101, 1156, 4, array(224, 442, 644, 750)), array(105, 1258, 4, array(252, 476, 690, 816)), array(109, 1364, 4, array(270, 504, 750, 900)), array(113, 1474, 4, array(300, 560, 810, 960)), array(117, 1588, 4, array(312, 588, 870, 1050)), array(121, 1706, 4, array(336, 644, 952, 1110)), array(125, 1828, 4, array(360, 700, 1020, 1200)), array(129, 1921, 3, array(390, 728, 1050, 1260)), array(133, 2051, 3, array(420, 784, 1140, 1350)), array(137, 2185, 3, array(450, 812, 1200, 1440)), array(141, 2323, 3, array(480, 868, 1290, 1530)), array(145, 2465, 3, array(510, 924, 1350, 1620)), array(149, 2611, 3, array(540, 980, 1440, 1710)), array(153, 2761, 3, array(570, 1036, 1530, 1800)), array(157, 2876, 0, array(570, 1064, 1590, 1890)), array(161, 3034, 0, array(600, 1120, 1680, 1980)), array(165, 3196, 0, array(630, 1204, 1770, 2100)), array(169, 3362, 0, array(660, 1260, 1860, 2220)), array(173, 3532, 0, array(720, 1316, 1950, 2310)), array(177, 3706, 0, array(750, 1372, 2040, 2430)));
        /**
         * @var array Length indicator
         * @access protected
         */
        protected $lengthTableBits = array(array(10, 12, 14), array(9, 11, 13), array(8, 16, 16), array(8, 10, 12));
        /**
         * @var array Table of the error correction code (Reed-Solomon block)
         * See Table 12-16 (pp.30-36), JIS X0510:2004.
         * @access protected
         */
        protected $eccTable = array(array(array(0, 0), array(0, 0), array(0, 0), array(0, 0)), array(array(1, 0), array(1, 0), array(1, 0), array(1, 0)), array(array(1, 0), array(1, 0), array(1, 0), array(1, 0)), array(array(1, 0), array(1, 0), array(2, 0), array(2, 0)), array(array(1, 0), array(2, 0), array(2, 0), array(4, 0)), array(array(1, 0), array(2, 0), array(2, 2), array(2, 2)), array(array(2, 0), array(4, 0), array(4, 0), array(4, 0)), array(array(2, 0), array(4, 0), array(2, 4), array(4, 1)), array(array(2, 0), array(2, 2), array(4, 2), array(4, 2)), array(array(2, 0), array(3, 2), array(4, 4), array(4, 4)), array(array(2, 2), array(4, 1), array(6, 2), array(6, 2)), array(array(4, 0), array(1, 4), array(4, 4), array(3, 8)), array(array(2, 2), array(6, 2), array(4, 6), array(7, 4)), array(array(4, 0), array(8, 1), array(8, 4), array(12, 4)), array(array(3, 1), array(4, 5), array(11, 5), array(11, 5)), array(array(5, 1), array(5, 5), array(5, 7), array(11, 7)), array(array(5, 1), array(7, 3), array(15, 2), array(3, 13)), array(array(1, 5), array(10, 1), array(1, 15), array(2, 17)), array(array(5, 1), array(9, 4), array(17, 1), array(2, 19)), array(array(3, 4), array(3, 11), array(17, 4), array(9, 16)), array(array(3, 5), array(3, 13), array(15, 5), array(15, 10)), array(array(4, 4), array(17, 0), array(17, 6), array(19, 6)), array(array(2, 7), array(17, 0), array(7, 16), array(34, 0)), array(array(4, 5), array(4, 14), array(11, 14), array(16, 14)), array(array(6, 4), array(6, 14), array(11, 16), array(30, 2)), array(array(8, 4), array(8, 13), array(7, 22), array(22, 13)), array(array(10, 2), array(19, 4), array(28, 6), array(33, 4)), array(array(8, 4), array(22, 3), array(8, 26), array(12, 28)), array(array(3, 10), array(3, 23), array(4, 31), array(11, 31)), array(array(7, 7), array(21, 7), array(1, 37), array(19, 26)), array(array(5, 10), array(19, 10), array(15, 25), array(23, 25)), array(array(13, 3), array(2, 29), array(42, 1), array(23, 28)), array(array(17, 0), array(10, 23), array(10, 35), array(19, 35)), array(array(17, 1), array(14, 21), array(29, 19), array(11, 46)), array(array(13, 6), array(14, 23), array(44, 7), array(59, 1)), array(array(12, 7), array(12, 26), array(39, 14), array(22, 41)), array(array(6, 14), array(6, 34), array(46, 10), array(2, 64)), array(array(17, 4), array(29, 14), array(49, 10), array(24, 46)), array(array(4, 18), array(13, 32), array(48, 14), array(42, 32)), array(array(20, 4), array(40, 7), array(43, 22), array(10, 67)), array(array(19, 6), array(18, 31), array(34, 34), array(20, 61)));
        /**
         * @var array Positions of alignment patterns.
         * This array includes only the second and the third position of the alignment patterns. Rest of them can be calculated from the distance between them.
         * See Table 1 in Appendix E (pp.71) of JIS X0510:2004.
         * @access protected
         */
        protected $alignmentPattern = array(array(0, 0), array(0, 0), array(18, 0), array(22, 0), array(26, 0), array(30, 0), array(34, 0), array(22, 38), array(24, 42), array(26, 46), array(28, 50), array(30, 54), array(32, 58), array(34, 62), array(26, 46), array(26, 48), array(26, 50), array(30, 54), array(30, 56), array(30, 58), array(34, 62), array(28, 50), array(26, 50), array(30, 54), array(28, 54), array(32, 58), array(30, 58), array(34, 62), array(26, 50), array(30, 54), array(26, 52), array(30, 56), array(34, 60), array(30, 58), array(34, 62), array(30, 54), array(24, 50), array(28, 54), array(32, 58), array(26, 54), array(30, 58));
        /**
         * @var array Version information pattern (BCH coded).
         * See Table 1 in Appendix D (pp.68) of JIS X0510:2004.
         * size: [QRSPEC_VERSION_MAX - 6]
         * @access protected
         */
        protected $versionPattern = array(31892, 34236, 39577, 42195, 48118, 51042, 55367, 58893, 63784, 68472, 70749, 76311, 79154, 84390, 87683, 92361, 96236, 102084, 102881, 110507, 110734, 117786, 119615, 126325, 127568, 133589, 136944, 141498, 145311, 150283, 152622, 158308, 161089, 167017);
        /**
         * @var array Format information
         * @access protected
         */
        protected $formatInfo = array(array(30660, 29427, 32170, 30877, 26159, 25368, 27713, 26998), array(21522, 20773, 24188, 23371, 17913, 16590, 20375, 19104), array(13663, 12392, 16177, 14854, 9396, 8579, 11994, 11245), array(5769, 5054, 7399, 6608, 1890, 597, 3340, 2107));
        public function __construct($code, $eclevel = 'L')
        {
            $barcode_array = array();
            if (is_null($code) || $code == '\\0' || $code == '') {
                return false;
            }
            $this->level = array_search($eclevel, array('L', 'M', 'Q', 'H'));
            if ($this->level === false) {
                $this->level = QR_ECLEVEL_L;
            }
            if ($this->hint != QR_MODE_8B && $this->hint != QR_MODE_KJ) {
                return false;
            }
            if ($this->version < 0 || QRSPEC_VERSION_MAX < $this->version) {
                return false;
            }
            $this->items = array();
            $this->encodeString($code);
            $qrTab = $this->binarize($this->data);
            $size = count($qrTab);
            $barcode_array['num_rows'] = $size;
            $barcode_array['num_cols'] = $size;
            $barcode_array['bcode'] = array();
            foreach ($qrTab as $line) {
                $arrAdd = array();
                foreach (str_split($line) as $char) {
                    $arrAdd[] = $char == '1' ? 1 : 0;
                }
                $barcode_array['bcode'][] = $arrAdd;
            }
            $this->barcode_array = $barcode_array;
        }
        public function getBarcodeArray()
        {
            return $this->barcode_array;
        }
        protected function binarize($frame)
        {
            $len = count($frame);
            foreach ($frame as &$frameLine) {
                for ($i = 0; $i < $len; $i++) {
                    $frameLine[$i] = ord($frameLine[$i]) & 1 ? '1' : '0';
                }
            }
            return $frame;
        }
        protected function encodeString($string)
        {
            $this->dataStr = $string;
            if (!$this->casesensitive) {
                $this->toUpper();
            }
            $ret = $this->splitString();
            if ($ret < 0) {
                return NULL;
            }
            $this->encodeMask(-1);
        }
        protected function encodeMask($mask)
        {
            $spec = array(0, 0, 0, 0, 0);
            $this->datacode = $this->getByteStream($this->items);
            if (is_null($this->datacode)) {
                return NULL;
            }
            $spec = $this->getEccSpec($this->version, $this->level, $spec);
            $this->b1 = $this->rsBlockNum1($spec);
            $this->dataLength = $this->rsDataLength($spec);
            $this->eccLength = $this->rsEccLength($spec);
            $this->ecccode = array_fill(0, $this->eccLength, 0);
            $this->blocks = $this->rsBlockNum($spec);
            $ret = $this->init($spec);
            if ($ret < 0) {
                return NULL;
            }
            $this->count = 0;
            $this->width = $this->getWidth($this->version);
            $this->frame = $this->newFrame($this->version);
            $this->x = $this->width - 1;
            $this->y = $this->width - 1;
            $this->dir = -1;
            $this->bit = -1;
            for ($i = 0; $i < $this->dataLength + $this->eccLength; $i++) {
                $code = $this->getCode();
                $bit = 128;
                for ($j = 0; $j < 8; $j++) {
                    $addr = $this->getNextPosition();
                    $this->setFrameAt($addr, 2 | ($bit & $code) != 0);
                    $bit = $bit >> 1;
                }
            }
            $j = $this->getRemainder($this->version);
            for ($i = 0; $i < $j; $i++) {
                $addr = $this->getNextPosition();
                $this->setFrameAt($addr, 2);
            }
            $this->runLength = array_fill(0, QRSPEC_WIDTH_MAX + 1, 0);
            if ($mask < 0) {
                if (QR_FIND_BEST_MASK) {
                    $masked = $this->mask($this->width, $this->frame, $this->level);
                } else {
                    $masked = $this->makeMask($this->width, $this->frame, intval(QR_DEFAULT_MASK) % 8, $this->level);
                }
            } else {
                $masked = $this->makeMask($this->width, $this->frame, $mask, $this->level);
            }
            if ($masked == NULL) {
                return NULL;
            }
            $this->data = $masked;
        }
        protected function setFrameAt($at, $val)
        {
            $this->frame[$at['y']][$at['x']] = chr($val);
        }
        protected function getFrameAt($at)
        {
            return ord($this->frame[$at['y']][$at['x']]);
        }
        protected function getNextPosition()
        {
            do {
                if ($this->bit == -1) {
                    $this->bit = 0;
                    return array('x' => $this->x, 'y' => $this->y);
                }
                $x = $this->x;
                $y = $this->y;
                $w = $this->width;
                if ($this->bit == 0) {
                    $x--;
                    $this->bit++;
                } else {
                    $x++;
                    $y += $this->dir;
                    $this->bit--;
                }
                if ($this->dir < 0) {
                    if ($y < 0) {
                        $y = 0;
                        $x -= 2;
                        $this->dir = 1;
                        if ($x == 6) {
                            $x--;
                            $y = 9;
                        }
                    }
                } else {
                    if ($y == $w) {
                        $y = $w - 1;
                        $x -= 2;
                        $this->dir = -1;
                        if ($x == 6) {
                            $x--;
                            $y -= 8;
                        }
                    }
                }
                if ($x < 0 || $y < 0) {
                    return NULL;
                }
                $this->x = $x;
                $this->y = $y;
            } while (ord($this->frame[$y][$x]) & 128);
            return array('x' => $x, 'y' => $y);
        }
        protected function init($spec)
        {
            $dl = $this->rsDataCodes1($spec);
            $el = $this->rsEccCodes1($spec);
            $rs = $this->init_rs(8, 285, 0, 1, $el, 255 - $dl - $el);
            $blockNo = 0;
            $dataPos = 0;
            $eccPos = 0;
            $endfor = $this->rsBlockNum1($spec);
            for ($i = 0; $i < $endfor; ++$i) {
                $ecc = array_slice($this->ecccode, $eccPos);
                $this->rsblocks[$blockNo] = array();
                $this->rsblocks[$blockNo]['dataLength'] = $dl;
                $this->rsblocks[$blockNo]['data'] = array_slice($this->datacode, $dataPos);
                $this->rsblocks[$blockNo]['eccLength'] = $el;
                $ecc = $this->encode_rs_char($rs, $this->rsblocks[$blockNo]['data'], $ecc);
                $this->rsblocks[$blockNo]['ecc'] = $ecc;
                $this->ecccode = array_merge(array_slice($this->ecccode, 0, $eccPos), $ecc);
                $dataPos += $dl;
                $eccPos += $el;
                $blockNo++;
            }
            if ($this->rsBlockNum2($spec) == 0) {
                return 0;
            }
            $dl = $this->rsDataCodes2($spec);
            $el = $this->rsEccCodes2($spec);
            $rs = $this->init_rs(8, 285, 0, 1, $el, 255 - $dl - $el);
            if ($rs == NULL) {
                return -1;
            }
            $endfor = $this->rsBlockNum2($spec);
            for ($i = 0; $i < $endfor; ++$i) {
                $ecc = array_slice($this->ecccode, $eccPos);
                $this->rsblocks[$blockNo] = array();
                $this->rsblocks[$blockNo]['dataLength'] = $dl;
                $this->rsblocks[$blockNo]['data'] = array_slice($this->datacode, $dataPos);
                $this->rsblocks[$blockNo]['eccLength'] = $el;
                $ecc = $this->encode_rs_char($rs, $this->rsblocks[$blockNo]['data'], $ecc);
                $this->rsblocks[$blockNo]['ecc'] = $ecc;
                $this->ecccode = array_merge(array_slice($this->ecccode, 0, $eccPos), $ecc);
                $dataPos += $dl;
                $eccPos += $el;
                $blockNo++;
            }
            return 0;
        }
        protected function getCode()
        {
            if ($this->count < $this->dataLength) {
                $row = $this->count % $this->blocks;
                $col = $this->count / $this->blocks;
                if ($this->rsblocks[0]['dataLength'] <= $col) {
                    $row += $this->b1;
                }
                $ret = $this->rsblocks[$row]['data'][$col];
            } else {
                if ($this->count < $this->dataLength + $this->eccLength) {
                    $row = ($this->count - $this->dataLength) % $this->blocks;
                    $col = ($this->count - $this->dataLength) / $this->blocks;
                    $ret = $this->rsblocks[$row]['ecc'][$col];
                } else {
                    return 0;
                }
            }
            $this->count++;
            return $ret;
        }
        protected function writeFormatInformation($width, &$frame, $mask, $level)
        {
            $blacks = 0;
            $format = $this->getFormatInfo($mask, $level);
            for ($i = 0; $i < 8; ++$i) {
                if ($format & 1) {
                    $blacks += 2;
                    $v = 133;
                } else {
                    $v = 132;
                }
                $frame[8][$width - 1 - $i] = chr($v);
                if ($i < 6) {
                    $frame[$i][8] = chr($v);
                } else {
                    $frame[$i + 1][8] = chr($v);
                }
                $format = $format >> 1;
            }
            for ($i = 0; $i < 7; ++$i) {
                if ($format & 1) {
                    $blacks += 2;
                    $v = 133;
                } else {
                    $v = 132;
                }
                $frame[$width - 7 + $i][8] = chr($v);
                if ($i == 0) {
                    $frame[8][7] = chr($v);
                } else {
                    $frame[8][6 - $i] = chr($v);
                }
                $format = $format >> 1;
            }
            return $blacks;
        }
        protected function mask0($x, $y)
        {
            return $x + $y & 1;
        }
        protected function mask1($x, $y)
        {
            return $y & 1;
        }
        protected function mask2($x, $y)
        {
            return $x % 3;
        }
        protected function mask3($x, $y)
        {
            return ($x + $y) % 3;
        }
        protected function mask4($x, $y)
        {
            return (int) $y / 2 + (int) $x / 3 & 1;
        }
        protected function mask5($x, $y)
        {
            return ($x * $y & 1) + $x * $y % 3;
        }
        protected function mask6($x, $y)
        {
            return ($x * $y & 1) + $x * $y % 3 & 1;
        }
        protected function mask7($x, $y)
        {
            return $x * $y % 3 + ($x + $y & 1) & 1;
        }
        protected function generateMaskNo($maskNo, $width, $frame)
        {
            $bitMask = array_fill(0, $width, array_fill(0, $width, 0));
            for ($y = 0; $y < $width; ++$y) {
                for ($x = 0; $x < $width; ++$x) {
                    if (ord($frame[$y][$x]) & 128) {
                        $bitMask[$y][$x] = 0;
                    } else {
                        $maskFunc = call_user_func(array($this, 'mask' . $maskNo), $x, $y);
                        $bitMask[$y][$x] = $maskFunc == 0 ? 1 : 0;
                    }
                }
            }
            return $bitMask;
        }
        protected function makeMaskNo($maskNo, $width, $s, &$d, $maskGenOnly = false)
        {
            $b = 0;
            $bitMask = array();
            $bitMask = $this->generateMaskNo($maskNo, $width, $s, $d);
            if ($maskGenOnly) {
                return NULL;
            }
            $d = $s;
            for ($y = 0; $y < $width; ++$y) {
                for ($x = 0; $x < $width; ++$x) {
                    if ($bitMask[$y][$x] == 1) {
                        $d[$y][$x] = chr(ord($s[$y][$x]) ^ (int) $bitMask[$y][$x]);
                    }
                    $b += (int) ord($d[$y][$x]) & 1;
                }
            }
            return $b;
        }
        protected function makeMask($width, $frame, $maskNo, $level)
        {
            $masked = array_fill(0, $width, str_repeat('' . "\0" . '', $width));
            $this->makeMaskNo($maskNo, $width, $frame, $masked);
            $this->writeFormatInformation($width, $masked, $maskNo, $level);
            return $masked;
        }
        protected function calcN1N3($length)
        {
            $demerit = 0;
            for ($i = 0; $i < $length; ++$i) {
                if (5 <= $this->runLength[$i]) {
                    $demerit += N1 + ($this->runLength[$i] - 5);
                }
                if ($i & 1) {
                    if (3 <= $i && $i < $length - 2 && $this->runLength[$i] % 3 == 0) {
                        $fact = (int) $this->runLength[$i] / 3;
                        if ($this->runLength[$i - 2] == $fact && $this->runLength[$i - 1] == $fact && $this->runLength[$i + 1] == $fact && $this->runLength[$i + 2] == $fact) {
                            if ($this->runLength[$i - 3] < 0 || 4 * $fact <= $this->runLength[$i - 3]) {
                                $demerit += N3;
                            } else {
                                if ($length <= $i + 3 || 4 * $fact <= $this->runLength[$i + 3]) {
                                    $demerit += N3;
                                }
                            }
                        }
                    }
                }
            }
            return $demerit;
        }
        protected function evaluateSymbol($width, $frame)
        {
            $head = 0;
            $demerit = 0;
            for ($y = 0; $y < $width; ++$y) {
                $head = 0;
                $this->runLength[0] = 1;
                $frameY = $frame[$y];
                if (0 < $y) {
                    $frameYM = $frame[$y - 1];
                }
                for ($x = 0; $x < $width; ++$x) {
                    if (0 < $x && 0 < $y) {
                        $b22 = ord($frameY[$x]) & ord($frameY[$x - 1]) & ord($frameYM[$x]) & ord($frameYM[$x - 1]);
                        $w22 = ord($frameY[$x]) | ord($frameY[$x - 1]) | ord($frameYM[$x]) | ord($frameYM[$x - 1]);
                        if (($b22 | $w22 ^ 1) & 1) {
                            $demerit += N2;
                        }
                    }
                    if ($x == 0 && ord($frameY[$x]) & 1) {
                        $this->runLength[0] = -1;
                        $head = 1;
                        $this->runLength[$head] = 1;
                    } else {
                        if (0 < $x) {
                            if ((ord($frameY[$x]) ^ ord($frameY[$x - 1])) & 1) {
                                $head++;
                                $this->runLength[$head] = 1;
                            } else {
                                $this->runLength[$head]++;
                            }
                        }
                    }
                }
                $demerit += $this->calcN1N3($head + 1);
            }
            for ($x = 0; $x < $width; ++$x) {
                $head = 0;
                $this->runLength[0] = 1;
                for ($y = 0; $y < $width; ++$y) {
                    if ($y == 0 && ord($frame[$y][$x]) & 1) {
                        $this->runLength[0] = -1;
                        $head = 1;
                        $this->runLength[$head] = 1;
                    } else {
                        if (0 < $y) {
                            if ((ord($frame[$y][$x]) ^ ord($frame[$y - 1][$x])) & 1) {
                                $head++;
                                $this->runLength[$head] = 1;
                            } else {
                                $this->runLength[$head]++;
                            }
                        }
                    }
                }
                $demerit += $this->calcN1N3($head + 1);
            }
            return $demerit;
        }
        protected function mask($width, $frame, $level)
        {
            $minDemerit = PHP_INT_MAX;
            $bestMaskNum = 0;
            $bestMask = array();
            $checked_masks = array(0, 1, 2, 3, 4, 5, 6, 7);
            if (QR_FIND_FROM_RANDOM !== false) {
                $howManuOut = 8 - QR_FIND_FROM_RANDOM % 9;
                for ($i = 0; $i < $howManuOut; ++$i) {
                    $remPos = rand(0, count($checked_masks) - 1);
                    unset($checked_masks[$remPos]);
                    $checked_masks = array_values($checked_masks);
                }
            }
            $bestMask = $frame;
            foreach ($checked_masks as $i) {
                $mask = array_fill(0, $width, str_repeat('' . "\0" . '', $width));
                $demerit = 0;
                $blacks = 0;
                $blacks = $this->makeMaskNo($i, $width, $frame, $mask);
                $blacks += $this->writeFormatInformation($width, $mask, $i, $level);
                $blacks = (int) (100 * $blacks) / ($width * $width);
                $demerit = (int) (int) abs($blacks - 50) / 5 * N4;
                $demerit += $this->evaluateSymbol($width, $mask);
                if ($demerit < $minDemerit) {
                    $minDemerit = $demerit;
                    $bestMask = $mask;
                    $bestMaskNum = $i;
                }
            }
            return $bestMask;
        }
        protected function isdigitat($str, $pos)
        {
            if (strlen($str) <= $pos) {
                return false;
            }
            return ord('0') <= ord($str[$pos]) && ord($str[$pos]) <= ord('9');
        }
        protected function isalnumat($str, $pos)
        {
            if (strlen($str) <= $pos) {
                return false;
            }
            return 0 <= $this->lookAnTable(ord($str[$pos]));
        }
        protected function identifyMode($pos)
        {
            if (strlen($this->dataStr) <= $pos) {
                return QR_MODE_NL;
            }
            $c = $this->dataStr[$pos];
            if ($this->isdigitat($this->dataStr, $pos)) {
                return QR_MODE_NM;
            } else {
                if ($this->isalnumat($this->dataStr, $pos)) {
                    return QR_MODE_AN;
                } else {
                    if ($this->hint == QR_MODE_KJ) {
                        if ($pos + 1 < strlen($this->dataStr)) {
                            $d = $this->dataStr[$pos + 1];
                            $word = ord($c) << 8 | ord($d);
                            if (33088 <= $word && $word <= 40956 || 57408 <= $word && $word <= 60351) {
                                return QR_MODE_KJ;
                            }
                        }
                    }
                }
            }
            return QR_MODE_8B;
        }
        protected function eatNum()
        {
            $ln = $this->lengthIndicator(QR_MODE_NM, $this->version);
            $p = 0;
            while ($this->isdigitat($this->dataStr, $p)) {
                $p++;
            }
            $run = $p;
            $mode = $this->identifyMode($p);
            if ($mode == QR_MODE_8B) {
                $dif = $this->estimateBitsModeNum($run) + 4 + $ln + $this->estimateBitsMode8(1) - $this->estimateBitsMode8($run + 1);
                if (0 < $dif) {
                    return $this->eat8();
                }
            }
            if ($mode == QR_MODE_AN) {
                $dif = $this->estimateBitsModeNum($run) + 4 + $ln + $this->estimateBitsModeAn(1) - $this->estimateBitsModeAn($run + 1);
                if (0 < $dif) {
                    return $this->eatAn();
                }
            }
            $this->items = $this->appendNewInputItem($this->items, QR_MODE_NM, $run, str_split($this->dataStr));
            return $run;
        }
        protected function eatAn()
        {
            $la = $this->lengthIndicator(QR_MODE_AN, $this->version);
            $ln = $this->lengthIndicator(QR_MODE_NM, $this->version);
            $p = 0;
            while ($this->isalnumat($this->dataStr, $p)) {
                if ($this->isdigitat($this->dataStr, $p)) {
                    $q = $p;
                    while ($this->isdigitat($this->dataStr, $q)) {
                        $q++;
                    }
                    $dif = $this->estimateBitsModeAn($p) + $this->estimateBitsModeNum($q - $p) + 4 + $ln - $this->estimateBitsModeAn($q);
                    if ($dif < 0) {
                        break;
                    } else {
                        $p = $q;
                    }
                } else {
                    $p++;
                }
            }
            $run = $p;
            if (!$this->isalnumat($this->dataStr, $p)) {
                $dif = $this->estimateBitsModeAn($run) + 4 + $la + $this->estimateBitsMode8(1) - $this->estimateBitsMode8($run + 1);
                if (0 < $dif) {
                    return $this->eat8();
                }
            }
            $this->items = $this->appendNewInputItem($this->items, QR_MODE_AN, $run, str_split($this->dataStr));
            return $run;
        }
        protected function eatKanji()
        {
            $p = 0;
            while ($this->identifyMode($p) == QR_MODE_KJ) {
                $p += 2;
            }
            $this->items = $this->appendNewInputItem($this->items, QR_MODE_KJ, $p, str_split($this->dataStr));
            return $run;
        }
        protected function eat8()
        {
            $la = $this->lengthIndicator(QR_MODE_AN, $this->version);
            $ln = $this->lengthIndicator(QR_MODE_NM, $this->version);
            $p = 1;
            $dataStrLen = strlen($this->dataStr);
            while ($p < $dataStrLen) {
                $mode = $this->identifyMode($p);
                if ($mode == QR_MODE_KJ) {
                    break;
                }
                if ($mode == QR_MODE_NM) {
                    $q = $p;
                    while ($this->isdigitat($this->dataStr, $q)) {
                        $q++;
                    }
                    $dif = $this->estimateBitsMode8($p) + $this->estimateBitsModeNum($q - $p) + 4 + $ln - $this->estimateBitsMode8($q);
                    if ($dif < 0) {
                        break;
                    } else {
                        $p = $q;
                    }
                } else {
                    if ($mode == QR_MODE_AN) {
                        $q = $p;
                        while ($this->isalnumat($this->dataStr, $q)) {
                            $q++;
                        }
                        $dif = $this->estimateBitsMode8($p) + $this->estimateBitsModeAn($q - $p) + 4 + $la - $this->estimateBitsMode8($q);
                        if ($dif < 0) {
                            break;
                        } else {
                            $p = $q;
                        }
                    } else {
                        $p++;
                    }
                }
            }
            $run = $p;
            $this->items = $this->appendNewInputItem($this->items, QR_MODE_8B, $run, str_split($this->dataStr));
            return $run;
        }
        protected function splitString()
        {
            while (0 < strlen($this->dataStr)) {
                if ($this->dataStr == '') {
                    return 0;
                }
                $mode = $this->identifyMode(0);
                switch ($mode) {
                    case QR_MODE_NM:
                        $length = $this->eatNum();
                        break;
                    case QR_MODE_AN:
                        $length = $this->eatAn();
                        break;
                    case QR_MODE_KJ:
                        if ($hint == QR_MODE_KJ) {
                            $length = $this->eatKanji();
                        } else {
                            $length = $this->eat8();
                        }
                        break;
                    default:
                        $length = $this->eat8();
                        break;
                }
                if ($length == 0) {
                    return 0;
                }
                if ($length < 0) {
                    return -1;
                }
                $this->dataStr = substr($this->dataStr, $length);
            }
        }
        protected function toUpper()
        {
            $stringLen = strlen($this->dataStr);
            $p = 0;
            while ($p < $stringLen) {
                $mode = $this->identifyMode(substr($this->dataStr, $p), $this->hint);
                if ($mode == QR_MODE_KJ) {
                    $p += 2;
                } else {
                    if (ord('a') <= ord($this->dataStr[$p]) && ord($this->dataStr[$p]) <= ord('z')) {
                        $this->dataStr[$p] = chr(ord($this->dataStr[$p]) - 32);
                    }
                    $p++;
                }
            }
            return $this->dataStr;
        }
        protected function newInputItem($mode, $size, $data, $bstream = NULL)
        {
            $setData = array_slice($data, 0, $size);
            if (count($setData) < $size) {
                $setData = array_merge($setData, array_fill(0, $size - count($setData), 0));
            }
            if (!$this->check($mode, $size, $setData)) {
                return NULL;
            }
            $inputitem = array();
            $inputitem['mode'] = $mode;
            $inputitem['size'] = $size;
            $inputitem['data'] = $setData;
            $inputitem['bstream'] = $bstream;
            return $inputitem;
        }
        protected function encodeModeNum($inputitem, $version)
        {
            $words = (int) $inputitem['size'] / 3;
            $inputitem['bstream'] = array();
            $val = 1;
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, $val);
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], $this->lengthIndicator(QR_MODE_NM, $version), $inputitem['size']);
            for ($i = 0; $i < $words; ++$i) {
                $val = (ord($inputitem['data'][$i * 3]) - ord('0')) * 100;
                $val += (ord($inputitem['data'][$i * 3 + 1]) - ord('0')) * 10;
                $val += ord($inputitem['data'][$i * 3 + 2]) - ord('0');
                $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 10, $val);
            }
            if ($inputitem['size'] - $words * 3 == 1) {
                $val = ord($inputitem['data'][$words * 3]) - ord('0');
                $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, $val);
            } else {
                if ($inputitem['size'] - $words * 3 == 2) {
                    $val = (ord($inputitem['data'][$words * 3]) - ord('0')) * 10;
                    $val += ord($inputitem['data'][$words * 3 + 1]) - ord('0');
                    $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 7, $val);
                }
            }
            return $inputitem;
        }
        protected function encodeModeAn($inputitem, $version)
        {
            $words = (int) $inputitem['size'] / 2;
            $inputitem['bstream'] = array();
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, 2);
            $inputitem['bstream'] = $this->appendNum(v, $this->lengthIndicator(QR_MODE_AN, $version), $inputitem['size']);
            for ($i = 0; $i < $words; ++$i) {
                $val = (int) $this->lookAnTable(ord($inputitem['data'][$i * 2])) * 45;
                $val += (int) $this->lookAnTable(ord($inputitem['data'][$i * 2 + 1]));
                $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 11, $val);
            }
            if ($inputitem['size'] & 1) {
                $val = $this->lookAnTable(ord($inputitem['data'][$words * 2]));
                $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 6, $val);
            }
            return $inputitem;
        }
        protected function encodeMode8($inputitem, $version)
        {
            $inputitem['bstream'] = array();
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, 4);
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], $this->lengthIndicator(QR_MODE_8B, $version), $inputitem['size']);
            for ($i = 0; $i < $inputitem['size']; ++$i) {
                $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 8, ord($inputitem['data'][$i]));
            }
            return $inputitem;
        }
        protected function encodeModeKanji($inputitem, $version)
        {
            $inputitem['bstream'] = array();
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, 8);
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], $this->lengthIndicator(QR_MODE_KJ, $version), (int) $inputitem['size'] / 2);
            for ($i = 0; $i < $inputitem['size']; $i += 2) {
                $val = ord($inputitem['data'][$i]) << 8 | ord($inputitem['data'][$i + 1]);
                if ($val <= 40956) {
                    $val -= 33088;
                } else {
                    $val -= 49472;
                }
                $h = ($val >> 8) * 192;
                $val = ($val & 255) + $h;
                $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 13, $val);
            }
            return $inputitem;
        }
        protected function encodeModeStructure($inputitem)
        {
            $inputitem['bstream'] = array();
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, 3);
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, ord($inputitem['data'][1]) - 1);
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 4, ord($inputitem['data'][0]) - 1);
            $inputitem['bstream'] = $this->appendNum($inputitem['bstream'], 8, ord($inputitem['data'][2]));
            return $inputitem;
        }
        protected function encodeBitStream($inputitem, $version)
        {
            $inputitem['bstream'] = array();
            $words = $this->maximumWords($inputitem['mode'], $version);
            if ($words < $inputitem['size']) {
                $st1 = $this->newInputItem($inputitem['mode'], $words, $inputitem['data']);
                $st2 = $this->newInputItem($inputitem['mode'], $inputitem['size'] - $words, array_slice($inputitem['data'], $words));
                $st1 = $this->encodeBitStream($st1, $version);
                $st2 = $this->encodeBitStream($st2, $version);
                $inputitem['bstream'] = array();
                $inputitem['bstream'] = $this->appendBitstream($inputitem['bstream'], $st1['bstream']);
                $inputitem['bstream'] = $this->appendBitstream($inputitem['bstream'], $st2['bstream']);
            } else {
                switch ($inputitem['mode']) {
                    case QR_MODE_NM:
                        $inputitem = $this->encodeModeNum($inputitem, $version);
                        break;
                    case QR_MODE_AN:
                        $inputitem = $this->encodeModeAn($inputitem, $version);
                        break;
                    case QR_MODE_8B:
                        $inputitem = $this->encodeMode8($inputitem, $version);
                        break;
                    case QR_MODE_KJ:
                        $inputitem = $this->encodeModeKanji($inputitem, $version);
                        break;
                    case QR_MODE_ST:
                        $inputitem = $this->encodeModeStructure($inputitem);
                        break;
                    default:
                        break;
                }
            }
            return $inputitem;
        }
        protected function appendNewInputItem($items, $mode, $size, $data)
        {
            $items[] = $this->newInputItem($mode, $size, $data);
            return $items;
        }
        protected function insertStructuredAppendHeader($items, $size, $index, $parity)
        {
            if (MAX_STRUCTURED_SYMBOLS < $size) {
                return -1;
            }
            if ($index <= 0 || MAX_STRUCTURED_SYMBOLS < $index) {
                return -1;
            }
            $buf = array($size, $index, $parity);
            $entry = $this->newInputItem(QR_MODE_ST, 3, buf);
            array_unshift($items, $entry);
            return $items;
        }
        protected function calcParity($items)
        {
            $parity = 0;
            foreach ($items as $item) {
                if ($item['mode'] != QR_MODE_ST) {
                    for ($i = $item['size'] - 1; 0 <= $i; --$i) {
                        $parity ^= $item['data'][$i];
                    }
                }
            }
            return $parity;
        }
        protected function checkModeNum($size, $data)
        {
            for ($i = 0; $i < $size; ++$i) {
                if (ord($data[$i]) < ord('0') || ord('9') < ord($data[$i])) {
                    return false;
                }
            }
            return true;
        }
        protected function estimateBitsModeNum($size)
        {
            $w = (int) $size / 3;
            $bits = $w * 10;
            switch ($size - $w * 3) {
                case 1:
                    $bits += 4;
                    break;
                case 2:
                    $bits += 7;
                    break;
                default:
                    break;
            }
            $size - $w * 3;
            return $bits;
        }
        protected function lookAnTable($c)
        {
            return 127 < $c ? -1 : $this->anTable[$c];
        }
        protected function checkModeAn($size, $data)
        {
            for ($i = 0; $i < $size; ++$i) {
                if ($this->lookAnTable(ord($data[$i])) == -1) {
                    return false;
                }
            }
            return true;
        }
        protected function estimateBitsModeAn($size)
        {
            $w = (int) $size / 2;
            $bits = $w * 11;
            if ($size & 1) {
                $bits += 6;
            }
            return $bits;
        }
        protected function estimateBitsMode8($size)
        {
            return $size * 8;
        }
        protected function estimateBitsModeKanji($size)
        {
            return (int) ($size / 2) * 13;
        }
        protected function checkModeKanji($size, $data)
        {
            if ($size & 1) {
                return false;
            }
            for ($i = 0; $i < $size; $i += 2) {
                $val = ord($data[$i]) << 8 | ord($data[$i + 1]);
                if ($val < 33088 || 40956 < $val && $val < 57408 || 60351 < $val) {
                    return false;
                }
            }
            return true;
        }
        protected function check($mode, $size, $data)
        {
            if ($size <= 0) {
                return false;
            }
            switch ($mode) {
                case QR_MODE_NM:
                    return $this->checkModeNum($size, $data);
                case QR_MODE_AN:
                    return $this->checkModeAn($size, $data);
                case QR_MODE_KJ:
                    return $this->checkModeKanji($size, $data);
                case QR_MODE_8B:
                    return true;
                case QR_MODE_ST:
                    return true;
                default:
                    break;
            }
            return false;
        }
        protected function estimateBitStreamSize($items, $version)
        {
            $bits = 0;
            if ($version == 0) {
                $version = 1;
            }
            foreach ($items as $item) {
                switch ($item['mode']) {
                    case QR_MODE_NM:
                        $bits = $this->estimateBitsModeNum($item['size']);
                        break;
                    case QR_MODE_AN:
                        $bits = $this->estimateBitsModeAn($item['size']);
                        break;
                    case QR_MODE_8B:
                        $bits = $this->estimateBitsMode8($item['size']);
                        break;
                    case QR_MODE_KJ:
                        $bits = $this->estimateBitsModeKanji($item['size']);
                        break;
                    case QR_MODE_ST:
                        return STRUCTURE_HEADER_BITS;
                    default:
                        return 0;
                }
                $l = $this->lengthIndicator($item['mode'], $version);
                $m = 1 << $l;
                $num = (int) ($item['size'] + $m - 1) / $m;
                $bits += $num * (4 + $l);
            }
            return $bits;
        }
        protected function estimateVersion($items)
        {
            $version = 0;
            $prev = 0;
            do {
                $prev = $version;
                $bits = $this->estimateBitStreamSize($items, $prev);
                $version = $this->getMinimumVersion((int) ($bits + 7) / 8, $this->level);
                if ($version < 0) {
                    return -1;
                }
            } while ($prev < $version);
            return $version;
        }
        protected function lengthOfCode($mode, $version, $bits)
        {
            $payload = $bits - 4 - $this->lengthIndicator($mode, $version);
            switch ($mode) {
                case QR_MODE_NM:
                    $chunks = (int) $payload / 10;
                    $remain = $payload - $chunks * 10;
                    $size = $chunks * 3;
                    if (7 <= $remain) {
                        $size += 2;
                    } else {
                        if (4 <= $remain) {
                            $size += 1;
                        }
                    }
                    break;
                case QR_MODE_AN:
                    $chunks = (int) $payload / 11;
                    $remain = $payload - $chunks * 11;
                    $size = $chunks * 2;
                    if (6 <= $remain) {
                        ++$size;
                    }
                    break;
                case QR_MODE_8B:
                    $size = (int) $payload / 8;
                    break;
                case QR_MODE_KJ:
                    $size = (int) ($payload / 13) * 2;
                    break;
                case QR_MODE_ST:
                    $size = (int) $payload / 8;
                    break;
                default:
                    $size = 0;
                    break;
            }
            $maxsize = $this->maximumWords($mode, $version);
            if ($size < 0) {
                $size = 0;
            }
            if ($maxsize < $size) {
                $size = $maxsize;
            }
            return $size;
        }
        protected function createBitStream($items)
        {
            $total = 0;
            foreach ($items as $key => $item) {
                $items[$key] = $this->encodeBitStream($item, $this->version);
                $bits = count($items[$key]['bstream']);
                $total += $bits;
            }
            return array($items, $total);
        }
        protected function convertData($items)
        {
            $ver = $this->estimateVersion($items);
            if ($this->version < $ver) {
                $this->version = $ver;
            }
            for (;;) {
                $cbs = $this->createBitStream($items);
                $items = $cbs[0];
                $bits = $cbs[1];
                if ($bits < 0) {
                    return -1;
                }
                $ver = $this->getMinimumVersion((int) ($bits + 7) / 8, $this->level);
                if ($ver < 0) {
                    return -1;
                } else {
                    if ($this->version < $ver) {
                        $this->version = $ver;
                    } else {
                        break;
                    }
                }
            }
            return $items;
        }
        protected function appendPaddingBit($bstream)
        {
            $bits = count($bstream);
            $maxwords = $this->getDataLength($this->version, $this->level);
            $maxbits = $maxwords * 8;
            if ($maxbits == $bits) {
                return 0;
            }
            if ($maxbits - $bits < 5) {
                return $this->appendNum($bstream, $maxbits - $bits, 0);
            }
            $bits += 4;
            $words = (int) ($bits + 7) / 8;
            $padding = array();
            $padding = $this->appendNum($padding, $words * 8 - $bits + 4, 0);
            $padlen = $maxwords - $words;
            if (0 < $padlen) {
                $padbuf = array();
                for ($i = 0; $i < $padlen; ++$i) {
                    $padbuf[$i] = $i & 1 ? 17 : 236;
                }
                $padding = $this->appendBytes($padding, $padlen, $padbuf);
            }
            return $this->appendBitstream($bstream, $padding);
        }
        protected function mergeBitStream($items)
        {
            $items = $this->convertData($items);
            $bstream = array();
            foreach ($items as $item) {
                $bstream = $this->appendBitstream($bstream, $item['bstream']);
            }
            return $bstream;
        }
        protected function getBitStream($items)
        {
            $bstream = $this->mergeBitStream($items);
            return $this->appendPaddingBit($bstream);
        }
        protected function getByteStream($items)
        {
            $bstream = $this->getBitStream($items);
            return $this->bitstreamToByte($bstream);
        }
        protected function allocate($setLength)
        {
            return array_fill(0, $setLength, 0);
        }
        protected function newFromNum($bits, $num)
        {
            $bstream = $this->allocate($bits);
            $mask = 1 << $bits - 1;
            for ($i = 0; $i < $bits; ++$i) {
                if ($num & $mask) {
                    $bstream[$i] = 1;
                } else {
                    $bstream[$i] = 0;
                }
                $mask = $mask >> 1;
            }
            return $bstream;
        }
        protected function newFromBytes($size, $data)
        {
            $bstream = $this->allocate($size * 8);
            $p = 0;
            for ($i = 0; $i < $size; ++$i) {
                $mask = 128;
                for ($j = 0; $j < 8; ++$j) {
                    if ($data[$i] & $mask) {
                        $bstream[$p] = 1;
                    } else {
                        $bstream[$p] = 0;
                    }
                    $p++;
                    $mask = $mask >> 1;
                }
            }
            return $bstream;
        }
        protected function appendBitstream($bitstream, $append)
        {
            if (!is_array($append) || count($append) == 0) {
                return $bitstream;
            }
            if (count($bitstream) == 0) {
                return $append;
            }
            return array_values(array_merge($bitstream, $append));
        }
        protected function appendNum($bitstream, $bits, $num)
        {
            if ($bits == 0) {
                return 0;
            }
            $b = $this->newFromNum($bits, $num);
            return $this->appendBitstream($bitstream, $b);
        }
        protected function appendBytes($bitstream, $size, $data)
        {
            if ($size == 0) {
                return 0;
            }
            $b = $this->newFromBytes($size, $data);
            return $this->appendBitstream($bitstream, $b);
        }
        protected function bitstreamToByte($bstream)
        {
            $size = count($bstream);
            if ($size == 0) {
                return array();
            }
            $data = array_fill(0, (int) ($size + 7) / 8, 0);
            $bytes = (int) $size / 8;
            $p = 0;
            for ($i = 0; $i < $bytes; $i++) {
                $v = 0;
                for ($j = 0; $j < 8; $j++) {
                    $v = $v << 1;
                    $v |= $bstream[$p];
                    $p++;
                }
                $data[$i] = $v;
            }
            if ($size & 7) {
                $v = 0;
                for ($j = 0; $j < ($size & 7); $j++) {
                    $v = $v << 1;
                    $v |= $bstream[$p];
                    $p++;
                }
                $data[$bytes] = $v;
            }
            return $data;
        }
        protected function qrstrset($srctab, $x, $y, $repl, $replLen = false)
        {
            $srctab[$y] = substr_replace($srctab[$y], $replLen !== false ? substr($repl, 0, $replLen) : $repl, $x, $replLen !== false ? $replLen : strlen($repl));
            return $srctab;
        }
        protected function getDataLength($version, $level)
        {
            return $this->capacity[$version][QRCAP_WORDS] - $this->capacity[$version][QRCAP_EC][$level];
        }
        protected function getECCLength($version, $level)
        {
            return $this->capacity[$version][QRCAP_EC][$level];
        }
        protected function getWidth($version)
        {
            return $this->capacity[$version][QRCAP_WIDTH];
        }
        protected function getRemainder($version)
        {
            return $this->capacity[$version][QRCAP_REMINDER];
        }
        protected function getMinimumVersion($size, $level)
        {
            for ($i = 1; $i <= QRSPEC_VERSION_MAX; ++$i) {
                $words = $this->capacity[$i][QRCAP_WORDS] - $this->capacity[$i][QRCAP_EC][$level];
                if ($size <= $words) {
                    return $i;
                }
            }
            return -1;
        }
        protected function lengthIndicator($mode, $version)
        {
            if ($mode == QR_MODE_ST) {
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
            return $this->lengthTableBits[$mode][$l];
        }
        protected function maximumWords($mode, $version)
        {
            if ($mode == QR_MODE_ST) {
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
            $bits = $this->lengthTableBits[$mode][$l];
            $words = (1 << $bits) - 1;
            if ($mode == QR_MODE_KJ) {
                $words *= 2;
            }
            return $words;
        }
        protected function getEccSpec($version, $level, $spec)
        {
            if (count($spec) < 5) {
                $spec = array(0, 0, 0, 0, 0);
            }
            $b1 = $this->eccTable[$version][$level][0];
            $b2 = $this->eccTable[$version][$level][1];
            $data = $this->getDataLength($version, $level);
            $ecc = $this->getECCLength($version, $level);
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
            return $spec;
        }
        protected function putAlignmentMarker($frame, $ox, $oy)
        {
            $finder = array('　　?, '牋?, '?, '牋?, '　　?);
            $yStart = $oy - 2;
            $xStart = $ox - 2;
            for ($y = 0; $y < 5; $y++) {
                $frame = $this->qrstrset($frame, $xStart, $yStart + $y, $finder[$y]);
            }
            return $frame;
        }
        protected function putAlignmentPattern($version, $frame, $width)
        {
            if ($version < 2) {
                return $frame;
            }
            $d = $this->alignmentPattern[$version][1] - $this->alignmentPattern[$version][0];
            if ($d < 0) {
                $w = 2;
            } else {
                $w = (int) (($width - $this->alignmentPattern[$version][0]) / $d) + 2;
            }
            if ($w * $w - 3 == 1) {
                $x = $this->alignmentPattern[$version][0];
                $y = $this->alignmentPattern[$version][0];
                $frame = $this->putAlignmentMarker($frame, $x, $y);
                return $frame;
            }
            $cx = $this->alignmentPattern[$version][0];
            $wo = $w - 1;
            for ($x = 1; $x < $wo; ++$x) {
                $frame = $this->putAlignmentMarker($frame, 6, $cx);
                $frame = $this->putAlignmentMarker($frame, $cx, 6);
                $cx += $d;
            }
            $cy = $this->alignmentPattern[$version][0];
            for ($y = 0; $y < $wo; ++$y) {
                $cx = $this->alignmentPattern[$version][0];
                for ($x = 0; $x < $wo; ++$x) {
                    $frame = $this->putAlignmentMarker($frame, $cx, $cy);
                    $cx += $d;
                }
                $cy += $d;
            }
            return $frame;
        }
        protected function getVersionPattern($version)
        {
            if ($version < 7 || QRSPEC_VERSION_MAX < $version) {
                return 0;
            }
            return $this->versionPattern[$version - 7];
        }
        protected function getFormatInfo($mask, $level)
        {
            if ($mask < 0 || 7 < $mask) {
                return 0;
            }
            if ($level < 0 || 3 < $level) {
                return 0;
            }
            return $this->formatInfo[$level][$mask];
        }
        protected function putFinderPattern($frame, $ox, $oy)
        {
            $finder = array('亮亮亮?, '晾览览?, '晾亮晾?, '晾亮晾?, '晾亮晾?, '晾览览?, '亮亮亮?);
            for ($y = 0; $y < 7; $y++) {
                $frame = $this->qrstrset($frame, $ox, $oy + $y, $finder[$y]);
            }
            return $frame;
        }
        protected function createFrame($version)
        {
            $width = $this->capacity[$version][QRCAP_WIDTH];
            $frameLine = str_repeat('' . "\0" . '', $width);
            $frame = array_fill(0, $width, $frameLine);
            $frame = $this->putFinderPattern($frame, 0, 0);
            $frame = $this->putFinderPattern($frame, $width - 7, 0);
            $frame = $this->putFinderPattern($frame, 0, $width - 7);
            $yOffset = $width - 7;
            for ($y = 0; $y < 7; ++$y) {
                $frame[$y][7] = '?;
                $frame[$y][$width - 8] = '?;
                $frame[$yOffset][7] = '?;
                ++$yOffset;
            }
            $setPattern = str_repeat('?, 8);
            $frame = $this->qrstrset($frame, 0, 7, $setPattern);
            $frame = $this->qrstrset($frame, $width - 8, 7, $setPattern);
            $frame = $this->qrstrset($frame, 0, $width - 8, $setPattern);
            $setPattern = str_repeat('?, 9);
            $frame = $this->qrstrset($frame, 0, 8, $setPattern);
            $frame = $this->qrstrset($frame, $width - 8, 8, $setPattern, 8);
            $yOffset = $width - 8;
            for ($y = 0; $y < 8; ++$y, ++$yOffset) {
                $frame[$y][8] = '?;
                $frame[$yOffset][8] = '?;
            }
            $wo = $width - 15;
            for ($i = 1; $i < $wo; ++$i) {
                $frame[6][7 + $i] = chr(144 | $i & 1);
                $frame[7 + $i][6] = chr(144 | $i & 1);
            }
            $frame = $this->putAlignmentPattern($version, $frame, $width);
            if (7 <= $version) {
                $vinf = $this->getVersionPattern($version);
                $v = $vinf;
                for ($x = 0; $x < 6; ++$x) {
                    for ($y = 0; $y < 3; ++$y) {
                        $frame[$width - 11 + $y][$x] = chr(136 | $v & 1);
                        $v = $v >> 1;
                    }
                }
                $v = $vinf;
                for ($y = 0; $y < 6; ++$y) {
                    for ($x = 0; $x < 3; ++$x) {
                        $frame[$y][$x + ($width - 11)] = chr(136 | $v & 1);
                        $v = $v >> 1;
                    }
                }
            }
            $frame[$width - 8][8] = '?;
            return $frame;
        }
        protected function newFrame($version)
        {
            if ($version < 1 || QRSPEC_VERSION_MAX < $version) {
                return NULL;
            }
            if (!isset($this->frames[$version])) {
                $this->frames[$version] = $this->createFrame($version);
            }
            if (is_null($this->frames[$version])) {
                return NULL;
            }
            return $this->frames[$version];
        }
        protected function rsBlockNum($spec)
        {
            return $spec[0] + $spec[3];
        }
        protected function rsBlockNum1($spec)
        {
            return $spec[0];
        }
        protected function rsDataCodes1($spec)
        {
            return $spec[1];
        }
        protected function rsEccCodes1($spec)
        {
            return $spec[2];
        }
        protected function rsBlockNum2($spec)
        {
            return $spec[3];
        }
        protected function rsDataCodes2($spec)
        {
            return $spec[4];
        }
        protected function rsEccCodes2($spec)
        {
            return $spec[2];
        }
        protected function rsDataLength($spec)
        {
            return $spec[0] * $spec[1] + $spec[3] * $spec[4];
        }
        protected function rsEccLength($spec)
        {
            return ($spec[0] + $spec[3]) * $spec[2];
        }
        protected function init_rs($symsize, $gfpoly, $fcr, $prim, $nroots, $pad)
        {
            foreach ($this->rsitems as $rs) {
                if ($rs['pad'] != $pad || $rs['nroots'] != $nroots || $rs['mm'] != $symsize || $rs['gfpoly'] != $gfpoly || $rs['fcr'] != $fcr || $rs['prim'] != $prim) {
                    continue;
                }
                return $rs;
            }
            $rs = $this->init_rs_char($symsize, $gfpoly, $fcr, $prim, $nroots, $pad);
            array_unshift($this->rsitems, $rs);
            return $rs;
        }
        protected function modnn($rs, $x)
        {
            while ($rs['nn'] <= $x) {
                $x -= $rs['nn'];
                $x = ($x >> $rs['mm']) + ($x & $rs['nn']);
            }
            return $x;
        }
        protected function init_rs_char($symsize, $gfpoly, $fcr, $prim, $nroots, $pad)
        {
            $rs = NULL;
            if ($symsize < 0 || 8 < $symsize) {
                return $rs;
            }
            if ($fcr < 0 || 1 << $symsize <= $fcr) {
                return $rs;
            }
            if ($prim <= 0 || 1 << $symsize <= $prim) {
                return $rs;
            }
            if ($nroots < 0 || 1 << $symsize <= $nroots) {
                return $rs;
            }
            if ($pad < 0 || (1 << $symsize) - 1 - $nroots <= $pad) {
                return $rs;
            }
            $rs = array();
            $rs['mm'] = $symsize;
            $rs['nn'] = (1 << $symsize) - 1;
            $rs['pad'] = $pad;
            $rs['alpha_to'] = array_fill(0, $rs['nn'] + 1, 0);
            $rs['index_of'] = array_fill(0, $rs['nn'] + 1, 0);
            $NN =& $rs['nn'];
            $A0 =& $NN;
            $rs['index_of'][0] = $A0;
            $rs['alpha_to'][$A0] = 0;
            $sr = 1;
            for ($i = 0; $i < $rs['nn']; ++$i) {
                $rs['index_of'][$sr] = $i;
                $rs['alpha_to'][$i] = $sr;
                $sr <<= 1;
                if ($sr & 1 << $symsize) {
                    $sr ^= $gfpoly;
                }
                $sr &= $rs['nn'];
            }
            if ($sr != 1) {
                return NULL;
            }
            $rs['genpoly'] = array_fill(0, $nroots + 1, 0);
            $rs['fcr'] = $fcr;
            $rs['prim'] = $prim;
            $rs['nroots'] = $nroots;
            $rs['gfpoly'] = $gfpoly;
            for ($iprim = 1; $iprim % $prim != 0; $iprim += $rs['nn']) {
            }
            $rs['iprim'] = (int) $iprim / $prim;
            $rs['genpoly'][0] = 1;
            $i = 0;
            for ($root = $fcr * $prim; $i < $nroots; $i++, $root += $prim) {
                $rs['genpoly'][$i + 1] = 1;
                for ($j = $i; 0 < $j; --$j) {
                    if ($rs['genpoly'][$j] != 0) {
                        $rs['genpoly'][$j] = $rs['genpoly'][$j - 1] ^ $rs['alpha_to'][$this->modnn($rs, $rs['index_of'][$rs['genpoly'][$j]] + $root)];
                    } else {
                        $rs['genpoly'][$j] = $rs['genpoly'][$j - 1];
                    }
                }
                $rs['genpoly'][0] = $rs['alpha_to'][$this->modnn($rs, $rs['index_of'][$rs['genpoly'][0]] + $root)];
            }
            for ($i = 0; $i <= $nroots; ++$i) {
                $rs['genpoly'][$i] = $rs['index_of'][$rs['genpoly'][$i]];
            }
            return $rs;
        }
        protected function encode_rs_char($rs, $data, $parity)
        {
            $MM =& $rs['mm'];
            $NN =& $rs['nn'];
            $ALPHA_TO =& $rs['alpha_to'];
            $INDEX_OF =& $rs['index_of'];
            $GENPOLY =& $rs['genpoly'];
            $NROOTS =& $rs['nroots'];
            $FCR =& $rs['fcr'];
            $PRIM =& $rs['prim'];
            $IPRIM =& $rs['iprim'];
            $PAD =& $rs['pad'];
            $A0 =& $NN;
            $parity = array_fill(0, $NROOTS, 0);
            for ($i = 0; $i < $NN - $NROOTS - $PAD; $i++) {
                $feedback = $INDEX_OF[$data[$i] ^ $parity[0]];
                if ($feedback != $A0) {
                    $feedback = $this->modnn($rs, $NN - $GENPOLY[$NROOTS] + $feedback);
                    for ($j = 1; $j < $NROOTS; ++$j) {
                        $parity[$j] ^= $ALPHA_TO[$this->modnn($rs, $feedback + $GENPOLY[$NROOTS - $j])];
                    }
                }
                array_shift($parity);
                if ($feedback != $A0) {
                    array_push($parity, $ALPHA_TO[$this->modnn($rs, $feedback + $GENPOLY[0])]);
                } else {
                    array_push($parity, 0);
                }
            }
            return $parity;
        }
    }
}
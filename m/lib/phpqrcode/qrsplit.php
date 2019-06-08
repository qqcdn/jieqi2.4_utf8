<?php

class QRsplit
{
    public $dataStr = '';
    public $input;
    public $modeHint;
    public function __construct($dataStr, $input, $modeHint)
    {
        $this->dataStr = $dataStr;
        $this->input = $input;
        $this->modeHint = $modeHint;
    }
    public static function isdigitat($str, $pos)
    {
        if (strlen($str) <= $pos) {
            return false;
        }
        return ord('0') <= ord($str[$pos]) && ord($str[$pos]) <= ord('9');
    }
    public static function isalnumat($str, $pos)
    {
        if (strlen($str) <= $pos) {
            return false;
        }
        return 0 <= QRinput::lookAnTable(ord($str[$pos]));
    }
    public function identifyMode($pos)
    {
        if (strlen($this->dataStr) <= $pos) {
            return QR_MODE_NUL;
        }
        $c = $this->dataStr[$pos];
        if (self::isdigitat($this->dataStr, $pos)) {
            return QR_MODE_NUM;
        } else {
            if (self::isalnumat($this->dataStr, $pos)) {
                return QR_MODE_AN;
            } else {
                if ($this->modeHint == QR_MODE_KANJI) {
                    if ($pos + 1 < strlen($this->dataStr)) {
                        $d = $this->dataStr[$pos + 1];
                        $word = ord($c) << 8 | ord($d);
                        if (33088 <= $word && $word <= 40956 || 57408 <= $word && $word <= 60351) {
                            return QR_MODE_KANJI;
                        }
                    }
                }
            }
        }
        return QR_MODE_8;
    }
    public function eatNum()
    {
        $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());
        $p = 0;
        while (self::isdigitat($this->dataStr, $p)) {
            $p++;
        }
        $run = $p;
        $mode = $this->identifyMode($p);
        if ($mode == QR_MODE_8) {
            $dif = QRinput::estimateBitsModeNum($run) + 4 + $ln + QRinput::estimateBitsMode8(1) - QRinput::estimateBitsMode8($run + 1);
            if (0 < $dif) {
                return $this->eat8();
            }
        }
        if ($mode == QR_MODE_AN) {
            $dif = QRinput::estimateBitsModeNum($run) + 4 + $ln + QRinput::estimateBitsModeAn(1) - QRinput::estimateBitsModeAn($run + 1);
            if (0 < $dif) {
                return $this->eatAn();
            }
        }
        $ret = $this->input->append(QR_MODE_NUM, $run, str_split($this->dataStr));
        if ($ret < 0) {
            return -1;
        }
        return $run;
    }
    public function eatAn()
    {
        $la = QRspec::lengthIndicator(QR_MODE_AN, $this->input->getVersion());
        $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());
        $p = 0;
        while (self::isalnumat($this->dataStr, $p)) {
            if (self::isdigitat($this->dataStr, $p)) {
                $q = $p;
                while (self::isdigitat($this->dataStr, $q)) {
                    $q++;
                }
                $dif = QRinput::estimateBitsModeAn($p) + QRinput::estimateBitsModeNum($q - $p) + 4 + $ln - QRinput::estimateBitsModeAn($q);
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
        if (!self::isalnumat($this->dataStr, $p)) {
            $dif = QRinput::estimateBitsModeAn($run) + 4 + $la + QRinput::estimateBitsMode8(1) - QRinput::estimateBitsMode8($run + 1);
            if (0 < $dif) {
                return $this->eat8();
            }
        }
        $ret = $this->input->append(QR_MODE_AN, $run, str_split($this->dataStr));
        if ($ret < 0) {
            return -1;
        }
        return $run;
    }
    public function eatKanji()
    {
        $p = 0;
        while ($this->identifyMode($p) == QR_MODE_KANJI) {
            $p += 2;
        }
        $ret = $this->input->append(QR_MODE_KANJI, $p, str_split($this->dataStr));
        if ($ret < 0) {
            return -1;
        }
        return $run;
    }
    public function eat8()
    {
        $la = QRspec::lengthIndicator(QR_MODE_AN, $this->input->getVersion());
        $ln = QRspec::lengthIndicator(QR_MODE_NUM, $this->input->getVersion());
        $p = 1;
        $dataStrLen = strlen($this->dataStr);
        while ($p < $dataStrLen) {
            $mode = $this->identifyMode($p);
            if ($mode == QR_MODE_KANJI) {
                break;
            }
            if ($mode == QR_MODE_NUM) {
                $q = $p;
                while (self::isdigitat($this->dataStr, $q)) {
                    $q++;
                }
                $dif = QRinput::estimateBitsMode8($p) + QRinput::estimateBitsModeNum($q - $p) + 4 + $ln - QRinput::estimateBitsMode8($q);
                if ($dif < 0) {
                    break;
                } else {
                    $p = $q;
                }
            } else {
                if ($mode == QR_MODE_AN) {
                    $q = $p;
                    while (self::isalnumat($this->dataStr, $q)) {
                        $q++;
                    }
                    $dif = QRinput::estimateBitsMode8($p) + QRinput::estimateBitsModeAn($q - $p) + 4 + $la - QRinput::estimateBitsMode8($q);
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
        $ret = $this->input->append(QR_MODE_8, $run, str_split($this->dataStr));
        if ($ret < 0) {
            return -1;
        }
        return $run;
    }
    public function splitString()
    {
        while (0 < strlen($this->dataStr)) {
            if ($this->dataStr == '') {
                return 0;
            }
            $mode = $this->identifyMode(0);
            switch ($mode) {
                case QR_MODE_NUM:
                    $length = $this->eatNum();
                    break;
                case QR_MODE_AN:
                    $length = $this->eatAn();
                    break;
                case QR_MODE_KANJI:
                    if ($hint == QR_MODE_KANJI) {
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
    public function toUpper()
    {
        $stringLen = strlen($this->dataStr);
        $p = 0;
        while ($p < $stringLen) {
            $mode = self::identifyMode(substr($this->dataStr, $p), $this->modeHint);
            if ($mode == QR_MODE_KANJI) {
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
    public static function splitStringToQRinput($string, QRinput $input, $modeHint, $casesensitive = true)
    {
        if (is_null($string) || $string == '\\0' || $string == '') {
            throw new Exception('empty string!!!');
        }
        $split = new QRsplit($string, $input, $modeHint);
        if (!$casesensitive) {
            $split->toUpper();
        }
        return $split->splitString();
    }
}
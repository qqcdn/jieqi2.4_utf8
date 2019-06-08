<?php

class TextFilter extends JieqiObject
{
    public $badwords = array();
    public $hidewords = array();
    public $replacewords = array();
    public function loadBadwords(&$badwords)
    {
        if (is_array($badwords)) {
            $this->badwords = $badwords;
        }
    }
    public function loadHidewords(&$hidewords)
    {
        if (is_array($hidewords)) {
            $this->hidewords = $hidewords;
        }
    }
    public function loadReplacewords(&$replacewords)
    {
        if (is_array($replacewords)) {
            $this->replacewords = $replacewords;
        }
    }
    public function checkBadwords(&$text)
    {
        $ret = true;
        if (0 < count($this->badwords)) {
            foreach ($this->badwords as $v) {
                if ($ret && 0 < strlen($v) && !empty($v)) {
                    if (strstr($text, $v)) {
                        $ret = false;
                    }
                }
            }
        }
        return $ret;
    }
    public function doHidewords($text, $replace = '***')
    {
        if (0 < count($this->hidewords)) {
            $text = str_replace($this->hidewords, $replace, $text);
            return $text;
        } else {
            return $text;
        }
    }
    public function doReplacewords($text)
    {
        if (0 < count($this->replacewords)) {
            $from = array();
            $to = array();
            foreach ($this->replacewords as $k => $v) {
                $from[] = $k;
                $to[] = $v;
            }
            return str_replace($from, $to, $text);
        } else {
            return $text;
        }
    }
    public function checkRubbish(&$text)
    {
        $ret = false;
        $len = strlen($text);
        $specialnum = 0;
        $tmpstr = '';
        $tmpstr1 = '';
        $renum = 0;
        for ($i = 0; $i < $len; $i++) {
            if (128 < ord($text[$i])) {
                $tmpstr = $text[$i] . $text[$i + 1];
                $i++;
            } else {
                $tmpstr = $text[$i];
                $tmpasc = ord($text[$i]);
                if ($tmpasc < 65 || 90 < $tmpasc && $tmpasc < 97 || 122 < $tmpasc) {
                    $specialnum++;
                }
            }
            if ($tmpstr == $tmpstr1) {
                $renum++;
                if (4 < $renum) {
                    return true;
                }
            } else {
                $renum = 0;
            }
            if ($tmpstr != ' ') {
                $tmpstr1 = $tmpstr;
            }
        }
        return $ret;
    }
}
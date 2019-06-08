<?php

class TextTypeset extends JieqiObject
{
    public $freplace = array();
    public $treplace = array();
    public $delchars = array();
    public $delstart = array();
    public $errstartchars = array();
    public $fmore = array();
    public $tmore = array();
    public function __construct()
    {
        $this->freplace = array(',', '.', '·', '．', ';', '!', '?', ':', '(', ')');
        $this->treplace = array('，', '。', '。', '。', '；', '！', '？', '：', '（', '）');
        $this->delchars = array("\r");
        $this->delstart = array(' ', '　');
        $this->errstartchars = array('。', '？', '！', '」', '”', '）');
        $this->fmore = array('.', '。', '-');
        $this->tmore = array('……', '……', '——');
    }
    public function doTypeset(&$str)
    {
        $ret = '';
        $tmpstr = '';
        $tmpstr1 = '';
        $repeatnum = 0;
        $start = true;
        $sectionstart = true;
        $strlen = strlen($str);
        for ($i = 0; $i < $strlen; $i++) {
            $tmpstr = $str[$i];
            if (128 < ord($str[$i]) && $i + 1 < $strlen) {
                $tmpstr .= $str[++$i];
            }
            if (in_array($tmpstr, $this->delchars)) {
                continue;
            }
            if ($sectionstart && in_array($tmpstr, $this->delstart)) {
                continue;
            }
            if ($tmpstr == "\n") {
                $sectionstart = true;
                continue;
            }
            if ($sectionstart && in_array($tmpstr, $this->errstartchars)) {
                $sectionstart = false;
            }
            $tmpvar = $repeatnum;
            if (in_array($tmpstr, $this->fmore)) {
                if ($tmpstr == $tmpstr1) {
                    $repeatnum++;
                } else {
                    $tmpstr1 = $tmpstr;
                    $repeatnum = 1;
                }
                continue;
            }
            if (0 < $tmpvar && $tmpvar == $repeatnum) {
                if ($repeatnum == 1) {
                    $ret .= $tmpstr1;
                } else {
                    $key = array_search($tmpstr1, $this->fmore);
                    if ($key !== false) {
                        $ret .= $this->tmore[$key];
                    }
                }
                $tmpstr1 = '';
                $repeatnum = 0;
            }
            if ($sectionstart) {
                if (!$start) {
                    $ret .= '' . "\r\n" . '' . "\r\n" . '';
                } else {
                    $start = false;
                }
                $ret .= '    ';
                $sectionstart = false;
            }
            $ret .= $tmpstr;
        }
        if ($repeatnum == 1) {
            $ret .= $tmpstr1;
        } else {
            if (1 < $repeatnum) {
                $key = array_search($tmpstr1, $this->fmore);
                if ($key !== false) {
                    $ret .= $this->tmore[$key];
                }
            }
        }
        return $ret;
    }
}
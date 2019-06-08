<?php

class TextConvert extends JieqiObject
{
    public $smiles = array();
    public function __construct()
    {
    }
    public function getSmiles()
    {
        return $this->smiles;
    }
    public function loadSmiles()
    {
        global $jieqiSmiles;
        jieqi_getconfigs('system', 'smiles');
        $this->smiles =& $jieqiSmiles;
    }
    public function smile($message)
    {
        if (count($this->smiles) == 0) {
            $this->loadSmiles();
        }
        $from = array();
        $to = array();
        foreach ($this->smiles as $k => $v) {
            $from[$k] = $v['code'];
            $to[$k] = '<img class="smile" src="' . JIEQI_URL . '/images/smiles/' . $v['url'] . '" alt="' . jieqi_htmlstr($v['emotion']) . '" border="0" />';
        }
        $message = str_replace($from, $to, $message);
        return $message;
    }
    public function makeClickable($text, $limitsize = 0)
    {
        $styles = array();
        if (0 < $limitsize) {
            $styles['imgresize'] = $limitsize;
        }
        return jieqi_htmlclickable($text, $styles);
    }
    public function jieqiCodeDecode($text, $allowimage = 1, $limitsize = 0)
    {
        $patterns = array();
        $replacements = array();
        $patterns[] = '/\\[code](.*)\\[\\/code\\]/sU';
        $replacements[] = '<div class="jieqiCode"><code>\\1</code></div>';
        $patterns[] = '/\\[url=([\'"]?)(http[s]?:\\/\\/[^"\'<>]*)\\1](.*)\\[\\/url\\]/sU';
        $replacements[] = '<a href="\\2" target="_blank">\\3</a>';
        $patterns[] = '/\\[url=([\'"]?)(ftp?:\\/\\/[^"\'<>]*)\\1](.*)\\[\\/url\\]/sU';
        $replacements[] = '<a href="\\2" target="_blank">\\3</a>';
        $patterns[] = '/\\[url=([\'"]?)([^"\'<>]*)\\1](.*)\\[\\/url\\]/sU';
        $replacements[] = '<a href="http://\\2" target="_blank">\\3</a>';
        $patterns[] = '/\\[color=([\'"]?)([a-zA-Z0-9]*)\\1](.*)\\[\\/color\\]/sU';
        $replacements[] = '<span style="color: #\\2;">\\3</span>';
        $patterns[] = '/\\[size=([\'"]?)([a-z0-9-]*)\\1](.*)\\[\\/size\\]/sU';
        $replacements[] = '<span style="font-size: \\2px;">\\3</span>';
        $patterns[] = '/\\[font=([\'"]?)([^;<>\\*\\(\\)"\']*)\\1](.*)\\[\\/font\\]/sU';
        $replacements[] = '<span style="font-family: \\2;">\\3</span>';
        $patterns[] = '/\\[align=([\'"]?)([^;<>\\*\\(\\)"\']*)\\1](.*)\\[\\/align\\]/sU';
        $replacements[] = '<p align="\\2">\\3</p>';
        $patterns[] = '/\\[email]([^;<>\\*\\(\\)"\']*)\\[\\/email\\]/sU';
        $replacements[] = '<a href="mailto:\\1">\\1</a>';
        $patterns[] = '/\\[b](.*)\\[\\/b\\]/sU';
        $replacements[] = '<b>\\1</b>';
        $patterns[] = '/\\[i](.*)\\[\\/i\\]/sU';
        $replacements[] = '<i>\\1</i>';
        $patterns[] = '/\\[u](.*)\\[\\/u\\]/sU';
        $replacements[] = '<u>\\1</u>';
        $patterns[] = '/\\[d](.*)\\[\\/d\\]/sU';
        $replacements[] = '<del>\\1</del>';
        $patterns[] = '/\\[img align=([\'"]?)(left|center|right)\\1]([^"\\(\\)\\?\\&\'<>]*)\\[\\/img\\]/sU';
        $patterns[] = '/\\[img]([^"\\(\\)\\?\\&\'<>]*)\\[\\/img\\]/sU';
        $patterns[] = '/\\[img align=([\'"]?)(left|center|right)\\1 id=([\'"]?)([0-9]*)\\3]([^"\\(\\)\\?\\&\'<>]*)\\[\\/img\\]/sU';
        $patterns[] = '/\\[img id=([\'"]?)([0-9]*)\\1]([^"\\(\\)\\?\\&\'<>]*)\\[\\/img\\]/sU';
        if ($allowimage != 1) {
            $replacements[] = '<a href="\\3" target="_blank">\\3</a>';
            $replacements[] = '<a href="\\1" target="_blank">\\1</a>';
            $replacements[] = '<a href="' . JIEQI_URL . '/image.php?id=\\4" target="_blank">\\4</a>';
            $replacements[] = '<a href="' . JIEQI_URL . '/image.php?id=\\2" target="_blank">\\3</a>';
        } else {
            if (!empty($limitsize)) {
                $resizestr = ' onload="imgResize(this);" onmouseover="imgMenu(this);" onclick="imgDialog(\'\\1\\3\', this);"';
            } else {
                $resizestr = '';
            }
            $replacements[] = '<img src="\\3" align="\\2" alt=""' . $resizestr . ' />';
            $replacements[] = '<img src="\\1" alt=""' . $resizestr . ' />';
            $replacements[] = '<img src="' . JIEQI_URL . '/image.php?id=\\4" align="\\2" alt="\\4"' . $resizestr . ' />';
            $replacements[] = '<img src="' . JIEQI_URL . '/image.php?id=\\2" alt="\\3"' . $resizestr . ' />';
        }
        $patterns[] = '/\\[quote]/sU';
        $replacements[] = 'Quote:' . '<div class="jieqiQuote">';
        $patterns[] = '/\\[\\/quote]/sU';
        $replacements[] = '</div>';
        $patterns[] = '/\\[added]/sU';
        $replacements[] = '<div class="jieqiAdded">';
        $patterns[] = '/\\[\\/added]/sU';
        $replacements[] = '</div>';
        $patterns[] = '/javascript:/si';
        $replacements[] = 'java script:';
        $patterns[] = '/about:/si';
        $replacements[] = 'about :';
        return preg_replace($patterns, $replacements, $text);
    }
    public function displayTarea($text, $html = 0, $clickable = 1, $smile = 1, $xcode = 1, $image = 1, $limitsize = 600)
    {
        if ($html != 1) {
            $text = jieqi_htmlstr($text);
        }
        if ($clickable != 0) {
            $text = $this->makeClickable($text, $limitsize);
        }
        if ($smile != 0) {
            $text = $this->smile($text);
        }
        if ($xcode != 0) {
            if ($image != 0) {
                $text = $this->jieqiCodeDecode($text, 1, $limitsize);
            } else {
                $text = $this->jieqiCodeDecode($text, 0, $limitsize);
            }
        }
        return $text;
    }
}
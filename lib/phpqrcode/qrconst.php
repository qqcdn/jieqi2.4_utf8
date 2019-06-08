<?php

class qrstr
{
    public static function set(&$srctab, $x, $y, $repl, $replLen = false)
    {
        $srctab[$y] = substr_replace($srctab[$y], $replLen !== false ? substr($repl, 0, $replLen) : $repl, $x, $replLen !== false ? $replLen : strlen($repl));
    }
}
define('QR_MODE_NUL', -1);
define('QR_MODE_NUM', 0);
define('QR_MODE_AN', 1);
define('QR_MODE_8', 2);
define('QR_MODE_KANJI', 3);
define('QR_MODE_STRUCTURE', 4);
define('QR_ECLEVEL_L', 0);
define('QR_ECLEVEL_M', 1);
define('QR_ECLEVEL_Q', 2);
define('QR_ECLEVEL_H', 3);
define('QR_FORMAT_TEXT', 0);
define('QR_FORMAT_PNG', 1);
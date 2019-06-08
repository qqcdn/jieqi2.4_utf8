<?php

function jieqi_zendoptimizerver()
{
    ob_start();
    phpinfo();
    $phpinfo = ob_get_contents();
    ob_end_clean();
    preg_match('/Zend(\\s|&nbsp;)Optimizer(\\s|&nbsp;)v([\\.\\d]*),/is', $phpinfo, $matches);
    if (!empty($matches[3])) {
        return $matches[3];
    } else {
        return '';
    }
}
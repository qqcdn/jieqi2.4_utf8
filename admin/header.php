<?php

define('JIEQI_ADMIN_PAGE', 1);
include_once JIEQI_ROOT_PATH . '/header.php';
if (!defined('JIEQI_ADMIN_FRAME')) {
    $_SESSION['adminurl'] = jieqi_addurlvars(array());
}
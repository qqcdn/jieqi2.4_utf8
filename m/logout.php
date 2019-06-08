<?php

define('JIEQI_MODULE_NAME', 'system');
define('JIEQI_ADMIN_LOGIN', 1);
require_once 'global.php';
if (empty($_REQUEST['jumpurl'])) {
    $_REQUEST['jumpurl'] = empty($_REQUEST['forward']) ? JIEQI_URL . '/' : $_REQUEST['forward'];
}
jieqi_useraction('logout', $_REQUEST);
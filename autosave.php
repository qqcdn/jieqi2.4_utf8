<?php

define('JIEQI_MODULE_NAME', 'system');
require_once 'global.php';
jieqi_checklogin();
if (!empty($_POST['savedata']) && strlen($_POST['savedata']) < 200000) {
    $_SESSION['jieqiAutoSave'] = $_POST['savedata'];
    echo '1';
} else {
    echo '0';
}
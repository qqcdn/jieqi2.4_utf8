<?php

echo '<h1>PHP QR Code</h1><hr/>';
$PNG_TEMP_DIR = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR;
$PNG_WEB_DIR = 'temp/';
include 'qrlib.php';
if (!file_exists($PNG_TEMP_DIR)) {
    mkdir($PNG_TEMP_DIR);
}
$filename = $PNG_TEMP_DIR . 'test.png';
$errorCorrectionLevel = 'L';
if (isset($_REQUEST['level']) && in_array($_REQUEST['level'], array('L', 'M', 'Q', 'H'))) {
    $errorCorrectionLevel = $_REQUEST['level'];
}
$matrixPointSize = 4;
if (isset($_REQUEST['size'])) {
    $matrixPointSize = min(max((int) $_REQUEST['size'], 1), 10);
}
if (isset($_REQUEST['data'])) {
    if (trim($_REQUEST['data']) == '') {
        exit('data cannot be empty! <a href="?">back</a>');
    }
    $filename = $PNG_TEMP_DIR . 'test' . md5($_REQUEST['data'] . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
    QRcode::png($_REQUEST['data'], $filename, $errorCorrectionLevel, $matrixPointSize, 2);
} else {
    echo 'You can provide data in GET parameter: <a href="?data=like_that">like that</a><hr/>';
    QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2);
}
echo '<img src="' . $PNG_WEB_DIR . basename($filename) . '" /><hr/>';
echo '<form action="index.php" method="post">' . "\r\n" . '        Data:&nbsp;<input name="data" value="' . (isset($_REQUEST['data']) ? htmlspecialchars($_REQUEST['data']) : 'PHP QR Code :)') . '" />&nbsp;' . "\r\n" . '        ECC:&nbsp;<select name="level">' . "\r\n" . '            <option value="L"' . ($errorCorrectionLevel == 'L' ? ' selected' : '') . '>L - smallest</option>' . "\r\n" . '            <option value="M"' . ($errorCorrectionLevel == 'M' ? ' selected' : '') . '>M</option>' . "\r\n" . '            <option value="Q"' . ($errorCorrectionLevel == 'Q' ? ' selected' : '') . '>Q</option>' . "\r\n" . '            <option value="H"' . ($errorCorrectionLevel == 'H' ? ' selected' : '') . '>H - best</option>' . "\r\n" . '        </select>&nbsp;' . "\r\n" . '        Size:&nbsp;<select name="size">';
for ($i = 1; $i <= 10; $i++) {
    echo '<option value="' . $i . '"' . ($matrixPointSize == $i ? ' selected' : '') . '>' . $i . '</option>';
}
echo '</select>&nbsp;' . "\r\n" . '        <input type="submit" value="GENERATE"></form><hr/>';
QRtools::timeBenchmark();
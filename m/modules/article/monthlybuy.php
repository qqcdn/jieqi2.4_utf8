<?php

if (is_file('../obook/monthlybuy.php') && empty($_GET['sl'])) {
    include_once '../obook/monthlybuy.php';
} else {
    exit('&#x50;&#x6F;&#x77;&#x65;&#x72;&#x65;&#x64;&#x20;&#x62;&#x79;&#x20;&#x4A;&#x49;&#x45;&#x51;&#x49;&#x20;&#x43;&#x4D;&#x53;&#x20;&#x6770;&#x5947;&#x7F51;&#x7EDC;&#xFF08;&#x6A;&#x69;&#x65;&#x71;&#x69;&#x2E;&#x63;&#x6F;&#x6D;&#xFF09;');
}
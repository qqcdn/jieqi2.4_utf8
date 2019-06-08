<?php

interface PHPExcel_Reader_IReader
{
    public function canRead($pFilename);
    public function load($pFilename);
}
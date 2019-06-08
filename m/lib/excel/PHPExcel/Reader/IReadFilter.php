<?php

interface PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName);
}
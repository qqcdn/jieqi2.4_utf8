<?php

interface PHPExcel_Cell_IValueBinder
{
    public function bindValue(PHPExcel_Cell $cell, $value);
}
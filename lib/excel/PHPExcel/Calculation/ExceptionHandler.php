<?php

class PHPExcel_Calculation_ExceptionHandler
{
    public function __construct()
    {
        set_error_handler(array('PHPExcel_Calculation_Exception', 'errorHandlerCallback'), 30719);
    }
    public function __destruct()
    {
        restore_error_handler();
    }
}
<?php

class PHPExcel_Calculation_Function
{
    const CATEGORY_CUBE = 'Cube';
    const CATEGORY_DATABASE = 'Database';
    const CATEGORY_DATE_AND_TIME = 'Date and Time';
    const CATEGORY_ENGINEERING = 'Engineering';
    const CATEGORY_FINANCIAL = 'Financial';
    const CATEGORY_INFORMATION = 'Information';
    const CATEGORY_LOGICAL = 'Logical';
    const CATEGORY_LOOKUP_AND_REFERENCE = 'Lookup and Reference';
    const CATEGORY_MATH_AND_TRIG = 'Math and Trig';
    const CATEGORY_STATISTICAL = 'Statistical';
    const CATEGORY_TEXT_AND_DATA = 'Text and Data';
    /**
     * Category (represented by CATEGORY_*)
     *
     * @var string
     */
    private $_category;
    /**
     * Excel name
     *
     * @var string
     */
    private $_excelName;
    /**
     * PHPExcel name
     *
     * @var string
     */
    private $_phpExcelName;
    public function __construct($pCategory = NULL, $pExcelName = NULL, $pPHPExcelName = NULL)
    {
        if ($pCategory !== NULL && $pExcelName !== NULL && $pPHPExcelName !== NULL) {
            $this->_category = $pCategory;
            $this->_excelName = $pExcelName;
            $this->_phpExcelName = $pPHPExcelName;
        } else {
            throw new PHPExcel_Calculation_Exception('Invalid parameters passed.');
        }
    }
    public function getCategory()
    {
        return $this->_category;
    }
    public function setCategory($value = NULL)
    {
        if (!is_null($value)) {
            $this->_category = $value;
        } else {
            throw new PHPExcel_Calculation_Exception('Invalid parameter passed.');
        }
    }
    public function getExcelName()
    {
        return $this->_excelName;
    }
    public function setExcelName($value)
    {
        $this->_excelName = $value;
    }
    public function getPHPExcelName()
    {
        return $this->_phpExcelName;
    }
    public function setPHPExcelName($value)
    {
        $this->_phpExcelName = $value;
    }
}
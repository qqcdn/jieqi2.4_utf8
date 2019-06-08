<?php

class PHPExcel_Cell_DataValidation
{
    const TYPE_NONE = 'none';
    const TYPE_CUSTOM = 'custom';
    const TYPE_DATE = 'date';
    const TYPE_DECIMAL = 'decimal';
    const TYPE_LIST = 'list';
    const TYPE_TEXTLENGTH = 'textLength';
    const TYPE_TIME = 'time';
    const TYPE_WHOLE = 'whole';
    const STYLE_STOP = 'stop';
    const STYLE_WARNING = 'warning';
    const STYLE_INFORMATION = 'information';
    const OPERATOR_BETWEEN = 'between';
    const OPERATOR_EQUAL = 'equal';
    const OPERATOR_GREATERTHAN = 'greaterThan';
    const OPERATOR_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const OPERATOR_LESSTHAN = 'lessThan';
    const OPERATOR_LESSTHANOREQUAL = 'lessThanOrEqual';
    const OPERATOR_NOTBETWEEN = 'notBetween';
    const OPERATOR_NOTEQUAL = 'notEqual';
    /**
     * Formula 1
     *
     * @var string
     */
    private $_formula1;
    /**
     * Formula 2
     *
     * @var string
     */
    private $_formula2;
    /**
     * Type
     *
     * @var string
     */
    private $_type = PHPExcel_Cell_DataValidation::TYPE_NONE;
    /**
     * Error style
     *
     * @var string
     */
    private $_errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP;
    /**
     * Operator
     *
     * @var string
     */
    private $_operator;
    /**
     * Allow Blank
     *
     * @var boolean
     */
    private $_allowBlank;
    /**
     * Show DropDown
     *
     * @var boolean
     */
    private $_showDropDown;
    /**
     * Show InputMessage
     *
     * @var boolean
     */
    private $_showInputMessage;
    /**
     * Show ErrorMessage
     *
     * @var boolean
     */
    private $_showErrorMessage;
    /**
     * Error title
     *
     * @var string
     */
    private $_errorTitle;
    /**
     * Error
     *
     * @var string
     */
    private $_error;
    /**
     * Prompt title
     *
     * @var string
     */
    private $_promptTitle;
    /**
     * Prompt
     *
     * @var string
     */
    private $_prompt;
    public function __construct()
    {
        $this->_formula1 = '';
        $this->_formula2 = '';
        $this->_type = PHPExcel_Cell_DataValidation::TYPE_NONE;
        $this->_errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP;
        $this->_operator = '';
        $this->_allowBlank = false;
        $this->_showDropDown = false;
        $this->_showInputMessage = false;
        $this->_showErrorMessage = false;
        $this->_errorTitle = '';
        $this->_error = '';
        $this->_promptTitle = '';
        $this->_prompt = '';
    }
    public function getFormula1()
    {
        return $this->_formula1;
    }
    public function setFormula1($value = '')
    {
        $this->_formula1 = $value;
        return $this;
    }
    public function getFormula2()
    {
        return $this->_formula2;
    }
    public function setFormula2($value = '')
    {
        $this->_formula2 = $value;
        return $this;
    }
    public function getType()
    {
        return $this->_type;
    }
    public function setType($value = PHPExcel_Cell_DataValidation::TYPE_NONE)
    {
        $this->_type = $value;
        return $this;
    }
    public function getErrorStyle()
    {
        return $this->_errorStyle;
    }
    public function setErrorStyle($value = PHPExcel_Cell_DataValidation::STYLE_STOP)
    {
        $this->_errorStyle = $value;
        return $this;
    }
    public function getOperator()
    {
        return $this->_operator;
    }
    public function setOperator($value = '')
    {
        $this->_operator = $value;
        return $this;
    }
    public function getAllowBlank()
    {
        return $this->_allowBlank;
    }
    public function setAllowBlank($value = false)
    {
        $this->_allowBlank = $value;
        return $this;
    }
    public function getShowDropDown()
    {
        return $this->_showDropDown;
    }
    public function setShowDropDown($value = false)
    {
        $this->_showDropDown = $value;
        return $this;
    }
    public function getShowInputMessage()
    {
        return $this->_showInputMessage;
    }
    public function setShowInputMessage($value = false)
    {
        $this->_showInputMessage = $value;
        return $this;
    }
    public function getShowErrorMessage()
    {
        return $this->_showErrorMessage;
    }
    public function setShowErrorMessage($value = false)
    {
        $this->_showErrorMessage = $value;
        return $this;
    }
    public function getErrorTitle()
    {
        return $this->_errorTitle;
    }
    public function setErrorTitle($value = '')
    {
        $this->_errorTitle = $value;
        return $this;
    }
    public function getError()
    {
        return $this->_error;
    }
    public function setError($value = '')
    {
        $this->_error = $value;
        return $this;
    }
    public function getPromptTitle()
    {
        return $this->_promptTitle;
    }
    public function setPromptTitle($value = '')
    {
        $this->_promptTitle = $value;
        return $this;
    }
    public function getPrompt()
    {
        return $this->_prompt;
    }
    public function setPrompt($value = '')
    {
        $this->_prompt = $value;
        return $this;
    }
    public function getHashCode()
    {
        return md5($this->_formula1 . $this->_formula2 . ($this->_type = PHPExcel_Cell_DataValidation::TYPE_NONE . ($this->_errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP . $this->_operator . ($this->_allowBlank ? 't' : 'f') . ($this->_showDropDown ? 't' : 'f') . ($this->_showInputMessage ? 't' : 'f') . ($this->_showErrorMessage ? 't' : 'f') . $this->_errorTitle . $this->_error . $this->_promptTitle . $this->_prompt . 'PHPExcel_Cell_DataValidation')));
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->{$key} = clone $value;
            } else {
                $this->{$key} = $value;
            }
        }
    }
}
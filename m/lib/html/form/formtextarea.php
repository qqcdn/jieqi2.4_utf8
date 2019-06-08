<?php

class JieqiFormTextArea extends JieqiFormElement
{
    public $_cols;
    public $_rows;
    public $_value;
    public function __construct($caption, $name, $value = '', $rows = 5, $cols = 50)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_rows = intval($rows);
        $this->_cols = intval($cols);
        $this->_value = $value;
    }
    public function getRows()
    {
        return $this->_rows;
    }
    public function getCols()
    {
        return $this->_cols;
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function render()
    {
        return '<textarea class="textarea" name="' . $this->getName() . '" id="' . $this->getName() . '" rows="' . $this->getRows() . '" cols="' . $this->getCols() . '"' . $this->getExtra() . '>' . $this->getValue() . '</textarea>';
    }
}
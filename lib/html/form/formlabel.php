<?php

class JieqiFormLabel extends JieqiFormElement
{
    public $_value;
    public function __construct($caption = '', $value = '')
    {
        $this->setCaption($caption);
        $this->_value = $value;
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function render()
    {
        return $this->getValue();
    }
}
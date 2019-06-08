<?php

class JieqiFormPassword extends JieqiFormElement
{
    public $_size;
    public $_maxlength;
    public $_value;
    public function __construct($caption, $name, $size, $maxlength, $value = '')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_size = intval($size);
        $this->_maxlength = intval($maxlength);
        $this->_value = $value;
    }
    public function getSize()
    {
        return $this->_size;
    }
    public function getMaxlength()
    {
        return $this->_maxlength;
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function render()
    {
        return '<input type="password" class="text" name="' . $this->getName() . '" id="' . $this->getName() . '" size="' . $this->getSize() . '" maxlength="' . $this->getMaxlength() . '" value="' . $this->getValue() . '"' . $this->getExtra() . ' />';
    }
}
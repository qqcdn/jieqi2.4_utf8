<?php

class JieqiFormButton extends JieqiFormElement
{
    public $_value;
    public $_type;
    public function __construct($caption, $name, $value = '', $type = 'button')
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_type = $type;
        $this->_value = $value;
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function getType()
    {
        return $this->_type;
    }
    public function render()
    {
        return '<input type="' . $this->getType() . '" class="button" name="' . $this->getName() . '"  id="' . $this->getName() . '" value="' . $this->getValue() . '"' . $this->getExtra() . ' />';
    }
}
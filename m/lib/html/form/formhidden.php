<?php

class JieqiFormHidden extends JieqiFormElement
{
    public $_value;
    public function __construct($name, $value)
    {
        $this->setName($name);
        $this->setHidden();
        $this->_value = $value;
        $this->setCaption('');
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function render()
    {
        return '<input type="hidden" name="' . $this->getName() . '" id="' . $this->getName() . '" value="' . $this->getValue() . '" />';
    }
}
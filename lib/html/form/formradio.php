<?php

class JieqiFormRadio extends JieqiFormElement
{
    public $_options = array();
    public $_value;
    public function __construct($caption, $name, $value = NULL)
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (isset($value)) {
            $this->_value = $value;
        }
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function addOption($value, $name = '')
    {
        if ($name != '') {
            $this->_options[$value] = $name;
        } else {
            $this->_options[$value] = $value;
        }
    }
    public function addOptionArray($options)
    {
        if (is_array($options)) {
            foreach ($options as $k => $v) {
                $this->addOption($k, $v);
            }
        }
    }
    public function getOptions()
    {
        return $this->_options;
    }
    public function render()
    {
        $ret = '';
        foreach ($this->getOptions() as $value => $name) {
            $ret .= '<input type="radio" class="radio" name="' . $this->getName() . '" value="' . $value . '"';
            $selected = $this->getValue();
            if (isset($selected) && $value == $selected) {
                $ret .= ' checked="checked"';
            }
            $ret .= $this->getExtra() . ' />' . $name . "\n";
        }
        return $ret;
    }
}
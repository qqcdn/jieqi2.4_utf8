<?php

class JieqiFormCheckBox extends JieqiFormElement
{
    public $_options = array();
    public $_value = array();
    public function __construct($caption, $name, $value = NULL)
    {
        $this->setCaption($caption);
        $this->setName($name);
        if (isset($value)) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $this->_value[] = $v;
                }
            } else {
                $this->_value[] = $value;
            }
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
        if (1 < count($this->getOptions()) && substr($this->getName(), -2, 2) != '[]') {
            $newname = $this->getName() . '[]';
            $this->setName($newname);
        }
        foreach ($this->getOptions() as $value => $name) {
            $ret .= '<input type="checkbox" class="checkbox" name="' . $this->getName() . '" value="' . $value . '"';
            if (0 < count($this->getValue()) && in_array($value, $this->getValue())) {
                $ret .= ' checked="checked"';
            }
            $ret .= $this->getExtra() . ' />' . $name . "\n";
        }
        return $ret;
    }
}
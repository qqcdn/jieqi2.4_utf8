<?php

class JieqiFormSelect extends JieqiFormElement
{
    public $_options = array();
    public $_multiple = false;
    public $_size;
    public $_value = array();
    public function __construct($caption, $name, $value = NULL, $size = 1, $multiple = false)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_multiple = $multiple;
        $this->_size = intval($size);
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
    public function isMultiple()
    {
        return $this->_multiple;
    }
    public function getSize()
    {
        return $this->_size;
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
        $ret = '<select class="select"  size="' . $this->getSize() . '"' . $this->getExtra() . '';
        if ($this->isMultiple() != false) {
            $ret .= ' name="' . $this->getName() . '[]" id="' . $this->getName() . '[]" multiple="multiple">' . "\n" . '';
        } else {
            $ret .= ' name="' . $this->getName() . '" id="' . $this->getName() . '">' . "\n" . '';
        }
        foreach ($this->getOptions() as $value => $name) {
            $ret .= '<option value="' . jieqi_htmlchars($value, ENT_QUOTES) . '"';
            if (0 < count($this->getValue()) && in_array($value, $this->getValue())) {
                $ret .= ' selected="selected"';
            }
            $ret .= '>' . $name . '</option>' . "\n" . '';
        }
        $ret .= '</select>';
        return $ret;
    }
}
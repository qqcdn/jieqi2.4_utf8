<?php

class JieqiFormTextDateSelect extends JieqiFormText
{
    public function __construct($caption, $name, $size = 18, $value = '')
    {
        if (is_numeric($value)) {
            if ($value == 0) {
                $value = date(JIEQI_DATE_FORMAT, JIEQI_NOW_TIME);
            } else {
                $value = date(JIEQI_DATE_FORMAT, $value);
            }
        }
        parent::__construct($caption, $name, $size, 10, $value);
    }
    public function render()
    {
        $ret = '<input type="text" class="text" name="' . $this->getName() . '" id="' . $this->getName() . '" size="' . $this->getSize() . '" maxlength="' . $this->getMaxlength() . '" value="' . $this->getValue() . '"' . $this->getExtra() . ' onfocus="showCalendar(this,event)" onclick="showCalendar(this,event)" />';
        if (!defined('JIEQI_CALENDAR_INCLUDE')) {
            define('JIEQI_CALENDAR_INCLUDE', true);
            $ret = '<script src="' . JIEQI_URL . '/scripts/calendar.js' . '"></script>' . $ret;
        }
        return $ret;
    }
}
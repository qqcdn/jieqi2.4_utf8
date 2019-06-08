<?php

class JieqiFormElementTray extends JieqiFormElement
{
    public $_elements = array();
    public $_delimeter;
    public function __construct($caption, $delimeter = '&nbsp;')
    {
        $this->setCaption($caption);
        $this->_delimeter = $delimeter;
    }
    public function addElement($element)
    {
        $this->_elements[] = $element;
    }
    public function getElements()
    {
        return $this->_elements;
    }
    public function getDelimeter()
    {
        return $this->_delimeter;
    }
    public function render()
    {
        $count = 0;
        $ret = '';
        foreach ($this->getElements() as $ele) {
            if (0 < $count) {
                $ret .= $this->getDelimeter();
            }
            $ret .= $ele->render() . "\n";
            if (!$ele->isHidden()) {
                $count++;
            }
        }
        return $ret;
    }
}
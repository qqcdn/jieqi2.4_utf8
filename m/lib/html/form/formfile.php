<?php

class JieqiFormFile extends JieqiFormElement
{
    public $_size;
    public function __construct($caption, $name, $size)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_size = intval($size);
    }
    public function getSize()
    {
        return $this->_size;
    }
    public function render()
    {
        return '<input type="file" class="text" size="' . $this->getSize() . '" name="' . $this->getName() . '" id="' . $this->getName() . '"' . $this->getExtra() . ' />';
    }
}
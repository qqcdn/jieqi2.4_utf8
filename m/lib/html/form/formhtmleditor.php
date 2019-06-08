<?php

class JieqiFormHtmlEditor extends JieqiFormElement
{
    public $_width;
    public $_height;
    public $_value;
    public function __construct($caption, $name, $value = '', $width = 600, $height = 400)
    {
        $this->setCaption($caption);
        $this->setName($name);
        $this->_width = intval($width);
        $this->_height = intval($height);
        $this->_value = $value;
    }
    public function getWidth()
    {
        return $this->_width;
    }
    public function getHeight()
    {
        return $this->_height;
    }
    public function getValue()
    {
        return $this->_value;
    }
    public function render()
    {
        include_once JIEQI_ROOT_PATH . '/lib/html/form/fckeditor/fckeditor.php';
        $editor = new FCKeditor();
        $editor->Value = $this->getValue();
        return $editor->ReturnFCKeditor($this->getName(), $this->getWidth(), $this->getHeight());
    }
}
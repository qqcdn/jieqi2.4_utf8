<?php

class JieqiFormElement
{
    public $_name;
    public $_caption;
    public $_hidden = false;
    public $_extra;
    public $_required = false;
    public $_description = '';
    public $_intro = '';
    public function __construct()
    {
    }
    public function setName($name)
    {
        $this->_name = trim($name);
    }
    public function getName($encode = true)
    {
        if (false != $encode) {
            return str_replace('&amp;', '&', str_replace('\'', '&#039;', jieqi_htmlchars($this->_name)));
        }
        return $this->_name;
    }
    public function setCaption($caption)
    {
        $this->_caption = trim($caption);
    }
    public function getCaption()
    {
        return $this->_caption;
    }
    public function setDescription($description)
    {
        $this->_description = trim($description);
    }
    public function getDescription()
    {
        return $this->_description;
    }
    public function setIntro($intro)
    {
        $this->_intro = trim($intro);
    }
    public function getIntro()
    {
        return $this->_intro;
    }
    public function setHidden()
    {
        $this->_hidden = true;
    }
    public function isHidden()
    {
        return $this->_hidden;
    }
    public function setExtra($extra)
    {
        $this->_extra = ' ' . trim($extra);
    }
    public function getExtra()
    {
        if (isset($this->_extra)) {
            return $this->_extra;
        }
    }
    public function render()
    {
    }
}
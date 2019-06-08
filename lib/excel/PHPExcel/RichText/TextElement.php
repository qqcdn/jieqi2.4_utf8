<?php

class PHPExcel_RichText_TextElement implements PHPExcel_RichText_ITextElement
{
    /**
     * Text
     *
     * @var string
     */
    private $_text;
    public function __construct($pText = '')
    {
        $this->_text = $pText;
    }
    public function getText()
    {
        return $this->_text;
    }
    public function setText($pText = '')
    {
        $this->_text = $pText;
        return $this;
    }
    public function getFont()
    {
        return NULL;
    }
    public function getHashCode()
    {
        return md5($this->_text . 'PHPExcel_RichText_TextElement');
    }
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->{$key} = clone $value;
            } else {
                $this->{$key} = $value;
            }
        }
    }
}
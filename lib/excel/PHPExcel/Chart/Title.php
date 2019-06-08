<?php

class PHPExcel_Chart_Title
{
    /**
     * Title Caption
     *
     * @var string
     */
    private $_caption;
    /**
     * Title Layout
     *
     * @var PHPExcel_Chart_Layout
     */
    private $_layout;
    public function __construct($caption = NULL, PHPExcel_Chart_Layout $layout = NULL)
    {
        $this->_caption = $caption;
        $this->_layout = $layout;
    }
    public function getCaption()
    {
        return $this->_caption;
    }
    public function setCaption($caption = NULL)
    {
        $this->_caption = $caption;
        return $this;
    }
    public function getLayout()
    {
        return $this->_layout;
    }
}
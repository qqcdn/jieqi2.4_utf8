<?php

class PHPExcel_Chart_Layout
{
    /**
     * layoutTarget
     *
     * @var string
     */
    private $_layoutTarget;
    /**
     * X Mode
     *
     * @var string
     */
    private $_xMode;
    /**
     * Y Mode
     *
     * @var string
     */
    private $_yMode;
    /**
     * X-Position
     *
     * @var float
     */
    private $_xPos;
    /**
     * Y-Position
     *
     * @var float
     */
    private $_yPos;
    /**
     * width
     *
     * @var float
     */
    private $_width;
    /**
     * height
     *
     * @var float
     */
    private $_height;
    /**
     * show legend key
     * Specifies that legend keys should be shown in data labels
     *
     * @var boolean
     */
    private $_showLegendKey;
    /**
     * show value
     * Specifies that the value should be shown in a data label.
     *
     * @var boolean
     */
    private $_showVal;
    /**
     * show category name
     * Specifies that the category name should be shown in the data label.
     *
     * @var boolean
     */
    private $_showCatName;
    /**
     * show data series name
     * Specifies that the series name should be shown in the data label.
     *
     * @var boolean
     */
    private $_showSerName;
    /**
     * show percentage
     * Specifies that the percentage should be shown in the data label.
     *
     * @var boolean
     */
    private $_showPercent;
    /**
     * show bubble size
     *
     * @var boolean
     */
    private $_showBubbleSize;
    /**
     * show leader lines
     * Specifies that leader lines should be shown for the data label.
     *
     * @var boolean
     */
    private $_showLeaderLines;
    public function __construct($layout = array())
    {
        if (isset($layout['layoutTarget'])) {
            $this->_layoutTarget = $layout['layoutTarget'];
        }
        if (isset($layout['xMode'])) {
            $this->_xMode = $layout['xMode'];
        }
        if (isset($layout['yMode'])) {
            $this->_yMode = $layout['yMode'];
        }
        if (isset($layout['x'])) {
            $this->_xPos = (double) $layout['x'];
        }
        if (isset($layout['y'])) {
            $this->_yPos = (double) $layout['y'];
        }
        if (isset($layout['w'])) {
            $this->_width = (double) $layout['w'];
        }
        if (isset($layout['h'])) {
            $this->_height = (double) $layout['h'];
        }
    }
    public function getLayoutTarget()
    {
        return $this->_layoutTarget;
    }
    public function setLayoutTarget($value)
    {
        $this->_layoutTarget = $value;
        return $this;
    }
    public function getXMode()
    {
        return $this->_xMode;
    }
    public function setXMode($value)
    {
        $this->_xMode = $value;
        return $this;
    }
    public function getYMode()
    {
        return $this->_yMode;
    }
    public function setYMode($value)
    {
        $this->_yMode = $value;
        return $this;
    }
    public function getXPosition()
    {
        return $this->_xPos;
    }
    public function setXPosition($value)
    {
        $this->_xPos = $value;
        return $this;
    }
    public function getYPosition()
    {
        return $this->_yPos;
    }
    public function setYPosition($value)
    {
        $this->_yPos = $value;
        return $this;
    }
    public function getWidth()
    {
        return $this->_width;
    }
    public function setWidth($value)
    {
        $this->_width = $value;
        return $this;
    }
    public function getHeight()
    {
        return $this->_height;
    }
    public function setHeight($value)
    {
        $this->_height = $value;
        return $this;
    }
    public function getShowLegendKey()
    {
        return $this->_showLegendKey;
    }
    public function setShowLegendKey($value)
    {
        $this->_showLegendKey = $value;
        return $this;
    }
    public function getShowVal()
    {
        return $this->_showVal;
    }
    public function setShowVal($value)
    {
        $this->_showVal = $value;
        return $this;
    }
    public function getShowCatName()
    {
        return $this->_showCatName;
    }
    public function setShowCatName($value)
    {
        $this->_showCatName = $value;
        return $this;
    }
    public function getShowSerName()
    {
        return $this->_showSerName;
    }
    public function setShowSerName($value)
    {
        $this->_showSerName = $value;
        return $this;
    }
    public function getShowPercent()
    {
        return $this->_showPercent;
    }
    public function setShowPercent($value)
    {
        $this->_showPercent = $value;
        return $this;
    }
    public function getShowBubbleSize()
    {
        return $this->_showBubbleSize;
    }
    public function setShowBubbleSize($value)
    {
        $this->_showBubbleSize = $value;
        return $this;
    }
    public function getShowLeaderLines()
    {
        return $this->_showLeaderLines;
    }
    public function setShowLeaderLines($value)
    {
        $this->_showLeaderLines = $value;
        return $this;
    }
}
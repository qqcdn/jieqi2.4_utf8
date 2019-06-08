<?php

class PHPExcel_Chart_DataSeries
{
    const TYPE_BARCHART = 'barChart';
    const TYPE_BARCHART_3D = 'bar3DChart';
    const TYPE_LINECHART = 'lineChart';
    const TYPE_LINECHART_3D = 'line3DChart';
    const TYPE_AREACHART = 'areaChart';
    const TYPE_AREACHART_3D = 'area3DChart';
    const TYPE_PIECHART = 'pieChart';
    const TYPE_PIECHART_3D = 'pie3DChart';
    const TYPE_DOUGHTNUTCHART = 'doughnutChart';
    const TYPE_DONUTCHART = self::TYPE_DOUGHTNUTCHART;
    const TYPE_SCATTERCHART = 'scatterChart';
    const TYPE_SURFACECHART = 'surfaceChart';
    const TYPE_SURFACECHART_3D = 'surface3DChart';
    const TYPE_RADARCHART = 'radarChart';
    const TYPE_BUBBLECHART = 'bubbleChart';
    const TYPE_STOCKCHART = 'stockChart';
    const TYPE_CANDLECHART = self::TYPE_STOCKCHART;
    const GROUPING_CLUSTERED = 'clustered';
    const GROUPING_STACKED = 'stacked';
    const GROUPING_PERCENT_STACKED = 'percentStacked';
    const GROUPING_STANDARD = 'standard';
    const DIRECTION_BAR = 'bar';
    const DIRECTION_HORIZONTAL = self::DIRECTION_BAR;
    const DIRECTION_COL = 'col';
    const DIRECTION_COLUMN = self::DIRECTION_COL;
    const DIRECTION_VERTICAL = self::DIRECTION_COL;
    const STYLE_LINEMARKER = 'lineMarker';
    const STYLE_SMOOTHMARKER = 'smoothMarker';
    const STYLE_MARKER = 'marker';
    const STYLE_FILLED = 'filled';
    /**
     * Series Plot Type
     *
     * @var string
     */
    private $_plotType;
    /**
     * Plot Grouping Type
     *
     * @var boolean
     */
    private $_plotGrouping;
    /**
     * Plot Direction
     *
     * @var boolean
     */
    private $_plotDirection;
    /**
     * Plot Style
     *
     * @var string
     */
    private $_plotStyle;
    /**
     * Order of plots in Series
     *
     * @var array of integer
     */
    private $_plotOrder = array();
    /**
     * Plot Label
     *
     * @var array of PHPExcel_Chart_DataSeriesValues
     */
    private $_plotLabel = array();
    /**
     * Plot Category
     *
     * @var array of PHPExcel_Chart_DataSeriesValues
     */
    private $_plotCategory = array();
    /**
     * Smooth Line
     *
     * @var string
     */
    private $_smoothLine;
    /**
     * Plot Values
     *
     * @var array of PHPExcel_Chart_DataSeriesValues
     */
    private $_plotValues = array();
    public function __construct($plotType = NULL, $plotGrouping = NULL, $plotOrder = array(), $plotLabel = array(), $plotCategory = array(), $plotValues = array(), $smoothLine = NULL, $plotStyle = NULL)
    {
        $this->_plotType = $plotType;
        $this->_plotGrouping = $plotGrouping;
        $this->_plotOrder = $plotOrder;
        $keys = array_keys($plotValues);
        $this->_plotValues = $plotValues;
        if (count($plotLabel) == 0 || is_null($plotLabel[$keys[0]])) {
            $plotLabel[$keys[0]] = new PHPExcel_Chart_DataSeriesValues();
        }
        $this->_plotLabel = $plotLabel;
        if (count($plotCategory) == 0 || is_null($plotCategory[$keys[0]])) {
            $plotCategory[$keys[0]] = new PHPExcel_Chart_DataSeriesValues();
        }
        $this->_plotCategory = $plotCategory;
        $this->_smoothLine = $smoothLine;
        $this->_plotStyle = $plotStyle;
    }
    public function getPlotType()
    {
        return $this->_plotType;
    }
    public function setPlotType($plotType = '')
    {
        $this->_plotType = $plotType;
        return $this;
    }
    public function getPlotGrouping()
    {
        return $this->_plotGrouping;
    }
    public function setPlotGrouping($groupingType = NULL)
    {
        $this->_plotGrouping = $groupingType;
        return $this;
    }
    public function getPlotDirection()
    {
        return $this->_plotDirection;
    }
    public function setPlotDirection($plotDirection = NULL)
    {
        $this->_plotDirection = $plotDirection;
        return $this;
    }
    public function getPlotOrder()
    {
        return $this->_plotOrder;
    }
    public function getPlotLabels()
    {
        return $this->_plotLabel;
    }
    public function getPlotLabelByIndex($index)
    {
        $keys = array_keys($this->_plotLabel);
        if (in_array($index, $keys)) {
            return $this->_plotLabel[$index];
        } else {
            if (isset($keys[$index])) {
                return $this->_plotLabel[$keys[$index]];
            }
        }
        return false;
    }
    public function getPlotCategories()
    {
        return $this->_plotCategory;
    }
    public function getPlotCategoryByIndex($index)
    {
        $keys = array_keys($this->_plotCategory);
        if (in_array($index, $keys)) {
            return $this->_plotCategory[$index];
        } else {
            if (isset($keys[$index])) {
                return $this->_plotCategory[$keys[$index]];
            }
        }
        return false;
    }
    public function getPlotStyle()
    {
        return $this->_plotStyle;
    }
    public function setPlotStyle($plotStyle = NULL)
    {
        $this->_plotStyle = $plotStyle;
        return $this;
    }
    public function getPlotValues()
    {
        return $this->_plotValues;
    }
    public function getPlotValuesByIndex($index)
    {
        $keys = array_keys($this->_plotValues);
        if (in_array($index, $keys)) {
            return $this->_plotValues[$index];
        } else {
            if (isset($keys[$index])) {
                return $this->_plotValues[$keys[$index]];
            }
        }
        return false;
    }
    public function getPlotSeriesCount()
    {
        return count($this->_plotValues);
    }
    public function getSmoothLine()
    {
        return $this->_smoothLine;
    }
    public function setSmoothLine($smoothLine = true)
    {
        $this->_smoothLine = $smoothLine;
        return $this;
    }
    public function refresh(PHPExcel_Worksheet $worksheet)
    {
        foreach ($this->_plotValues as $plotValues) {
            if ($plotValues !== NULL) {
                $plotValues->refresh($worksheet, true);
            }
        }
        foreach ($this->_plotLabel as $plotValues) {
            if ($plotValues !== NULL) {
                $plotValues->refresh($worksheet, true);
            }
        }
        foreach ($this->_plotCategory as $plotValues) {
            if ($plotValues !== NULL) {
                $plotValues->refresh($worksheet, false);
            }
        }
    }
}
<?php

class PHPExcel_Chart_PlotArea
{
    /**
     * PlotArea Layout
     *
     * @var PHPExcel_Chart_Layout
     */
    private $_layout;
    /**
     * Plot Series
     *
     * @var array of PHPExcel_Chart_DataSeries
     */
    private $_plotSeries = array();
    public function __construct(PHPExcel_Chart_Layout $layout = NULL, $plotSeries = array())
    {
        $this->_layout = $layout;
        $this->_plotSeries = $plotSeries;
    }
    public function getLayout()
    {
        return $this->_layout;
    }
    public function getPlotGroupCount()
    {
        return count($this->_plotSeries);
    }
    public function getPlotSeriesCount()
    {
        $seriesCount = 0;
        foreach ($this->_plotSeries as $plot) {
            $seriesCount += $plot->getPlotSeriesCount();
        }
        return $seriesCount;
    }
    public function getPlotGroup()
    {
        return $this->_plotSeries;
    }
    public function getPlotGroupByIndex($index)
    {
        return $this->_plotSeries[$index];
    }
    public function setPlotSeries($plotSeries = array())
    {
        $this->_plotSeries = $plotSeries;
        return $this;
    }
    public function refresh(PHPExcel_Worksheet $worksheet)
    {
        foreach ($this->_plotSeries as $plotSeries) {
            $plotSeries->refresh($worksheet);
        }
    }
}
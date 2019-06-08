<?php

class PHPExcel_Chart_Renderer_jpgraph
{
    private static $_width = 640;
    private static $_height = 480;
    private static $_colourSet = array('mediumpurple1', 'palegreen3', 'gold1', 'cadetblue1', 'darkmagenta', 'coral', 'dodgerblue3', 'eggplant', 'mediumblue', 'magenta', 'sandybrown', 'cyan', 'firebrick1', 'forestgreen', 'deeppink4', 'darkolivegreen', 'goldenrod2');
    private static $_markSet = array('diamond' => MARK_DIAMOND, 'square' => MARK_SQUARE, 'triangle' => MARK_UTRIANGLE, 'x' => MARK_X, 'star' => MARK_STAR, 'dot' => MARK_FILLEDCIRCLE, 'dash' => MARK_DTRIANGLE, 'circle' => MARK_CIRCLE, 'plus' => MARK_CROSS);
    private $_chart;
    private $_graph;
    private static $_plotColour = 0;
    private static $_plotMark = 0;
    private function _formatPointMarker($seriesPlot, $markerID)
    {
        $plotMarkKeys = array_keys(self::$_markSet);
        if (is_null($markerID)) {
            self::$_plotMark %= count(self::$_markSet);
            $seriesPlot->mark->SetType(self::$_markSet[$plotMarkKeys[self::$_plotMark++]]);
        } else {
            if ($markerID !== 'none') {
                if (isset(self::$_markSet[$markerID])) {
                    $seriesPlot->mark->SetType(self::$_markSet[$markerID]);
                } else {
                    self::$_plotMark %= count(self::$_markSet);
                    $seriesPlot->mark->SetType(self::$_markSet[$plotMarkKeys[self::$_plotMark++]]);
                }
            } else {
                $seriesPlot->mark->Hide();
            }
        }
        $seriesPlot->mark->SetColor(self::$_colourSet[self::$_plotColour]);
        $seriesPlot->mark->SetFillColor(self::$_colourSet[self::$_plotColour]);
        $seriesPlot->SetColor(self::$_colourSet[self::$_plotColour++]);
        return $seriesPlot;
    }
    private function _formatDataSetLabels($groupID, $datasetLabels, $labelCount, $rotation = '')
    {
        $datasetLabelFormatCode = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getFormatCode();
        if (!is_null($datasetLabelFormatCode)) {
            $datasetLabelFormatCode = stripslashes($datasetLabelFormatCode);
        }
        $testCurrentIndex = 0;
        foreach ($datasetLabels as $i => $datasetLabel) {
            if (is_array($datasetLabel)) {
                if ($rotation == 'bar') {
                    $datasetLabels[$i] = implode(' ', $datasetLabel);
                } else {
                    $datasetLabel = array_reverse($datasetLabel);
                    $datasetLabels[$i] = implode("\n", $datasetLabel);
                }
            } else {
                if (!is_null($datasetLabelFormatCode)) {
                    $datasetLabels[$i] = PHPExcel_Style_NumberFormat::toFormattedString($datasetLabel, $datasetLabelFormatCode);
                }
            }
            ++$testCurrentIndex;
        }
        return $datasetLabels;
    }
    private function _percentageSumCalculation($groupID, $seriesCount)
    {
        for ($i = 0; $i < $seriesCount; ++$i) {
            if ($i == 0) {
                $sumValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
            } else {
                $nextValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
                foreach ($nextValues as $k => $value) {
                    if (isset($sumValues[$k])) {
                        $sumValues[$k] += $value;
                    } else {
                        $sumValues[$k] = $value;
                    }
                }
            }
        }
        return $sumValues;
    }
    private function _percentageAdjustValues($dataValues, $sumValues)
    {
        foreach ($dataValues as $k => $dataValue) {
            $dataValues[$k] = $dataValue / $sumValues[$k] * 100;
        }
        return $dataValues;
    }
    private function _getCaption($captionElement)
    {
        $caption = !is_null($captionElement) ? $captionElement->getCaption() : NULL;
        if (!is_null($caption)) {
            if (is_array($caption)) {
                $caption = implode('', $caption);
            }
        }
        return $caption;
    }
    private function _renderTitle()
    {
        $title = $this->_getCaption($this->_chart->getTitle());
        if (!is_null($title)) {
            $this->_graph->title->Set($title);
        }
    }
    private function _renderLegend()
    {
        $legend = $this->_chart->getLegend();
        if (!is_null($legend)) {
            $legendPosition = $legend->getPosition();
            $legendOverlay = $legend->getOverlay();
            switch ($legendPosition) {
                case 'r':
                    $this->_graph->legend->SetPos(0.01, 0.5, 'right', 'center');
                    $this->_graph->legend->SetColumns(1);
                    break;
                case 'l':
                    $this->_graph->legend->SetPos(0.01, 0.5, 'left', 'center');
                    $this->_graph->legend->SetColumns(1);
                    break;
                case 't':
                    $this->_graph->legend->SetPos(0.5, 0.01, 'center', 'top');
                    break;
                case 'b':
                    $this->_graph->legend->SetPos(0.5, 0.99, 'center', 'bottom');
                    break;
                default:
                    $this->_graph->legend->SetPos(0.01, 0.01, 'right', 'top');
                    $this->_graph->legend->SetColumns(1);
                    break;
            }
        } else {
            $this->_graph->legend->Hide();
        }
    }
    private function _renderCartesianPlotArea($type = 'textlin')
    {
        $this->_graph = new Graph(self::$_width, self::$_height);
        $this->_graph->SetScale($type);
        $this->_renderTitle();
        $rotation = $this->_chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotDirection();
        $reverse = $rotation == 'bar' ? true : false;
        $xAxisLabel = $this->_chart->getXAxisLabel();
        if (!is_null($xAxisLabel)) {
            $title = $this->_getCaption($xAxisLabel);
            if (!is_null($title)) {
                $this->_graph->xaxis->SetTitle($title, 'center');
                $this->_graph->xaxis->title->SetMargin(35);
                if ($reverse) {
                    $this->_graph->xaxis->title->SetAngle(90);
                    $this->_graph->xaxis->title->SetMargin(90);
                }
            }
        }
        $yAxisLabel = $this->_chart->getYAxisLabel();
        if (!is_null($yAxisLabel)) {
            $title = $this->_getCaption($yAxisLabel);
            if (!is_null($title)) {
                $this->_graph->yaxis->SetTitle($title, 'center');
                if ($reverse) {
                    $this->_graph->yaxis->title->SetAngle(0);
                    $this->_graph->yaxis->title->SetMargin(-55);
                }
            }
        }
    }
    private function _renderPiePlotArea($doughnut = false)
    {
        $this->_graph = new PieGraph(self::$_width, self::$_height);
        $this->_renderTitle();
    }
    private function _renderRadarPlotArea()
    {
        $this->_graph = new RadarGraph(self::$_width, self::$_height);
        $this->_graph->SetScale('lin');
        $this->_renderTitle();
    }
    private function _renderPlotLine($groupID, $filled = false, $combination = false, $dimensions = '2d')
    {
        $grouping = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();
        $labelCount = count($this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
        if (0 < $labelCount) {
            $datasetLabels = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
            $datasetLabels = $this->_formatDataSetLabels($groupID, $datasetLabels, $labelCount);
            $this->_graph->xaxis->SetTickLabels($datasetLabels);
        }
        $seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();
        if ($grouping == 'percentStacked') {
            $sumValues = $this->_percentageSumCalculation($groupID, $seriesCount);
        }
        for ($i = 0; $i < $seriesCount; ++$i) {
            $dataValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
            $marker = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();
            if ($grouping == 'percentStacked') {
                $dataValues = $this->_percentageAdjustValues($dataValues, $sumValues);
            }
            $testCurrentIndex = 0;
            foreach ($dataValues as $k => $dataValue) {
                while ($k != $testCurrentIndex) {
                    $dataValues[$testCurrentIndex] = NULL;
                    ++$testCurrentIndex;
                }
                ++$testCurrentIndex;
            }
            $seriesPlot = new LinePlot($dataValues);
            if ($combination) {
                $seriesPlot->SetBarCenter();
            }
            if ($filled) {
                $seriesPlot->SetFilled(true);
                $seriesPlot->SetColor('black');
                $seriesPlot->SetFillColor(self::$_colourSet[self::$_plotColour++]);
            } else {
                $this->_formatPointMarker($seriesPlot, $marker);
            }
            $dataLabel = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
            $seriesPlot->SetLegend($dataLabel);
            $seriesPlots[] = $seriesPlot;
        }
        if ($grouping == 'standard') {
            $groupPlot = $seriesPlots;
        } else {
            $groupPlot = new AccLinePlot($seriesPlots);
        }
        $this->_graph->Add($groupPlot);
    }
    private function _renderPlotBar($groupID, $dimensions = '2d')
    {
        $rotation = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotDirection();
        if ($groupID == 0 && $rotation == 'bar') {
            $this->_graph->Set90AndMargin();
        }
        $grouping = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();
        $labelCount = count($this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
        if (0 < $labelCount) {
            $datasetLabels = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
            $datasetLabels = $this->_formatDataSetLabels($groupID, $datasetLabels, $labelCount, $rotation);
            if ($rotation == 'bar') {
                $datasetLabels = array_reverse($datasetLabels);
                $this->_graph->yaxis->SetPos('max');
                $this->_graph->yaxis->SetLabelAlign('center', 'top');
                $this->_graph->yaxis->SetLabelSide(SIDE_RIGHT);
            }
            $this->_graph->xaxis->SetTickLabels($datasetLabels);
        }
        $seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();
        if ($grouping == 'percentStacked') {
            $sumValues = $this->_percentageSumCalculation($groupID, $seriesCount);
        }
        for ($j = 0; $j < $seriesCount; ++$j) {
            $dataValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($j)->getDataValues();
            if ($grouping == 'percentStacked') {
                $dataValues = $this->_percentageAdjustValues($dataValues, $sumValues);
            }
            $testCurrentIndex = 0;
            foreach ($dataValues as $k => $dataValue) {
                while ($k != $testCurrentIndex) {
                    $dataValues[$testCurrentIndex] = NULL;
                    ++$testCurrentIndex;
                }
                ++$testCurrentIndex;
            }
            if ($rotation == 'bar') {
                $dataValues = array_reverse($dataValues);
            }
            $seriesPlot = new BarPlot($dataValues);
            $seriesPlot->SetColor('black');
            $seriesPlot->SetFillColor(self::$_colourSet[self::$_plotColour++]);
            if ($dimensions == '3d') {
                $seriesPlot->SetShadow();
            }
            if (!$this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($j)) {
                $dataLabel = '';
            } else {
                $dataLabel = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($j)->getDataValue();
            }
            $seriesPlot->SetLegend($dataLabel);
            $seriesPlots[] = $seriesPlot;
        }
        if ($rotation == 'bar' && !$grouping == 'percentStacked') {
            $seriesPlots = array_reverse($seriesPlots);
        }
        if ($grouping == 'clustered') {
            $groupPlot = new GroupBarPlot($seriesPlots);
        } else {
            if ($grouping == 'standard') {
                $groupPlot = new GroupBarPlot($seriesPlots);
            } else {
                $groupPlot = new AccBarPlot($seriesPlots);
                if ($dimensions == '3d') {
                    $groupPlot->SetShadow();
                }
            }
        }
        $this->_graph->Add($groupPlot);
    }
    private function _renderPlotScatter($groupID, $bubble)
    {
        $grouping = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();
        $scatterStyle = $bubbleSize = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();
        $seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();
        for ($i = 0; $i < $seriesCount; ++$i) {
            $dataValuesY = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
            $dataValuesX = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
            foreach ($dataValuesY as $k => $dataValueY) {
                $dataValuesY[$k] = $k;
            }
            $seriesPlot = new ScatterPlot($dataValuesX, $dataValuesY);
            if ($scatterStyle == 'lineMarker') {
                $seriesPlot->SetLinkPoints();
                $seriesPlot->link->SetColor(self::$_colourSet[self::$_plotColour]);
            } else {
                if ($scatterStyle == 'smoothMarker') {
                    $spline = new Spline($dataValuesY, $dataValuesX);
                    list($splineDataY, $splineDataX) = $spline->Get(count($dataValuesX) * self::$_width / 20);
                    $lplot = new LinePlot($splineDataX, $splineDataY);
                    $lplot->SetColor(self::$_colourSet[self::$_plotColour]);
                    $this->_graph->Add($lplot);
                }
            }
            if ($bubble) {
                $this->_formatPointMarker($seriesPlot, 'dot');
                $seriesPlot->mark->SetColor('black');
                $seriesPlot->mark->SetSize($bubbleSize);
            } else {
                $marker = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();
                $this->_formatPointMarker($seriesPlot, $marker);
            }
            $dataLabel = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
            $seriesPlot->SetLegend($dataLabel);
            $this->_graph->Add($seriesPlot);
        }
    }
    private function _renderPlotRadar($groupID)
    {
        $radarStyle = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();
        $seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();
        for ($i = 0; $i < $seriesCount; ++$i) {
            $dataValuesY = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
            $dataValuesX = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
            $marker = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getPointMarker();
            $dataValues = array();
            foreach ($dataValuesY as $k => $dataValueY) {
                $dataValues[$k] = implode(' ', array_reverse($dataValueY));
            }
            $tmp = array_shift($dataValues);
            $dataValues[] = $tmp;
            $tmp = array_shift($dataValuesX);
            $dataValuesX[] = $tmp;
            $this->_graph->SetTitles(array_reverse($dataValues));
            $seriesPlot = new RadarPlot(array_reverse($dataValuesX));
            $dataLabel = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotLabelByIndex($i)->getDataValue();
            $seriesPlot->SetColor(self::$_colourSet[self::$_plotColour++]);
            if ($radarStyle == 'filled') {
                $seriesPlot->SetFillColor(self::$_colourSet[self::$_plotColour]);
            }
            $this->_formatPointMarker($seriesPlot, $marker);
            $seriesPlot->SetLegend($dataLabel);
            $this->_graph->Add($seriesPlot);
        }
    }
    private function _renderPlotContour($groupID)
    {
        $contourStyle = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();
        $seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $seriesPlots = array();
        $dataValues = array();
        for ($i = 0; $i < $seriesCount; ++$i) {
            $dataValuesY = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex($i)->getDataValues();
            $dataValuesX = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($i)->getDataValues();
            $dataValues[$i] = $dataValuesX;
        }
        $seriesPlot = new ContourPlot($dataValues);
        $this->_graph->Add($seriesPlot);
    }
    private function _renderPlotStock($groupID)
    {
        $seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
        $plotOrder = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotOrder();
        $dataValues = array();
        foreach ($plotOrder as $i => $v) {
            $dataValuesX = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($v)->getDataValues();
            foreach ($dataValuesX as $j => $dataValueX) {
                $dataValues[$plotOrder[$i]][$j] = $dataValueX;
            }
        }
        if (empty($dataValues)) {
            return NULL;
        }
        $dataValuesPlot = array();
        for ($j = 0; $j < count($dataValues[0]); $j++) {
            for ($i = 0; $i < $seriesCount; $i++) {
                $dataValuesPlot[] = $dataValues[$i][$j];
            }
        }
        $labelCount = count($this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
        if (0 < $labelCount) {
            $datasetLabels = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
            $datasetLabels = $this->_formatDataSetLabels($groupID, $datasetLabels, $labelCount);
            $this->_graph->xaxis->SetTickLabels($datasetLabels);
        }
        $seriesPlot = new StockPlot($dataValuesPlot);
        $seriesPlot->SetWidth(20);
        $this->_graph->Add($seriesPlot);
    }
    private function _renderAreaChart($groupCount, $dimensions = '2d')
    {
        require_once 'jpgraph_line.php';
        $this->_renderCartesianPlotArea();
        for ($i = 0; $i < $groupCount; ++$i) {
            $this->_renderPlotLine($i, true, false, $dimensions);
        }
    }
    private function _renderLineChart($groupCount, $dimensions = '2d')
    {
        require_once 'jpgraph_line.php';
        $this->_renderCartesianPlotArea();
        for ($i = 0; $i < $groupCount; ++$i) {
            $this->_renderPlotLine($i, false, false, $dimensions);
        }
    }
    private function _renderBarChart($groupCount, $dimensions = '2d')
    {
        require_once 'jpgraph_bar.php';
        $this->_renderCartesianPlotArea();
        for ($i = 0; $i < $groupCount; ++$i) {
            $this->_renderPlotBar($i, $dimensions);
        }
    }
    private function _renderScatterChart($groupCount)
    {
        require_once 'jpgraph_scatter.php';
        require_once 'jpgraph_regstat.php';
        require_once 'jpgraph_line.php';
        $this->_renderCartesianPlotArea('linlin');
        for ($i = 0; $i < $groupCount; ++$i) {
            $this->_renderPlotScatter($i, false);
        }
    }
    private function _renderBubbleChart($groupCount)
    {
        require_once 'jpgraph_scatter.php';
        $this->_renderCartesianPlotArea('linlin');
        for ($i = 0; $i < $groupCount; ++$i) {
            $this->_renderPlotScatter($i, true);
        }
    }
    private function _renderPieChart($groupCount, $dimensions = '2d', $doughnut = false, $multiplePlots = false)
    {
        require_once 'jpgraph_pie.php';
        if ($dimensions == '3d') {
            require_once 'jpgraph_pie3d.php';
        }
        $this->_renderPiePlotArea($doughnut);
        $iLimit = $multiplePlots ? $groupCount : 1;
        for ($groupID = 0; $groupID < $iLimit; ++$groupID) {
            $grouping = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotGrouping();
            $exploded = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotStyle();
            if ($groupID == 0) {
                $labelCount = count($this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex(0)->getPointCount());
                if (0 < $labelCount) {
                    $datasetLabels = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotCategoryByIndex(0)->getDataValues();
                    $datasetLabels = $this->_formatDataSetLabels($groupID, $datasetLabels, $labelCount);
                }
            }
            $seriesCount = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotSeriesCount();
            $seriesPlots = array();
            $jLimit = $multiplePlots ? $seriesCount : 1;
            for ($j = 0; $j < $jLimit; ++$j) {
                $dataValues = $this->_chart->getPlotArea()->getPlotGroupByIndex($groupID)->getPlotValuesByIndex($j)->getDataValues();
                $testCurrentIndex = 0;
                foreach ($dataValues as $k => $dataValue) {
                    while ($k != $testCurrentIndex) {
                        $dataValues[$testCurrentIndex] = NULL;
                        ++$testCurrentIndex;
                    }
                    ++$testCurrentIndex;
                }
                if ($dimensions == '3d') {
                    $seriesPlot = new PiePlot3D($dataValues);
                } else {
                    if ($doughnut) {
                        $seriesPlot = new PiePlotC($dataValues);
                    } else {
                        $seriesPlot = new PiePlot($dataValues);
                    }
                }
                if ($multiplePlots) {
                    $seriesPlot->SetSize(($jLimit - $j) / ($jLimit * 4));
                }
                if ($doughnut) {
                    $seriesPlot->SetMidColor('white');
                }
                $seriesPlot->SetColor(self::$_colourSet[self::$_plotColour++]);
                if (0 < count($datasetLabels)) {
                    $seriesPlot->SetLabels(array_fill(0, count($datasetLabels), ''));
                }
                if ($dimensions != '3d') {
                    $seriesPlot->SetGuideLines(false);
                }
                if ($j == 0) {
                    if ($exploded) {
                        $seriesPlot->ExplodeAll();
                    }
                    $seriesPlot->SetLegends($datasetLabels);
                }
                $this->_graph->Add($seriesPlot);
            }
        }
    }
    private function _renderRadarChart($groupCount)
    {
        require_once 'jpgraph_radar.php';
        $this->_renderRadarPlotArea();
        for ($groupID = 0; $groupID < $groupCount; ++$groupID) {
            $this->_renderPlotRadar($groupID);
        }
    }
    private function _renderStockChart($groupCount)
    {
        require_once 'jpgraph_stock.php';
        $this->_renderCartesianPlotArea('intint');
        for ($groupID = 0; $groupID < $groupCount; ++$groupID) {
            $this->_renderPlotStock($groupID);
        }
    }
    private function _renderContourChart($groupCount, $dimensions)
    {
        require_once 'jpgraph_contour.php';
        $this->_renderCartesianPlotArea('intint');
        for ($i = 0; $i < $groupCount; ++$i) {
            $this->_renderPlotContour($i);
        }
    }
    private function _renderCombinationChart($groupCount, $dimensions, $outputDestination)
    {
        require_once 'jpgraph_line.php';
        require_once 'jpgraph_bar.php';
        require_once 'jpgraph_scatter.php';
        require_once 'jpgraph_regstat.php';
        require_once 'jpgraph_line.php';
        $this->_renderCartesianPlotArea();
        for ($i = 0; $i < $groupCount; ++$i) {
            $dimensions = NULL;
            $chartType = $this->_chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
            switch ($chartType) {
                case 'area3DChart':
                    $dimensions = '3d';
                case 'areaChart':
                    $this->_renderPlotLine($i, true, true, $dimensions);
                    break;
                case 'bar3DChart':
                    $dimensions = '3d';
                case 'barChart':
                    $this->_renderPlotBar($i, $dimensions);
                    break;
                case 'line3DChart':
                    $dimensions = '3d';
                case 'lineChart':
                    $this->_renderPlotLine($i, false, true, $dimensions);
                    break;
                case 'scatterChart':
                    $this->_renderPlotScatter($i, false);
                    break;
                case 'bubbleChart':
                    $this->_renderPlotScatter($i, true);
                    break;
                default:
                    $this->_graph = NULL;
                    return false;
            }
        }
        $this->_renderLegend();
        $this->_graph->Stroke($outputDestination);
        return true;
    }
    public function render($outputDestination)
    {
        self::$_plotColour = 0;
        $groupCount = $this->_chart->getPlotArea()->getPlotGroupCount();
        $dimensions = NULL;
        if ($groupCount == 1) {
            $chartType = $this->_chart->getPlotArea()->getPlotGroupByIndex(0)->getPlotType();
        } else {
            $chartTypes = array();
            for ($i = 0; $i < $groupCount; ++$i) {
                $chartTypes[] = $this->_chart->getPlotArea()->getPlotGroupByIndex($i)->getPlotType();
            }
            $chartTypes = array_unique($chartTypes);
            if (count($chartTypes) == 1) {
                $chartType = array_pop($chartTypes);
            } else {
                if (count($chartTypes) == 0) {
                    echo 'Chart is not yet implemented<br />';
                    return false;
                } else {
                    return $this->_renderCombinationChart($groupCount, $dimensions, $outputDestination);
                }
            }
        }
        switch ($chartType) {
            case 'area3DChart':
                $dimensions = '3d';
            case 'areaChart':
                $this->_renderAreaChart($groupCount, $dimensions);
                break;
            case 'bar3DChart':
                $dimensions = '3d';
            case 'barChart':
                $this->_renderBarChart($groupCount, $dimensions);
                break;
            case 'line3DChart':
                $dimensions = '3d';
            case 'lineChart':
                $this->_renderLineChart($groupCount, $dimensions);
                break;
            case 'pie3DChart':
                $dimensions = '3d';
            case 'pieChart':
                $this->_renderPieChart($groupCount, $dimensions, false, false);
                break;
            case 'doughnut3DChart':
                $dimensions = '3d';
            case 'doughnutChart':
                $this->_renderPieChart($groupCount, $dimensions, true, true);
                break;
            case 'scatterChart':
                $this->_renderScatterChart($groupCount);
                break;
            case 'bubbleChart':
                $this->_renderBubbleChart($groupCount);
                break;
            case 'radarChart':
                $this->_renderRadarChart($groupCount);
                break;
            case 'surface3DChart':
                $dimensions = '3d';
            case 'surfaceChart':
                $this->_renderContourChart($groupCount, $dimensions);
                break;
            case 'stockChart':
                $this->_renderStockChart($groupCount, $dimensions);
                break;
            default:
                echo $chartType . ' is not yet implemented<br />';
                return false;
        }
        $this->_renderLegend();
        $this->_graph->Stroke($outputDestination);
        return true;
    }
    public function __construct(PHPExcel_Chart $chart)
    {
        $this->_graph = NULL;
        $this->_chart = $chart;
    }
}
require_once PHPExcel_Settings::getChartRendererPath() . '/jpgraph.php';
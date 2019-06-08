<?php

class PHPExcel_Best_Fit
{
	/**
	 * Indicator flag for a calculation error
	 *
	 * @var	boolean
	 **/
	protected $_error = false;
	/**
	 * Algorithm type to use for best-fit
	 *
	 * @var	string
	 **/
	protected $_bestFitType = 'undetermined';
	/**
	 * Number of entries in the sets of x- and y-value arrays
	 *
	 * @var	int
	 **/
	protected $_valueCount = 0;
	/**
	 * X-value dataseries of values
	 *
	 * @var	float[]
	 **/
	protected $_xValues = array();
	/**
	 * Y-value dataseries of values
	 *
	 * @var	float[]
	 **/
	protected $_yValues = array();
	/**
	 * Flag indicating whether values should be adjusted to Y=0
	 *
	 * @var	boolean
	 **/
	protected $_adjustToZero = false;
	/**
	 * Y-value series of best-fit values
	 *
	 * @var	float[]
	 **/
	protected $_yBestFitValues = array();
	protected $_goodnessOfFit = 1;
	protected $_stdevOfResiduals = 0;
	protected $_covariance = 0;
	protected $_correlation = 0;
	protected $_SSRegression = 0;
	protected $_SSResiduals = 0;
	protected $_DFResiduals = 0;
	protected $_F = 0;
	protected $_slope = 0;
	protected $_slopeSE = 0;
	protected $_intersect = 0;
	protected $_intersectSE = 0;
	protected $_Xoffset = 0;
	protected $_Yoffset = 0;

	public function getError()
	{
		return $this->_error;
	}

	public function getBestFitType()
	{
		return $this->_bestFitType;
	}

	public function getValueOfYForX($xValue)
	{
		return false;
	}

	public function getValueOfXForY($yValue)
	{
		return false;
	}

	public function getXValues()
	{
		return $this->_xValues;
	}

	public function getEquation($dp = 0)
	{
		return false;
	}

	public function getSlope($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_slope, $dp);
		}

		return $this->_slope;
	}

	public function getSlopeSE($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_slopeSE, $dp);
		}

		return $this->_slopeSE;
	}

	public function getIntersect($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_intersect, $dp);
		}

		return $this->_intersect;
	}

	public function getIntersectSE($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_intersectSE, $dp);
		}

		return $this->_intersectSE;
	}

	public function getGoodnessOfFit($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_goodnessOfFit, $dp);
		}

		return $this->_goodnessOfFit;
	}

	public function getGoodnessOfFitPercent($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_goodnessOfFit * 100, $dp);
		}

		return $this->_goodnessOfFit * 100;
	}

	public function getStdevOfResiduals($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_stdevOfResiduals, $dp);
		}

		return $this->_stdevOfResiduals;
	}

	public function getSSRegression($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_SSRegression, $dp);
		}

		return $this->_SSRegression;
	}

	public function getSSResiduals($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_SSResiduals, $dp);
		}

		return $this->_SSResiduals;
	}

	public function getDFResiduals($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_DFResiduals, $dp);
		}

		return $this->_DFResiduals;
	}

	public function getF($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_F, $dp);
		}

		return $this->_F;
	}

	public function getCovariance($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_covariance, $dp);
		}

		return $this->_covariance;
	}

	public function getCorrelation($dp = 0)
	{
		if ($dp != 0) {
			return round($this->_correlation, $dp);
		}

		return $this->_correlation;
	}

	public function getYBestFitValues()
	{
		return $this->_yBestFitValues;
	}

	protected function _calculateGoodnessOfFit($sumX, $sumY, $sumX2, $sumY2, $sumXY, $meanX, $meanY, $const)
	{
		$SSres = $SScov = $SScor = $SStot = $SSsex = 0;

		foreach ($this->_xValues as $xKey => $xValue) {
			$bestFitY = $this->_yBestFitValues[$xKey] = $this->getValueOfYForX($xValue);
			$SSres += ($this->_yValues[$xKey] - $bestFitY) * ($this->_yValues[$xKey] - $bestFitY);

			if ($const) {
				$SStot += ($this->_yValues[$xKey] - $meanY) * ($this->_yValues[$xKey] - $meanY);
			}
			else {
				$SStot += $this->_yValues[$xKey] * $this->_yValues[$xKey];
			}

			$SScov += ($this->_xValues[$xKey] - $meanX) * ($this->_yValues[$xKey] - $meanY);

			if ($const) {
				$SSsex += ($this->_xValues[$xKey] - $meanX) * ($this->_xValues[$xKey] - $meanX);
			}
			else {
				$SSsex += $this->_xValues[$xKey] * $this->_xValues[$xKey];
			}
		}

		$this->_SSResiduals = $SSres;
		$this->_DFResiduals = $this->_valueCount - 1 - $const;

		if ($this->_DFResiduals == 0) {
			$this->_stdevOfResiduals = 0;
		}
		else {
			$this->_stdevOfResiduals = sqrt($SSres / $this->_DFResiduals);
		}

		if (($SStot == 0) || ($SSres == $SStot)) {
			$this->_goodnessOfFit = 1;
		}
		else {
			$this->_goodnessOfFit = 1 - ($SSres / $SStot);
		}

		$this->_SSRegression = $this->_goodnessOfFit * $SStot;
		$this->_covariance = $SScov / $this->_valueCount;
		$this->_correlation = (($this->_valueCount * $sumXY) - ($sumX * $sumY)) / sqrt((($this->_valueCount * $sumX2) - pow($sumX, 2)) * (($this->_valueCount * $sumY2) - pow($sumY, 2)));
		$this->_slopeSE = $this->_stdevOfResiduals / sqrt($SSsex);
		$this->_intersectSE = $this->_stdevOfResiduals * sqrt(1 / ($this->_valueCount - (($sumX * $sumX) / $sumX2)));

		if ($this->_SSResiduals != 0) {
			if ($this->_DFResiduals == 0) {
				$this->_F = 0;
			}
			else {
				$this->_F = $this->_SSRegression / $this->_SSResiduals / $this->_DFResiduals;
			}
		}
		else if ($this->_DFResiduals == 0) {
			$this->_F = 0;
		}
		else {
			$this->_F = $this->_SSRegression / $this->_DFResiduals;
		}
	}

	protected function _leastSquareFit($yValues, $xValues, $const)
	{
		$x_sum = array_sum($xValues);
		$y_sum = array_sum($yValues);
		$meanX = $x_sum / $this->_valueCount;
		$meanY = $y_sum / $this->_valueCount;
		$mBase = $mDivisor = $xx_sum = $xy_sum = $yy_sum = 0;

		for ($i = 0; $i < $this->_valueCount; ++$i) {
			$xy_sum += $xValues[$i] * $yValues[$i];
			$xx_sum += $xValues[$i] * $xValues[$i];
			$yy_sum += $yValues[$i] * $yValues[$i];

			if ($const) {
				$mBase += ($xValues[$i] - $meanX) * ($yValues[$i] - $meanY);
				$mDivisor += ($xValues[$i] - $meanX) * ($xValues[$i] - $meanX);
			}
			else {
				$mBase += $xValues[$i] * $yValues[$i];
				$mDivisor += $xValues[$i] * $xValues[$i];
			}
		}

		$this->_slope = $mBase / $mDivisor;

		if ($const) {
			$this->_intersect = $meanY - ($this->_slope * $meanX);
		}
		else {
			$this->_intersect = 0;
		}

		$this->_calculateGoodnessOfFit($x_sum, $y_sum, $xx_sum, $yy_sum, $xy_sum, $meanX, $meanY, $const);
	}

	public function __construct($yValues, $xValues = array(), $const = true)
	{
		$nY = count($yValues);
		$nX = count($xValues);

		if ($nX == 0) {
			$xValues = range(1, $nY);
			$nX = $nY;
		}
		else if ($nY != $nX) {
			$this->_error = true;
			return false;
		}

		$this->_valueCount = $nY;
		$this->_xValues = $xValues;
		$this->_yValues = $yValues;
	}
}


?>

<?php

require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/bestFitClass.php';
class PHPExcel_Exponential_Best_Fit extends PHPExcel_Best_Fit
{
	/**
	 * Algorithm type to use for best-fit
	 * (Name of this trend class)
	 *
	 * @var	string
	 **/
	protected $_bestFitType = 'exponential';

	public function getValueOfYForX($xValue)
	{
		return $this->getIntersect() * pow($this->getSlope(), $xValue - $this->_Xoffset);
	}

	public function getValueOfXForY($yValue)
	{
		return log(($yValue + $this->_Yoffset) / $this->getIntersect()) / log($this->getSlope());
	}

	public function getEquation($dp = 0)
	{
		$slope = $this->getSlope($dp);
		$intersect = $this->getIntersect($dp);
		return 'Y = ' . $intersect . ' * ' . $slope . '^X';
	}

	public function getSlope($dp = 0)
	{
		if ($dp != 0) {
			return round(exp($this->_slope), $dp);
		}

		return exp($this->_slope);
	}

	public function getIntersect($dp = 0)
	{
		if ($dp != 0) {
			return round(exp($this->_intersect), $dp);
		}

		return exp($this->_intersect);
	}

	private function _exponential_regression($yValues, $xValues, $const)
	{
		foreach ($yValues as &$value) {
			if ($value < 0) {
				$value = 0 - log(abs($value));
			}
			else if (0 < $value) {
				$value = log($value);
			}
		}

		unset($value);
		$this->_leastSquareFit($yValues, $xValues, $const);
	}

	public function __construct($yValues, $xValues = array(), $const = true)
	{
		if (parent::__construct($yValues, $xValues) !== false) {
			$this->_exponential_regression($yValues, $xValues, $const);
		}
	}
}

?>

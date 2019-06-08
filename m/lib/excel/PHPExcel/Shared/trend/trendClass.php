<?php

class trendClass
{
	const TREND_LINEAR = 'Linear';
	const TREND_LOGARITHMIC = 'Logarithmic';
	const TREND_EXPONENTIAL = 'Exponential';
	const TREND_POWER = 'Power';
	const TREND_POLYNOMIAL_2 = 'Polynomial_2';
	const TREND_POLYNOMIAL_3 = 'Polynomial_3';
	const TREND_POLYNOMIAL_4 = 'Polynomial_4';
	const TREND_POLYNOMIAL_5 = 'Polynomial_5';
	const TREND_POLYNOMIAL_6 = 'Polynomial_6';
	const TREND_BEST_FIT = 'Bestfit';
	const TREND_BEST_FIT_NO_POLY = 'Bestfit_no_Polynomials';

	/**
	 * Names of the best-fit trend analysis methods
	 *
	 * @var string[]
	 **/
	static private $_trendTypes = array(self::TREND_LINEAR, self::TREND_LOGARITHMIC, self::TREND_EXPONENTIAL, self::TREND_POWER);
	/**
	 * Names of the best-fit trend polynomial orders
	 *
	 * @var string[]
	 **/
	static private $_trendTypePolyOrders = array(self::TREND_POLYNOMIAL_2, self::TREND_POLYNOMIAL_3, self::TREND_POLYNOMIAL_4, self::TREND_POLYNOMIAL_5, self::TREND_POLYNOMIAL_6);
	/**
	 * Cached results for each method when trying to identify which provides the best fit
	 *
	 * @var PHPExcel_Best_Fit[]
	 **/
	static private $_trendCache = array();

	static public function calculate($trendType = self::TREND_BEST_FIT, $yValues, $xValues = array(), $const = true)
	{
		$nY = count($yValues);
		$nX = count($xValues);

		if ($nX == 0) {
			$xValues = range(1, $nY);
			$nX = $nY;
		}
		else if ($nY != $nX) {
			trigger_error('trend(): Number of elements in coordinate arrays do not match.', 256);
		}

		$key = md5($trendType . $const . serialize($yValues) . serialize($xValues));

		switch ($trendType) {
		case self::TREND_LINEAR:
		case self::TREND_LOGARITHMIC:
		case self::TREND_EXPONENTIAL:
		case self::TREND_POWER:
			if (!isset(self::$_trendCache[$key])) {
				$className = 'PHPExcel_' . $trendType . '_Best_Fit';
				self::$_trendCache[$key] = new $className($yValues, $xValues, $const);
			}

			return self::$_trendCache[$key];
			break;

		case self::TREND_POLYNOMIAL_2:
		case self::TREND_POLYNOMIAL_3:
		case self::TREND_POLYNOMIAL_4:
		case self::TREND_POLYNOMIAL_5:
		case self::TREND_POLYNOMIAL_6:
			if (!isset(self::$_trendCache[$key])) {
				$order = substr($trendType, -1);
				self::$_trendCache[$key] = new PHPExcel_Polynomial_Best_Fit($order, $yValues, $xValues, $const);
			}

			return self::$_trendCache[$key];
			break;

		case self::TREND_BEST_FIT:
		case self::TREND_BEST_FIT_NO_POLY:
			foreach (self::$_trendTypes as $trendMethod) {
				$className = 'PHPExcel_' . $trendMethod . 'BestFit';
				$bestFit[$trendMethod] = new $className($yValues, $xValues, $const);
				$bestFitValue[$trendMethod] = $bestFit[$trendMethod]->getGoodnessOfFit();
			}

			if ($trendType != self::TREND_BEST_FIT_NO_POLY) {
				foreach (self::$_trendTypePolyOrders as $trendMethod) {
					$order = substr($trendMethod, -1);
					$bestFit[$trendMethod] = new PHPExcel_Polynomial_Best_Fit($order, $yValues, $xValues, $const);

					if ($bestFit[$trendMethod]->getError()) {
						unset($bestFit[$trendMethod]);
					}
					else {
						$bestFitValue[$trendMethod] = $bestFit[$trendMethod]->getGoodnessOfFit();
					}
				}
			}

			arsort($bestFitValue);
			$bestFitType = key($bestFitValue);
			return $bestFit[$bestFitType];
			break;

		default:
			return false;
		}
	}
}

require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/linearBestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/logarithmicBestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/exponentialBestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/powerBestFitClass.php';
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/polynomialBestFitClass.php';

?>

<?php

class PHPExcel_Shared_TimeZone
{
	static protected $_timezone = 'UTC';

	static public function _validateTimeZone($timezone)
	{
		if (in_array($timezone, DateTimeZone::listIdentifiers())) {
			return true;
		}

		return false;
	}

	static public function setTimeZone($timezone)
	{
		if (self::_validateTimezone($timezone)) {
			self::$_timezone = $timezone;
			return true;
		}

		return false;
	}

	static public function getTimeZone()
	{
		return self::$_timezone;
	}

	static private function _getTimezoneTransitions($objTimezone, $timestamp)
	{
		$allTransitions = $objTimezone->getTransitions();
		$transitions = array();

		foreach ($allTransitions as $key => $transition) {
			if ($timestamp < $transition['ts']) {
				$transitions[] = 0 < $key ? $allTransitions[$key - 1] : $transition;
				break;
			}

			if (empty($transitions)) {
				$transitions[] = end($allTransitions);
			}
		}

		return $transitions;
	}

	static public function getTimeZoneAdjustment($timezone, $timestamp)
	{
		if ($timezone !== NULL) {
			if (!self::_validateTimezone($timezone)) {
				throw new PHPExcel_Exception('Invalid timezone ' . $timezone);
			}
		}
		else {
			$timezone = self::$_timezone;
		}

		if ($timezone == 'UST') {
			return 0;
		}

		$objTimezone = new DateTimeZone($timezone);

		if (0 <= version_compare(PHP_VERSION, '5.3.0')) {
			$transitions = $objTimezone->getTransitions($timestamp, $timestamp);
		}
		else {
			$transitions = self::_getTimezoneTransitions($objTimezone, $timestamp);
		}

		return 0 < count($transitions) ? $transitions[0]['offset'] : 0;
	}
}


?>

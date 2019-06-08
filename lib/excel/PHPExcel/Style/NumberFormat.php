<?php

class PHPExcel_Style_NumberFormat extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
	const FORMAT_GENERAL = 'General';
	const FORMAT_TEXT = '@';
	const FORMAT_NUMBER = '0';
	const FORMAT_NUMBER_00 = '0.00';
	const FORMAT_NUMBER_COMMA_SEPARATED1 = '#,##0.00';
	const FORMAT_NUMBER_COMMA_SEPARATED2 = '#,##0.00_-';
	const FORMAT_PERCENTAGE = '0%';
	const FORMAT_PERCENTAGE_00 = '0.00%';
	const FORMAT_DATE_YYYYMMDD2 = 'yyyy-mm-dd';
	const FORMAT_DATE_YYYYMMDD = 'yy-mm-dd';
	const FORMAT_DATE_DDMMYYYY = 'dd/mm/yy';
	const FORMAT_DATE_DMYSLASH = 'd/m/y';
	const FORMAT_DATE_DMYMINUS = 'd-m-y';
	const FORMAT_DATE_DMMINUS = 'd-m';
	const FORMAT_DATE_MYMINUS = 'm-y';
	const FORMAT_DATE_XLSX14 = 'mm-dd-yy';
	const FORMAT_DATE_XLSX15 = 'd-mmm-yy';
	const FORMAT_DATE_XLSX16 = 'd-mmm';
	const FORMAT_DATE_XLSX17 = 'mmm-yy';
	const FORMAT_DATE_XLSX22 = 'm/d/yy h:mm';
	const FORMAT_DATE_DATETIME = 'd/m/y h:mm';
	const FORMAT_DATE_TIME1 = 'h:mm AM/PM';
	const FORMAT_DATE_TIME2 = 'h:mm:ss AM/PM';
	const FORMAT_DATE_TIME3 = 'h:mm';
	const FORMAT_DATE_TIME4 = 'h:mm:ss';
	const FORMAT_DATE_TIME5 = 'mm:ss';
	const FORMAT_DATE_TIME6 = 'h:mm:ss';
	const FORMAT_DATE_TIME7 = 'i:s.S';
	const FORMAT_DATE_TIME8 = 'h:mm:ss;@';
	const FORMAT_DATE_YYYYMMDDSLASH = 'yy/mm/dd;@';
	const FORMAT_CURRENCY_USD_SIMPLE = '"$"#,##0.00_-';
	const FORMAT_CURRENCY_USD = '$#,##0_-';
	const FORMAT_CURRENCY_EUR_SIMPLE = '[$EUR ]#,##0.00_-';

	/**
	 * Excel built-in number formats
	 *
	 * @var array
	 */
	static protected $_builtInFormats;
	/**
	 * Excel built-in number formats (flipped, for faster lookups)
	 *
	 * @var array
	 */
	static protected $_flippedBuiltInFormats;
	/**
	 * Format Code
	 *
	 * @var string
	 */
	protected $_formatCode = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
	/**
	 * Built-in format Code
	 *
	 * @var string
	 */
	protected $_builtInFormatCode = 0;
	/**
	 * Search/replace values to convert Excel date/time format masks to PHP format masks
	 *
	 * @var array
	 */
	static private $_dateFormatReplacements = array('\\' => '', 'am/pm' => 'A', 'e' => 'Y', 'yyyy' => 'Y', 'yy' => 'y', 'mmmmm' => 'M', 'mmmm' => 'F', 'mmm' => 'M', ':mm' => ':i', 'mm:' => 'i:', 'mm' => 'm', 'm' => 'n', 'dddd' => 'l', 'ddd' => 'D', 'dd' => 'd', 'd' => 'j', 'ss' => 's', '.s' => '');
	/**
	 * Search/replace values to convert Excel date/time format masks hours to PHP format masks (24 hr clock)
	 *
	 * @var array
	 */
	static private $_dateFormatReplacements24 = array('hh' => 'H', 'h' => 'G');
	/**
	 * Search/replace values to convert Excel date/time format masks hours to PHP format masks (12 hr clock)
	 *
	 * @var array
	 */
	static private $_dateFormatReplacements12 = array('hh' => 'h', 'h' => 'g');

	public function __construct($isSupervisor = false, $isConditional = false)
	{
		parent::__construct($isSupervisor);

		if ($isConditional) {
			$this->_formatCode = NULL;
		}
	}

	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getNumberFormat();
	}

	public function getStyleArray($array)
	{
		return array('numberformat' => $array);
	}

	public function applyFromArray($pStyles = NULL)
	{
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			}
			else if (array_key_exists('code', $pStyles)) {
				$this->setFormatCode($pStyles['code']);
			}
		}
		else {
			throw new PHPExcel_Exception('Invalid style array passed.');
		}

		return $this;
	}

	public function getFormatCode()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getFormatCode();
		}

		if ($this->_builtInFormatCode !== false) {
			return self::builtInFormatCode($this->_builtInFormatCode);
		}

		return $this->_formatCode;
	}

	public function setFormatCode($pValue = PHPExcel_Style_NumberFormat::FORMAT_GENERAL)
	{
		if ($pValue == '') {
			$pValue = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('code' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_formatCode = $pValue;
			$this->_builtInFormatCode = self::builtInFormatCodeIndex($pValue);
		}

		return $this;
	}

	public function getBuiltInFormatCode()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getBuiltInFormatCode();
		}

		return $this->_builtInFormatCode;
	}

	public function setBuiltInFormatCode($pValue = 0)
	{
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('code' => self::builtInFormatCode($pValue)));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_builtInFormatCode = $pValue;
			$this->_formatCode = self::builtInFormatCode($pValue);
		}

		return $this;
	}

	static private function fillBuiltInFormatCodes()
	{
		if (is_null(self::$_builtInFormats)) {
			self::$_builtInFormats = array();
			self::$_builtInFormats[0] = PHPExcel_Style_NumberFormat::FORMAT_GENERAL;
			self::$_builtInFormats[1] = '0';
			self::$_builtInFormats[2] = '0.00';
			self::$_builtInFormats[3] = '#,##0';
			self::$_builtInFormats[4] = '#,##0.00';
			self::$_builtInFormats[9] = '0%';
			self::$_builtInFormats[10] = '0.00%';
			self::$_builtInFormats[11] = '0.00E+00';
			self::$_builtInFormats[12] = '# ?/?';
			self::$_builtInFormats[13] = '# ??/??';
			self::$_builtInFormats[14] = 'mm-dd-yy';
			self::$_builtInFormats[15] = 'd-mmm-yy';
			self::$_builtInFormats[16] = 'd-mmm';
			self::$_builtInFormats[17] = 'mmm-yy';
			self::$_builtInFormats[18] = 'h:mm AM/PM';
			self::$_builtInFormats[19] = 'h:mm:ss AM/PM';
			self::$_builtInFormats[20] = 'h:mm';
			self::$_builtInFormats[21] = 'h:mm:ss';
			self::$_builtInFormats[22] = 'm/d/yy h:mm';
			self::$_builtInFormats[37] = '#,##0 ;(#,##0)';
			self::$_builtInFormats[38] = '#,##0 ;[Red](#,##0)';
			self::$_builtInFormats[39] = '#,##0.00;(#,##0.00)';
			self::$_builtInFormats[40] = '#,##0.00;[Red](#,##0.00)';
			self::$_builtInFormats[44] = '_("$"* #,##0.00_);_("$"* \\(#,##0.00\\);_("$"* "-"??_);_(@_)';
			self::$_builtInFormats[45] = 'mm:ss';
			self::$_builtInFormats[46] = '[h]:mm:ss';
			self::$_builtInFormats[47] = 'mmss.0';
			self::$_builtInFormats[48] = '##0.0E+0';
			self::$_builtInFormats[49] = '@';
			self::$_builtInFormats[27] = '[$-404]e/m/d';
			self::$_builtInFormats[30] = 'm/d/yy';
			self::$_builtInFormats[36] = '[$-404]e/m/d';
			self::$_builtInFormats[50] = '[$-404]e/m/d';
			self::$_builtInFormats[57] = '[$-404]e/m/d';
			self::$_builtInFormats[59] = 't0';
			self::$_builtInFormats[60] = 't0.00';
			self::$_builtInFormats[61] = 't#,##0';
			self::$_builtInFormats[62] = 't#,##0.00';
			self::$_builtInFormats[67] = 't0%';
			self::$_builtInFormats[68] = 't0.00%';
			self::$_builtInFormats[69] = 't# ?/?';
			self::$_builtInFormats[70] = 't# ??/??';
			self::$_flippedBuiltInFormats = array_flip(self::$_builtInFormats);
		}
	}

	static public function builtInFormatCode($pIndex)
	{
		$pIndex = intval($pIndex);
		self::fillBuiltInFormatCodes();

		if (isset(self::$_builtInFormats[$pIndex])) {
			return self::$_builtInFormats[$pIndex];
		}

		return '';
	}

	static public function builtInFormatCodeIndex($formatCode)
	{
		self::fillBuiltInFormatCodes();

		if (isset(self::$_flippedBuiltInFormats[$formatCode])) {
			return self::$_flippedBuiltInFormats[$formatCode];
		}

		return false;
	}

	public function getHashCode()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashCode();
		}

		return md5($this->_formatCode . $this->_builtInFormatCode . 'PHPExcel_Style_NumberFormat');
	}

	static private function _formatAsDate(&$value, &$format)
	{
		$format = preg_replace('/^(\\[\\$[A-Z]*-[0-9A-F]*\\])/i', '', $format);
		$format = strtolower($format);
		$format = strtr($format, self::$_dateFormatReplacements);

		if (!strpos($format, 'A')) {
			$format = strtr($format, self::$_dateFormatReplacements24);
		}
		else {
			$format = strtr($format, self::$_dateFormatReplacements12);
		}

		$dateObj = PHPExcel_Shared_Date::ExcelToPHPObject($value);
		$value = $dateObj->format($format);
	}

	static private function _formatAsPercentage(&$value, &$format)
	{
		if ($format === self::FORMAT_PERCENTAGE) {
			$value = round(100 * $value, 0) . '%';
		}
		else {
			if (preg_match('/\\.[#0]+/i', $format, $m)) {
				$s = substr($m[0], 0, 1) . (strlen($m[0]) - 1);
				$format = str_replace($m[0], $s, $format);
			}

			if (preg_match('/^[#0]+/', $format, $m)) {
				$format = str_replace($m[0], strlen($m[0]), $format);
			}

			$format = '%' . str_replace('%', 'f%%', $format);
			$value = sprintf($format, 100 * $value);
		}
	}

	static private function _formatAsFraction(&$value, &$format)
	{
		$sign = ($value < 0 ? '-' : '');
		$integerPart = floor(abs($value));
		$decimalPart = trim(fmod(abs($value), 1), '0.');
		$decimalLength = strlen($decimalPart);
		$decimalDivisor = pow(10, $decimalLength);
		$GCD = PHPExcel_Calculation_MathTrig::GCD($decimalPart, $decimalDivisor);
		$adjustedDecimalPart = $decimalPart / $GCD;
		$adjustedDecimalDivisor = $decimalDivisor / $GCD;
		if ((strpos($format, '0') !== false) || (strpos($format, '#') !== false) || (substr($format, 0, 3) == '? ?')) {
			if ($integerPart == 0) {
				$integerPart = '';
			}

			$value = $sign . $integerPart . ' ' . $adjustedDecimalPart . '/' . $adjustedDecimalDivisor;
		}
		else {
			$adjustedDecimalPart += $integerPart * $adjustedDecimalDivisor;
			$value = $sign . $adjustedDecimalPart . '/' . $adjustedDecimalDivisor;
		}
	}

	static private function _complexNumberFormatMask($number, $mask)
	{
		if (strpos($mask, '.') !== false) {
			$numbers = explode('.', $number . '.0');
			$masks = explode('.', $mask . '.0');
			$result1 = self::_complexNumberFormatMask($numbers[0], $masks[0]);
			$result2 = strrev(self::_complexNumberFormatMask(strrev($numbers[1]), strrev($masks[1])));
			return $result1 . '.' . $result2;
		}

		$r = preg_match_all('/0+/', $mask, $result, PREG_OFFSET_CAPTURE);

		if (1 < $r) {
			$result = array_reverse($result[0]);

			foreach ($result as $block) {
				$divisor = 1 . $block[0];
				$size = strlen($block[0]);
				$offset = $block[1];
				$blockValue = sprintf('%0' . $size . 'd', fmod($number, $divisor));
				$number = floor($number / $divisor);
				$mask = substr_replace($mask, $blockValue, $offset, $size);
			}

			if (0 < $number) {
				$mask = substr_replace($mask, $number, $offset, 0);
			}

			$result = $mask;
		}
		else {
			$result = $number;
		}

		return $result;
	}

	static public function toFormattedString($value = '0', $format = PHPExcel_Style_NumberFormat::FORMAT_GENERAL, $callBack = NULL)
	{
		if (!is_numeric($value)) {
			return $value;
		}

		if (($format === PHPExcel_Style_NumberFormat::FORMAT_GENERAL) || ($format === PHPExcel_Style_NumberFormat::FORMAT_TEXT)) {
			return $value;
		}

		$sections = explode(';', $format);

		switch (count($sections)) {
		case 1:
			$format = $sections[0];
			break;

		case 2:
			$format = (0 <= $value ? $sections[0] : $sections[1]);
			$value = abs($value);
			break;

		case 3:
			$format = (0 < $value ? $sections[0] : ($value < 0 ? $sections[1] : $sections[2]));
			$value = abs($value);
			break;

		case 4:
			$format = (0 < $value ? $sections[0] : ($value < 0 ? $sections[1] : $sections[2]));
			$value = abs($value);
			break;

		default:
			$format = $sections[0];
			break;
		}

		$formatColor = $format;
		$color_regex = '/^\\[[a-zA-Z]+\\]/';
		$format = preg_replace($color_regex, '', $format);

		if (preg_match('/^(\\[\\$[A-Z]*-[0-9A-F]*\\])*[hmsdy]/i', $format)) {
			self::_formatAsDate($value, $format);
		}
		else if (preg_match('/%$/', $format)) {
			self::_formatAsPercentage($value, $format);
		}
		else if ($format === self::FORMAT_CURRENCY_EUR_SIMPLE) {
			$value = 'EUR ' . sprintf('%1.2f', $value);
		}
		else {
			$format = preg_replace('/_./', '', $format);
			$format = preg_replace('/\\\\/', '', $format);
			$format = str_replace(array('"', '*'), '', $format);
			$useThousands = preg_match('/(#,#|0,0)/', $format);

			if ($useThousands) {
				$format = preg_replace('/0,0/', '00', $format);
				$format = preg_replace('/#,#/', '##', $format);
			}

			$scale = 1;
			$matches = array();

			if (preg_match('/(#|0)(,+)/', $format, $matches)) {
				$scale = pow(1000, strlen($matches[2]));
				$format = preg_replace('/0,+/', '0', $format);
				$format = preg_replace('/#,+/', '#', $format);
			}

			if (preg_match('/#?.*\\?\\/\\?/', $format, $m)) {
				if ($value != (int) $value) {
					self::_formatAsFraction($value, $format);
				}
			}
			else {
				$value = $value / $scale;
				$format = preg_replace('/\\#/', '0', $format);
				$n = '/\\[[^\\]]+\\]/';
				$m = preg_replace($n, '', $format);
				$number_regex = '/(0+)(\\.?)(0*)/';

				if (preg_match($number_regex, $m, $matches)) {
					$left = $matches[1];
					$dec = $matches[2];
					$right = $matches[3];
					$minWidth = strlen($left) + strlen($dec) + strlen($right);

					if ($useThousands) {
						$value = number_format($value, strlen($right), PHPExcel_Shared_String::getDecimalSeparator(), PHPExcel_Shared_String::getThousandsSeparator());
						$value = preg_replace($number_regex, $value, $format);
					}
					else if (preg_match('/[0#]E[+-]0/i', $format)) {
						$value = sprintf('%5.2E', $value);
					}
					else if (preg_match('/0([^\\d\\.]+)0/', $format)) {
						$value = self::_complexNumberFormatMask($value, $format);
					}
					else {
						$sprintf_pattern = '%0' . $minWidth . '.' . strlen($right) . 'f';
						$value = sprintf($sprintf_pattern, $value);
						$value = preg_replace($number_regex, $value, $format);
					}
				}
			}

			if (preg_match('/\\[\\$(.*)\\]/u', $format, $m)) {
				$currencyFormat = $m[0];
				$currencyCode = $m[1];
				list($currencyCode) = explode('-', $currencyCode);

				if ($currencyCode == '') {
					$currencyCode = PHPExcel_Shared_String::getCurrencyCode();
				}

				$value = preg_replace('/\\[\\$([^\\]]*)\\]/u', $currencyCode, $value);
			}
		}

		if ($callBack !== NULL) {
			list($writerInstance, $function) = $callBack;
			$value = $writerInstance->$function($value, $formatColor);
		}

		return $value;
	}
}

?>

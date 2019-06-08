<?php

class PHPExcel_Shared_String
{
	const STRING_REGEXP_FRACTION = '(-?)(\\d+)\\s+(\\d+\\/\\d+)';

	/**
	 * Control characters array
	 *
	 * @var string[]
	 */
	static private $_controlCharacters = array();
	/**
	 * SYLK Characters array
	 *
	 * $var array
	 */
	static private $_SYLKCharacters = array();
	/**
	 * Decimal separator
	 *
	 * @var string
	 */
	static private $_decimalSeparator;
	/**
	 * Thousands separator
	 *
	 * @var string
	 */
	static private $_thousandsSeparator;
	/**
	 * Currency code
	 *
	 * @var string
	 */
	static private $_currencyCode;
	/**
	 * Is mbstring extension avalable?
	 *
	 * @var boolean
	 */
	static private $_isMbstringEnabled;
	/**
	 * Is iconv extension avalable?
	 *
	 * @var boolean
	 */
	static private $_isIconvEnabled;

	static private function _buildControlCharacters()
	{
		for ($i = 0; $i <= 31; ++$i) {
			if (($i != 9) && ($i != 10) && ($i != 13)) {
				$find = '_x' . sprintf('%04s', strtoupper(dechex($i))) . '_';
				$replace = chr($i);
				self::$_controlCharacters[$find] = $replace;
			}
		}
	}

	static private function _buildSYLKCharacters()
	{
		self::$_SYLKCharacters = array("\x1b 0" => chr(0), "\x1b 1" => chr(1), "\x1b 2" => chr(2), "\x1b 3" => chr(3), "\x1b 4" => chr(4), "\x1b 5" => chr(5), "\x1b 6" => chr(6), "\x1b 7" => chr(7), "\x1b 8" => chr(8), "\x1b 9" => chr(9), "\x1b :" => chr(10), "\x1b ;" => chr(11), "\x1b <" => chr(12), "\x1b :" => chr(13), "\x1b >" => chr(14), "\x1b ?" => chr(15), "\x1b!0" => chr(16), "\x1b!1" => chr(17), "\x1b!2" => chr(18), "\x1b!3" => chr(19), "\x1b!4" => chr(20), "\x1b!5" => chr(21), "\x1b!6" => chr(22), "\x1b!7" => chr(23), "\x1b!8" => chr(24), "\x1b!9" => chr(25), "\x1b!:" => chr(26), "\x1b!;" => chr(27), "\x1b!<" => chr(28), "\x1b!=" => chr(29), "\x1b!>" => chr(30), "\x1b!?" => chr(31), "\x1b'?" => chr(127), "\x1b(0" => '钪?, "\x1b(2" => '钬?, "\x1b(3" => '茠', "\x1b(4" => '钬?, "\x1b(5" => '钬?, "\x1b(6" => '钬?, "\x1b(7" => '钬?, "\x1b(8" => '藛', "\x1b(9" => '钬?, "\x1b(:" => '艩', "\x1b(;" => '钬?, "\x1bNj" => '艗', "\x1b(>" => '沤', "\x1b)1" => '钬?, "\x1b)2" => '钬?, "\x1b)3" => '钬?, "\x1b)4" => '钬?, "\x1b)5" => '钬?, "\x1b)6" => '钬?, "\x1b)7" => '钬?, "\x1b)8" => '藴', "\x1b)9" => '鈩?, "\x1b):" => '拧', "\x1b);" => '钬?, "\x1bNz" => '舱', "\x1b)>" => '啪', "\x1b)?" => '鸥', "\x1b*0" => '听', "\x1bN!" => '隆', "\x1bN\"" => '垄', "\x1bN#" => '拢', "\x1bN(" => '陇', "\x1bN%" => '楼', "\x1b*6" => '娄', "\x1bN'" => '搂', "\x1bNH " => '篓', "\x1bNS" => '漏', "\x1bNc" => '陋', "\x1bN+" => '芦', "\x1b*<" => '卢', "\x1b*=" => '颅', "\x1bNR" => '庐', "\x1b*?" => '炉', "\x1bN0" => '掳', "\x1bN1" => '卤', "\x1bN2" => '虏', "\x1bN3" => '鲁', "\x1bNB " => '麓', "\x1bN5" => '碌', "\x1bN6" => '露', "\x1bN7" => '路', "\x1b+8" => '赂', "\x1bNQ" => '鹿', "\x1bNk" => '潞', "\x1bN;" => '禄', "\x1bN<" => '录', "\x1bN=" => '陆', "\x1bN>" => '戮', "\x1bN?" => '驴', "\x1bNAA" => '脌', "\x1bNBA" => '脕', "\x1bNCA" => '脗', "\x1bNDA" => '脙', "\x1bNHA" => '胫', "\x1bNJA" => '脜', "\x1bNa" => '脝', "\x1bNKC" => '脟', "\x1bNAE" => '脠', "\x1bNBE" => '脡', "\x1bNCE" => '脢', "\x1bNHE" => '唇', "\x1bNAI" => '脤', "\x1bNBI" => '脥', "\x1bNCI" => '脦', "\x1bNHI" => '睃', "\x1bNb" => '脨', "\x1bNDN" => '修', "\x1bNAO" => '脪', "\x1bNBO" => '脱', "\x1bNCO" => '脭', "\x1bNDO" => '脮', "\x1bNHO" => '脰', "\x1b-7" => '脳', "\x1bNi" => '脴', "\x1bNAU" => '脵', "\x1bNBU" => '脷', "\x1bNCU" => '胀', "\x1bNHU" => '脺', "\x1b-=" => '脻', "\x1bNl" => '脼', "\x1bN{" => '脽', "\x1bNAa" => '脿', "\x1bNBa" => '谩', "\x1bNCa" => '芒', "\x1bNDa" => '茫', "\x1bNHa" => '盲', "\x1bNJa" => '氓', "\x1bNq" => '忙', "\x1bNKc" => '莽', "\x1bNAe" => '猫', "\x1bNBe" => '茅', "\x1bNCe" => '锚', "\x1bNHe" => '毛', "\x1bNAi" => '矛', "\x1bNBi" => '铆', "\x1bNCi" => '卯', "\x1bNHi" => '茂', "\x1bNs" => '冒', "\x1bNDn" => '帽', "\x1bNAo" => '貌', "\x1bNBo" => '贸', "\x1bNCo" => '么', "\x1bNDo" => '玫', "\x1bNHo" => '枚', "\x1b/7" => '梅', "\x1bNy" => '酶', "\x1bNAu" => '霉', "\x1bNBu" => '煤', "\x1bNCu" => '没', "\x1bNHu" => '眉', "\x1b/=" => '媒', "\x1bN|" => '镁', "\x1bNHy" => '每');
	}

	static public function getIsMbstringEnabled()
	{
		if (isset(self::$_isMbstringEnabled)) {
			return self::$_isMbstringEnabled;
		}

		self::$_isMbstringEnabled = (function_exists('mb_convert_encoding') ? true : false);
		return self::$_isMbstringEnabled;
	}

	static public function getIsIconvEnabled()
	{
		if (isset(self::$_isIconvEnabled)) {
			return self::$_isIconvEnabled;
		}

		if (!function_exists('iconv')) {
			self::$_isIconvEnabled = false;
			return false;
		}

		if (!@iconv('UTF-8', 'UTF-16LE', 'x')) {
			self::$_isIconvEnabled = false;
			return false;
		}

		if (!@iconv_substr('A', 0, 1, 'UTF-8')) {
			self::$_isIconvEnabled = false;
			return false;
		}

		if (defined('PHP_OS') && @stristr(PHP_OS, 'AIX') && defined('ICONV_IMPL') && (@strcasecmp(ICONV_IMPL, 'unknown') == 0) && defined('ICONV_VERSION') && (@strcasecmp(ICONV_VERSION, 'unknown') == 0)) {
			self::$_isIconvEnabled = false;
			return false;
		}

		self::$_isIconvEnabled = true;
		return true;
	}

	static public function buildCharacterSets()
	{
		if (empty(self::$_controlCharacters)) {
			self::_buildControlCharacters();
		}

		if (empty(self::$_SYLKCharacters)) {
			self::_buildSYLKCharacters();
		}
	}

	static public function ControlCharacterOOXML2PHP($value = '')
	{
		return str_replace(array_keys(self::$_controlCharacters), array_values(self::$_controlCharacters), $value);
	}

	static public function ControlCharacterPHP2OOXML($value = '')
	{
		return str_replace(array_values(self::$_controlCharacters), array_keys(self::$_controlCharacters), $value);
	}

	static public function SanitizeUTF8($value)
	{
		if (self::getIsIconvEnabled()) {
			$value = @iconv('UTF-8', 'UTF-8', $value);
			return $value;
		}

		if (self::getIsMbstringEnabled()) {
			$value = mb_convert_encoding($value, 'UTF-8', 'UTF-8');
			return $value;
		}

		return $value;
	}

	static public function IsUTF8($value = '')
	{
		return ($string === '') || (preg_match('/^./su', $string) === 1);
	}

	static public function FormatNumber($value)
	{
		if (is_float($value)) {
			return str_replace(',', '.', $value);
		}

		return (string) $value;
	}

	static public function UTF8toBIFF8UnicodeShort($value, $arrcRuns = array())
	{
		$ln = self::CountCharacters($value, 'UTF-8');

		if (empty($arrcRuns)) {
			$opt = (self::getIsIconvEnabled() || self::getIsMbstringEnabled() ? 1 : 0);
			$data = pack('CC', $ln, $opt);
			$data .= self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');
		}
		else {
			$data = pack('vC', $ln, 9);
			$data .= pack('v', count($arrcRuns));
			$data .= self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');

			foreach ($arrcRuns as $cRun) {
				$data .= pack('v', $cRun['strlen']);
				$data .= pack('v', $cRun['fontidx']);
			}
		}

		return $data;
	}

	static public function UTF8toBIFF8UnicodeLong($value)
	{
		$ln = self::CountCharacters($value, 'UTF-8');
		$opt = (self::getIsIconvEnabled() || self::getIsMbstringEnabled() ? 1 : 0);
		$chars = self::ConvertEncoding($value, 'UTF-16LE', 'UTF-8');
		$data = pack('vC', $ln, $opt) . $chars;
		return $data;
	}

	static public function ConvertEncoding($value, $to, $from)
	{
		if (self::getIsIconvEnabled()) {
			return iconv($from, $to, $value);
		}

		if (self::getIsMbstringEnabled()) {
			return mb_convert_encoding($value, $to, $from);
		}

		if ($from == 'UTF-16LE') {
			return self::utf16_decode($value, false);
		}
		else if ($from == 'UTF-16BE') {
			return self::utf16_decode($value);
		}

		return $value;
	}

	static public function utf16_decode($str, $bom_be = true)
	{
		if (strlen($str) < 2) {
			return $str;
		}

		$c0 = ord($str[0]);
		$c1 = ord($str[1]);
		if (($c0 == 254) && ($c1 == 255)) {
			$str = substr($str, 2);
		}
		else {
			if (($c0 == 255) && ($c1 == 254)) {
				$str = substr($str, 2);
				$bom_be = false;
			}
		}

		$len = strlen($str);
		$newstr = '';

		for ($i = 0; $i < $len; $i += 2) {
			if ($bom_be) {
				$val = ord($str[$i]) << 4;
				$val += ord($str[$i + 1]);
			}
			else {
				$val = ord($str[$i + 1]) << 4;
				$val += ord($str[$i]);
			}

			$newstr .= ($val == 552 ? "\n" : chr($val));
		}

		return $newstr;
	}

	static public function CountCharacters($value, $enc = 'UTF-8')
	{
		if (self::getIsMbstringEnabled()) {
			return mb_strlen($value, $enc);
		}

		if (self::getIsIconvEnabled()) {
			return iconv_strlen($value, $enc);
		}

		return strlen($value);
	}

	static public function Substring($pValue = '', $pStart = 0, $pLength = 0)
	{
		if (self::getIsMbstringEnabled()) {
			return mb_substr($pValue, $pStart, $pLength, 'UTF-8');
		}

		if (self::getIsIconvEnabled()) {
			return iconv_substr($pValue, $pStart, $pLength, 'UTF-8');
		}

		return substr($pValue, $pStart, $pLength);
	}

	static public function StrToUpper($pValue = '')
	{
		if (function_exists('mb_convert_case')) {
			return mb_convert_case($pValue, MB_CASE_UPPER, 'UTF-8');
		}

		return strtoupper($pValue);
	}

	static public function StrToLower($pValue = '')
	{
		if (function_exists('mb_convert_case')) {
			return mb_convert_case($pValue, MB_CASE_LOWER, 'UTF-8');
		}

		return strtolower($pValue);
	}

	static public function StrToTitle($pValue = '')
	{
		if (function_exists('mb_convert_case')) {
			return mb_convert_case($pValue, MB_CASE_TITLE, 'UTF-8');
		}

		return ucwords($pValue);
	}

	static public function convertToNumberIfFraction(&$operand)
	{
		if (preg_match('/^' . self::STRING_REGEXP_FRACTION . '$/i', $operand, $match)) {
			$sign = ($match[1] == '-' ? '-' : '+');
			$fractionFormula = '=' . $sign . $match[2] . $sign . $match[3];
			$operand = PHPExcel_Calculation::getInstance()->_calculateFormulaValue($fractionFormula);
			return true;
		}

		return false;
	}

	static public function getDecimalSeparator()
	{
		if (!isset(self::$_decimalSeparator)) {
			$localeconv = localeconv();
			self::$_decimalSeparator = ($localeconv['decimal_point'] != '' ? $localeconv['decimal_point'] : $localeconv['mon_decimal_point']);

			if (self::$_decimalSeparator == '') {
				self::$_decimalSeparator = '.';
			}
		}

		return self::$_decimalSeparator;
	}

	static public function setDecimalSeparator($pValue = '.')
	{
		self::$_decimalSeparator = $pValue;
	}

	static public function getThousandsSeparator()
	{
		if (!isset(self::$_thousandsSeparator)) {
			$localeconv = localeconv();
			self::$_thousandsSeparator = ($localeconv['thousands_sep'] != '' ? $localeconv['thousands_sep'] : $localeconv['mon_thousands_sep']);

			if (self::$_thousandsSeparator == '') {
				self::$_thousandsSeparator = ',';
			}
		}

		return self::$_thousandsSeparator;
	}

	static public function setThousandsSeparator($pValue = ',')
	{
		self::$_thousandsSeparator = $pValue;
	}

	static public function getCurrencyCode()
	{
		if (!isset(self::$_currencyCode)) {
			$localeconv = localeconv();
			self::$_currencyCode = ($localeconv['currency_symbol'] != '' ? $localeconv['currency_symbol'] : $localeconv['int_curr_symbol']);

			if (self::$_currencyCode == '') {
				self::$_currencyCode = '$';
			}
		}

		return self::$_currencyCode;
	}

	static public function setCurrencyCode($pValue = '$')
	{
		self::$_currencyCode = $pValue;
	}

	static public function SYLKtoUTF8($pValue = '')
	{
		if (strpos($pValue, "\x1b") === false) {
			return $pValue;
		}

		foreach (self::$_SYLKCharacters as $k => $v) {
			$pValue = str_replace($k, $v, $pValue);
		}

		return $pValue;
	}

	static public function testStringAsNumeric($value)
	{
		if (is_numeric($value)) {
			return $value;
		}

		$v = floatval($value);
		return is_numeric(substr($value, 0, strlen($v))) ? $v : $value;
	}
}


?>

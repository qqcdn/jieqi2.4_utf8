<?php

class PHPExcel_Writer_Excel5_Parser
{
	const REGEX_SHEET_TITLE_UNQUOTED = '[^\\*\\:\\/\\\\\\?\\[\\]\\+\\-\\% \\\'\\^\\&\\<\\>\\=\\,\\;\\#\\(\\)\\"\\{\\}]+';
	const REGEX_SHEET_TITLE_QUOTED = '(([^\\*\\:\\/\\\\\\?\\[\\]\\\'])+|(\\\'\\\')+)+';

	/**
	 * The index of the character we are currently looking at
	 * @var integer
	 */
	public $_current_char;
	/**
	 * The token we are working on.
	 * @var string
	 */
	public $_current_token;
	/**
	 * The formula to parse
	 * @var string
	 */
	public $_formula;
	/**
	 * The character ahead of the current char
	 * @var string
	 */
	public $_lookahead;
	/**
	 * The parse tree to be generated
	 * @var string
	 */
	public $_parse_tree;
	/**
	 * Array of external sheets
	 * @var array
	 */
	public $_ext_sheets;
	/**
	 * Array of sheet references in the form of REF structures
	 * @var array
	 */
	public $_references;

	public function __construct()
	{
		$this->_current_char = 0;
		$this->_current_token = '';
		$this->_formula = '';
		$this->_lookahead = '';
		$this->_parse_tree = '';
		$this->_initializeHashes();
		$this->_ext_sheets = array();
		$this->_references = array();
	}

	public function _initializeHashes()
	{
		$this->ptg = array('ptgExp' => 1, 'ptgTbl' => 2, 'ptgAdd' => 3, 'ptgSub' => 4, 'ptgMul' => 5, 'ptgDiv' => 6, 'ptgPower' => 7, 'ptgConcat' => 8, 'ptgLT' => 9, 'ptgLE' => 10, 'ptgEQ' => 11, 'ptgGE' => 12, 'ptgGT' => 13, 'ptgNE' => 14, 'ptgIsect' => 15, 'ptgUnion' => 16, 'ptgRange' => 17, 'ptgUplus' => 18, 'ptgUminus' => 19, 'ptgPercent' => 20, 'ptgParen' => 21, 'ptgMissArg' => 22, 'ptgStr' => 23, 'ptgAttr' => 25, 'ptgSheet' => 26, 'ptgEndSheet' => 27, 'ptgErr' => 28, 'ptgBool' => 29, 'ptgInt' => 30, 'ptgNum' => 31, 'ptgArray' => 32, 'ptgFunc' => 33, 'ptgFuncVar' => 34, 'ptgName' => 35, 'ptgRef' => 36, 'ptgArea' => 37, 'ptgMemArea' => 38, 'ptgMemErr' => 39, 'ptgMemNoMem' => 40, 'ptgMemFunc' => 41, 'ptgRefErr' => 42, 'ptgAreaErr' => 43, 'ptgRefN' => 44, 'ptgAreaN' => 45, 'ptgMemAreaN' => 46, 'ptgMemNoMemN' => 47, 'ptgNameX' => 57, 'ptgRef3d' => 58, 'ptgArea3d' => 59, 'ptgRefErr3d' => 60, 'ptgAreaErr3d' => 61, 'ptgArrayV' => 64, 'ptgFuncV' => 65, 'ptgFuncVarV' => 66, 'ptgNameV' => 67, 'ptgRefV' => 68, 'ptgAreaV' => 69, 'ptgMemAreaV' => 70, 'ptgMemErrV' => 71, 'ptgMemNoMemV' => 72, 'ptgMemFuncV' => 73, 'ptgRefErrV' => 74, 'ptgAreaErrV' => 75, 'ptgRefNV' => 76, 'ptgAreaNV' => 77, 'ptgMemAreaNV' => 78, 'ptgMemNoMemN' => 79, 'ptgFuncCEV' => 88, 'ptgNameXV' => 89, 'ptgRef3dV' => 90, 'ptgArea3dV' => 91, 'ptgRefErr3dV' => 92, 'ptgAreaErr3d' => 93, 'ptgArrayA' => 96, 'ptgFuncA' => 97, 'ptgFuncVarA' => 98, 'ptgNameA' => 99, 'ptgRefA' => 100, 'ptgAreaA' => 101, 'ptgMemAreaA' => 102, 'ptgMemErrA' => 103, 'ptgMemNoMemA' => 104, 'ptgMemFuncA' => 105, 'ptgRefErrA' => 106, 'ptgAreaErrA' => 107, 'ptgRefNA' => 108, 'ptgAreaNA' => 109, 'ptgMemAreaNA' => 110, 'ptgMemNoMemN' => 111, 'ptgFuncCEA' => 120, 'ptgNameXA' => 121, 'ptgRef3dA' => 122, 'ptgArea3dA' => 123, 'ptgRefErr3dA' => 124, 'ptgAreaErr3d' => 125);
		$this->_functions = array(
	'COUNT'        => array(0, -1, 0, 0),
	'IF'           => array(1, -1, 1, 0),
	'ISNA'         => array(2, 1, 1, 0),
	'ISERROR'      => array(3, 1, 1, 0),
	'SUM'          => array(4, -1, 0, 0),
	'AVERAGE'      => array(5, -1, 0, 0),
	'MIN'          => array(6, -1, 0, 0),
	'MAX'          => array(7, -1, 0, 0),
	'ROW'          => array(8, -1, 0, 0),
	'COLUMN'       => array(9, -1, 0, 0),
	'NA'           => array(10, 0, 0, 0),
	'NPV'          => array(11, -1, 1, 0),
	'STDEV'        => array(12, -1, 0, 0),
	'DOLLAR'       => array(13, -1, 1, 0),
	'FIXED'        => array(14, -1, 1, 0),
	'SIN'          => array(15, 1, 1, 0),
	'COS'          => array(16, 1, 1, 0),
	'TAN'          => array(17, 1, 1, 0),
	'ATAN'         => array(18, 1, 1, 0),
	'PI'           => array(19, 0, 1, 0),
	'SQRT'         => array(20, 1, 1, 0),
	'EXP'          => array(21, 1, 1, 0),
	'LN'           => array(22, 1, 1, 0),
	'LOG10'        => array(23, 1, 1, 0),
	'ABS'          => array(24, 1, 1, 0),
	'INT'          => array(25, 1, 1, 0),
	'SIGN'         => array(26, 1, 1, 0),
	'ROUND'        => array(27, 2, 1, 0),
	'LOOKUP'       => array(28, -1, 0, 0),
	'INDEX'        => array(29, -1, 0, 1),
	'REPT'         => array(30, 2, 1, 0),
	'MID'          => array(31, 3, 1, 0),
	'LEN'          => array(32, 1, 1, 0),
	'VALUE'        => array(33, 1, 1, 0),
	'TRUE'         => array(34, 0, 1, 0),
	'FALSE'        => array(35, 0, 1, 0),
	'AND'          => array(36, -1, 0, 0),
	'OR'           => array(37, -1, 0, 0),
	'NOT'          => array(38, 1, 1, 0),
	'MOD'          => array(39, 2, 1, 0),
	'DCOUNT'       => array(40, 3, 0, 0),
	'DSUM'         => array(41, 3, 0, 0),
	'DAVERAGE'     => array(42, 3, 0, 0),
	'DMIN'         => array(43, 3, 0, 0),
	'DMAX'         => array(44, 3, 0, 0),
	'DSTDEV'       => array(45, 3, 0, 0),
	'VAR'          => array(46, -1, 0, 0),
	'DVAR'         => array(47, 3, 0, 0),
	'TEXT'         => array(48, 2, 1, 0),
	'LINEST'       => array(49, -1, 0, 0),
	'TREND'        => array(50, -1, 0, 0),
	'LOGEST'       => array(51, -1, 0, 0),
	'GROWTH'       => array(52, -1, 0, 0),
	'PV'           => array(56, -1, 1, 0),
	'FV'           => array(57, -1, 1, 0),
	'NPER'         => array(58, -1, 1, 0),
	'PMT'          => array(59, -1, 1, 0),
	'RATE'         => array(60, -1, 1, 0),
	'MIRR'         => array(61, 3, 0, 0),
	'IRR'          => array(62, -1, 0, 0),
	'RAND'         => array(63, 0, 1, 1),
	'MATCH'        => array(64, -1, 0, 0),
	'DATE'         => array(65, 3, 1, 0),
	'TIME'         => array(66, 3, 1, 0),
	'DAY'          => array(67, 1, 1, 0),
	'MONTH'        => array(68, 1, 1, 0),
	'YEAR'         => array(69, 1, 1, 0),
	'WEEKDAY'      => array(70, -1, 1, 0),
	'HOUR'         => array(71, 1, 1, 0),
	'MINUTE'       => array(72, 1, 1, 0),
	'SECOND'       => array(73, 1, 1, 0),
	'NOW'          => array(74, 0, 1, 1),
	'AREAS'        => array(75, 1, 0, 1),
	'ROWS'         => array(76, 1, 0, 1),
	'COLUMNS'      => array(77, 1, 0, 1),
	'OFFSET'       => array(78, -1, 0, 1),
	'SEARCH'       => array(82, -1, 1, 0),
	'TRANSPOSE'    => array(83, 1, 1, 0),
	'TYPE'         => array(86, 1, 1, 0),
	'ATAN2'        => array(97, 2, 1, 0),
	'ASIN'         => array(98, 1, 1, 0),
	'ACOS'         => array(99, 1, 1, 0),
	'CHOOSE'       => array(100, -1, 1, 0),
	'HLOOKUP'      => array(101, -1, 0, 0),
	'VLOOKUP'      => array(102, -1, 0, 0),
	'ISREF'        => array(105, 1, 0, 0),
	'LOG'          => array(109, -1, 1, 0),
	'CHAR'         => array(111, 1, 1, 0),
	'LOWER'        => array(112, 1, 1, 0),
	'UPPER'        => array(113, 1, 1, 0),
	'PROPER'       => array(114, 1, 1, 0),
	'LEFT'         => array(115, -1, 1, 0),
	'RIGHT'        => array(116, -1, 1, 0),
	'EXACT'        => array(117, 2, 1, 0),
	'TRIM'         => array(118, 1, 1, 0),
	'REPLACE'      => array(119, 4, 1, 0),
	'SUBSTITUTE'   => array(120, -1, 1, 0),
	'CODE'         => array(121, 1, 1, 0),
	'FIND'         => array(124, -1, 1, 0),
	'CELL'         => array(125, -1, 0, 1),
	'ISERR'        => array(126, 1, 1, 0),
	'ISTEXT'       => array(127, 1, 1, 0),
	'ISNUMBER'     => array(128, 1, 1, 0),
	'ISBLANK'      => array(129, 1, 1, 0),
	'T'            => array(130, 1, 0, 0),
	'N'            => array(131, 1, 0, 0),
	'DATEVALUE'    => array(140, 1, 1, 0),
	'TIMEVALUE'    => array(141, 1, 1, 0),
	'SLN'          => array(142, 3, 1, 0),
	'SYD'          => array(143, 4, 1, 0),
	'DDB'          => array(144, -1, 1, 0),
	'INDIRECT'     => array(148, -1, 1, 1),
	'CALL'         => array(150, -1, 1, 0),
	'CLEAN'        => array(162, 1, 1, 0),
	'MDETERM'      => array(163, 1, 2, 0),
	'MINVERSE'     => array(164, 1, 2, 0),
	'MMULT'        => array(165, 2, 2, 0),
	'IPMT'         => array(167, -1, 1, 0),
	'PPMT'         => array(168, -1, 1, 0),
	'COUNTA'       => array(169, -1, 0, 0),
	'PRODUCT'      => array(183, -1, 0, 0),
	'FACT'         => array(184, 1, 1, 0),
	'DPRODUCT'     => array(189, 3, 0, 0),
	'ISNONTEXT'    => array(190, 1, 1, 0),
	'STDEVP'       => array(193, -1, 0, 0),
	'VARP'         => array(194, -1, 0, 0),
	'DSTDEVP'      => array(195, 3, 0, 0),
	'DVARP'        => array(196, 3, 0, 0),
	'TRUNC'        => array(197, -1, 1, 0),
	'ISLOGICAL'    => array(198, 1, 1, 0),
	'DCOUNTA'      => array(199, 3, 0, 0),
	'USDOLLAR'     => array(204, -1, 1, 0),
	'FINDB'        => array(205, -1, 1, 0),
	'SEARCHB'      => array(206, -1, 1, 0),
	'REPLACEB'     => array(207, 4, 1, 0),
	'LEFTB'        => array(208, -1, 1, 0),
	'RIGHTB'       => array(209, -1, 1, 0),
	'MIDB'         => array(210, 3, 1, 0),
	'LENB'         => array(211, 1, 1, 0),
	'ROUNDUP'      => array(212, 2, 1, 0),
	'ROUNDDOWN'    => array(213, 2, 1, 0),
	'ASC'          => array(214, 1, 1, 0),
	'DBCS'         => array(215, 1, 1, 0),
	'RANK'         => array(216, -1, 0, 0),
	'ADDRESS'      => array(219, -1, 1, 0),
	'DAYS360'      => array(220, -1, 1, 0),
	'TODAY'        => array(221, 0, 1, 1),
	'VDB'          => array(222, -1, 1, 0),
	'MEDIAN'       => array(227, -1, 0, 0),
	'SUMPRODUCT'   => array(228, -1, 2, 0),
	'SINH'         => array(229, 1, 1, 0),
	'COSH'         => array(230, 1, 1, 0),
	'TANH'         => array(231, 1, 1, 0),
	'ASINH'        => array(232, 1, 1, 0),
	'ACOSH'        => array(233, 1, 1, 0),
	'ATANH'        => array(234, 1, 1, 0),
	'DGET'         => array(235, 3, 0, 0),
	'INFO'         => array(244, 1, 1, 1),
	'DB'           => array(247, -1, 1, 0),
	'FREQUENCY'    => array(252, 2, 0, 0),
	'ERROR.TYPE'   => array(261, 1, 1, 0),
	'REGISTER.ID'  => array(267, -1, 1, 0),
	'AVEDEV'       => array(269, -1, 0, 0),
	'BETADIST'     => array(270, -1, 1, 0),
	'GAMMALN'      => array(271, 1, 1, 0),
	'BETAINV'      => array(272, -1, 1, 0),
	'BINOMDIST'    => array(273, 4, 1, 0),
	'CHIDIST'      => array(274, 2, 1, 0),
	'CHIINV'       => array(275, 2, 1, 0),
	'COMBIN'       => array(276, 2, 1, 0),
	'CONFIDENCE'   => array(277, 3, 1, 0),
	'CRITBINOM'    => array(278, 3, 1, 0),
	'EVEN'         => array(279, 1, 1, 0),
	'EXPONDIST'    => array(280, 3, 1, 0),
	'FDIST'        => array(281, 3, 1, 0),
	'FINV'         => array(282, 3, 1, 0),
	'FISHER'       => array(283, 1, 1, 0),
	'FISHERINV'    => array(284, 1, 1, 0),
	'FLOOR'        => array(285, 2, 1, 0),
	'GAMMADIST'    => array(286, 4, 1, 0),
	'GAMMAINV'     => array(287, 3, 1, 0),
	'CEILING'      => array(288, 2, 1, 0),
	'HYPGEOMDIST'  => array(289, 4, 1, 0),
	'LOGNORMDIST'  => array(290, 3, 1, 0),
	'LOGINV'       => array(291, 3, 1, 0),
	'NEGBINOMDIST' => array(292, 3, 1, 0),
	'NORMDIST'     => array(293, 4, 1, 0),
	'NORMSDIST'    => array(294, 1, 1, 0),
	'NORMINV'      => array(295, 3, 1, 0),
	'NORMSINV'     => array(296, 1, 1, 0),
	'STANDARDIZE'  => array(297, 3, 1, 0),
	'ODD'          => array(298, 1, 1, 0),
	'PERMUT'       => array(299, 2, 1, 0),
	'POISSON'      => array(300, 3, 1, 0),
	'TDIST'        => array(301, 3, 1, 0),
	'WEIBULL'      => array(302, 4, 1, 0),
	'SUMXMY2'      => array(303, 2, 2, 0),
	'SUMX2MY2'     => array(304, 2, 2, 0),
	'SUMX2PY2'     => array(305, 2, 2, 0),
	'CHITEST'      => array(306, 2, 2, 0),
	'CORREL'       => array(307, 2, 2, 0),
	'COVAR'        => array(308, 2, 2, 0),
	'FORECAST'     => array(309, 3, 2, 0),
	'FTEST'        => array(310, 2, 2, 0),
	'INTERCEPT'    => array(311, 2, 2, 0),
	'PEARSON'      => array(312, 2, 2, 0),
	'RSQ'          => array(313, 2, 2, 0),
	'STEYX'        => array(314, 2, 2, 0),
	'SLOPE'        => array(315, 2, 2, 0),
	'TTEST'        => array(316, 4, 2, 0),
	'PROB'         => array(317, -1, 2, 0),
	'DEVSQ'        => array(318, -1, 0, 0),
	'GEOMEAN'      => array(319, -1, 0, 0),
	'HARMEAN'      => array(320, -1, 0, 0),
	'SUMSQ'        => array(321, -1, 0, 0),
	'KURT'         => array(322, -1, 0, 0),
	'SKEW'         => array(323, -1, 0, 0),
	'ZTEST'        => array(324, -1, 0, 0),
	'LARGE'        => array(325, 2, 0, 0),
	'SMALL'        => array(326, 2, 0, 0),
	'QUARTILE'     => array(327, 2, 0, 0),
	'PERCENTILE'   => array(328, 2, 0, 0),
	'PERCENTRANK'  => array(329, -1, 0, 0),
	'MODE'         => array(330, -1, 2, 0),
	'TRIMMEAN'     => array(331, 2, 0, 0),
	'TINV'         => array(332, 2, 1, 0),
	'CONCATENATE'  => array(336, -1, 1, 0),
	'POWER'        => array(337, 2, 1, 0),
	'RADIANS'      => array(342, 1, 1, 0),
	'DEGREES'      => array(343, 1, 1, 0),
	'SUBTOTAL'     => array(344, -1, 0, 0),
	'SUMIF'        => array(345, -1, 0, 0),
	'COUNTIF'      => array(346, 2, 0, 0),
	'COUNTBLANK'   => array(347, 1, 0, 0),
	'ISPMT'        => array(350, 4, 1, 0),
	'DATEDIF'      => array(351, 3, 1, 0),
	'DATESTRING'   => array(352, 1, 1, 0),
	'NUMBERSTRING' => array(353, 2, 1, 0),
	'ROMAN'        => array(354, -1, 1, 0),
	'GETPIVOTDATA' => array(358, -1, 0, 0),
	'HYPERLINK'    => array(359, -1, 1, 0),
	'PHONETIC'     => array(360, 1, 0, 0),
	'AVERAGEA'     => array(361, -1, 0, 0),
	'MAXA'         => array(362, -1, 0, 0),
	'MINA'         => array(363, -1, 0, 0),
	'STDEVPA'      => array(364, -1, 0, 0),
	'VARPA'        => array(365, -1, 0, 0),
	'STDEVA'       => array(366, -1, 0, 0),
	'VARA'         => array(367, -1, 0, 0),
	'BAHTTEXT'     => array(368, 1, 0, 0)
	);
	}

	public function _convert($token)
	{
		if (preg_match('/"([^"]|""){0,255}"/', $token)) {
			return $this->_convertString($token);
		}
		else if (is_numeric($token)) {
			return $this->_convertNumber($token);
		}
		else if (preg_match('/^\\$?([A-Ia-i]?[A-Za-z])\\$?(\\d+)$/', $token)) {
			return $this->_convertRef2d($token);
		}
		else if (preg_match('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\\!\\$?[A-Ia-i]?[A-Za-z]\\$?(\\d+)$/u', $token)) {
			return $this->_convertRef3d($token);
		}
		else if (preg_match('/^\'' . self::REGEX_SHEET_TITLE_QUOTED . '(\\:' . self::REGEX_SHEET_TITLE_QUOTED . ')?\'\\!\\$?[A-Ia-i]?[A-Za-z]\\$?(\\d+)$/u', $token)) {
			return $this->_convertRef3d($token);
		}
		else if (preg_match('/^(\\$)?[A-Ia-i]?[A-Za-z](\\$)?(\\d+)\\:(\\$)?[A-Ia-i]?[A-Za-z](\\$)?(\\d+)$/', $token)) {
			return $this->_convertRange2d($token);
		}
		else if (preg_match('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\\!\\$?([A-Ia-i]?[A-Za-z])?\\$?(\\d+)\\:\\$?([A-Ia-i]?[A-Za-z])?\\$?(\\d+)$/u', $token)) {
			return $this->_convertRange3d($token);
		}
		else if (preg_match('/^\'' . self::REGEX_SHEET_TITLE_QUOTED . '(\\:' . self::REGEX_SHEET_TITLE_QUOTED . ')?\'\\!\\$?([A-Ia-i]?[A-Za-z])?\\$?(\\d+)\\:\\$?([A-Ia-i]?[A-Za-z])?\\$?(\\d+)$/u', $token)) {
			return $this->_convertRange3d($token);
		}
		else if (isset($this->ptg[$token])) {
			return pack('C', $this->ptg[$token]);
		}
		else {
			if (preg_match('/^#[A-Z0\\/]{3,5}[!?]{1}$/', $token) || ($token == '#N/A')) {
				return $this->_convertError($token);
			}
			else if ($token == 'arg') {
				return '';
			}
		}

		throw new PHPExcel_Writer_Exception('Unknown token ' . $token);
	}

	public function _convertNumber($num)
	{
		if (preg_match('/^\\d+$/', $num) && ($num <= 65535)) {
			return pack('Cv', $this->ptg['ptgInt'], $num);
		}
		else {
			if (PHPExcel_Writer_Excel5_BIFFwriter::getByteOrder()) {
				$num = strrev($num);
			}

			return pack('Cd', $this->ptg['ptgNum'], $num);
		}
	}

	public function _convertString($string)
	{
		$string = substr($string, 1, strlen($string) - 2);

		if (255 < strlen($string)) {
			throw new PHPExcel_Writer_Exception('String is too long');
		}

		return pack('C', $this->ptg['ptgStr']) . PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($string);
	}

	public function _convertFunction($token, $num_args)
	{
		$args = $this->_functions[$token][1];

		if (0 <= $args) {
			return pack('Cv', $this->ptg['ptgFuncV'], $this->_functions[$token][0]);
		}

		if ($args == -1) {
			return pack('CCv', $this->ptg['ptgFuncVarV'], $num_args, $this->_functions[$token][0]);
		}
	}

	public function _convertRange2d($range, $class = 0)
	{
		if (preg_match('/^(\\$)?([A-Ia-i]?[A-Za-z])(\\$)?(\\d+)\\:(\\$)?([A-Ia-i]?[A-Za-z])(\\$)?(\\d+)$/', $range)) {
			list($cell1, $cell2) = explode(':', $range);
		}
		else {
			throw new PHPExcel_Writer_Exception('Unknown range separator');
		}

		list($row1, $col1) = $this->_cellToPackedRowcol($cell1);
		list($row2, $col2) = $this->_cellToPackedRowcol($cell2);

		if ($class == 0) {
			$ptgArea = pack('C', $this->ptg['ptgArea']);
		}
		else if ($class == 1) {
			$ptgArea = pack('C', $this->ptg['ptgAreaV']);
		}
		else if ($class == 2) {
			$ptgArea = pack('C', $this->ptg['ptgAreaA']);
		}
		else {
			throw new PHPExcel_Writer_Exception('Unknown class ' . $class);
		}

		return $ptgArea . $row1 . $row2 . $col1 . $col2;
	}

	public function _convertRange3d($token)
	{
		list($ext_ref, $range) = explode('!', $token);
		$ext_ref = $this->_getRefIndex($ext_ref);
		list($cell1, $cell2) = explode(':', $range);

		if (preg_match('/^(\\$)?[A-Ia-i]?[A-Za-z](\\$)?(\\d+)$/', $cell1)) {
			list($row1, $col1) = $this->_cellToPackedRowcol($cell1);
			list($row2, $col2) = $this->_cellToPackedRowcol($cell2);
		}
		else {
			list($row1, $col1, $row2, $col2) = $this->_rangeToPackedRange($cell1 . ':' . $cell2);
		}

		$ptgArea = pack('C', $this->ptg['ptgArea3d']);
		return $ptgArea . $ext_ref . $row1 . $row2 . $col1 . $col2;
	}

	public function _convertRef2d($cell)
	{
		$cell_array = $this->_cellToPackedRowcol($cell);
		list($row, $col) = $cell_array;
		$ptgRef = pack('C', $this->ptg['ptgRefA']);
		return $ptgRef . $row . $col;
	}

	public function _convertRef3d($cell)
	{
		list($ext_ref, $cell) = explode('!', $cell);
		$ext_ref = $this->_getRefIndex($ext_ref);
		list($row, $col) = $this->_cellToPackedRowcol($cell);
		$ptgRef = pack('C', $this->ptg['ptgRef3dA']);
		return $ptgRef . $ext_ref . $row . $col;
	}

	public function _convertError($errorCode)
	{
		switch ($errorCode) {
		case '#NULL!':
			return pack('C', 0);
		case '#DIV/0!':
			return pack('C', 7);
		case '#VALUE!':
			return pack('C', 15);
		case '#REF!':
			return pack('C', 23);
		case '#NAME?':
			return pack('C', 29);
		case '#NUM!':
			return pack('C', 36);
		case '#N/A':
			return pack('C', 42);
		}

		return pack('C', 255);
	}

	public function _packExtRef($ext_ref)
	{
		$ext_ref = preg_replace('/^\'/', '', $ext_ref);
		$ext_ref = preg_replace('/\'$/', '', $ext_ref);

		if (preg_match('/:/', $ext_ref)) {
			list($sheet_name1, $sheet_name2) = explode(':', $ext_ref);
			$sheet1 = $this->_getSheetIndex($sheet_name1);

			if ($sheet1 == -1) {
				throw new PHPExcel_Writer_Exception('Unknown sheet name ' . $sheet_name1 . ' in formula');
			}

			$sheet2 = $this->_getSheetIndex($sheet_name2);

			if ($sheet2 == -1) {
				throw new PHPExcel_Writer_Exception('Unknown sheet name ' . $sheet_name2 . ' in formula');
			}

			if ($sheet2 < $sheet1) {
				list($sheet1, $sheet2) = array($sheet2, $sheet1);
			}
		}
		else {
			$sheet1 = $this->_getSheetIndex($ext_ref);

			if ($sheet1 == -1) {
				throw new PHPExcel_Writer_Exception('Unknown sheet name ' . $ext_ref . ' in formula');
			}

			$sheet2 = $sheet1;
		}

		$offset = -1 - $sheet1;
		return pack('vdvv', $offset, 0, $sheet1, $sheet2);
	}

	public function _getRefIndex($ext_ref)
	{
		$ext_ref = preg_replace('/^\'/', '', $ext_ref);
		$ext_ref = preg_replace('/\'$/', '', $ext_ref);
		$ext_ref = str_replace('\'\'', '\'', $ext_ref);

		if (preg_match('/:/', $ext_ref)) {
			list($sheet_name1, $sheet_name2) = explode(':', $ext_ref);
			$sheet1 = $this->_getSheetIndex($sheet_name1);

			if ($sheet1 == -1) {
				throw new PHPExcel_Writer_Exception('Unknown sheet name ' . $sheet_name1 . ' in formula');
			}

			$sheet2 = $this->_getSheetIndex($sheet_name2);

			if ($sheet2 == -1) {
				throw new PHPExcel_Writer_Exception('Unknown sheet name ' . $sheet_name2 . ' in formula');
			}

			if ($sheet2 < $sheet1) {
				list($sheet1, $sheet2) = array($sheet2, $sheet1);
			}
		}
		else {
			$sheet1 = $this->_getSheetIndex($ext_ref);

			if ($sheet1 == -1) {
				throw new PHPExcel_Writer_Exception('Unknown sheet name ' . $ext_ref . ' in formula');
			}

			$sheet2 = $sheet1;
		}

		$supbook_index = 0;
		$ref = pack('vvv', $supbook_index, $sheet1, $sheet2);
		$total_references = count($this->_references);
		$index = -1;

		for ($i = 0; $i < $total_references; ++$i) {
			if ($ref == $this->_references[$i]) {
				$index = $i;
				break;
			}
		}

		if ($index == -1) {
			$this->_references[$total_references] = $ref;
			$index = $total_references;
		}

		return pack('v', $index);
	}

	public function _getSheetIndex($sheet_name)
	{
		if (!isset($this->_ext_sheets[$sheet_name])) {
			return -1;
		}
		else {
			return $this->_ext_sheets[$sheet_name];
		}
	}

	public function setExtSheet($name, $index)
	{
		$this->_ext_sheets[$name] = $index;
	}

	public function _cellToPackedRowcol($cell)
	{
		$cell = strtoupper($cell);
		list($row, $col, $row_rel, $col_rel) = $this->_cellToRowcol($cell);

		if (256 <= $col) {
			throw new PHPExcel_Writer_Exception('Column in: ' . $cell . ' greater than 255');
		}

		if (65536 <= $row) {
			throw new PHPExcel_Writer_Exception('Row in: ' . $cell . ' greater than 65536 ');
		}

		$col |= $col_rel << 14;
		$col |= $row_rel << 15;
		$col = pack('v', $col);
		$row = pack('v', $row);
		return array($row, $col);
	}

	public function _rangeToPackedRange($range)
	{
		preg_match('/(\\$)?(\\d+)\\:(\\$)?(\\d+)/', $range, $match);
		$row1_rel = (empty($match[1]) ? 1 : 0);
		$row1 = $match[2];
		$row2_rel = (empty($match[3]) ? 1 : 0);
		$row2 = $match[4];
		--$row1;
		--$row2;
		$col1 = 0;
		$col2 = 65535;
		if ((65536 <= $row1) || (65536 <= $row2)) {
			throw new PHPExcel_Writer_Exception('Row in: ' . $range . ' greater than 65536 ');
		}

		$col1 |= $row1_rel << 15;
		$col2 |= $row2_rel << 15;
		$col1 = pack('v', $col1);
		$col2 = pack('v', $col2);
		$row1 = pack('v', $row1);
		$row2 = pack('v', $row2);
		return array($row1, $col1, $row2, $col2);
	}

	public function _cellToRowcol($cell)
	{
		preg_match('/(\\$)?([A-I]?[A-Z])(\\$)?(\\d+)/', $cell, $match);
		$col_rel = (empty($match[1]) ? 1 : 0);
		$col_ref = $match[2];
		$row_rel = (empty($match[3]) ? 1 : 0);
		$row = $match[4];
		$expn = strlen($col_ref) - 1;
		$col = 0;
		$col_ref_length = strlen($col_ref);

		for ($i = 0; $i < $col_ref_length; ++$i) {
			$col += (ord($col_ref[$i]) - 64) * pow(26, $expn);
			--$expn;
		}

		--$row;
		--$col;
		return array($row, $col, $row_rel, $col_rel);
	}

	public function _advance()
	{
		$i = $this->_current_char;
		$formula_length = strlen($this->_formula);

		if ($i < $formula_length) {
			while ($this->_formula[$i] == ' ') {
				++$i;
			}

			if ($i < ($formula_length - 1)) {
				$this->_lookahead = $this->_formula[$i + 1];
			}

			$token = '';
		}

		while ($i < $formula_length) {
			$token .= $this->_formula[$i];

			if ($i < ($formula_length - 1)) {
				$this->_lookahead = $this->_formula[$i + 1];
			}
			else {
				$this->_lookahead = '';
			}

			if ($this->_match($token) != '') {
				$this->_current_char = $i + 1;
				$this->_current_token = $token;
				return 1;
			}

			if ($i < ($formula_length - 2)) {
				$this->_lookahead = $this->_formula[$i + 2];
			}
			else {
				$this->_lookahead = '';
			}

			++$i;
		}
	}

	public function _match($token)
	{
		switch ($token) {
		case '+':
		case '-':
		case '*':
		case '/':
		case '(':
		case ')':
		case ',':
		case ';':
		case '>=':
		case '<=':
		case '=':
		case '<>':
		case '^':
		case '&':
		case '%':
			return $token;
			break;

		case '>':
			if ($this->_lookahead == '=') {
				break;
			}

			return $token;
			break;

		case '<':
			if (($this->_lookahead == '=') || ($this->_lookahead == '>')) {
				break;
			}

			return $token;
			break;

		default:
			if (preg_match('/^\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/', $token) && !preg_match('/[0-9]/', $this->_lookahead) && ($this->_lookahead != ':') && ($this->_lookahead != '.') && ($this->_lookahead != '!')) {
				return $token;
			}
			else {
				if (preg_match('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\\!\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/u', $token) && !preg_match('/[0-9]/', $this->_lookahead) && ($this->_lookahead != ':') && ($this->_lookahead != '.')) {
					return $token;
				}
				else {
					if (preg_match('/^\'' . self::REGEX_SHEET_TITLE_QUOTED . '(\\:' . self::REGEX_SHEET_TITLE_QUOTED . ')?\'\\!\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/u', $token) && !preg_match('/[0-9]/', $this->_lookahead) && ($this->_lookahead != ':') && ($this->_lookahead != '.')) {
						return $token;
					}
					else {
						if (preg_match('/^(\\$)?[A-Ia-i]?[A-Za-z](\\$)?[0-9]+:(\\$)?[A-Ia-i]?[A-Za-z](\\$)?[0-9]+$/', $token) && !preg_match('/[0-9]/', $this->_lookahead)) {
							return $token;
						}
						else {
							if (preg_match('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\\!\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+:\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+$/u', $token) && !preg_match('/[0-9]/', $this->_lookahead)) {
								return $token;
							}
							else {
								if (preg_match('/^\'' . self::REGEX_SHEET_TITLE_QUOTED . '(\\:' . self::REGEX_SHEET_TITLE_QUOTED . ')?\'\\!\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+:\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+$/u', $token) && !preg_match('/[0-9]/', $this->_lookahead)) {
									return $token;
								}
								else {
									if (is_numeric($token) && (!is_numeric($token . $this->_lookahead) || ($this->_lookahead == '')) && ($this->_lookahead != '!') && ($this->_lookahead != ':')) {
										return $token;
									}
									else {
										if (preg_match('/"([^"]|""){0,255}"/', $token) && ($this->_lookahead != '"') && ((substr_count($token, '"') % 2) == 0)) {
											return $token;
										}
										else {
											if (preg_match('/^#[A-Z0\\/]{3,5}[!?]{1}$/', $token) || ($token == '#N/A')) {
												return $token;
											}
											else {
												if (preg_match("/^[A-Z0-9\xc0-\xdc\\.]+\$/i", $token) && ($this->_lookahead == '(')) {
													return $token;
												}
												else if (substr($token, -1) == ')') {
													return $token;
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}

			return '';
		}
	}

	public function parse($formula)
	{
		$this->_current_char = 0;
		$this->_formula = $formula;
		$this->_lookahead = isset($formula[1]) ? $formula[1] : '';
		$this->_advance();
		$this->_parse_tree = $this->_condition();
		return true;
	}

	public function _condition()
	{
		$result = $this->_expression();

		if ($this->_current_token == '<') {
			$this->_advance();
			$result2 = $this->_expression();
			$result = $this->_createTree('ptgLT', $result, $result2);
		}
		else if ($this->_current_token == '>') {
			$this->_advance();
			$result2 = $this->_expression();
			$result = $this->_createTree('ptgGT', $result, $result2);
		}
		else if ($this->_current_token == '<=') {
			$this->_advance();
			$result2 = $this->_expression();
			$result = $this->_createTree('ptgLE', $result, $result2);
		}
		else if ($this->_current_token == '>=') {
			$this->_advance();
			$result2 = $this->_expression();
			$result = $this->_createTree('ptgGE', $result, $result2);
		}
		else if ($this->_current_token == '=') {
			$this->_advance();
			$result2 = $this->_expression();
			$result = $this->_createTree('ptgEQ', $result, $result2);
		}
		else if ($this->_current_token == '<>') {
			$this->_advance();
			$result2 = $this->_expression();
			$result = $this->_createTree('ptgNE', $result, $result2);
		}
		else if ($this->_current_token == '&') {
			$this->_advance();
			$result2 = $this->_expression();
			$result = $this->_createTree('ptgConcat', $result, $result2);
		}

		return $result;
	}

	public function _expression()
	{
		if (preg_match('/"([^"]|""){0,255}"/', $this->_current_token)) {
			$tmp = str_replace('""', '"', $this->_current_token);
			if (($tmp == '"') || ($tmp == '')) {
				$tmp = '""';
			}

			$result = $this->_createTree($tmp, '', '');
			$this->_advance();
			return $result;
		}
		else {
			if (preg_match('/^#[A-Z0\\/]{3,5}[!?]{1}$/', $this->_current_token) || ($this->_current_token == '#N/A')) {
				$result = $this->_createTree($this->_current_token, 'ptgErr', '');
				$this->_advance();
				return $result;
			}
			else if ($this->_current_token == '-') {
				$this->_advance();
				$result2 = $this->_expression();
				$result = $this->_createTree('ptgUminus', $result2, '');
				return $result;
			}
			else if ($this->_current_token == '+') {
				$this->_advance();
				$result2 = $this->_expression();
				$result = $this->_createTree('ptgUplus', $result2, '');
				return $result;
			}
		}

		$result = $this->_term();

		while ($this->_current_token == '^') {
			if ($this->_current_token == '+') {
				$this->_advance();
				$result2 = $this->_term();
				$result = $this->_createTree('ptgAdd', $result, $result2);
			}
			else if ($this->_current_token == '-') {
				$this->_advance();
				$result2 = $this->_term();
				$result = $this->_createTree('ptgSub', $result, $result2);
			}
			else {
				$this->_advance();
				$result2 = $this->_term();
				$result = $this->_createTree('ptgPower', $result, $result2);
			}
		}

		return $result;
	}

	public function _parenthesizedExpression()
	{
		$result = $this->_createTree('ptgParen', $this->_expression(), '');
		return $result;
	}

	public function _term()
	{
		$result = $this->_fact();

		while ($this->_current_token == '/') {
			if ($this->_current_token == '*') {
				$this->_advance();
				$result2 = $this->_fact();
				$result = $this->_createTree('ptgMul', $result, $result2);
			}
			else {
				$this->_advance();
				$result2 = $this->_fact();
				$result = $this->_createTree('ptgDiv', $result, $result2);
			}
		}

		return $result;
	}

	public function _fact()
	{
		if ($this->_current_token == '(') {
			$this->_advance();
			$result = $this->_parenthesizedExpression();

			if ($this->_current_token != ')') {
				throw new PHPExcel_Writer_Exception('\')\' token expected.');
			}

			$this->_advance();
			return $result;
		}

		if (preg_match('/^\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/', $this->_current_token)) {
			$result = $this->_createTree($this->_current_token, '', '');
			$this->_advance();
			return $result;
		}
		else if (preg_match('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\\!\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/u', $this->_current_token)) {
			$result = $this->_createTree($this->_current_token, '', '');
			$this->_advance();
			return $result;
		}
		else if (preg_match('/^\'' . self::REGEX_SHEET_TITLE_QUOTED . '(\\:' . self::REGEX_SHEET_TITLE_QUOTED . ')?\'\\!\\$?[A-Ia-i]?[A-Za-z]\\$?[0-9]+$/u', $this->_current_token)) {
			$result = $this->_createTree($this->_current_token, '', '');
			$this->_advance();
			return $result;
		}
		else {
			if (preg_match('/^(\\$)?[A-Ia-i]?[A-Za-z](\\$)?[0-9]+:(\\$)?[A-Ia-i]?[A-Za-z](\\$)?[0-9]+$/', $this->_current_token) || preg_match('/^(\\$)?[A-Ia-i]?[A-Za-z](\\$)?[0-9]+\\.\\.(\\$)?[A-Ia-i]?[A-Za-z](\\$)?[0-9]+$/', $this->_current_token)) {
				$result = $this->_createTree($this->_current_token, '', '');
				$this->_advance();
				return $result;
			}
			else if (preg_match('/^' . self::REGEX_SHEET_TITLE_UNQUOTED . '(\\:' . self::REGEX_SHEET_TITLE_UNQUOTED . ')?\\!\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+:\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+$/u', $this->_current_token)) {
				$result = $this->_createTree($this->_current_token, '', '');
				$this->_advance();
				return $result;
			}
			else if (preg_match('/^\'' . self::REGEX_SHEET_TITLE_QUOTED . '(\\:' . self::REGEX_SHEET_TITLE_QUOTED . ')?\'\\!\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+:\\$?([A-Ia-i]?[A-Za-z])?\\$?[0-9]+$/u', $this->_current_token)) {
				$result = $this->_createTree($this->_current_token, '', '');
				$this->_advance();
				return $result;
			}
			else if (is_numeric($this->_current_token)) {
				if ($this->_lookahead == '%') {
					$result = $this->_createTree('ptgPercent', $this->_current_token, '');
				}
				else {
					$result = $this->_createTree($this->_current_token, '', '');
				}

				$this->_advance();
				return $result;
			}
			else if (preg_match("/^[A-Z0-9\xc0-\xdc\\.]+\$/i", $this->_current_token)) {
				$result = $this->_func();
				return $result;
			}
		}

		throw new PHPExcel_Writer_Exception('Syntax error: ' . $this->_current_token . ', lookahead: ' . $this->_lookahead . ', current char: ' . $this->_current_char);
	}

	public function _func()
	{
		$num_args = 0;
		$function = strtoupper($this->_current_token);
		$result = '';
		$this->_advance();
		$this->_advance();

		while ($this->_current_token != ')') {
			if (0 < $num_args) {
				if (($this->_current_token == ',') || ($this->_current_token == ';')) {
					$this->_advance();
				}
				else {
					throw new PHPExcel_Writer_Exception('Syntax error: comma expected in ' . 'function ' . $function . ', arg #' . $num_args);
				}

				$result2 = $this->_condition();
				$result = $this->_createTree('arg', $result, $result2);
			}
			else {
				$result2 = $this->_condition();
				$result = $this->_createTree('arg', '', $result2);
			}

			++$num_args;
		}

		if (!isset($this->_functions[$function])) {
			throw new PHPExcel_Writer_Exception('Function ' . $function . '() doesn\'t exist');
		}

		$args = $this->_functions[$function][1];
		if ((0 <= $args) && ($args != $num_args)) {
			throw new PHPExcel_Writer_Exception('Incorrect number of arguments in function ' . $function . '() ');
		}

		$result = $this->_createTree($function, $result, $num_args);
		$this->_advance();
		return $result;
	}

	public function _createTree($value, $left, $right)
	{
		return array('value' => $value, 'left' => $left, 'right' => $right);
	}

	public function toReversePolish($tree = array())
	{
		$polish = '';

		if (empty($tree)) {
			$tree = $this->_parse_tree;
		}

		if (is_array($tree['left'])) {
			$converted_tree = $this->toReversePolish($tree['left']);
			$polish .= $converted_tree;
		}
		else if ($tree['left'] != '') {
			$converted_tree = $this->_convert($tree['left']);
			$polish .= $converted_tree;
		}

		if (is_array($tree['right'])) {
			$converted_tree = $this->toReversePolish($tree['right']);
			$polish .= $converted_tree;
		}
		else if ($tree['right'] != '') {
			$converted_tree = $this->_convert($tree['right']);
			$polish .= $converted_tree;
		}

		if (preg_match("/^[A-Z0-9\xc0-\xdc\\.]+\$/", $tree['value']) && !preg_match('/^([A-Ia-i]?[A-Za-z])(\\d+)$/', $tree['value']) && !preg_match('/^[A-Ia-i]?[A-Za-z](\\d+)\\.\\.[A-Ia-i]?[A-Za-z](\\d+)$/', $tree['value']) && !is_numeric($tree['value']) && !isset($this->ptg[$tree['value']])) {
			if ($tree['left'] != '') {
				$left_tree = $this->toReversePolish($tree['left']);
			}
			else {
				$left_tree = '';
			}

			return $left_tree . $this->_convertFunction($tree['value'], $tree['right']);
		}
		else {
			$converted_tree = $this->_convert($tree['value']);
		}

		$polish .= $converted_tree;
		return $polish;
	}
}


?>

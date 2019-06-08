<?php

class PHPExcel_Writer_Excel5_Font
{
	/**
	 * Color index
	 *
	 * @var int
	 */
	private $_colorIndex;
	/**
	 * Font
	 *
	 * @var PHPExcel_Style_Font
	 */
	private $_font;
	/**
	 * Map of BIFF2-BIFF8 codes for underline styles
	 * @static	array of int
	 *
	 */
	static private $_mapUnderline = array(PHPExcel_Style_Font::UNDERLINE_NONE => 0, PHPExcel_Style_Font::UNDERLINE_SINGLE => 1, PHPExcel_Style_Font::UNDERLINE_DOUBLE => 2, PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING => 33, PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING => 34);

	public function __construct(PHPExcel_Style_Font $font = NULL)
	{
		$this->_colorIndex = 32767;
		$this->_font = $font;
	}

	public function setColorIndex($colorIndex)
	{
		$this->_colorIndex = $colorIndex;
	}

	public function writeFont()
	{
		$font_outline = 0;
		$font_shadow = 0;
		$icv = $this->_colorIndex;

		if ($this->_font->getSuperScript()) {
			$sss = 1;
		}
		else if ($this->_font->getSubScript()) {
			$sss = 2;
		}
		else {
			$sss = 0;
		}

		$bFamily = 0;
		$bCharSet = PHPExcel_Shared_Font::getCharsetFromFontName($this->_font->getName());
		$record = 49;
		$reserved = 0;
		$grbit = 0;

		if ($this->_font->getItalic()) {
			$grbit |= 2;
		}

		if ($this->_font->getStrikethrough()) {
			$grbit |= 8;
		}

		if ($font_outline) {
			$grbit |= 16;
		}

		if ($font_shadow) {
			$grbit |= 32;
		}

		$data = pack('vvvvvCCCC', $this->_font->getSize() * 20, $grbit, $icv, self::_mapBold($this->_font->getBold()), $sss, self::_mapUnderline($this->_font->getUnderline()), $bFamily, $bCharSet, $reserved);
		$data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($this->_font->getName());
		$length = strlen($data);
		$header = pack('vv', $record, $length);
		return $header . $data;
	}

	static private function _mapBold($bold)
	{
		if ($bold) {
			return 700;
		}

		return 400;
	}

	static private function _mapUnderline($underline)
	{
		if (isset(self::$_mapUnderline[$underline])) {
			return self::$_mapUnderline[$underline];
		}

		return 0;
	}
}


?>

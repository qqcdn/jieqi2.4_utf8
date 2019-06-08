<?php

class PHPExcel_Writer_Excel2007_Theme extends PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Map of Major fonts to write
	 * @static	array of string
	 *
	 */
	static private $_majorFonts = array('Jpan' => '锛汲 锛般偞銈枫儍銈?, 'Hang' => '毵戬潃 瓿犽敃', 'Hans' => '瀹嬩綋', 'Hant' => '鏂扮窗鏄庨珨', 'Arab' => 'Times New Roman', 'Hebr' => 'Times New Roman', 'Thai' => 'Tahoma', 'Ethi' => 'Nyala', 'Beng' => 'Vrinda', 'Gujr' => 'Shruti', 'Khmr' => 'MoolBoran', 'Knda' => 'Tunga', 'Guru' => 'Raavi', 'Cans' => 'Euphemia', 'Cher' => 'Plantagenet Cherokee', 'Yiii' => 'Microsoft Yi Baiti', 'Tibt' => 'Microsoft Himalaya', 'Thaa' => 'MV Boli', 'Deva' => 'Mangal', 'Telu' => 'Gautami', 'Taml' => 'Latha', 'Syrc' => 'Estrangelo Edessa', 'Orya' => 'Kalinga', 'Mlym' => 'Kartika', 'Laoo' => 'DokChampa', 'Sinh' => 'Iskoola Pota', 'Mong' => 'Mongolian Baiti', 'Viet' => 'Times New Roman', 'Uigh' => 'Microsoft Uighur', 'Geor' => 'Sylfaen');
	/**
	 * Map of Minor fonts to write
	 * @static	array of string
	 *
	 */
	static private $_minorFonts = array('Jpan' => '锛汲 锛般偞銈枫儍銈?, 'Hang' => '毵戬潃 瓿犽敃', 'Hans' => '瀹嬩綋', 'Hant' => '鏂扮窗鏄庨珨', 'Arab' => 'Arial', 'Hebr' => 'Arial', 'Thai' => 'Tahoma', 'Ethi' => 'Nyala', 'Beng' => 'Vrinda', 'Gujr' => 'Shruti', 'Khmr' => 'DaunPenh', 'Knda' => 'Tunga', 'Guru' => 'Raavi', 'Cans' => 'Euphemia', 'Cher' => 'Plantagenet Cherokee', 'Yiii' => 'Microsoft Yi Baiti', 'Tibt' => 'Microsoft Himalaya', 'Thaa' => 'MV Boli', 'Deva' => 'Mangal', 'Telu' => 'Gautami', 'Taml' => 'Latha', 'Syrc' => 'Estrangelo Edessa', 'Orya' => 'Kalinga', 'Mlym' => 'Kartika', 'Laoo' => 'DokChampa', 'Sinh' => 'Iskoola Pota', 'Mong' => 'Mongolian Baiti', 'Viet' => 'Arial', 'Uigh' => 'Microsoft Uighur', 'Geor' => 'Sylfaen');
	/**
	 * Map of core colours
	 * @static	array of string
	 *
	 */
	static private $_colourScheme = array('dk2' => '1F497D', 'lt2' => 'EEECE1', 'accent1' => '4F81BD', 'accent2' => 'C0504D', 'accent3' => '9BBB59', 'accent4' => '8064A2', 'accent5' => '4BACC6', 'accent6' => 'F79646', 'hlink' => '0000FF', 'folHlink' => '800080');

	public function writeTheme(PHPExcel $pPHPExcel = NULL)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$objWriter->startElement('a:theme');
		$objWriter->writeAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
		$objWriter->writeAttribute('name', 'Office Theme');
		$objWriter->startElement('a:themeElements');
		$objWriter->startElement('a:clrScheme');
		$objWriter->writeAttribute('name', 'Office');
		$objWriter->startElement('a:dk1');
		$objWriter->startElement('a:sysClr');
		$objWriter->writeAttribute('val', 'windowText');
		$objWriter->writeAttribute('lastClr', '000000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:lt1');
		$objWriter->startElement('a:sysClr');
		$objWriter->writeAttribute('val', 'window');
		$objWriter->writeAttribute('lastClr', 'FFFFFF');
		$objWriter->endElement();
		$objWriter->endElement();
		$this->_writeColourScheme($objWriter);
		$objWriter->endElement();
		$objWriter->startElement('a:fontScheme');
		$objWriter->writeAttribute('name', 'Office');
		$objWriter->startElement('a:majorFont');
		$this->_writeFonts($objWriter, 'Cambria', self::$_majorFonts);
		$objWriter->endElement();
		$objWriter->startElement('a:minorFont');
		$this->_writeFonts($objWriter, 'Calibri', self::$_minorFonts);
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:fmtScheme');
		$objWriter->writeAttribute('name', 'Office');
		$objWriter->startElement('a:fillStyleLst');
		$objWriter->startElement('a:solidFill');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gradFill');
		$objWriter->writeAttribute('rotWithShape', '1');
		$objWriter->startElement('a:gsLst');
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '0');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:tint');
		$objWriter->writeAttribute('val', '50000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '300000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '35000');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:tint');
		$objWriter->writeAttribute('val', '37000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '300000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '100000');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:tint');
		$objWriter->writeAttribute('val', '15000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '350000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:lin');
		$objWriter->writeAttribute('ang', '16200000');
		$objWriter->writeAttribute('scaled', '1');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gradFill');
		$objWriter->writeAttribute('rotWithShape', '1');
		$objWriter->startElement('a:gsLst');
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '0');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:shade');
		$objWriter->writeAttribute('val', '51000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '130000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '80000');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:shade');
		$objWriter->writeAttribute('val', '93000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '130000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '100000');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:shade');
		$objWriter->writeAttribute('val', '94000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '135000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:lin');
		$objWriter->writeAttribute('ang', '16200000');
		$objWriter->writeAttribute('scaled', '0');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:lnStyleLst');
		$objWriter->startElement('a:ln');
		$objWriter->writeAttribute('w', '9525');
		$objWriter->writeAttribute('cap', 'flat');
		$objWriter->writeAttribute('cmpd', 'sng');
		$objWriter->writeAttribute('algn', 'ctr');
		$objWriter->startElement('a:solidFill');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:shade');
		$objWriter->writeAttribute('val', '95000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '105000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:prstDash');
		$objWriter->writeAttribute('val', 'solid');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:ln');
		$objWriter->writeAttribute('w', '25400');
		$objWriter->writeAttribute('cap', 'flat');
		$objWriter->writeAttribute('cmpd', 'sng');
		$objWriter->writeAttribute('algn', 'ctr');
		$objWriter->startElement('a:solidFill');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:prstDash');
		$objWriter->writeAttribute('val', 'solid');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:ln');
		$objWriter->writeAttribute('w', '38100');
		$objWriter->writeAttribute('cap', 'flat');
		$objWriter->writeAttribute('cmpd', 'sng');
		$objWriter->writeAttribute('algn', 'ctr');
		$objWriter->startElement('a:solidFill');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:prstDash');
		$objWriter->writeAttribute('val', 'solid');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:effectStyleLst');
		$objWriter->startElement('a:effectStyle');
		$objWriter->startElement('a:effectLst');
		$objWriter->startElement('a:outerShdw');
		$objWriter->writeAttribute('blurRad', '40000');
		$objWriter->writeAttribute('dist', '20000');
		$objWriter->writeAttribute('dir', '5400000');
		$objWriter->writeAttribute('rotWithShape', '0');
		$objWriter->startElement('a:srgbClr');
		$objWriter->writeAttribute('val', '000000');
		$objWriter->startElement('a:alpha');
		$objWriter->writeAttribute('val', '38000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:effectStyle');
		$objWriter->startElement('a:effectLst');
		$objWriter->startElement('a:outerShdw');
		$objWriter->writeAttribute('blurRad', '40000');
		$objWriter->writeAttribute('dist', '23000');
		$objWriter->writeAttribute('dir', '5400000');
		$objWriter->writeAttribute('rotWithShape', '0');
		$objWriter->startElement('a:srgbClr');
		$objWriter->writeAttribute('val', '000000');
		$objWriter->startElement('a:alpha');
		$objWriter->writeAttribute('val', '35000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:effectStyle');
		$objWriter->startElement('a:effectLst');
		$objWriter->startElement('a:outerShdw');
		$objWriter->writeAttribute('blurRad', '40000');
		$objWriter->writeAttribute('dist', '23000');
		$objWriter->writeAttribute('dir', '5400000');
		$objWriter->writeAttribute('rotWithShape', '0');
		$objWriter->startElement('a:srgbClr');
		$objWriter->writeAttribute('val', '000000');
		$objWriter->startElement('a:alpha');
		$objWriter->writeAttribute('val', '35000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:scene3d');
		$objWriter->startElement('a:camera');
		$objWriter->writeAttribute('prst', 'orthographicFront');
		$objWriter->startElement('a:rot');
		$objWriter->writeAttribute('lat', '0');
		$objWriter->writeAttribute('lon', '0');
		$objWriter->writeAttribute('rev', '0');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:lightRig');
		$objWriter->writeAttribute('rig', 'threePt');
		$objWriter->writeAttribute('dir', 't');
		$objWriter->startElement('a:rot');
		$objWriter->writeAttribute('lat', '0');
		$objWriter->writeAttribute('lon', '0');
		$objWriter->writeAttribute('rev', '1200000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:sp3d');
		$objWriter->startElement('a:bevelT');
		$objWriter->writeAttribute('w', '63500');
		$objWriter->writeAttribute('h', '25400');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:bgFillStyleLst');
		$objWriter->startElement('a:solidFill');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gradFill');
		$objWriter->writeAttribute('rotWithShape', '1');
		$objWriter->startElement('a:gsLst');
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '0');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:tint');
		$objWriter->writeAttribute('val', '40000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '350000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '40000');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:tint');
		$objWriter->writeAttribute('val', '45000');
		$objWriter->endElement();
		$objWriter->startElement('a:shade');
		$objWriter->writeAttribute('val', '99000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '350000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '100000');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:shade');
		$objWriter->writeAttribute('val', '20000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '255000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:path');
		$objWriter->writeAttribute('path', 'circle');
		$objWriter->startElement('a:fillToRect');
		$objWriter->writeAttribute('l', '50000');
		$objWriter->writeAttribute('t', '-80000');
		$objWriter->writeAttribute('r', '50000');
		$objWriter->writeAttribute('b', '180000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gradFill');
		$objWriter->writeAttribute('rotWithShape', '1');
		$objWriter->startElement('a:gsLst');
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '0');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:tint');
		$objWriter->writeAttribute('val', '80000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '300000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:gs');
		$objWriter->writeAttribute('pos', '100000');
		$objWriter->startElement('a:schemeClr');
		$objWriter->writeAttribute('val', 'phClr');
		$objWriter->startElement('a:shade');
		$objWriter->writeAttribute('val', '30000');
		$objWriter->endElement();
		$objWriter->startElement('a:satMod');
		$objWriter->writeAttribute('val', '200000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:path');
		$objWriter->writeAttribute('path', 'circle');
		$objWriter->startElement('a:fillToRect');
		$objWriter->writeAttribute('l', '50000');
		$objWriter->writeAttribute('t', '50000');
		$objWriter->writeAttribute('r', '50000');
		$objWriter->writeAttribute('b', '50000');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->writeElement('a:objectDefaults', NULL);
		$objWriter->writeElement('a:extraClrSchemeLst', NULL);
		$objWriter->endElement();
		return $objWriter->getData();
	}

	private function _writeFonts($objWriter, $latinFont, $fontSet)
	{
		$objWriter->startElement('a:latin');
		$objWriter->writeAttribute('typeface', $latinFont);
		$objWriter->endElement();
		$objWriter->startElement('a:ea');
		$objWriter->writeAttribute('typeface', '');
		$objWriter->endElement();
		$objWriter->startElement('a:cs');
		$objWriter->writeAttribute('typeface', '');
		$objWriter->endElement();

		foreach ($fontSet as $fontScript => $typeface) {
			$objWriter->startElement('a:font');
			$objWriter->writeAttribute('script', $fontScript);
			$objWriter->writeAttribute('typeface', $typeface);
			$objWriter->endElement();
		}
	}

	private function _writeColourScheme($objWriter)
	{
		foreach (self::$_colourScheme as $colourName => $colourValue) {
			$objWriter->startElement('a:' . $colourName);
			$objWriter->startElement('a:srgbClr');
			$objWriter->writeAttribute('val', $colourValue);
			$objWriter->endElement();
			$objWriter->endElement();
		}
	}
}

?>

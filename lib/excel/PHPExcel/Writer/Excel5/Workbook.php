<?php

class PHPExcel_Writer_Excel5_Workbook extends PHPExcel_Writer_Excel5_BIFFwriter
{
	/**
	 * Formula parser
	 *
	 * @var PHPExcel_Writer_Excel5_Parser
	 */
	private $_parser;
	/**
	 * The BIFF file size for the workbook.
	 * @var integer
	 * @see _calcSheetOffsets()
	 */
	public $_biffsize;
	/**
	 * XF Writers
	 * @var PHPExcel_Writer_Excel5_Xf[]
	 */
	private $_xfWriters = array();
	/**
	 * Array containing the colour palette
	 * @var array
	 */
	public $_palette;
	/**
	 * The codepage indicates the text encoding used for strings
	 * @var integer
	 */
	public $_codepage;
	/**
	 * The country code used for localization
	 * @var integer
	 */
	public $_country_code;
	/**
	 * Workbook
	 * @var PHPExcel
	 */
	private $_phpExcel;
	/**
	 * Fonts writers
	 *
	 * @var PHPExcel_Writer_Excel5_Font[]
	 */
	private $_fontWriters = array();
	/**
	 * Added fonts. Maps from font's hash => index in workbook
	 *
	 * @var array
	 */
	private $_addedFonts = array();
	/**
	 * Shared number formats
	 *
	 * @var array
	 */
	private $_numberFormats = array();
	/**
	 * Added number formats. Maps from numberFormat's hash => index in workbook
	 *
	 * @var array
	 */
	private $_addedNumberFormats = array();
	/**
	 * Sizes of the binary worksheet streams
	 *
	 * @var array
	 */
	private $_worksheetSizes = array();
	/**
	 * Offsets of the binary worksheet streams relative to the start of the global workbook stream
	 *
	 * @var array
	 */
	private $_worksheetOffsets = array();
	/**
	 * Total number of shared strings in workbook
	 *
	 * @var int
	 */
	private $_str_total;
	/**
	 * Number of unique shared strings in workbook
	 *
	 * @var int
	 */
	private $_str_unique;
	/**
	 * Array of unique shared strings in workbook
	 *
	 * @var array
	 */
	private $_str_table;
	/**
	 * Color cache
	 */
	private $_colors;
	/**
	 * Escher object corresponding to MSODRAWINGGROUP
	 *
	 * @var PHPExcel_Shared_Escher
	 */
	private $_escher;

	public function __construct(PHPExcel $phpExcel = NULL, &$str_total, &$str_unique, &$str_table, &$colors, $parser)
	{
		parent::__construct();
		$this->_parser = $parser;
		$this->_biffsize = 0;
		$this->_palette = array();
		$this->_country_code = -1;
		$this->_str_total = &$str_total;
		$this->_str_unique = &$str_unique;
		$this->_str_table = &$str_table;
		$this->_colors = &$colors;
		$this->_setPaletteXl97();
		$this->_phpExcel = $phpExcel;
		$this->_codepage = 1200;
		$countSheets = $phpExcel->getSheetCount();

		for ($i = 0; $i < $countSheets; ++$i) {
			$phpSheet = $phpExcel->getSheet($i);
			$this->_parser->setExtSheet($phpSheet->getTitle(), $i);
			$supbook_index = 0;
			$ref = pack('vvv', $supbook_index, $i, $i);
			$this->_parser->_references[] = $ref;

			if ($phpSheet->isTabColorSet()) {
				$this->_addColor($phpSheet->getTabColor()->getRGB());
			}
		}
	}

	public function addXfWriter($style, $isStyleXf = false)
	{
		$xfWriter = new PHPExcel_Writer_Excel5_Xf($style);
		$xfWriter->setIsStyleXf($isStyleXf);
		$fontIndex = $this->_addFont($style->getFont());
		$xfWriter->setFontIndex($fontIndex);
		$xfWriter->setFgColor($this->_addColor($style->getFill()->getStartColor()->getRGB()));
		$xfWriter->setBgColor($this->_addColor($style->getFill()->getEndColor()->getRGB()));
		$xfWriter->setBottomColor($this->_addColor($style->getBorders()->getBottom()->getColor()->getRGB()));
		$xfWriter->setTopColor($this->_addColor($style->getBorders()->getTop()->getColor()->getRGB()));
		$xfWriter->setRightColor($this->_addColor($style->getBorders()->getRight()->getColor()->getRGB()));
		$xfWriter->setLeftColor($this->_addColor($style->getBorders()->getLeft()->getColor()->getRGB()));
		$xfWriter->setDiagColor($this->_addColor($style->getBorders()->getDiagonal()->getColor()->getRGB()));

		if ($style->getNumberFormat()->getBuiltInFormatCode() === false) {
			$numberFormatHashCode = $style->getNumberFormat()->getHashCode();

			if (isset($this->_addedNumberFormats[$numberFormatHashCode])) {
				$numberFormatIndex = $this->_addedNumberFormats[$numberFormatHashCode];
			}
			else {
				$numberFormatIndex = 164 + count($this->_numberFormats);
				$this->_numberFormats[$numberFormatIndex] = $style->getNumberFormat();
				$this->_addedNumberFormats[$numberFormatHashCode] = $numberFormatIndex;
			}
		}
		else {
			$numberFormatIndex = (int) $style->getNumberFormat()->getBuiltInFormatCode();
		}

		$xfWriter->setNumberFormatIndex($numberFormatIndex);
		$this->_xfWriters[] = $xfWriter;
		$xfIndex = count($this->_xfWriters) - 1;
		return $xfIndex;
	}

	public function _addFont(PHPExcel_Style_Font $font)
	{
		$fontHashCode = $font->getHashCode();

		if (isset($this->_addedFonts[$fontHashCode])) {
			$fontIndex = $this->_addedFonts[$fontHashCode];
		}
		else {
			$countFonts = count($this->_fontWriters);
			$fontIndex = ($countFonts < 4 ? $countFonts : $countFonts + 1);
			$fontWriter = new PHPExcel_Writer_Excel5_Font($font);
			$fontWriter->setColorIndex($this->_addColor($font->getColor()->getRGB()));
			$this->_fontWriters[] = $fontWriter;
			$this->_addedFonts[$fontHashCode] = $fontIndex;
		}

		return $fontIndex;
	}

	private function _addColor($rgb)
	{
		if (!isset($this->_colors[$rgb])) {
			if (count($this->_colors) < 57) {
				$colorIndex = 8 + count($this->_colors);
				$this->_palette[$colorIndex] = array(hexdec(substr($rgb, 0, 2)), hexdec(substr($rgb, 2, 2)), hexdec(substr($rgb, 4)), 0);
				$this->_colors[$rgb] = $colorIndex;
			}
			else {
				$colorIndex = 0;
			}
		}
		else {
			$colorIndex = $this->_colors[$rgb];
		}

		return $colorIndex;
	}

	public function _setPaletteXl97()
	{
		$this->_palette = array(
	8  => array(0, 0, 0, 0),
	9  => array(255, 255, 255, 0),
	10 => array(255, 0, 0, 0),
	11 => array(0, 255, 0, 0),
	12 => array(0, 0, 255, 0),
	13 => array(255, 255, 0, 0),
	14 => array(255, 0, 255, 0),
	15 => array(0, 255, 255, 0),
	16 => array(128, 0, 0, 0),
	17 => array(0, 128, 0, 0),
	18 => array(0, 0, 128, 0),
	19 => array(128, 128, 0, 0),
	20 => array(128, 0, 128, 0),
	21 => array(0, 128, 128, 0),
	22 => array(192, 192, 192, 0),
	23 => array(128, 128, 128, 0),
	24 => array(153, 153, 255, 0),
	25 => array(153, 51, 102, 0),
	26 => array(255, 255, 204, 0),
	27 => array(204, 255, 255, 0),
	28 => array(102, 0, 102, 0),
	29 => array(255, 128, 128, 0),
	30 => array(0, 102, 204, 0),
	31 => array(204, 204, 255, 0),
	32 => array(0, 0, 128, 0),
	33 => array(255, 0, 255, 0),
	34 => array(255, 255, 0, 0),
	35 => array(0, 255, 255, 0),
	36 => array(128, 0, 128, 0),
	37 => array(128, 0, 0, 0),
	38 => array(0, 128, 128, 0),
	39 => array(0, 0, 255, 0),
	40 => array(0, 204, 255, 0),
	41 => array(204, 255, 255, 0),
	42 => array(204, 255, 204, 0),
	43 => array(255, 255, 153, 0),
	44 => array(153, 204, 255, 0),
	45 => array(255, 153, 204, 0),
	46 => array(204, 153, 255, 0),
	47 => array(255, 204, 153, 0),
	48 => array(51, 102, 255, 0),
	49 => array(51, 204, 204, 0),
	50 => array(153, 204, 0, 0),
	51 => array(255, 204, 0, 0),
	52 => array(255, 153, 0, 0),
	53 => array(255, 102, 0, 0),
	54 => array(102, 102, 153, 0),
	55 => array(150, 150, 150, 0),
	56 => array(0, 51, 102, 0),
	57 => array(51, 153, 102, 0),
	58 => array(0, 51, 0, 0),
	59 => array(51, 51, 0, 0),
	60 => array(153, 51, 0, 0),
	61 => array(153, 51, 102, 0),
	62 => array(51, 51, 153, 0),
	63 => array(51, 51, 51, 0)
	);
	}

	public function writeWorkbook($pWorksheetSizes = NULL)
	{
		$this->_worksheetSizes = $pWorksheetSizes;
		$total_worksheets = $this->_phpExcel->getSheetCount();
		$this->_storeBof(5);
		$this->_writeCodepage();
		$this->_writeWindow1();
		$this->_writeDatemode();
		$this->_writeAllFonts();
		$this->_writeAllNumFormats();
		$this->_writeAllXfs();
		$this->_writeAllStyles();
		$this->_writePalette();
		$part3 = '';

		if ($this->_country_code != -1) {
			$part3 .= $this->_writeCountry();
		}

		$part3 .= $this->_writeRecalcId();
		$part3 .= $this->_writeSupbookInternal();
		$part3 .= $this->_writeExternsheetBiff8();
		$part3 .= $this->_writeAllDefinedNamesBiff8();
		$part3 .= $this->_writeMsoDrawingGroup();
		$part3 .= $this->_writeSharedStringsTable();
		$part3 .= $this->writeEof();
		$this->_calcSheetOffsets();

		for ($i = 0; $i < $total_worksheets; ++$i) {
			$this->_writeBoundsheet($this->_phpExcel->getSheet($i), $this->_worksheetOffsets[$i]);
		}

		$this->_data .= $part3;
		return $this->_data;
	}

	public function _calcSheetOffsets()
	{
		$boundsheet_length = 10;
		$offset = $this->_datasize;
		$total_worksheets = count($this->_phpExcel->getAllSheets());

		foreach ($this->_phpExcel->getWorksheetIterator() as $sheet) {
			$offset += $boundsheet_length + strlen(PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($sheet->getTitle()));
		}

		for ($i = 0; $i < $total_worksheets; ++$i) {
			$this->_worksheetOffsets[$i] = $offset;
			$offset += $this->_worksheetSizes[$i];
		}

		$this->_biffsize = $offset;
	}

	private function _writeAllFonts()
	{
		foreach ($this->_fontWriters as $fontWriter) {
			$this->_append($fontWriter->writeFont());
		}
	}

	private function _writeAllNumFormats()
	{
		foreach ($this->_numberFormats as $numberFormatIndex => $numberFormat) {
			$this->_writeNumFormat($numberFormat->getFormatCode(), $numberFormatIndex);
		}
	}

	private function _writeAllXfs()
	{
		foreach ($this->_xfWriters as $xfWriter) {
			$this->_append($xfWriter->writeXf());
		}
	}

	private function _writeAllStyles()
	{
		$this->_writeStyle();
	}

	private function _writeExterns()
	{
		$countSheets = $this->_phpExcel->getSheetCount();
		$this->_writeExterncount($countSheets);

		for ($i = 0; $i < $countSheets; ++$i) {
			$this->_writeExternsheet($this->_phpExcel->getSheet($i)->getTitle());
		}
	}

	private function _writeNames()
	{
		$total_worksheets = $this->_phpExcel->getSheetCount();

		for ($i = 0; $i < $total_worksheets; ++$i) {
			$sheetSetup = $this->_phpExcel->getSheet($i)->getPageSetup();

			if ($sheetSetup->isPrintAreaSet()) {
				$printArea = PHPExcel_Cell::splitRange($sheetSetup->getPrintArea());
				$printArea = $printArea[0];
				$printArea[0] = PHPExcel_Cell::coordinateFromString($printArea[0]);
				$printArea[1] = PHPExcel_Cell::coordinateFromString($printArea[1]);
				$print_rowmin = $printArea[0][1] - 1;
				$print_rowmax = $printArea[1][1] - 1;
				$print_colmin = PHPExcel_Cell::columnIndexFromString($printArea[0][0]) - 1;
				$print_colmax = PHPExcel_Cell::columnIndexFromString($printArea[1][0]) - 1;
				$this->_writeNameShort($i, 6, $print_rowmin, $print_rowmax, $print_colmin, $print_colmax);
			}
		}

		for ($i = 0; $i < $total_worksheets; ++$i) {
			$sheetSetup = $this->_phpExcel->getSheet($i)->getPageSetup();
			if ($sheetSetup->isColumnsToRepeatAtLeftSet() && $sheetSetup->isRowsToRepeatAtTopSet()) {
				$repeat = $sheetSetup->getColumnsToRepeatAtLeft();
				$colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
				$colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
				$repeat = $sheetSetup->getRowsToRepeatAtTop();
				$rowmin = $repeat[0] - 1;
				$rowmax = $repeat[1] - 1;
				$this->_writeNameLong($i, 7, $rowmin, $rowmax, $colmin, $colmax);
			}
			else {
				if ($sheetSetup->isColumnsToRepeatAtLeftSet() || $sheetSetup->isRowsToRepeatAtTopSet()) {
					if ($sheetSetup->isColumnsToRepeatAtLeftSet()) {
						$repeat = $sheetSetup->getColumnsToRepeatAtLeft();
						$colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
						$colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
					}
					else {
						$colmin = 0;
						$colmax = 255;
					}

					if ($sheetSetup->isRowsToRepeatAtTopSet()) {
						$repeat = $sheetSetup->getRowsToRepeatAtTop();
						$rowmin = $repeat[0] - 1;
						$rowmax = $repeat[1] - 1;
					}
					else {
						$rowmin = 0;
						$rowmax = 65535;
					}

					$this->_writeNameShort($i, 7, $rowmin, $rowmax, $colmin, $colmax);
				}
			}
		}
	}

	private function _writeAllDefinedNamesBiff8()
	{
		$chunk = '';

		if (0 < count($this->_phpExcel->getNamedRanges())) {
			$namedRanges = $this->_phpExcel->getNamedRanges();

			foreach ($namedRanges as $namedRange) {
				$range = PHPExcel_Cell::splitRange($namedRange->getRange());

				for ($i = 0; $i < count($range); $i++) {
					$range[$i][0] = '\'' . str_replace('\'', '\'\'', $namedRange->getWorksheet()->getTitle()) . '\'!' . PHPExcel_Cell::absoluteCoordinate($range[$i][0]);

					if (isset($range[$i][1])) {
						$range[$i][1] = PHPExcel_Cell::absoluteCoordinate($range[$i][1]);
					}
				}

				$range = PHPExcel_Cell::buildRange($range);

				try {
					$error = $this->_parser->parse($range);
					$formulaData = $this->_parser->toReversePolish();
					if (isset($formulaData[0]) && (($formulaData[0] == 'z') || ($formulaData[0] == 'Z'))) {
						$formulaData = ':' . substr($formulaData, 1);
					}

					if ($namedRange->getLocalOnly()) {
						$scope = $this->_phpExcel->getIndex($namedRange->getScope()) + 1;
					}
					else {
						$scope = 0;
					}

					$chunk .= $this->writeData($this->_writeDefinedNameBiff8($namedRange->getName(), $formulaData, $scope, false));
				}
				catch (PHPExcel_Exception $e) {
				}
			}
		}

		$total_worksheets = $this->_phpExcel->getSheetCount();

		for ($i = 0; $i < $total_worksheets; ++$i) {
			$sheetSetup = $this->_phpExcel->getSheet($i)->getPageSetup();
			if ($sheetSetup->isColumnsToRepeatAtLeftSet() && $sheetSetup->isRowsToRepeatAtTopSet()) {
				$repeat = $sheetSetup->getColumnsToRepeatAtLeft();
				$colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
				$colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
				$repeat = $sheetSetup->getRowsToRepeatAtTop();
				$rowmin = $repeat[0] - 1;
				$rowmax = $repeat[1] - 1;
				$formulaData = pack('Cv', 41, 23);
				$formulaData .= pack('Cvvvvv', 59, $i, 0, 65535, $colmin, $colmax);
				$formulaData .= pack('Cvvvvv', 59, $i, $rowmin, $rowmax, 0, 255);
				$formulaData .= pack('C', 16);
				$chunk .= $this->writeData($this->_writeDefinedNameBiff8(pack('C', 7), $formulaData, $i + 1, true));
			}
			else {
				if ($sheetSetup->isColumnsToRepeatAtLeftSet() || $sheetSetup->isRowsToRepeatAtTopSet()) {
					if ($sheetSetup->isColumnsToRepeatAtLeftSet()) {
						$repeat = $sheetSetup->getColumnsToRepeatAtLeft();
						$colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
						$colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
					}
					else {
						$colmin = 0;
						$colmax = 255;
					}

					if ($sheetSetup->isRowsToRepeatAtTopSet()) {
						$repeat = $sheetSetup->getRowsToRepeatAtTop();
						$rowmin = $repeat[0] - 1;
						$rowmax = $repeat[1] - 1;
					}
					else {
						$rowmin = 0;
						$rowmax = 65535;
					}

					$formulaData = pack('Cvvvvv', 59, $i, $rowmin, $rowmax, $colmin, $colmax);
					$chunk .= $this->writeData($this->_writeDefinedNameBiff8(pack('C', 7), $formulaData, $i + 1, true));
				}
			}
		}

		for ($i = 0; $i < $total_worksheets; ++$i) {
			$sheetSetup = $this->_phpExcel->getSheet($i)->getPageSetup();

			if ($sheetSetup->isPrintAreaSet()) {
				$printArea = PHPExcel_Cell::splitRange($sheetSetup->getPrintArea());
				$countPrintArea = count($printArea);
				$formulaData = '';

				for ($j = 0; $j < $countPrintArea; ++$j) {
					$printAreaRect = $printArea[$j];
					$printAreaRect[0] = PHPExcel_Cell::coordinateFromString($printAreaRect[0]);
					$printAreaRect[1] = PHPExcel_Cell::coordinateFromString($printAreaRect[1]);
					$print_rowmin = $printAreaRect[0][1] - 1;
					$print_rowmax = $printAreaRect[1][1] - 1;
					$print_colmin = PHPExcel_Cell::columnIndexFromString($printAreaRect[0][0]) - 1;
					$print_colmax = PHPExcel_Cell::columnIndexFromString($printAreaRect[1][0]) - 1;
					$formulaData .= pack('Cvvvvv', 59, $i, $print_rowmin, $print_rowmax, $print_colmin, $print_colmax);

					if (0 < $j) {
						$formulaData .= pack('C', 16);
					}
				}

				$chunk .= $this->writeData($this->_writeDefinedNameBiff8(pack('C', 6), $formulaData, $i + 1, true));
			}
		}

		for ($i = 0; $i < $total_worksheets; ++$i) {
			$sheetAutoFilter = $this->_phpExcel->getSheet($i)->getAutoFilter();
			$autoFilterRange = $sheetAutoFilter->getRange();

			if (!empty($autoFilterRange)) {
				$rangeBounds = PHPExcel_Cell::rangeBoundaries($autoFilterRange);
				$name = pack('C', 13);
				$chunk .= $this->writeData($this->_writeShortNameBiff8($name, $i + 1, $rangeBounds, true));
			}
		}

		return $chunk;
	}

	private function _writeDefinedNameBiff8($name, $formulaData, $sheetIndex = 0, $isBuiltIn = false)
	{
		$record = 24;
		$options = ($isBuiltIn ? 32 : 0);
		$nlen = PHPExcel_Shared_String::CountCharacters($name);
		$name = substr(PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($name), 2);
		$sz = strlen($formulaData);
		$data = pack('vCCvvvCCCC', $options, 0, $nlen, $sz, 0, $sheetIndex, 0, 0, 0, 0) . $name . $formulaData;
		$length = strlen($data);
		$header = pack('vv', $record, $length);
		return $header . $data;
	}

	private function _writeShortNameBiff8($name, $sheetIndex = 0, $rangeBounds, $isHidden = false)
	{
		$record = 24;
		$options = ($isHidden ? 33 : 0);
		$extra = pack('Cvvvvv', 59, $sheetIndex - 1, $rangeBounds[0][1] - 1, $rangeBounds[1][1] - 1, $rangeBounds[0][0] - 1, $rangeBounds[1][0] - 1);
		$sz = strlen($extra);
		$data = pack('vCCvvvCCCCC', $options, 0, 1, $sz, 0, $sheetIndex, 0, 0, 0, 0, 0) . $name . $extra;
		$length = strlen($data);
		$header = pack('vv', $record, $length);
		return $header . $data;
	}

	private function _writeCodepage()
	{
		$record = 66;
		$length = 2;
		$cv = $this->_codepage;
		$header = pack('vv', $record, $length);
		$data = pack('v', $cv);
		$this->_append($header . $data);
	}

	private function _writeWindow1()
	{
		$record = 61;
		$length = 18;
		$xWn = 0;
		$yWn = 0;
		$dxWn = 9660;
		$dyWn = 5490;
		$grbit = 56;
		$ctabsel = 1;
		$wTabRatio = 600;
		$itabFirst = 0;
		$itabCur = $this->_phpExcel->getActiveSheetIndex();
		$header = pack('vv', $record, $length);
		$data = pack('vvvvvvvvv', $xWn, $yWn, $dxWn, $dyWn, $grbit, $itabCur, $itabFirst, $ctabsel, $wTabRatio);
		$this->_append($header . $data);
	}

	private function _writeBoundsheet($sheet, $offset)
	{
		$sheetname = $sheet->getTitle();
		$record = 133;

		switch ($sheet->getSheetState()) {
		case PHPExcel_Worksheet::SHEETSTATE_VISIBLE:
			$ss = 0;
			break;

		case PHPExcel_Worksheet::SHEETSTATE_HIDDEN:
			$ss = 1;
			break;

		case PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN:
			$ss = 2;
			break;

		default:
			$ss = 0;
			break;
		}

		$st = 0;
		$grbit = 0;
		$data = pack('VCC', $offset, $ss, $st);
		$data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($sheetname);
		$length = strlen($data);
		$header = pack('vv', $record, $length);
		$this->_append($header . $data);
	}

	private function _writeSupbookInternal()
	{
		$record = 430;
		$length = 4;
		$header = pack('vv', $record, $length);
		$data = pack('vv', $this->_phpExcel->getSheetCount(), 1025);
		return $this->writeData($header . $data);
	}

	private function _writeExternsheetBiff8()
	{
		$total_references = count($this->_parser->_references);
		$record = 23;
		$length = 2 + (6 * $total_references);
		$supbook_index = 0;
		$header = pack('vv', $record, $length);
		$data = pack('v', $total_references);

		for ($i = 0; $i < $total_references; ++$i) {
			$data .= $this->_parser->_references[$i];
		}

		return $this->writeData($header . $data);
	}

	private function _writeStyle()
	{
		$record = 659;
		$length = 4;
		$ixfe = 32768;
		$BuiltIn = 0;
		$iLevel = 255;
		$header = pack('vv', $record, $length);
		$data = pack('vCC', $ixfe, $BuiltIn, $iLevel);
		$this->_append($header . $data);
	}

	private function _writeNumFormat($format, $ifmt)
	{
		$record = 1054;
		$numberFormatString = PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($format);
		$length = 2 + strlen($numberFormatString);
		$header = pack('vv', $record, $length);
		$data = pack('v', $ifmt) . $numberFormatString;
		$this->_append($header . $data);
	}

	private function _writeDatemode()
	{
		$record = 34;
		$length = 2;
		$f1904 = (PHPExcel_Shared_Date::getExcelCalendar() == PHPExcel_Shared_Date::CALENDAR_MAC_1904 ? 1 : 0);
		$header = pack('vv', $record, $length);
		$data = pack('v', $f1904);
		$this->_append($header . $data);
	}

	private function _writeExterncount($cxals)
	{
		$record = 22;
		$length = 2;
		$header = pack('vv', $record, $length);
		$data = pack('v', $cxals);
		$this->_append($header . $data);
	}

	private function _writeExternsheet($sheetname)
	{
		$record = 23;
		$length = 2 + strlen($sheetname);
		$cch = strlen($sheetname);
		$rgch = 3;
		$header = pack('vv', $record, $length);
		$data = pack('CC', $cch, $rgch);
		$this->_append($header . $data . $sheetname);
	}

	private function _writeNameShort($index, $type, $rowmin, $rowmax, $colmin, $colmax)
	{
		$record = 24;
		$length = 36;
		$grbit = 32;
		$chKey = 0;
		$cch = 1;
		$cce = 21;
		$ixals = $index + 1;
		$itab = $ixals;
		$cchCustMenu = 0;
		$cchDescription = 0;
		$cchHelptopic = 0;
		$cchStatustext = 0;
		$rgch = $type;
		$unknown03 = 59;
		$unknown04 = 65535 - $index;
		$unknown05 = 0;
		$unknown06 = 0;
		$unknown07 = 4231;
		$unknown08 = 32773;
		$header = pack('vv', $record, $length);
		$data = pack('v', $grbit);
		$data .= pack('C', $chKey);
		$data .= pack('C', $cch);
		$data .= pack('v', $cce);
		$data .= pack('v', $ixals);
		$data .= pack('v', $itab);
		$data .= pack('C', $cchCustMenu);
		$data .= pack('C', $cchDescription);
		$data .= pack('C', $cchHelptopic);
		$data .= pack('C', $cchStatustext);
		$data .= pack('C', $rgch);
		$data .= pack('C', $unknown03);
		$data .= pack('v', $unknown04);
		$data .= pack('v', $unknown05);
		$data .= pack('v', $unknown06);
		$data .= pack('v', $unknown07);
		$data .= pack('v', $unknown08);
		$data .= pack('v', $index);
		$data .= pack('v', $index);
		$data .= pack('v', $rowmin);
		$data .= pack('v', $rowmax);
		$data .= pack('C', $colmin);
		$data .= pack('C', $colmax);
		$this->_append($header . $data);
	}

	private function _writeNameLong($index, $type, $rowmin, $rowmax, $colmin, $colmax)
	{
		$record = 24;
		$length = 61;
		$grbit = 32;
		$chKey = 0;
		$cch = 1;
		$cce = 46;
		$ixals = $index + 1;
		$itab = $ixals;
		$cchCustMenu = 0;
		$cchDescription = 0;
		$cchHelptopic = 0;
		$cchStatustext = 0;
		$rgch = $type;
		$unknown01 = 41;
		$unknown02 = 43;
		$unknown03 = 59;
		$unknown04 = 65535 - $index;
		$unknown05 = 0;
		$unknown06 = 0;
		$unknown07 = 4231;
		$unknown08 = 32776;
		$header = pack('vv', $record, $length);
		$data = pack('v', $grbit);
		$data .= pack('C', $chKey);
		$data .= pack('C', $cch);
		$data .= pack('v', $cce);
		$data .= pack('v', $ixals);
		$data .= pack('v', $itab);
		$data .= pack('C', $cchCustMenu);
		$data .= pack('C', $cchDescription);
		$data .= pack('C', $cchHelptopic);
		$data .= pack('C', $cchStatustext);
		$data .= pack('C', $rgch);
		$data .= pack('C', $unknown01);
		$data .= pack('v', $unknown02);
		$data .= pack('C', $unknown03);
		$data .= pack('v', $unknown04);
		$data .= pack('v', $unknown05);
		$data .= pack('v', $unknown06);
		$data .= pack('v', $unknown07);
		$data .= pack('v', $unknown08);
		$data .= pack('v', $index);
		$data .= pack('v', $index);
		$data .= pack('v', 0);
		$data .= pack('v', 16383);
		$data .= pack('C', $colmin);
		$data .= pack('C', $colmax);
		$data .= pack('C', $unknown03);
		$data .= pack('v', $unknown04);
		$data .= pack('v', $unknown05);
		$data .= pack('v', $unknown06);
		$data .= pack('v', $unknown07);
		$data .= pack('v', $unknown08);
		$data .= pack('v', $index);
		$data .= pack('v', $index);
		$data .= pack('v', $rowmin);
		$data .= pack('v', $rowmax);
		$data .= pack('C', 0);
		$data .= pack('C', 255);
		$data .= pack('C', 16);
		$this->_append($header . $data);
	}

	private function _writeCountry()
	{
		$record = 140;
		$length = 4;
		$header = pack('vv', $record, $length);
		$data = pack('vv', $this->_country_code, $this->_country_code);
		return $this->writeData($header . $data);
	}

	private function _writeRecalcId()
	{
		$record = 449;
		$length = 8;
		$header = pack('vv', $record, $length);
		$data = pack('VV', 449, 124519);
		return $this->writeData($header . $data);
	}

	private function _writePalette()
	{
		$aref = $this->_palette;
		$record = 146;
		$length = 2 + (4 * count($aref));
		$ccv = count($aref);
		$data = '';

		foreach ($aref as $color) {
			foreach ($color as $byte) {
				$data .= pack('C', $byte);
			}
		}

		$header = pack('vvv', $record, $length, $ccv);
		$this->_append($header . $data);
	}

	private function _writeSharedStringsTable()
	{
		$continue_limit = 8224;
		$recordDatas = array();
		$recordData = pack('VV', $this->_str_total, $this->_str_unique);

		foreach (array_keys($this->_str_table) as $string) {
			$headerinfo = unpack('vlength/Cencoding', $string);
			$encoding = $headerinfo['encoding'];
			$finished = false;

			while ($finished === false) {
				if ((strlen($recordData) + strlen($string)) <= $continue_limit) {
					$recordData .= $string;

					if ((strlen($recordData) + strlen($string)) == $continue_limit) {
						$recordDatas[] = $recordData;
						$recordData = '';
					}

					$finished = true;
				}
				else {
					$space_remaining = $continue_limit - strlen($recordData);
					$min_space_needed = ($encoding == 1 ? 5 : 4);

					if ($space_remaining < $min_space_needed) {
						$recordDatas[] = $recordData;
						$recordData = '';
					}
					else {
						$effective_space_remaining = $space_remaining;
						if (($encoding == 1) && (((strlen($string) - $space_remaining) % 2) == 1)) {
							--$effective_space_remaining;
						}

						$recordData .= substr($string, 0, $effective_space_remaining);
						$string = substr($string, $effective_space_remaining);
						$recordDatas[] = $recordData;
						$recordData = pack('C', $encoding);
					}
				}
			}
		}

		if (0 < strlen($recordData)) {
			$recordDatas[] = $recordData;
		}

		$chunk = '';

		foreach ($recordDatas as $i => $recordData) {
			$record = ($i == 0 ? 252 : 60);
			$header = pack('vv', $record, strlen($recordData));
			$data = $header . $recordData;
			$chunk .= $this->writeData($data);
		}

		return $chunk;
	}

	private function _writeMsoDrawingGroup()
	{
		if (isset($this->_escher)) {
			$writer = new PHPExcel_Writer_Excel5_Escher($this->_escher);
			$data = $writer->close();
			$record = 235;
			$length = strlen($data);
			$header = pack('vv', $record, $length);
			return $this->writeData($header . $data);
		}
		else {
			return '';
		}
	}

	public function getEscher()
	{
		return $this->_escher;
	}

	public function setEscher(PHPExcel_Shared_Escher $pValue = NULL)
	{
		$this->_escher = $pValue;
	}
}

?>

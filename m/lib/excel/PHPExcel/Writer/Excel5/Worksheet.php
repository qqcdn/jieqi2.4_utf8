<?php

class PHPExcel_Writer_Excel5_Worksheet extends PHPExcel_Writer_Excel5_BIFFwriter
{
	/**
	 * Formula parser
	 *
	 * @var PHPExcel_Writer_Excel5_Parser
	 */
	private $_parser;
	/**
	 * Maximum number of characters for a string (LABEL record in BIFF5)
	 * @var integer
	 */
	public $_xls_strmax;
	/**
	 * Array containing format information for columns
	 * @var array
	 */
	public $_colinfo;
	/**
	 * Array containing the selected area for the worksheet
	 * @var array
	 */
	public $_selection;
	/**
	 * The active pane for the worksheet
	 * @var integer
	 */
	public $_active_pane;
	/**
	 * Whether to use outline.
	 * @var integer
	 */
	public $_outline_on;
	/**
	 * Auto outline styles.
	 * @var bool
	 */
	public $_outline_style;
	/**
	 * Whether to have outline summary below.
	 * @var bool
	 */
	public $_outline_below;
	/**
	 * Whether to have outline summary at the right.
	 * @var bool
	 */
	public $_outline_right;
	/**
	 * Reference to the total number of strings in the workbook
	 * @var integer
	 */
	public $_str_total;
	/**
	 * Reference to the number of unique strings in the workbook
	 * @var integer
	 */
	public $_str_unique;
	/**
	 * Reference to the array containing all the unique strings in the workbook
	 * @var array
	 */
	public $_str_table;
	/**
	 * Color cache
	 */
	private $_colors;
	/**
	 * Index of first used row (at least 0)
	 * @var int
	 */
	private $_firstRowIndex;
	/**
	 * Index of last used row. (no used rows means -1)
	 * @var int
	 */
	private $_lastRowIndex;
	/**
	 * Index of first used column (at least 0)
	 * @var int
	 */
	private $_firstColumnIndex;
	/**
	 * Index of last used column (no used columns means -1)
	 * @var int
	 */
	private $_lastColumnIndex;
	/**
	 * Sheet object
	 * @var PHPExcel_Worksheet
	 */
	public $_phpSheet;
	/**
	 * Count cell style Xfs
	 *
	 * @var int
	 */
	private $_countCellStyleXfs;
	/**
	 * Escher object corresponding to MSODRAWING
	 *
	 * @var PHPExcel_Shared_Escher
	 */
	private $_escher;
	/**
	 * Array of font hashes associated to FONT records index
	 *
	 * @var array
	 */
	public $_fntHashIndex;

	public function __construct(&$str_total, &$str_unique, &$str_table, &$colors, $parser, $preCalculateFormulas, $phpSheet)
	{
		parent::__construct();
		$this->_preCalculateFormulas = $preCalculateFormulas;
		$this->_str_total = &$str_total;
		$this->_str_unique = &$str_unique;
		$this->_str_table = &$str_table;
		$this->_colors = &$colors;
		$this->_parser = $parser;
		$this->_phpSheet = $phpSheet;
		$this->_xls_strmax = 255;
		$this->_colinfo = array();
		$this->_selection = array(0, 0, 0, 0);
		$this->_active_pane = 3;
		$this->_print_headers = 0;
		$this->_outline_style = 0;
		$this->_outline_below = 1;
		$this->_outline_right = 1;
		$this->_outline_on = 1;
		$this->_fntHashIndex = array();
		$minR = 1;
		$minC = 'A';
		$maxR = $this->_phpSheet->getHighestRow();
		$maxC = $this->_phpSheet->getHighestColumn();
		$this->_lastRowIndex = 65535 < $maxR ? 65535 : $maxR;
		$this->_firstColumnIndex = PHPExcel_Cell::columnIndexFromString($minC);
		$this->_lastColumnIndex = PHPExcel_Cell::columnIndexFromString($maxC);

		if (255 < $this->_lastColumnIndex) {
			$this->_lastColumnIndex = 255;
		}

		$this->_countCellStyleXfs = count($phpSheet->getParent()->getCellStyleXfCollection());
	}

	public function close()
	{
		$_phpSheet = $this->_phpSheet;
		$num_sheets = $_phpSheet->getParent()->getSheetCount();
		$this->_storeBof(16);
		$this->_writePrintHeaders();
		$this->_writePrintGridlines();
		$this->_writeGridset();
		$_phpSheet->calculateColumnWidths();

		if (($defaultWidth = $_phpSheet->getDefaultColumnDimension()->getWidth()) < 0) {
			$defaultWidth = PHPExcel_Shared_Font::getDefaultColumnWidthByFont($_phpSheet->getParent()->getDefaultStyle()->getFont());
		}

		$columnDimensions = $_phpSheet->getColumnDimensions();
		$maxCol = $this->_lastColumnIndex - 1;

		for ($i = 0; $i <= $maxCol; ++$i) {
			$hidden = 0;
			$level = 0;
			$xfIndex = 15;
			$width = $defaultWidth;
			$columnLetter = PHPExcel_Cell::stringFromColumnIndex($i);

			if (isset($columnDimensions[$columnLetter])) {
				$columnDimension = $columnDimensions[$columnLetter];

				if (0 <= $columnDimension->getWidth()) {
					$width = $columnDimension->getWidth();
				}

				$hidden = ($columnDimension->getVisible() ? 0 : 1);
				$level = $columnDimension->getOutlineLevel();
				$xfIndex = $columnDimension->getXfIndex() + 15;
			}

			$this->_colinfo[] = array($i, $i, $width, $xfIndex, $hidden, $level);
		}

		$this->_writeGuts();
		$this->_writeDefaultRowHeight();
		$this->_writeWsbool();
		$this->_writeBreaks();
		$this->_writeHeader();
		$this->_writeFooter();
		$this->_writeHcenter();
		$this->_writeVcenter();
		$this->_writeMarginLeft();
		$this->_writeMarginRight();
		$this->_writeMarginTop();
		$this->_writeMarginBottom();
		$this->_writeSetup();
		$this->_writeProtect();
		$this->_writeScenProtect();
		$this->_writeObjectProtect();
		$this->_writePassword();
		$this->_writeDefcol();

		if (!empty($this->_colinfo)) {
			$colcount = count($this->_colinfo);

			for ($i = 0; $i < $colcount; ++$i) {
				$this->_writeColinfo($this->_colinfo[$i]);
			}
		}

		$autoFilterRange = $_phpSheet->getAutoFilter()->getRange();

		if (!empty($autoFilterRange)) {
			$this->_writeAutoFilterInfo();
		}

		$this->_writeDimensions();

		foreach ($_phpSheet->getRowDimensions() as $rowDimension) {
			$xfIndex = $rowDimension->getXfIndex() + 15;
			$this->_writeRow($rowDimension->getRowIndex() - 1, $rowDimension->getRowHeight(), $xfIndex, $rowDimension->getVisible() ? '0' : '1', $rowDimension->getOutlineLevel());
		}

		foreach ($_phpSheet->getCellCollection() as $cellID) {
			$cell = $_phpSheet->getCell($cellID);
			$row = $cell->getRow() - 1;
			$column = PHPExcel_Cell::columnIndexFromString($cell->getColumn()) - 1;
			if ((65535 < $row) || (255 < $column)) {
				break;
			}

			$xfIndex = $cell->getXfIndex() + 15;
			$cVal = $cell->getValue();

			if ($cVal instanceof PHPExcel_RichText) {
				$arrcRun = array();
				$str_len = PHPExcel_Shared_String::CountCharacters($cVal->getPlainText(), 'UTF-8');
				$str_pos = 0;
				$elements = $cVal->getRichTextElements();

				foreach ($elements as $element) {
					if ($element instanceof PHPExcel_RichText_Run) {
						$str_fontidx = $this->_fntHashIndex[$element->getFont()->getHashCode()];
					}
					else {
						$str_fontidx = 0;
					}

					$arrcRun[] = array('strlen' => $str_pos, 'fontidx' => $str_fontidx);
					$str_pos += PHPExcel_Shared_String::CountCharacters($element->getText(), 'UTF-8');
				}

				$this->_writeRichTextString($row, $column, $cVal->getPlainText(), $xfIndex, $arrcRun);
			}
			else {
				switch ($cell->getDatatype()) {
				case PHPExcel_Cell_DataType::TYPE_STRING:
				case PHPExcel_Cell_DataType::TYPE_NULL:
					if (($cVal === '') || ($cVal === NULL)) {
						$this->_writeBlank($row, $column, $xfIndex);
					}
					else {
						$this->_writeString($row, $column, $cVal, $xfIndex);
					}

					break;

				case PHPExcel_Cell_DataType::TYPE_NUMERIC:
					$this->_writeNumber($row, $column, $cVal, $xfIndex);
					break;

				case PHPExcel_Cell_DataType::TYPE_FORMULA:
					$calculatedValue = ($this->_preCalculateFormulas ? $cell->getCalculatedValue() : NULL);
					$this->_writeFormula($row, $column, $cVal, $xfIndex, $calculatedValue);
					break;

				case PHPExcel_Cell_DataType::TYPE_BOOL:
					$this->_writeBoolErr($row, $column, $cVal, 0, $xfIndex);
					break;

				case PHPExcel_Cell_DataType::TYPE_ERROR:
					$this->_writeBoolErr($row, $column, self::_mapErrorCode($cVal), 1, $xfIndex);
					break;
				}
			}
		}

		$this->_writeMsoDrawing();
		$this->_writeWindow2();
		$this->_writePageLayoutView();
		$this->_writeZoom();

		if ($_phpSheet->getFreezePane()) {
			$this->_writePanes();
		}

		$this->_writeSelection();
		$this->_writeMergedCells();

		foreach ($_phpSheet->getHyperLinkCollection() as $coordinate => $hyperlink) {
			list($column, $row) = PHPExcel_Cell::coordinateFromString($coordinate);
			$url = $hyperlink->getUrl();

			if (strpos($url, 'sheet://') !== false) {
				$url = str_replace('sheet://', 'internal:', $url);
			}
			else if (preg_match('/^(http:|https:|ftp:|mailto:)/', $url)) {
			}
			else {
				$url = 'external:' . $url;
			}

			$this->_writeUrl($row - 1, PHPExcel_Cell::columnIndexFromString($column) - 1, $url);
		}

		$this->_writeDataValidity();
		$this->_writeSheetLayout();
		$this->_writeSheetProtection();
		$this->_writeRangeProtection();
		$arrConditionalStyles = $_phpSheet->getConditionalStylesCollection();

		if (!empty($arrConditionalStyles)) {
			$arrConditional = array();
			$this->_writeCFHeader();

			foreach ($arrConditionalStyles as $cellCoordinate => $conditionalStyles) {
				foreach ($conditionalStyles as $conditional) {
					if (($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_EXPRESSION) || ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CELLIS)) {
						if (!in_array($conditional->getHashCode(), $arrConditional)) {
							$arrConditional[] = $conditional->getHashCode();
							$this->_writeCFRule($conditional);
						}
					}
				}
			}
		}

		$this->_storeEof();
	}

	private function _writeBIFF8CellRangeAddressFixed($range = 'A1')
	{
		$explodes = explode(':', $range);
		$firstCell = $explodes[0];

		if (count($explodes) == 1) {
			$lastCell = $firstCell;
		}
		else {
			$lastCell = $explodes[1];
		}

		$firstCellCoordinates = PHPExcel_Cell::coordinateFromString($firstCell);
		$lastCellCoordinates = PHPExcel_Cell::coordinateFromString($lastCell);
		return pack('vvvv', $firstCellCoordinates[1] - 1, $lastCellCoordinates[1] - 1, PHPExcel_Cell::columnIndexFromString($firstCellCoordinates[0]) - 1, PHPExcel_Cell::columnIndexFromString($lastCellCoordinates[0]) - 1);
	}

	public function getData()
	{
		$buffer = 4096;

		if (isset($this->_data)) {
			$tmp = $this->_data;
			unset($this->_data);
			return $tmp;
		}

		return false;
	}

	public function printRowColHeaders($print = 1)
	{
		$this->_print_headers = $print;
	}

	public function setOutline($visible = true, $symbols_below = true, $symbols_right = true, $auto_style = false)
	{
		$this->_outline_on = $visible;
		$this->_outline_below = $symbols_below;
		$this->_outline_right = $symbols_right;
		$this->_outline_style = $auto_style;

		if ($this->_outline_on) {
			$this->_outline_on = 1;
		}
	}

	private function _writeNumber($row, $col, $num, $xfIndex)
	{
		$record = 515;
		$length = 14;
		$header = pack('vv', $record, $length);
		$data = pack('vvv', $row, $col, $xfIndex);
		$xl_double = pack('d', $num);

		if (self::getByteOrder()) {
			$xl_double = strrev($xl_double);
		}

		$this->_append($header . $data . $xl_double);
		return 0;
	}

	private function _writeString($row, $col, $str, $xfIndex)
	{
		$this->_writeLabelSst($row, $col, $str, $xfIndex);
	}

	private function _writeRichTextString($row, $col, $str, $xfIndex, $arrcRun)
	{
		$record = 253;
		$length = 10;
		$str = PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($str, $arrcRun);

		if (!isset($this->_str_table[$str])) {
			$this->_str_table[$str] = $this->_str_unique++;
		}

		$this->_str_total++;
		$header = pack('vv', $record, $length);
		$data = pack('vvvV', $row, $col, $xfIndex, $this->_str_table[$str]);
		$this->_append($header . $data);
	}

	private function _writeLabel($row, $col, $str, $xfIndex)
	{
		$strlen = strlen($str);
		$record = 516;
		$length = 8 + $strlen;
		$str_error = 0;

		if ($this->_xls_strmax < $strlen) {
			$str = substr($str, 0, $this->_xls_strmax);
			$length = 8 + $this->_xls_strmax;
			$strlen = $this->_xls_strmax;
			$str_error = -3;
		}

		$header = pack('vv', $record, $length);
		$data = pack('vvvv', $row, $col, $xfIndex, $strlen);
		$this->_append($header . $data . $str);
		return $str_error;
	}

	private function _writeLabelSst($row, $col, $str, $xfIndex)
	{
		$record = 253;
		$length = 10;
		$str = PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($str);

		if (!isset($this->_str_table[$str])) {
			$this->_str_table[$str] = $this->_str_unique++;
		}

		$this->_str_total++;
		$header = pack('vv', $record, $length);
		$data = pack('vvvV', $row, $col, $xfIndex, $this->_str_table[$str]);
		$this->_append($header . $data);
	}

	private function _writeNote($row, $col, $note)
	{
		$note_length = strlen($note);
		$record = 28;
		$max_length = 2048;
		$length = 6 + min($note_length, 2048);
		$header = pack('vv', $record, $length);
		$data = pack('vvv', $row, $col, $note_length);
		$this->_append($header . $data . substr($note, 0, 2048));

		for ($i = $max_length; $i < $note_length; $i += $max_length) {
			$chunk = substr($note, $i, $max_length);
			$length = 6 + strlen($chunk);
			$header = pack('vv', $record, $length);
			$data = pack('vvv', -1, 0, strlen($chunk));
			$this->_append($header . $data . $chunk);
		}

		return 0;
	}

	public function _writeBlank($row, $col, $xfIndex)
	{
		$record = 513;
		$length = 6;
		$header = pack('vv', $record, $length);
		$data = pack('vvv', $row, $col, $xfIndex);
		$this->_append($header . $data);
		return 0;
	}

	private function _writeBoolErr($row, $col, $value, $isError, $xfIndex)
	{
		$record = 517;
		$length = 8;
		$header = pack('vv', $record, $length);
		$data = pack('vvvCC', $row, $col, $xfIndex, $value, $isError);
		$this->_append($header . $data);
		return 0;
	}

	private function _writeFormula($row, $col, $formula, $xfIndex, $calculatedValue)
	{
		$record = 6;
		$stringValue = NULL;

		if (isset($calculatedValue)) {
			if (is_bool($calculatedValue)) {
				$num = pack('CCCvCv', 1, 0, (int) $calculatedValue, 0, 0, 65535);
			}
			else {
				if (is_int($calculatedValue) || is_float($calculatedValue)) {
					$num = pack('d', $calculatedValue);
				}
				else if (is_string($calculatedValue)) {
					if (array_key_exists($calculatedValue, PHPExcel_Cell_DataType::getErrorCodes())) {
						$num = pack('CCCvCv', 2, 0, self::_mapErrorCode($calculatedValue), 0, 0, 65535);
					}
					else if ($calculatedValue === '') {
						$num = pack('CCCvCv', 3, 0, 0, 0, 0, 65535);
					}
					else {
						$stringValue = $calculatedValue;
						$num = pack('CCCvCv', 0, 0, 0, 0, 0, 65535);
					}
				}
				else {
					$num = pack('d', 0);
				}
			}
		}
		else {
			$num = pack('d', 0);
		}

		$grbit = 3;
		$unknown = 0;

		if ($formula[0] == '=') {
			$formula = substr($formula, 1);
		}
		else {
			$this->_writeString($row, $col, 'Unrecognised character for formula');
			return -1;
		}

		try {
			$error = $this->_parser->parse($formula);
			$formula = $this->_parser->toReversePolish();
			$formlen = strlen($formula);
			$length = 22 + $formlen;
			$header = pack('vv', $record, $length);
			$data = pack('vvv', $row, $col, $xfIndex) . $num . pack('vVv', $grbit, $unknown, $formlen);
			$this->_append($header . $data . $formula);

			if ($stringValue !== NULL) {
				$this->_writeStringRecord($stringValue);
			}

			return 0;
		}
		catch (PHPExcel_Exception $e) {
		}
	}

	private function _writeStringRecord($stringValue)
	{
		$record = 519;
		$data = PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($stringValue);
		$length = strlen($data);
		$header = pack('vv', $record, $length);
		$this->_append($header . $data);
	}

	private function _writeUrl($row, $col, $url)
	{
		return $this->_writeUrlRange($row, $col, $row, $col, $url);
	}

	public function _writeUrlRange($row1, $col1, $row2, $col2, $url)
	{
		if (preg_match('[^internal:]', $url)) {
			return $this->_writeUrlInternal($row1, $col1, $row2, $col2, $url);
		}

		if (preg_match('[^external:]', $url)) {
			return $this->_writeUrlExternal($row1, $col1, $row2, $col2, $url);
		}

		return $this->_writeUrlWeb($row1, $col1, $row2, $col2, $url);
	}

	public function _writeUrlWeb($row1, $col1, $row2, $col2, $url)
	{
		$record = 440;
		$length = 0;
		$unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');
		$unknown2 = pack('H*', 'E0C9EA79F9BACE118C8200AA004BA90B');
		$options = pack('V', 3);
		$url = join("\x00", preg_split('\'\'', $url, -1, PREG_SPLIT_NO_EMPTY));
		$url = $url . "\x00\x00\x00";
		$url_len = pack('V', strlen($url));
		$length = 52 + strlen($url);
		$header = pack('vv', $record, $length);
		$data = pack('vvvv', $row1, $row2, $col1, $col2);
		$this->_append($header . $data . $unknown1 . $options . $unknown2 . $url_len . $url);
		return 0;
	}

	public function _writeUrlInternal($row1, $col1, $row2, $col2, $url)
	{
		$record = 440;
		$length = 0;
		$url = preg_replace('/^internal:/', '', $url);
		$unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');
		$options = pack('V', 8);
		$url .= "\x00";
		$url_len = PHPExcel_Shared_String::CountCharacters($url);
		$url_len = pack('V', $url_len);
		$url = PHPExcel_Shared_String::ConvertEncoding($url, 'UTF-16LE', 'UTF-8');
		$length = 36 + strlen($url);
		$header = pack('vv', $record, $length);
		$data = pack('vvvv', $row1, $row2, $col1, $col2);
		$this->_append($header . $data . $unknown1 . $options . $url_len . $url);
		return 0;
	}

	public function _writeUrlExternal($row1, $col1, $row2, $col2, $url)
	{
		if (preg_match('[^external:\\\\]', $url)) {
			return NULL;
		}

		$record = 440;
		$length = 0;
		$url = preg_replace('/^external:/', '', $url);
		$url = preg_replace('/\\//', '\\', $url);
		$absolute = 0;

		if (preg_match('/^[A-Z]:/', $url)) {
			$absolute = 2;
		}

		$link_type = 1 | $absolute;
		$dir_long = $url;

		if (preg_match('/\\#/', $url)) {
			$link_type |= 8;
		}

		$link_type = pack('V', $link_type);
		$up_count = preg_match_all('/\\.\\.\\\\/', $dir_long, $useless);
		$up_count = pack('v', $up_count);
		$dir_short = preg_replace('/\\.\\.\\\\/', '', $dir_long) . "\x00";
		$dir_long = $dir_long . "\x00";
		$dir_short_len = pack('V', strlen($dir_short));
		$dir_long_len = pack('V', strlen($dir_long));
		$stream_len = pack('V', 0);
		$unknown1 = pack('H*', 'D0C9EA79F9BACE118C8200AA004BA90B02000000');
		$unknown2 = pack('H*', '0303000000000000C000000000000046');
		$unknown3 = pack('H*', 'FFFFADDE000000000000000000000000000000000000000');
		$unknown4 = pack('v', 3);
		$data = pack('vvvv', $row1, $row2, $col1, $col2) . $unknown1 . $link_type . $unknown2 . $up_count . $dir_short_len . $dir_short . $unknown3 . $stream_len;
		$length = strlen($data);
		$header = pack('vv', $record, $length);
		$this->_append($header . $data);
		return 0;
	}

	private function _writeRow($row, $height, $xfIndex, $hidden = false, $level = 0)
	{
		$record = 520;
		$length = 16;
		$colMic = 0;
		$colMac = 0;
		$irwMac = 0;
		$reserved = 0;
		$grbit = 0;
		$ixfe = $xfIndex;

		if ($height < 0) {
			$height = NULL;
		}

		if ($height != NULL) {
			$miyRw = $height * 20;
		}
		else {
			$miyRw = 255;
		}

		$grbit |= $level;

		if ($hidden) {
			$grbit |= 32;
		}

		if ($height !== NULL) {
			$grbit |= 64;
		}

		if ($xfIndex !== 15) {
			$grbit |= 128;
		}

		$grbit |= 256;
		$header = pack('vv', $record, $length);
		$data = pack('vvvvvvvv', $row, $colMic, $colMac, $miyRw, $irwMac, $reserved, $grbit, $ixfe);
		$this->_append($header . $data);
	}

	private function _writeDimensions()
	{
		$record = 512;
		$length = 14;
		$data = pack('VVvvv', $this->_firstRowIndex, $this->_lastRowIndex + 1, $this->_firstColumnIndex, $this->_lastColumnIndex + 1, 0);
		$header = pack('vv', $record, $length);
		$this->_append($header . $data);
	}

	private function _writeWindow2()
	{
		$record = 574;
		$length = 18;
		$grbit = 182;
		$rwTop = 0;
		$colLeft = 0;
		$fDspFmla = 0;
		$fDspGrid = ($this->_phpSheet->getShowGridlines() ? 1 : 0);
		$fDspRwCol = ($this->_phpSheet->getShowRowColHeaders() ? 1 : 0);
		$fFrozen = ($this->_phpSheet->getFreezePane() ? 1 : 0);
		$fDspZeros = 1;
		$fDefaultHdr = 1;
		$fArabic = ($this->_phpSheet->getRightToLeft() ? 1 : 0);
		$fDspGuts = $this->_outline_on;
		$fFrozenNoSplit = 0;
		$fSelected = ($this->_phpSheet === $this->_phpSheet->getParent()->getActiveSheet() ? 1 : 0);
		$fPaged = 1;
		$fPageBreakPreview = $this->_phpSheet->getSheetView()->getView() === PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW;
		$grbit = $fDspFmla;
		$grbit |= $fDspGrid << 1;
		$grbit |= $fDspRwCol << 2;
		$grbit |= $fFrozen << 3;
		$grbit |= $fDspZeros << 4;
		$grbit |= $fDefaultHdr << 5;
		$grbit |= $fArabic << 6;
		$grbit |= $fDspGuts << 7;
		$grbit |= $fFrozenNoSplit << 8;
		$grbit |= $fSelected << 9;
		$grbit |= $fPaged << 10;
		$grbit |= $fPageBreakPreview << 11;
		$header = pack('vv', $record, $length);
		$data = pack('vvv', $grbit, $rwTop, $colLeft);
		$rgbHdr = 64;
		$zoom_factor_page_break = ($fPageBreakPreview ? $this->_phpSheet->getSheetView()->getZoomScale() : 0);
		$zoom_factor_normal = $this->_phpSheet->getSheetView()->getZoomScaleNormal();
		$data .= pack('vvvvV', $rgbHdr, 0, $zoom_factor_page_break, $zoom_factor_normal, 0);
		$this->_append($header . $data);
	}

	private function _writeDefaultRowHeight()
	{
		$defaultRowHeight = $this->_phpSheet->getDefaultRowDimension()->getRowHeight();

		if ($defaultRowHeight < 0) {
			return NULL;
		}

		$defaultRowHeight = (int) 20 * $defaultRowHeight;
		$record = 549;
		$length = 4;
		$header = pack('vv', $record, $length);
		$data = pack('vv', 1, $defaultRowHeight);
		$this->_append($header . $data);
	}

	private function _writeDefcol()
	{
		$defaultColWidth = 8;
		$record = 85;
		$length = 2;
		$header = pack('vv', $record, $length);
		$data = pack('v', $defaultColWidth);
		$this->_append($header . $data);
	}

	private function _writeColinfo($col_array)
	{
		if (isset($col_array[0])) {
			$colFirst = $col_array[0];
		}

		if (isset($col_array[1])) {
			$colLast = $col_array[1];
		}

		if (isset($col_array[2])) {
			$coldx = $col_array[2];
		}
		else {
			$coldx = 8.43;
		}

		if (isset($col_array[3])) {
			$xfIndex = $col_array[3];
		}
		else {
			$xfIndex = 15;
		}

		if (isset($col_array[4])) {
			$grbit = $col_array[4];
		}
		else {
			$grbit = 0;
		}

		if (isset($col_array[5])) {
			$level = $col_array[5];
		}
		else {
			$level = 0;
		}

		$record = 125;
		$length = 12;
		$coldx *= 256;
		$ixfe = $xfIndex;
		$reserved = 0;
		$level = max(0, min($level, 7));
		$grbit |= $level << 8;
		$header = pack('vv', $record, $length);
		$data = pack('vvvvvv', $colFirst, $colLast, $coldx, $ixfe, $grbit, $reserved);
		$this->_append($header . $data);
	}

	private function _writeSelection()
	{
		$selectedCells = $this->_phpSheet->getSelectedCells();
		$selectedCells = PHPExcel_Cell::splitRange($this->_phpSheet->getSelectedCells());
		$selectedCells = $selectedCells[0];

		if (count($selectedCells) == 2) {
			list($first, $last) = $selectedCells;
		}
		else {
			$first = $selectedCells[0];
			$last = $selectedCells[0];
		}

		list($colFirst, $rwFirst) = PHPExcel_Cell::coordinateFromString($first);
		$colFirst = PHPExcel_Cell::columnIndexFromString($colFirst) - 1;
		--$rwFirst;
		list($colLast, $rwLast) = PHPExcel_Cell::coordinateFromString($last);
		$colLast = PHPExcel_Cell::columnIndexFromString($colLast) - 1;
		--$rwLast;
		$colFirst = min($colFirst, 255);
		$colLast = min($colLast, 255);
		$rwFirst = min($rwFirst, 65535);
		$rwLast = min($rwLast, 65535);
		$record = 29;
		$length = 15;
		$pnn = $this->_active_pane;
		$rwAct = $rwFirst;
		$colAct = $colFirst;
		$irefAct = 0;
		$cref = 1;

		if (!isset($rwLast)) {
			$rwLast = $rwFirst;
		}

		if (!isset($colLast)) {
			$colLast = $colFirst;
		}

		if ($rwLast < $rwFirst) {
			list($rwFirst, $rwLast) = array($rwLast, $rwFirst);
		}

		if ($colLast < $colFirst) {
			list($colFirst, $colLast) = array($colLast, $colFirst);
		}

		$header = pack('vv', $record, $length);
		$data = pack('CvvvvvvCC', $pnn, $rwAct, $colAct, $irefAct, $cref, $rwFirst, $rwLast, $colFirst, $colLast);
		$this->_append($header . $data);
	}

	private function _writeMergedCells()
	{
		$mergeCells = $this->_phpSheet->getMergeCells();
		$countMergeCells = count($mergeCells);

		if ($countMergeCells == 0) {
			return NULL;
		}

		$maxCountMergeCellsPerRecord = 1027;
		$record = 229;
		$i = 0;
		$j = 0;
		$recordData = '';

		foreach ($mergeCells as $mergeCell) {
			++$i;
			++$j;
			$range = PHPExcel_Cell::splitRange($mergeCell);
			list($first, $last) = $range[0];
			list($firstColumn, $firstRow) = PHPExcel_Cell::coordinateFromString($first);
			list($lastColumn, $lastRow) = PHPExcel_Cell::coordinateFromString($last);
			$recordData .= pack('vvvv', $firstRow - 1, $lastRow - 1, PHPExcel_Cell::columnIndexFromString($firstColumn) - 1, PHPExcel_Cell::columnIndexFromString($lastColumn) - 1);
			if (($j == $maxCountMergeCellsPerRecord) || ($i == $countMergeCells)) {
				$recordData = pack('v', $j) . $recordData;
				$length = strlen($recordData);
				$header = pack('vv', $record, $length);
				$this->_append($header . $recordData);
				$recordData = '';
				$j = 0;
			}
		}
	}

	private function _writeSheetLayout()
	{
		if (!$this->_phpSheet->isTabColorSet()) {
			return NULL;
		}

		$recordData = pack('vvVVVvv', 2146, 0, 0, 0, 20, $this->_colors[$this->_phpSheet->getTabColor()->getRGB()], 0);
		$length = strlen($recordData);
		$record = 2146;
		$header = pack('vv', $record, $length);
		$this->_append($header . $recordData);
	}

	private function _writeSheetProtection()
	{
		$record = 2151;
		$options = (int) !$this->_phpSheet->getProtection()->getObjects() | ((int) !$this->_phpSheet->getProtection()->getScenarios() << 1) | ((int) !$this->_phpSheet->getProtection()->getFormatCells() << 2) | ((int) !$this->_phpSheet->getProtection()->getFormatColumns() << 3) | ((int) !$this->_phpSheet->getProtection()->getFormatRows() << 4) | ((int) !$this->_phpSheet->getProtection()->getInsertColumns() << 5) | ((int) !$this->_phpSheet->getProtection()->getInsertRows() << 6) | ((int) !$this->_phpSheet->getProtection()->getInsertHyperlinks() << 7) | ((int) !$this->_phpSheet->getProtection()->getDeleteColumns() << 8) | ((int) !$this->_phpSheet->getProtection()->getDeleteRows() << 9) | ((int) !$this->_phpSheet->getProtection()->getSelectLockedCells() << 10) | ((int) !$this->_phpSheet->getProtection()->getSort() << 11) | ((int) !$this->_phpSheet->getProtection()->getAutoFilter() << 12) | ((int) !$this->_phpSheet->getProtection()->getPivotTables() << 13) | ((int) !$this->_phpSheet->getProtection()->getSelectUnlockedCells() << 14);
		$recordData = pack('vVVCVVvv', 2151, 0, 0, 0, 16777728, 4294967295, $options, 0);
		$length = strlen($recordData);
		$header = pack('vv', $record, $length);
		$this->_append($header . $recordData);
	}

	private function _writeRangeProtection()
	{
		foreach ($this->_phpSheet->getProtectedCells() as $range => $password) {
			$cellRanges = explode(' ', $range);
			$cref = count($cellRanges);
			$recordData = pack('vvVVvCVvVv', 2152, 0, 0, 0, 2, 0, 0, $cref, 0, 0);

			foreach ($cellRanges as $cellRange) {
				$recordData .= $this->_writeBIFF8CellRangeAddressFixed($cellRange);
			}

			$recordData .= pack('VV', 0, hexdec($password));
			$recordData .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong('p' . md5($recordData));
			$length = strlen($recordData);
			$record = 2152;
			$header = pack('vv', $record, $length);
			$this->_append($header . $recordData);
		}
	}

	private function _writeExterncount($count)
	{
		$record = 22;
		$length = 2;
		$header = pack('vv', $record, $length);
		$data = pack('v', $count);
		$this->_append($header . $data);
	}

	private function _writeExternsheet($sheetname)
	{
		$record = 23;

		if ($this->_phpSheet->getTitle() == $sheetname) {
			$sheetname = '';
			$length = 2;
			$cch = 1;
			$rgch = 2;
		}
		else {
			$length = 2 + strlen($sheetname);
			$cch = strlen($sheetname);
			$rgch = 3;
		}

		$header = pack('vv', $record, $length);
		$data = pack('CC', $cch, $rgch);
		$this->_append($header . $data . $sheetname);
	}

	private function _writePanes()
	{
		$panes = array();

		if ($freezePane = $this->_phpSheet->getFreezePane()) {
			list($column, $row) = PHPExcel_Cell::coordinateFromString($freezePane);
			$panes[0] = $row - 1;
			$panes[1] = PHPExcel_Cell::columnIndexFromString($column) - 1;
		}
		else {
			return NULL;
		}

		$y = (isset($panes[0]) ? $panes[0] : NULL);
		$x = (isset($panes[1]) ? $panes[1] : NULL);
		$rwTop = (isset($panes[2]) ? $panes[2] : NULL);
		$colLeft = (isset($panes[3]) ? $panes[3] : NULL);

		if (4 < count($panes)) {
			$pnnAct = $panes[4];
		}
		else {
			$pnnAct = NULL;
		}

		$record = 65;
		$length = 10;

		if ($this->_phpSheet->getFreezePane()) {
			if (!isset($rwTop)) {
				$rwTop = $y;
			}

			if (!isset($colLeft)) {
				$colLeft = $x;
			}
		}
		else {
			if (!isset($rwTop)) {
				$rwTop = 0;
			}

			if (!isset($colLeft)) {
				$colLeft = 0;
			}

			$y = (20 * $y) + 255;
			$x = (113.879 * $x) + 390;
		}

		if (!isset($pnnAct)) {
			if (($x != 0) && ($y != 0)) {
				$pnnAct = 0;
			}

			if (($x != 0) && ($y == 0)) {
				$pnnAct = 1;
			}

			if (($x == 0) && ($y != 0)) {
				$pnnAct = 2;
			}

			if (($x == 0) && ($y == 0)) {
				$pnnAct = 3;
			}
		}

		$this->_active_pane = $pnnAct;
		$header = pack('vv', $record, $length);
		$data = pack('vvvvv', $x, $y, $rwTop, $colLeft, $pnnAct);
		$this->_append($header . $data);
	}

	private function _writeSetup()
	{
		$record = 161;
		$length = 34;
		$iPaperSize = $this->_phpSheet->getPageSetup()->getPaperSize();
		$iScale = ($this->_phpSheet->getPageSetup()->getScale() ? $this->_phpSheet->getPageSetup()->getScale() : 100);
		$iPageStart = 1;
		$iFitWidth = (int) $this->_phpSheet->getPageSetup()->getFitToWidth();
		$iFitHeight = (int) $this->_phpSheet->getPageSetup()->getFitToHeight();
		$grbit = 0;
		$iRes = 600;
		$iVRes = 600;
		$numHdr = $this->_phpSheet->getPageMargins()->getHeader();
		$numFtr = $this->_phpSheet->getPageMargins()->getFooter();
		$iCopies = 1;
		$fLeftToRight = 0;
		$fLandscape = ($this->_phpSheet->getPageSetup()->getOrientation() == PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE ? 0 : 1);
		$fNoPls = 0;
		$fNoColor = 0;
		$fDraft = 0;
		$fNotes = 0;
		$fNoOrient = 0;
		$fUsePage = 0;
		$grbit = $fLeftToRight;
		$grbit |= $fLandscape << 1;
		$grbit |= $fNoPls << 2;
		$grbit |= $fNoColor << 3;
		$grbit |= $fDraft << 4;
		$grbit |= $fNotes << 5;
		$grbit |= $fNoOrient << 6;
		$grbit |= $fUsePage << 7;
		$numHdr = pack('d', $numHdr);
		$numFtr = pack('d', $numFtr);

		if (self::getByteOrder()) {
			$numHdr = strrev($numHdr);
			$numFtr = strrev($numFtr);
		}

		$header = pack('vv', $record, $length);
		$data1 = pack('vvvvvvvv', $iPaperSize, $iScale, $iPageStart, $iFitWidth, $iFitHeight, $grbit, $iRes, $iVRes);
		$data2 = $numHdr . $numFtr;
		$data3 = pack('v', $iCopies);
		$this->_append($header . $data1 . $data2 . $data3);
	}

	private function _writeHeader()
	{
		$record = 20;
		$recordData = PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($this->_phpSheet->getHeaderFooter()->getOddHeader());
		$length = strlen($recordData);
		$header = pack('vv', $record, $length);
		$this->_append($header . $recordData);
	}

	private function _writeFooter()
	{
		$record = 21;
		$recordData = PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($this->_phpSheet->getHeaderFooter()->getOddFooter());
		$length = strlen($recordData);
		$header = pack('vv', $record, $length);
		$this->_append($header . $recordData);
	}

	private function _writeHcenter()
	{
		$record = 131;
		$length = 2;
		$fHCenter = ($this->_phpSheet->getPageSetup()->getHorizontalCentered() ? 1 : 0);
		$header = pack('vv', $record, $length);
		$data = pack('v', $fHCenter);
		$this->_append($header . $data);
	}

	private function _writeVcenter()
	{
		$record = 132;
		$length = 2;
		$fVCenter = ($this->_phpSheet->getPageSetup()->getVerticalCentered() ? 1 : 0);
		$header = pack('vv', $record, $length);
		$data = pack('v', $fVCenter);
		$this->_append($header . $data);
	}

	private function _writeMarginLeft()
	{
		$record = 38;
		$length = 8;
		$margin = $this->_phpSheet->getPageMargins()->getLeft();
		$header = pack('vv', $record, $length);
		$data = pack('d', $margin);

		if (self::getByteOrder()) {
			$data = strrev($data);
		}

		$this->_append($header . $data);
	}

	private function _writeMarginRight()
	{
		$record = 39;
		$length = 8;
		$margin = $this->_phpSheet->getPageMargins()->getRight();
		$header = pack('vv', $record, $length);
		$data = pack('d', $margin);

		if (self::getByteOrder()) {
			$data = strrev($data);
		}

		$this->_append($header . $data);
	}

	private function _writeMarginTop()
	{
		$record = 40;
		$length = 8;
		$margin = $this->_phpSheet->getPageMargins()->getTop();
		$header = pack('vv', $record, $length);
		$data = pack('d', $margin);

		if (self::getByteOrder()) {
			$data = strrev($data);
		}

		$this->_append($header . $data);
	}

	private function _writeMarginBottom()
	{
		$record = 41;
		$length = 8;
		$margin = $this->_phpSheet->getPageMargins()->getBottom();
		$header = pack('vv', $record, $length);
		$data = pack('d', $margin);

		if (self::getByteOrder()) {
			$data = strrev($data);
		}

		$this->_append($header . $data);
	}

	private function _writePrintHeaders()
	{
		$record = 42;
		$length = 2;
		$fPrintRwCol = $this->_print_headers;
		$header = pack('vv', $record, $length);
		$data = pack('v', $fPrintRwCol);
		$this->_append($header . $data);
	}

	private function _writePrintGridlines()
	{
		$record = 43;
		$length = 2;
		$fPrintGrid = ($this->_phpSheet->getPrintGridlines() ? 1 : 0);
		$header = pack('vv', $record, $length);
		$data = pack('v', $fPrintGrid);
		$this->_append($header . $data);
	}

	private function _writeGridset()
	{
		$record = 130;
		$length = 2;
		$fGridSet = !$this->_phpSheet->getPrintGridlines();
		$header = pack('vv', $record, $length);
		$data = pack('v', $fGridSet);
		$this->_append($header . $data);
	}

	private function _writeAutoFilterInfo()
	{
		$record = 157;
		$length = 2;
		$rangeBounds = PHPExcel_Cell::rangeBoundaries($this->_phpSheet->getAutoFilter()->getRange());
		$iNumFilters = (1 + $rangeBounds[1][0]) - $rangeBounds[0][0];
		$header = pack('vv', $record, $length);
		$data = pack('v', $iNumFilters);
		$this->_append($header . $data);
	}

	private function _writeGuts()
	{
		$record = 128;
		$length = 8;
		$dxRwGut = 0;
		$dxColGut = 0;
		$maxRowOutlineLevel = 0;

		foreach ($this->_phpSheet->getRowDimensions() as $rowDimension) {
			$maxRowOutlineLevel = max($maxRowOutlineLevel, $rowDimension->getOutlineLevel());
		}

		$col_level = 0;
		$colcount = count($this->_colinfo);

		for ($i = 0; $i < $colcount; ++$i) {
			$col_level = max($this->_colinfo[$i][5], $col_level);
		}

		$col_level = max(0, min($col_level, 7));

		if ($maxRowOutlineLevel) {
			++$maxRowOutlineLevel;
		}

		if ($col_level) {
			++$col_level;
		}

		$header = pack('vv', $record, $length);
		$data = pack('vvvv', $dxRwGut, $dxColGut, $maxRowOutlineLevel, $col_level);
		$this->_append($header . $data);
	}

	private function _writeWsbool()
	{
		$record = 129;
		$length = 2;
		$grbit = 0;
		$grbit |= 1;

		if ($this->_outline_style) {
			$grbit |= 32;
		}

		if ($this->_phpSheet->getShowSummaryBelow()) {
			$grbit |= 64;
		}

		if ($this->_phpSheet->getShowSummaryRight()) {
			$grbit |= 128;
		}

		if ($this->_phpSheet->getPageSetup()->getFitToPage()) {
			$grbit |= 256;
		}

		if ($this->_outline_on) {
			$grbit |= 1024;
		}

		$header = pack('vv', $record, $length);
		$data = pack('v', $grbit);
		$this->_append($header . $data);
	}

	private function _writeBreaks()
	{
		$vbreaks = array();
		$hbreaks = array();

		foreach ($this->_phpSheet->getBreaks() as $cell => $breakType) {
			$coordinates = PHPExcel_Cell::coordinateFromString($cell);

			switch ($breakType) {
			case PHPExcel_Worksheet::BREAK_COLUMN:
				$vbreaks[] = PHPExcel_Cell::columnIndexFromString($coordinates[0]) - 1;
				break;

			case PHPExcel_Worksheet::BREAK_ROW:
				$hbreaks[] = $coordinates[1];
				break;

			case PHPExcel_Worksheet::BREAK_NONE:
			default:
				break;
			}
		}

		if (!empty($hbreaks)) {
			sort($hbreaks, SORT_NUMERIC);

			if ($hbreaks[0] == 0) {
				array_shift($hbreaks);
			}

			$record = 27;
			$cbrk = count($hbreaks);
			$length = 2 + (6 * $cbrk);
			$header = pack('vv', $record, $length);
			$data = pack('v', $cbrk);

			foreach ($hbreaks as $hbreak) {
				$data .= pack('vvv', $hbreak, 0, 255);
			}

			$this->_append($header . $data);
		}

		if (!empty($vbreaks)) {
			$vbreaks = array_slice($vbreaks, 0, 1000);
			sort($vbreaks, SORT_NUMERIC);

			if ($vbreaks[0] == 0) {
				array_shift($vbreaks);
			}

			$record = 26;
			$cbrk = count($vbreaks);
			$length = 2 + (6 * $cbrk);
			$header = pack('vv', $record, $length);
			$data = pack('v', $cbrk);

			foreach ($vbreaks as $vbreak) {
				$data .= pack('vvv', $vbreak, 0, 65535);
			}

			$this->_append($header . $data);
		}
	}

	private function _writeProtect()
	{
		if (!$this->_phpSheet->getProtection()->getSheet()) {
			return NULL;
		}

		$record = 18;
		$length = 2;
		$fLock = 1;
		$header = pack('vv', $record, $length);
		$data = pack('v', $fLock);
		$this->_append($header . $data);
	}

	private function _writeScenProtect()
	{
		if (!$this->_phpSheet->getProtection()->getSheet()) {
			return NULL;
		}

		if (!$this->_phpSheet->getProtection()->getScenarios()) {
			return NULL;
		}

		$record = 221;
		$length = 2;
		$header = pack('vv', $record, $length);
		$data = pack('v', 1);
		$this->_append($header . $data);
	}

	private function _writeObjectProtect()
	{
		if (!$this->_phpSheet->getProtection()->getSheet()) {
			return NULL;
		}

		if (!$this->_phpSheet->getProtection()->getObjects()) {
			return NULL;
		}

		$record = 99;
		$length = 2;
		$header = pack('vv', $record, $length);
		$data = pack('v', 1);
		$this->_append($header . $data);
	}

	private function _writePassword()
	{
		if (!$this->_phpSheet->getProtection()->getSheet() || !$this->_phpSheet->getProtection()->getPassword()) {
			return NULL;
		}

		$record = 19;
		$length = 2;
		$wPassword = hexdec($this->_phpSheet->getProtection()->getPassword());
		$header = pack('vv', $record, $length);
		$data = pack('v', $wPassword);
		$this->_append($header . $data);
	}

	public function insertBitmap($row, $col, $bitmap, $x = 0, $y = 0, $scale_x = 1, $scale_y = 1)
	{
		$bitmap_array = (is_resource($bitmap) ? $this->_processBitmapGd($bitmap) : $this->_processBitmap($bitmap));
		list($width, $height, $size, $data) = $bitmap_array;
		$width *= $scale_x;
		$height *= $scale_y;
		$this->_positionImage($col, $row, $x, $y, $width, $height);
		$record = 127;
		$length = 8 + $size;
		$cf = 9;
		$env = 1;
		$lcb = $size;
		$header = pack('vvvvV', $record, $length, $cf, $env, $lcb);
		$this->_append($header . $data);
	}

	public function _positionImage($col_start, $row_start, $x1, $y1, $width, $height)
	{
		$col_end = $col_start;
		$row_end = $row_start;

		if (PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, PHPExcel_Cell::stringFromColumnIndex($col_start)) <= $x1) {
			$x1 = 0;
		}

		if (PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $row_start + 1) <= $y1) {
			$y1 = 0;
		}

		$width = ($width + $x1) - 1;
		$height = ($height + $y1) - 1;

		while (PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, PHPExcel_Cell::stringFromColumnIndex($col_end)) <= $width) {
			$width -= PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, PHPExcel_Cell::stringFromColumnIndex($col_end));
			++$col_end;
		}

		while (PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $row_end + 1) <= $height) {
			$height -= PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $row_end + 1);
			++$row_end;
		}

		if (PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, PHPExcel_Cell::stringFromColumnIndex($col_start)) == 0) {
			return NULL;
		}

		if (PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, PHPExcel_Cell::stringFromColumnIndex($col_end)) == 0) {
			return NULL;
		}

		if (PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $row_start + 1) == 0) {
			return NULL;
		}

		if (PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $row_end + 1) == 0) {
			return NULL;
		}

		$x1 = ($x1 / PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, PHPExcel_Cell::stringFromColumnIndex($col_start))) * 1024;
		$y1 = ($y1 / PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $row_start + 1)) * 256;
		$x2 = ($width / PHPExcel_Shared_Excel5::sizeCol($this->_phpSheet, PHPExcel_Cell::stringFromColumnIndex($col_end))) * 1024;
		$y2 = ($height / PHPExcel_Shared_Excel5::sizeRow($this->_phpSheet, $row_end + 1)) * 256;
		$this->_writeObjPicture($col_start, $x1, $row_start, $y1, $col_end, $x2, $row_end, $y2);
	}

	private function _writeObjPicture($colL, $dxL, $rwT, $dyT, $colR, $dxR, $rwB, $dyB)
	{
		$record = 93;
		$length = 60;
		$cObj = 1;
		$OT = 8;
		$id = 1;
		$grbit = 1556;
		$cbMacro = 0;
		$Reserved1 = 0;
		$Reserved2 = 0;
		$icvBack = 9;
		$icvFore = 9;
		$fls = 0;
		$fAuto = 0;
		$icv = 8;
		$lns = 255;
		$lnw = 1;
		$fAutoB = 0;
		$frs = 0;
		$cf = 9;
		$Reserved3 = 0;
		$cbPictFmla = 0;
		$Reserved4 = 0;
		$grbit2 = 1;
		$Reserved5 = 0;
		$header = pack('vv', $record, $length);
		$data = pack('V', $cObj);
		$data .= pack('v', $OT);
		$data .= pack('v', $id);
		$data .= pack('v', $grbit);
		$data .= pack('v', $colL);
		$data .= pack('v', $dxL);
		$data .= pack('v', $rwT);
		$data .= pack('v', $dyT);
		$data .= pack('v', $colR);
		$data .= pack('v', $dxR);
		$data .= pack('v', $rwB);
		$data .= pack('v', $dyB);
		$data .= pack('v', $cbMacro);
		$data .= pack('V', $Reserved1);
		$data .= pack('v', $Reserved2);
		$data .= pack('C', $icvBack);
		$data .= pack('C', $icvFore);
		$data .= pack('C', $fls);
		$data .= pack('C', $fAuto);
		$data .= pack('C', $icv);
		$data .= pack('C', $lns);
		$data .= pack('C', $lnw);
		$data .= pack('C', $fAutoB);
		$data .= pack('v', $frs);
		$data .= pack('V', $cf);
		$data .= pack('v', $Reserved3);
		$data .= pack('v', $cbPictFmla);
		$data .= pack('v', $Reserved4);
		$data .= pack('v', $grbit2);
		$data .= pack('V', $Reserved5);
		$this->_append($header . $data);
	}

	public function _processBitmapGd($image)
	{
		$width = imagesx($image);
		$height = imagesy($image);
		$data = pack('Vvvvv', 12, $width, $height, 1, 24);

		for ($j = $height; $j--; ) {
			for ($i = 0; $i < $width; ++$i) {
				$color = imagecolorsforindex($image, imagecolorat($image, $i, $j));

				foreach (array('red', 'green', 'blue') as $key) {
					$color[$key] = $color[$key] + round(((255 - $color[$key]) * $color['alpha']) / 127);
				}

				$data .= chr($color['blue']) . chr($color['green']) . chr($color['red']);
			}

			if ((3 * $width) % 4) {
				$data .= str_repeat("\x00", 4 - ((3 * $width) % 4));
			}
		}

		return array($width, $height, strlen($data), $data);
	}

	public function _processBitmap($bitmap)
	{
		$bmp_fd = @fopen($bitmap, 'rb');

		if (!$bmp_fd) {
			throw new PHPExcel_Writer_Exception('Couldn\'t import ' . $bitmap);
		}

		$data = fread($bmp_fd, filesize($bitmap));

		if (strlen($data) <= 54) {
			throw new PHPExcel_Writer_Exception($bitmap . " doesn't contain enough data.\n");
		}

		$identity = unpack('A2ident', $data);

		if ($identity['ident'] != 'BM') {
			throw new PHPExcel_Writer_Exception($bitmap . " doesn't appear to be a valid bitmap image.\n");
		}

		$data = substr($data, 2);
		$size_array = unpack('Vsa', substr($data, 0, 4));
		$size = $size_array['sa'];
		$data = substr($data, 4);
		$size -= 54;
		$size += 12;
		$data = substr($data, 12);
		$width_and_height = unpack('V2', substr($data, 0, 8));
		$width = $width_and_height[1];
		$height = $width_and_height[2];
		$data = substr($data, 8);

		if (65535 < $width) {
			throw new PHPExcel_Writer_Exception($bitmap . ": largest image width supported is 65k.\n");
		}

		if (65535 < $height) {
			throw new PHPExcel_Writer_Exception($bitmap . ": largest image height supported is 65k.\n");
		}

		$planes_and_bitcount = unpack('v2', substr($data, 0, 4));
		$data = substr($data, 4);

		if ($planes_and_bitcount[2] != 24) {
			throw new PHPExcel_Writer_Exception($bitmap . " isn't a 24bit true color bitmap.\n");
		}

		if ($planes_and_bitcount[1] != 1) {
			throw new PHPExcel_Writer_Exception($bitmap . ": only 1 plane supported in bitmap image.\n");
		}

		$compression = unpack('Vcomp', substr($data, 0, 4));
		$data = substr($data, 4);

		if ($compression['comp'] != 0) {
			throw new PHPExcel_Writer_Exception($bitmap . ": compression not supported in bitmap image.\n");
		}

		$data = substr($data, 20);
		$header = pack('Vvvvv', 12, $width, $height, 1, 24);
		$data = $header . $data;
		return array($width, $height, $size, $data);
	}

	private function _writeZoom()
	{
		if ($this->_phpSheet->getSheetView()->getZoomScale() == 100) {
			return NULL;
		}

		$record = 160;
		$length = 4;
		$header = pack('vv', $record, $length);
		$data = pack('vv', $this->_phpSheet->getSheetView()->getZoomScale(), 100);
		$this->_append($header . $data);
	}

	public function getEscher()
	{
		return $this->_escher;
	}

	public function setEscher(PHPExcel_Shared_Escher $pValue = NULL)
	{
		$this->_escher = $pValue;
	}

	private function _writeMsoDrawing()
	{
		if (isset($this->_escher)) {
			$writer = new PHPExcel_Writer_Excel5_Escher($this->_escher);
			$data = $writer->close();
			$spOffsets = $writer->getSpOffsets();
			$spTypes = $writer->getSpTypes();
			$spOffsets[0] = 0;
			$nm = count($spOffsets) - 1;

			for ($i = 1; $i <= $nm; ++$i) {
				$record = 236;
				$dataChunk = substr($data, $spOffsets[$i - 1], $spOffsets[$i] - $spOffsets[$i - 1]);
				$length = strlen($dataChunk);
				$header = pack('vv', $record, $length);
				$this->_append($header . $dataChunk);
				$record = 93;
				$objData = '';

				if ($spTypes[$i] == 201) {
					$objData .= pack('vvvvvVVV', 21, 18, 20, $i, 8449, 0, 0, 0);
					$objData .= pack('vv', 12, 20);
					$objData .= pack('H*', '0000000000000000640001000A00000010000100');
					$objData .= pack('vv', 19, 8174);
					$objData .= pack('H*', '00000000010001030000020008005700');
				}
				else {
					$objData .= pack('vvvvvVVV', 21, 18, 8, $i, 24593, 0, 0, 0);
				}

				$objData .= pack('vv', 0, 0);
				$length = strlen($objData);
				$header = pack('vv', $record, $length);
				$this->_append($header . $objData);
			}
		}
	}

	private function _writeDataValidity()
	{
		$dataValidationCollection = $this->_phpSheet->getDataValidationCollection();

		if (!empty($dataValidationCollection)) {
			$record = 434;
			$length = 18;
			$grbit = 0;
			$horPos = 0;
			$verPos = 0;
			$objId = 4294967295;
			$header = pack('vv', $record, $length);
			$data = pack('vVVVV', $grbit, $horPos, $verPos, $objId, count($dataValidationCollection));
			$this->_append($header . $data);
			$record = 446;

			foreach ($dataValidationCollection as $cellCoordinate => $dataValidation) {
				$data = '';
				$options = 0;
				$type = $dataValidation->getType();

				switch ($type) {
				case PHPExcel_Cell_DataValidation::TYPE_NONE:
					$type = 0;
					break;

				case PHPExcel_Cell_DataValidation::TYPE_WHOLE:
					$type = 1;
					break;

				case PHPExcel_Cell_DataValidation::TYPE_DECIMAL:
					$type = 2;
					break;

				case PHPExcel_Cell_DataValidation::TYPE_LIST:
					$type = 3;
					break;

				case PHPExcel_Cell_DataValidation::TYPE_DATE:
					$type = 4;
					break;

				case PHPExcel_Cell_DataValidation::TYPE_TIME:
					$type = 5;
					break;

				case PHPExcel_Cell_DataValidation::TYPE_TEXTLENGTH:
					$type = 6;
					break;

				case PHPExcel_Cell_DataValidation::TYPE_CUSTOM:
					$type = 7;
					break;
				}

				$options |= $type << 0;
				$errorStyle = $dataValidation->getType();

				switch ($errorStyle) {
				case PHPExcel_Cell_DataValidation::STYLE_STOP:
					$errorStyle = 0;
					break;

				case PHPExcel_Cell_DataValidation::STYLE_WARNING:
					$errorStyle = 1;
					break;

				case PHPExcel_Cell_DataValidation::STYLE_INFORMATION:
					$errorStyle = 2;
					break;
				}

				$options |= $errorStyle << 4;
				if (($type == 3) && preg_match('/^\\".*\\"$/', $dataValidation->getFormula1())) {
					$options |= 1 << 7;
				}

				$options |= $dataValidation->getAllowBlank() << 8;
				$options |= !$dataValidation->getShowDropDown() << 9;
				$options |= $dataValidation->getShowInputMessage() << 18;
				$options |= $dataValidation->getShowErrorMessage() << 19;
				$operator = $dataValidation->getOperator();

				switch ($operator) {
				case PHPExcel_Cell_DataValidation::OPERATOR_BETWEEN:
					$operator = 0;
					break;

				case PHPExcel_Cell_DataValidation::OPERATOR_NOTBETWEEN:
					$operator = 1;
					break;

				case PHPExcel_Cell_DataValidation::OPERATOR_EQUAL:
					$operator = 2;
					break;

				case PHPExcel_Cell_DataValidation::OPERATOR_NOTEQUAL:
					$operator = 3;
					break;

				case PHPExcel_Cell_DataValidation::OPERATOR_GREATERTHAN:
					$operator = 4;
					break;

				case PHPExcel_Cell_DataValidation::OPERATOR_LESSTHAN:
					$operator = 5;
					break;

				case PHPExcel_Cell_DataValidation::OPERATOR_GREATERTHANOREQUAL:
					$operator = 6;
					break;

				case PHPExcel_Cell_DataValidation::OPERATOR_LESSTHANOREQUAL:
					$operator = 7;
					break;
				}

				$options |= $operator << 20;
				$data = pack('V', $options);
				$promptTitle = ($dataValidation->getPromptTitle() !== '' ? $dataValidation->getPromptTitle() : chr(0));
				$data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($promptTitle);
				$errorTitle = ($dataValidation->getErrorTitle() !== '' ? $dataValidation->getErrorTitle() : chr(0));
				$data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($errorTitle);
				$prompt = ($dataValidation->getPrompt() !== '' ? $dataValidation->getPrompt() : chr(0));
				$data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($prompt);
				$error = ($dataValidation->getError() !== '' ? $dataValidation->getError() : chr(0));
				$data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($error);

				try {
					$formula1 = $dataValidation->getFormula1();

					if ($type == 3) {
						$formula1 = str_replace(',', chr(0), $formula1);
					}

					$this->_parser->parse($formula1);
					$formula1 = $this->_parser->toReversePolish();
					$sz1 = strlen($formula1);
				}
				catch (PHPExcel_Exception $e) {
					$sz1 = 0;
					$formula1 = '';
				}

				$data .= pack('vv', $sz1, 0);
				$data .= $formula1;

				try {
					$formula2 = $dataValidation->getFormula2();

					if ($formula2 === '') {
						throw new PHPExcel_Writer_Exception('No formula2');
					}

					$this->_parser->parse($formula2);
					$formula2 = $this->_parser->toReversePolish();
					$sz2 = strlen($formula2);
				}
				catch (PHPExcel_Exception $e) {
					$sz2 = 0;
					$formula2 = '';
				}

				$data .= pack('vv', $sz2, 0);
				$data .= $formula2;
				$data .= pack('v', 1);
				$data .= $this->_writeBIFF8CellRangeAddressFixed($cellCoordinate);
				$length = strlen($data);
				$header = pack('vv', $record, $length);
				$this->_append($header . $data);
			}
		}
	}

	static private function _mapErrorCode($errorCode)
	{
		switch ($errorCode) {
		case '#NULL!':
			return 0;
		case '#DIV/0!':
			return 7;
		case '#VALUE!':
			return 15;
		case '#REF!':
			return 23;
		case '#NAME?':
			return 29;
		case '#NUM!':
			return 36;
		case '#N/A':
			return 42;
		}

		return 0;
	}

	private function _writePageLayoutView()
	{
		$record = 2187;
		$length = 16;
		$rt = 2187;
		$grbitFrt = 0;
		$reserved = 0;
		$wScalvePLV = $this->_phpSheet->getSheetView()->getZoomScale();

		if ($this->_phpSheet->getSheetView()->getView() == PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_LAYOUT) {
			$fPageLayoutView = 1;
		}
		else {
			$fPageLayoutView = 0;
		}

		$fRulerVisible = 0;
		$fWhitespaceHidden = 0;
		$grbit = $fPageLayoutView;
		$grbit |= $fRulerVisible << 1;
		$grbit |= $fWhitespaceHidden << 3;
		$header = pack('vv', $record, $length);
		$data = pack('vvVVvv', $rt, $grbitFrt, 0, 0, $wScalvePLV, $grbit);
		$this->_append($header . $data);
	}

	private function _writeCFRule(PHPExcel_Style_Conditional $conditional)
	{
		$record = 433;

		if ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_EXPRESSION) {
			$type = 2;
			$operatorType = 0;
		}
		else if ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CELLIS) {
			$type = 1;

			switch ($conditional->getOperatorType()) {
			case PHPExcel_Style_Conditional::OPERATOR_NONE:
				$operatorType = 0;
				break;

			case PHPExcel_Style_Conditional::OPERATOR_EQUAL:
				$operatorType = 3;
				break;

			case PHPExcel_Style_Conditional::OPERATOR_GREATERTHAN:
				$operatorType = 5;
				break;

			case PHPExcel_Style_Conditional::OPERATOR_GREATERTHANOREQUAL:
				$operatorType = 7;
				break;

			case PHPExcel_Style_Conditional::OPERATOR_LESSTHAN:
				$operatorType = 6;
				break;

			case PHPExcel_Style_Conditional::OPERATOR_LESSTHANOREQUAL:
				$operatorType = 8;
				break;

			case PHPExcel_Style_Conditional::OPERATOR_NOTEQUAL:
				$operatorType = 4;
				break;

			case PHPExcel_Style_Conditional::OPERATOR_BETWEEN:
				$operatorType = 1;
				break;
			}
		}

		$arrConditions = $conditional->getConditions();
		$numConditions = sizeof($arrConditions);

		if ($numConditions == 1) {
			$szValue1 = ($arrConditions[0] <= 65535 ? 3 : 0);
			$szValue2 = 0;
			$operand1 = pack('Cv', 30, $arrConditions[0]);
			$operand2 = NULL;
		}
		else {
			if (($numConditions == 2) && ($conditional->getOperatorType() == PHPExcel_Style_Conditional::OPERATOR_BETWEEN)) {
				$szValue1 = ($arrConditions[0] <= 65535 ? 3 : 0);
				$szValue2 = ($arrConditions[1] <= 65535 ? 3 : 0);
				$operand1 = pack('Cv', 30, $arrConditions[0]);
				$operand2 = pack('Cv', 30, $arrConditions[1]);
			}
			else {
				$szValue1 = 0;
				$szValue2 = 0;
				$operand1 = NULL;
				$operand2 = NULL;
			}
		}

		$bAlignHz = ($conditional->getStyle()->getAlignment()->getHorizontal() == NULL ? 1 : 0);
		$bAlignVt = ($conditional->getStyle()->getAlignment()->getVertical() == NULL ? 1 : 0);
		$bAlignWrapTx = ($conditional->getStyle()->getAlignment()->getWrapText() == false ? 1 : 0);
		$bTxRotation = ($conditional->getStyle()->getAlignment()->getTextRotation() == NULL ? 1 : 0);
		$bIndent = ($conditional->getStyle()->getAlignment()->getIndent() == 0 ? 1 : 0);
		$bShrinkToFit = ($conditional->getStyle()->getAlignment()->getShrinkToFit() == false ? 1 : 0);
		if (($bAlignHz == 0) || ($bAlignVt == 0) || ($bAlignWrapTx == 0) || ($bTxRotation == 0) || ($bIndent == 0) || ($bShrinkToFit == 0)) {
			$bFormatAlign = 1;
		}
		else {
			$bFormatAlign = 0;
		}

		$bProtLocked = ($conditional->getStyle()->getProtection()->getLocked() == NULL ? 1 : 0);
		$bProtHidden = ($conditional->getStyle()->getProtection()->getHidden() == NULL ? 1 : 0);
		if (($bProtLocked == 0) || ($bProtHidden == 0)) {
			$bFormatProt = 1;
		}
		else {
			$bFormatProt = 0;
		}

		$bBorderLeft = (($conditional->getStyle()->getBorders()->getLeft()->getColor()->getARGB() == PHPExcel_Style_Color::COLOR_BLACK) && ($conditional->getStyle()->getBorders()->getLeft()->getBorderStyle() == PHPExcel_Style_Border::BORDER_NONE) ? 1 : 0);
		$bBorderRight = (($conditional->getStyle()->getBorders()->getRight()->getColor()->getARGB() == PHPExcel_Style_Color::COLOR_BLACK) && ($conditional->getStyle()->getBorders()->getRight()->getBorderStyle() == PHPExcel_Style_Border::BORDER_NONE) ? 1 : 0);
		$bBorderTop = (($conditional->getStyle()->getBorders()->getTop()->getColor()->getARGB() == PHPExcel_Style_Color::COLOR_BLACK) && ($conditional->getStyle()->getBorders()->getTop()->getBorderStyle() == PHPExcel_Style_Border::BORDER_NONE) ? 1 : 0);
		$bBorderBottom = (($conditional->getStyle()->getBorders()->getBottom()->getColor()->getARGB() == PHPExcel_Style_Color::COLOR_BLACK) && ($conditional->getStyle()->getBorders()->getBottom()->getBorderStyle() == PHPExcel_Style_Border::BORDER_NONE) ? 1 : 0);
		if (($bBorderLeft == 0) || ($bBorderRight == 0) || ($bBorderTop == 0) || ($bBorderBottom == 0)) {
			$bFormatBorder = 1;
		}
		else {
			$bFormatBorder = 0;
		}

		$bFillStyle = ($conditional->getStyle()->getFill()->getFillType() == NULL ? 0 : 1);
		$bFillColor = ($conditional->getStyle()->getFill()->getStartColor()->getARGB() == NULL ? 0 : 1);
		$bFillColorBg = ($conditional->getStyle()->getFill()->getEndColor()->getARGB() == NULL ? 0 : 1);
		if (($bFillStyle == 0) || ($bFillColor == 0) || ($bFillColorBg == 0)) {
			$bFormatFill = 1;
		}
		else {
			$bFormatFill = 0;
		}

		if (($conditional->getStyle()->getFont()->getName() != NULL) || ($conditional->getStyle()->getFont()->getSize() != NULL) || ($conditional->getStyle()->getFont()->getBold() != NULL) || ($conditional->getStyle()->getFont()->getItalic() != NULL) || ($conditional->getStyle()->getFont()->getSuperScript() != NULL) || ($conditional->getStyle()->getFont()->getSubScript() != NULL) || ($conditional->getStyle()->getFont()->getUnderline() != NULL) || ($conditional->getStyle()->getFont()->getStrikethrough() != NULL) || ($conditional->getStyle()->getFont()->getColor()->getARGB() != NULL)) {
			$bFormatFont = 1;
		}
		else {
			$bFormatFont = 0;
		}

		$flags = 0;
		$flags |= (1 == $bAlignHz ? 1 : 0);
		$flags |= (1 == $bAlignVt ? 2 : 0);
		$flags |= (1 == $bAlignWrapTx ? 4 : 0);
		$flags |= (1 == $bTxRotation ? 8 : 0);
		$flags |= (1 == 1 ? 16 : 0);
		$flags |= (1 == $bIndent ? 32 : 0);
		$flags |= (1 == $bShrinkToFit ? 64 : 0);
		$flags |= (1 == 1 ? 128 : 0);
		$flags |= (1 == $bProtLocked ? 256 : 0);
		$flags |= (1 == $bProtHidden ? 512 : 0);
		$flags |= (1 == $bBorderLeft ? 1024 : 0);
		$flags |= (1 == $bBorderRight ? 2048 : 0);
		$flags |= (1 == $bBorderTop ? 4096 : 0);
		$flags |= (1 == $bBorderBottom ? 8192 : 0);
		$flags |= (1 == 1 ? 16384 : 0);
		$flags |= (1 == 1 ? 32768 : 0);
		$flags |= (1 == $bFillStyle ? 65536 : 0);
		$flags |= (1 == $bFillColor ? 131072 : 0);
		$flags |= (1 == $bFillColorBg ? 262144 : 0);
		$flags |= (1 == 1 ? 3670016 : 0);
		$flags |= (1 == $bFormatFont ? 67108864 : 0);
		$flags |= (1 == $bFormatAlign ? 134217728 : 0);
		$flags |= (1 == $bFormatBorder ? 268435456 : 0);
		$flags |= (1 == $bFormatFill ? 536870912 : 0);
		$flags |= (1 == $bFormatProt ? 1073741824 : 0);
		$flags |= (1 == 0 ? 2147483648 : 0);

		if ($bFormatFont == 1) {
			if ($conditional->getStyle()->getFont()->getName() == NULL) {
				$dataBlockFont = pack('VVVVVVVV', 0, 0, 0, 0, 0, 0, 0, 0);
				$dataBlockFont .= pack('VVVVVVVV', 0, 0, 0, 0, 0, 0, 0, 0);
			}
			else {
				$dataBlockFont = PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($conditional->getStyle()->getFont()->getName());
			}

			if ($conditional->getStyle()->getFont()->getSize() == NULL) {
				$dataBlockFont .= pack('V', 20 * 11);
			}
			else {
				$dataBlockFont .= pack('V', 20 * $conditional->getStyle()->getFont()->getSize());
			}

			$dataBlockFont .= pack('V', 0);

			if ($conditional->getStyle()->getFont()->getBold() == true) {
				$dataBlockFont .= pack('v', 700);
			}
			else {
				$dataBlockFont .= pack('v', 400);
			}

			if ($conditional->getStyle()->getFont()->getSubScript() == true) {
				$dataBlockFont .= pack('v', 2);
				$fontEscapement = 0;
			}
			else if ($conditional->getStyle()->getFont()->getSuperScript() == true) {
				$dataBlockFont .= pack('v', 1);
				$fontEscapement = 0;
			}
			else {
				$dataBlockFont .= pack('v', 0);
				$fontEscapement = 1;
			}

			switch ($conditional->getStyle()->getFont()->getUnderline()) {
			case PHPExcel_Style_Font::UNDERLINE_NONE:
				$dataBlockFont .= pack('C', 0);
				$fontUnderline = 0;
				break;

			case PHPExcel_Style_Font::UNDERLINE_DOUBLE:
				$dataBlockFont .= pack('C', 2);
				$fontUnderline = 0;
				break;

			case PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING:
				$dataBlockFont .= pack('C', 34);
				$fontUnderline = 0;
				break;

			case PHPExcel_Style_Font::UNDERLINE_SINGLE:
				$dataBlockFont .= pack('C', 1);
				$fontUnderline = 0;
				break;

			case PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING:
				$dataBlockFont .= pack('C', 33);
				$fontUnderline = 0;
				break;

			default:
				$dataBlockFont .= pack('C', 0);
				$fontUnderline = 1;
				break;
			}

			$dataBlockFont .= pack('vC', 0, 0);

			switch ($conditional->getStyle()->getFont()->getColor()->getRGB()) {
			case '000000':
				$colorIdx = 8;
				break;

			case 'FFFFFF':
				$colorIdx = 9;
				break;

			case 'FF0000':
				$colorIdx = 10;
				break;

			case '00FF00':
				$colorIdx = 11;
				break;

			case '0000FF':
				$colorIdx = 12;
				break;

			case 'FFFF00':
				$colorIdx = 13;
				break;

			case 'FF00FF':
				$colorIdx = 14;
				break;

			case '00FFFF':
				$colorIdx = 15;
				break;

			case '800000':
				$colorIdx = 16;
				break;

			case '008000':
				$colorIdx = 17;
				break;

			case '000080':
				$colorIdx = 18;
				break;

			case '808000':
				$colorIdx = 19;
				break;

			case '800080':
				$colorIdx = 20;
				break;

			case '008080':
				$colorIdx = 21;
				break;

			case 'C0C0C0':
				$colorIdx = 22;
				break;

			case '808080':
				$colorIdx = 23;
				break;

			case '9999FF':
				$colorIdx = 24;
				break;

			case '993366':
				$colorIdx = 25;
				break;

			case 'FFFFCC':
				$colorIdx = 26;
				break;

			case 'CCFFFF':
				$colorIdx = 27;
				break;

			case '660066':
				$colorIdx = 28;
				break;

			case 'FF8080':
				$colorIdx = 29;
				break;

			case '0066CC':
				$colorIdx = 30;
				break;

			case 'CCCCFF':
				$colorIdx = 31;
				break;

			case '000080':
				$colorIdx = 32;
				break;

			case 'FF00FF':
				$colorIdx = 33;
				break;

			case 'FFFF00':
				$colorIdx = 34;
				break;

			case '00FFFF':
				$colorIdx = 35;
				break;

			case '800080':
				$colorIdx = 36;
				break;

			case '800000':
				$colorIdx = 37;
				break;

			case '008080':
				$colorIdx = 38;
				break;

			case '0000FF':
				$colorIdx = 39;
				break;

			case '00CCFF':
				$colorIdx = 40;
				break;

			case 'CCFFFF':
				$colorIdx = 41;
				break;

			case 'CCFFCC':
				$colorIdx = 42;
				break;

			case 'FFFF99':
				$colorIdx = 43;
				break;

			case '99CCFF':
				$colorIdx = 44;
				break;

			case 'FF99CC':
				$colorIdx = 45;
				break;

			case 'CC99FF':
				$colorIdx = 46;
				break;

			case 'FFCC99':
				$colorIdx = 47;
				break;

			case '3366FF':
				$colorIdx = 48;
				break;

			case '33CCCC':
				$colorIdx = 49;
				break;

			case '99CC00':
				$colorIdx = 50;
				break;

			case 'FFCC00':
				$colorIdx = 51;
				break;

			case 'FF9900':
				$colorIdx = 52;
				break;

			case 'FF6600':
				$colorIdx = 53;
				break;

			case '666699':
				$colorIdx = 54;
				break;

			case '969696':
				$colorIdx = 55;
				break;

			case '003366':
				$colorIdx = 56;
				break;

			case '339966':
				$colorIdx = 57;
				break;

			case '003300':
				$colorIdx = 58;
				break;

			case '333300':
				$colorIdx = 59;
				break;

			case '993300':
				$colorIdx = 60;
				break;

			case '993366':
				$colorIdx = 61;
				break;

			case '333399':
				$colorIdx = 62;
				break;

			case '333333':
				$colorIdx = 63;
				break;

			default:
				$colorIdx = 0;
				break;
			}

			$dataBlockFont .= pack('V', $colorIdx);
			$dataBlockFont .= pack('V', 0);
			$optionsFlags = 0;
			$optionsFlagsBold = ($conditional->getStyle()->getFont()->getBold() == NULL ? 1 : 0);
			$optionsFlags |= (1 == $optionsFlagsBold ? 2 : 0);
			$optionsFlags |= (1 == 1 ? 8 : 0);
			$optionsFlags |= (1 == 1 ? 16 : 0);
			$optionsFlags |= (1 == 0 ? 32 : 0);
			$optionsFlags |= (1 == 1 ? 128 : 0);
			$dataBlockFont .= pack('V', $optionsFlags);
			$dataBlockFont .= pack('V', $fontEscapement);
			$dataBlockFont .= pack('V', $fontUnderline);
			$dataBlockFont .= pack('V', 0);
			$dataBlockFont .= pack('V', 0);
			$dataBlockFont .= pack('VV', 0, 0);
			$dataBlockFont .= pack('v', 1);
		}

		if ($bFormatAlign == 1) {
			$blockAlign = 0;

			switch ($conditional->getStyle()->getAlignment()->getHorizontal()) {
			case PHPExcel_Style_Alignment::HORIZONTAL_GENERAL:
				$blockAlign = 0;
				break;

			case PHPExcel_Style_Alignment::HORIZONTAL_LEFT:
				$blockAlign = 1;
				break;

			case PHPExcel_Style_Alignment::HORIZONTAL_RIGHT:
				$blockAlign = 3;
				break;

			case PHPExcel_Style_Alignment::HORIZONTAL_CENTER:
				$blockAlign = 2;
				break;

			case PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS:
				$blockAlign = 6;
				break;

			case PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY:
				$blockAlign = 5;
				break;
			}

			if ($conditional->getStyle()->getAlignment()->getWrapText() == true) {
				$blockAlign |= 1 << 3;
			}
			else {
				$blockAlign |= 0 << 3;
			}

			switch ($conditional->getStyle()->getAlignment()->getVertical()) {
			case PHPExcel_Style_Alignment::VERTICAL_BOTTOM:
				$blockAlign = 2 << 4;
				break;

			case PHPExcel_Style_Alignment::VERTICAL_TOP:
				$blockAlign = 0 << 4;
				break;

			case PHPExcel_Style_Alignment::VERTICAL_CENTER:
				$blockAlign = 1 << 4;
				break;

			case PHPExcel_Style_Alignment::VERTICAL_JUSTIFY:
				$blockAlign = 3 << 4;
				break;
			}

			$blockAlign |= 0 << 7;
			$blockRotation = $conditional->getStyle()->getAlignment()->getTextRotation();
			$blockIndent = $conditional->getStyle()->getAlignment()->getIndent();

			if ($conditional->getStyle()->getAlignment()->getShrinkToFit() == true) {
				$blockIndent |= 1 << 4;
			}
			else {
				$blockIndent |= 0 << 4;
			}

			$blockIndent |= 0 << 6;
			$blockIndentRelative = 255;
			$dataBlockAlign = pack('CCvvv', $blockAlign, $blockRotation, $blockIndent, $blockIndentRelative, 0);
		}

		if ($bFormatBorder == 1) {
			$blockLineStyle = 0;

			switch ($conditional->getStyle()->getBorders()->getLeft()->getBorderStyle()) {
			case PHPExcel_Style_Border::BORDER_NONE:
				$blockLineStyle |= 0;
				break;

			case PHPExcel_Style_Border::BORDER_THIN:
				$blockLineStyle |= 1;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUM:
				$blockLineStyle |= 2;
				break;

			case PHPExcel_Style_Border::BORDER_DASHED:
				$blockLineStyle |= 3;
				break;

			case PHPExcel_Style_Border::BORDER_DOTTED:
				$blockLineStyle |= 4;
				break;

			case PHPExcel_Style_Border::BORDER_THICK:
				$blockLineStyle |= 5;
				break;

			case PHPExcel_Style_Border::BORDER_DOUBLE:
				$blockLineStyle |= 6;
				break;

			case PHPExcel_Style_Border::BORDER_HAIR:
				$blockLineStyle |= 7;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHED:
				$blockLineStyle |= 8;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOT:
				$blockLineStyle |= 9;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT:
				$blockLineStyle |= 10;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOTDOT:
				$blockLineStyle |= 11;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT:
				$blockLineStyle |= 12;
				break;

			case PHPExcel_Style_Border::BORDER_SLANTDASHDOT:
				$blockLineStyle |= 13;
				break;
			}

			switch ($conditional->getStyle()->getBorders()->getRight()->getBorderStyle()) {
			case PHPExcel_Style_Border::BORDER_NONE:
				$blockLineStyle |= 0 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_THIN:
				$blockLineStyle |= 1 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUM:
				$blockLineStyle |= 2 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_DASHED:
				$blockLineStyle |= 3 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_DOTTED:
				$blockLineStyle |= 4 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_THICK:
				$blockLineStyle |= 5 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_DOUBLE:
				$blockLineStyle |= 6 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_HAIR:
				$blockLineStyle |= 7 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHED:
				$blockLineStyle |= 8 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOT:
				$blockLineStyle |= 9 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT:
				$blockLineStyle |= 10 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOTDOT:
				$blockLineStyle |= 11 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT:
				$blockLineStyle |= 12 << 4;
				break;

			case PHPExcel_Style_Border::BORDER_SLANTDASHDOT:
				$blockLineStyle |= 13 << 4;
				break;
			}

			switch ($conditional->getStyle()->getBorders()->getTop()->getBorderStyle()) {
			case PHPExcel_Style_Border::BORDER_NONE:
				$blockLineStyle |= 0 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_THIN:
				$blockLineStyle |= 1 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUM:
				$blockLineStyle |= 2 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_DASHED:
				$blockLineStyle |= 3 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_DOTTED:
				$blockLineStyle |= 4 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_THICK:
				$blockLineStyle |= 5 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_DOUBLE:
				$blockLineStyle |= 6 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_HAIR:
				$blockLineStyle |= 7 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHED:
				$blockLineStyle |= 8 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOT:
				$blockLineStyle |= 9 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT:
				$blockLineStyle |= 10 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOTDOT:
				$blockLineStyle |= 11 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT:
				$blockLineStyle |= 12 << 8;
				break;

			case PHPExcel_Style_Border::BORDER_SLANTDASHDOT:
				$blockLineStyle |= 13 << 8;
				break;
			}

			switch ($conditional->getStyle()->getBorders()->getBottom()->getBorderStyle()) {
			case PHPExcel_Style_Border::BORDER_NONE:
				$blockLineStyle |= 0 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_THIN:
				$blockLineStyle |= 1 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUM:
				$blockLineStyle |= 2 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_DASHED:
				$blockLineStyle |= 3 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_DOTTED:
				$blockLineStyle |= 4 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_THICK:
				$blockLineStyle |= 5 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_DOUBLE:
				$blockLineStyle |= 6 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_HAIR:
				$blockLineStyle |= 7 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHED:
				$blockLineStyle |= 8 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOT:
				$blockLineStyle |= 9 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT:
				$blockLineStyle |= 10 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOTDOT:
				$blockLineStyle |= 11 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT:
				$blockLineStyle |= 12 << 12;
				break;

			case PHPExcel_Style_Border::BORDER_SLANTDASHDOT:
				$blockLineStyle |= 13 << 12;
				break;
			}

			$blockColor = 0;

			switch ($conditional->getStyle()->getBorders()->getDiagonal()->getBorderStyle()) {
			case PHPExcel_Style_Border::BORDER_NONE:
				$blockColor |= 0 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_THIN:
				$blockColor |= 1 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUM:
				$blockColor |= 2 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_DASHED:
				$blockColor |= 3 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_DOTTED:
				$blockColor |= 4 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_THICK:
				$blockColor |= 5 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_DOUBLE:
				$blockColor |= 6 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_HAIR:
				$blockColor |= 7 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHED:
				$blockColor |= 8 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOT:
				$blockColor |= 9 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT:
				$blockColor |= 10 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_DASHDOTDOT:
				$blockColor |= 11 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT:
				$blockColor |= 12 << 21;
				break;

			case PHPExcel_Style_Border::BORDER_SLANTDASHDOT:
				$blockColor |= 13 << 21;
				break;
			}

			$dataBlockBorder = pack('vv', $blockLineStyle, $blockColor);
		}

		if ($bFormatFill == 1) {
			$blockFillPatternStyle = 0;

			switch ($conditional->getStyle()->getFill()->getFillType()) {
			case PHPExcel_Style_Fill::FILL_NONE:
				$blockFillPatternStyle = 0;
				break;

			case PHPExcel_Style_Fill::FILL_SOLID:
				$blockFillPatternStyle = 1;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_MEDIUMGRAY:
				$blockFillPatternStyle = 2;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_DARKGRAY:
				$blockFillPatternStyle = 3;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY:
				$blockFillPatternStyle = 4;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_DARKHORIZONTAL:
				$blockFillPatternStyle = 5;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_DARKVERTICAL:
				$blockFillPatternStyle = 6;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_DARKDOWN:
				$blockFillPatternStyle = 7;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_DARKUP:
				$blockFillPatternStyle = 8;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_DARKGRID:
				$blockFillPatternStyle = 9;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_DARKTRELLIS:
				$blockFillPatternStyle = 10;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_LIGHTHORIZONTAL:
				$blockFillPatternStyle = 11;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_LIGHTVERTICAL:
				$blockFillPatternStyle = 12;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_LIGHTDOWN:
				$blockFillPatternStyle = 13;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_LIGHTUP:
				$blockFillPatternStyle = 14;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRID:
				$blockFillPatternStyle = 15;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_LIGHTTRELLIS:
				$blockFillPatternStyle = 16;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_GRAY125:
				$blockFillPatternStyle = 17;
				break;

			case PHPExcel_Style_Fill::FILL_PATTERN_GRAY0625:
				$blockFillPatternStyle = 18;
				break;

			case PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR:
				$blockFillPatternStyle = 0;
				break;

			case PHPExcel_Style_Fill::FILL_GRADIENT_PATH:
				$blockFillPatternStyle = 0;
				break;

			default:
				$blockFillPatternStyle = 0;
				break;
			}

			switch ($conditional->getStyle()->getFill()->getStartColor()->getRGB()) {
			case '000000':
				$colorIdxBg = 8;
				break;

			case 'FFFFFF':
				$colorIdxBg = 9;
				break;

			case 'FF0000':
				$colorIdxBg = 10;
				break;

			case '00FF00':
				$colorIdxBg = 11;
				break;

			case '0000FF':
				$colorIdxBg = 12;
				break;

			case 'FFFF00':
				$colorIdxBg = 13;
				break;

			case 'FF00FF':
				$colorIdxBg = 14;
				break;

			case '00FFFF':
				$colorIdxBg = 15;
				break;

			case '800000':
				$colorIdxBg = 16;
				break;

			case '008000':
				$colorIdxBg = 17;
				break;

			case '000080':
				$colorIdxBg = 18;
				break;

			case '808000':
				$colorIdxBg = 19;
				break;

			case '800080':
				$colorIdxBg = 20;
				break;

			case '008080':
				$colorIdxBg = 21;
				break;

			case 'C0C0C0':
				$colorIdxBg = 22;
				break;

			case '808080':
				$colorIdxBg = 23;
				break;

			case '9999FF':
				$colorIdxBg = 24;
				break;

			case '993366':
				$colorIdxBg = 25;
				break;

			case 'FFFFCC':
				$colorIdxBg = 26;
				break;

			case 'CCFFFF':
				$colorIdxBg = 27;
				break;

			case '660066':
				$colorIdxBg = 28;
				break;

			case 'FF8080':
				$colorIdxBg = 29;
				break;

			case '0066CC':
				$colorIdxBg = 30;
				break;

			case 'CCCCFF':
				$colorIdxBg = 31;
				break;

			case '000080':
				$colorIdxBg = 32;
				break;

			case 'FF00FF':
				$colorIdxBg = 33;
				break;

			case 'FFFF00':
				$colorIdxBg = 34;
				break;

			case '00FFFF':
				$colorIdxBg = 35;
				break;

			case '800080':
				$colorIdxBg = 36;
				break;

			case '800000':
				$colorIdxBg = 37;
				break;

			case '008080':
				$colorIdxBg = 38;
				break;

			case '0000FF':
				$colorIdxBg = 39;
				break;

			case '00CCFF':
				$colorIdxBg = 40;
				break;

			case 'CCFFFF':
				$colorIdxBg = 41;
				break;

			case 'CCFFCC':
				$colorIdxBg = 42;
				break;

			case 'FFFF99':
				$colorIdxBg = 43;
				break;

			case '99CCFF':
				$colorIdxBg = 44;
				break;

			case 'FF99CC':
				$colorIdxBg = 45;
				break;

			case 'CC99FF':
				$colorIdxBg = 46;
				break;

			case 'FFCC99':
				$colorIdxBg = 47;
				break;

			case '3366FF':
				$colorIdxBg = 48;
				break;

			case '33CCCC':
				$colorIdxBg = 49;
				break;

			case '99CC00':
				$colorIdxBg = 50;
				break;

			case 'FFCC00':
				$colorIdxBg = 51;
				break;

			case 'FF9900':
				$colorIdxBg = 52;
				break;

			case 'FF6600':
				$colorIdxBg = 53;
				break;

			case '666699':
				$colorIdxBg = 54;
				break;

			case '969696':
				$colorIdxBg = 55;
				break;

			case '003366':
				$colorIdxBg = 56;
				break;

			case '339966':
				$colorIdxBg = 57;
				break;

			case '003300':
				$colorIdxBg = 58;
				break;

			case '333300':
				$colorIdxBg = 59;
				break;

			case '993300':
				$colorIdxBg = 60;
				break;

			case '993366':
				$colorIdxBg = 61;
				break;

			case '333399':
				$colorIdxBg = 62;
				break;

			case '333333':
				$colorIdxBg = 63;
				break;

			default:
				$colorIdxBg = 65;
				break;
			}

			switch ($conditional->getStyle()->getFill()->getEndColor()->getRGB()) {
			case '000000':
				$colorIdxFg = 8;
				break;

			case 'FFFFFF':
				$colorIdxFg = 9;
				break;

			case 'FF0000':
				$colorIdxFg = 10;
				break;

			case '00FF00':
				$colorIdxFg = 11;
				break;

			case '0000FF':
				$colorIdxFg = 12;
				break;

			case 'FFFF00':
				$colorIdxFg = 13;
				break;

			case 'FF00FF':
				$colorIdxFg = 14;
				break;

			case '00FFFF':
				$colorIdxFg = 15;
				break;

			case '800000':
				$colorIdxFg = 16;
				break;

			case '008000':
				$colorIdxFg = 17;
				break;

			case '000080':
				$colorIdxFg = 18;
				break;

			case '808000':
				$colorIdxFg = 19;
				break;

			case '800080':
				$colorIdxFg = 20;
				break;

			case '008080':
				$colorIdxFg = 21;
				break;

			case 'C0C0C0':
				$colorIdxFg = 22;
				break;

			case '808080':
				$colorIdxFg = 23;
				break;

			case '9999FF':
				$colorIdxFg = 24;
				break;

			case '993366':
				$colorIdxFg = 25;
				break;

			case 'FFFFCC':
				$colorIdxFg = 26;
				break;

			case 'CCFFFF':
				$colorIdxFg = 27;
				break;

			case '660066':
				$colorIdxFg = 28;
				break;

			case 'FF8080':
				$colorIdxFg = 29;
				break;

			case '0066CC':
				$colorIdxFg = 30;
				break;

			case 'CCCCFF':
				$colorIdxFg = 31;
				break;

			case '000080':
				$colorIdxFg = 32;
				break;

			case 'FF00FF':
				$colorIdxFg = 33;
				break;

			case 'FFFF00':
				$colorIdxFg = 34;
				break;

			case '00FFFF':
				$colorIdxFg = 35;
				break;

			case '800080':
				$colorIdxFg = 36;
				break;

			case '800000':
				$colorIdxFg = 37;
				break;

			case '008080':
				$colorIdxFg = 38;
				break;

			case '0000FF':
				$colorIdxFg = 39;
				break;

			case '00CCFF':
				$colorIdxFg = 40;
				break;

			case 'CCFFFF':
				$colorIdxFg = 41;
				break;

			case 'CCFFCC':
				$colorIdxFg = 42;
				break;

			case 'FFFF99':
				$colorIdxFg = 43;
				break;

			case '99CCFF':
				$colorIdxFg = 44;
				break;

			case 'FF99CC':
				$colorIdxFg = 45;
				break;

			case 'CC99FF':
				$colorIdxFg = 46;
				break;

			case 'FFCC99':
				$colorIdxFg = 47;
				break;

			case '3366FF':
				$colorIdxFg = 48;
				break;

			case '33CCCC':
				$colorIdxFg = 49;
				break;

			case '99CC00':
				$colorIdxFg = 50;
				break;

			case 'FFCC00':
				$colorIdxFg = 51;
				break;

			case 'FF9900':
				$colorIdxFg = 52;
				break;

			case 'FF6600':
				$colorIdxFg = 53;
				break;

			case '666699':
				$colorIdxFg = 54;
				break;

			case '969696':
				$colorIdxFg = 55;
				break;

			case '003366':
				$colorIdxFg = 56;
				break;

			case '339966':
				$colorIdxFg = 57;
				break;

			case '003300':
				$colorIdxFg = 58;
				break;

			case '333300':
				$colorIdxFg = 59;
				break;

			case '993300':
				$colorIdxFg = 60;
				break;

			case '993366':
				$colorIdxFg = 61;
				break;

			case '333399':
				$colorIdxFg = 62;
				break;

			case '333333':
				$colorIdxFg = 63;
				break;

			default:
				$colorIdxFg = 64;
				break;
			}

			$dataBlockFill = pack('v', $blockFillPatternStyle);
			$dataBlockFill .= pack('v', $colorIdxFg | ($colorIdxBg << 7));
		}

		if ($bFormatProt == 1) {
			$dataBlockProtection = 0;

			if ($conditional->getStyle()->getProtection()->getLocked() == PHPExcel_Style_Protection::PROTECTION_PROTECTED) {
				$dataBlockProtection = 1;
			}

			if ($conditional->getStyle()->getProtection()->getHidden() == PHPExcel_Style_Protection::PROTECTION_PROTECTED) {
				$dataBlockProtection = 1 << 1;
			}
		}

		$data = pack('CCvvVv', $type, $operatorType, $szValue1, $szValue2, $flags, 0);

		if ($bFormatFont == 1) {
			$data .= $dataBlockFont;
		}

		if ($bFormatAlign == 1) {
			$data .= $dataBlockAlign;
		}

		if ($bFormatBorder == 1) {
			$data .= $dataBlockBorder;
		}

		if ($bFormatFill == 1) {
			$data .= $dataBlockFill;
		}

		if ($bFormatProt == 1) {
			$data .= $dataBlockProtection;
		}

		if (!is_null($operand1)) {
			$data .= $operand1;
		}

		if (!is_null($operand2)) {
			$data .= $operand2;
		}

		$header = pack('vv', $record, strlen($data));
		$this->_append($header . $data);
	}

	private function _writeCFHeader()
	{
		$record = 432;
		$length = 22;
		$numColumnMin = NULL;
		$numColumnMax = NULL;
		$numRowMin = NULL;
		$numRowMax = NULL;
		$arrConditional = array();

		foreach ($this->_phpSheet->getConditionalStylesCollection() as $cellCoordinate => $conditionalStyles) {
			foreach ($conditionalStyles as $conditional) {
				if (($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_EXPRESSION) || ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CELLIS)) {
					if (!in_array($conditional->getHashCode(), $arrConditional)) {
						$arrConditional[] = $conditional->getHashCode();
					}

					$arrCoord = PHPExcel_Cell::coordinateFromString($cellCoordinate);

					if (!is_numeric($arrCoord[0])) {
						$arrCoord[0] = PHPExcel_Cell::columnIndexFromString($arrCoord[0]);
					}

					if (is_null($numColumnMin) || ($arrCoord[0] < $numColumnMin)) {
						$numColumnMin = $arrCoord[0];
					}

					if (is_null($numColumnMax) || ($numColumnMax < $arrCoord[0])) {
						$numColumnMax = $arrCoord[0];
					}

					if (is_null($numRowMin) || ($arrCoord[1] < $numRowMin)) {
						$numRowMin = $arrCoord[1];
					}

					if (is_null($numRowMax) || ($numRowMax < $arrCoord[1])) {
						$numRowMax = $arrCoord[1];
					}
				}
			}
		}

		$needRedraw = 1;
		$cellRange = pack('vvvv', $numRowMin - 1, $numRowMax - 1, $numColumnMin - 1, $numColumnMax - 1);
		$header = pack('vv', $record, $length);
		$data = pack('vv', count($arrConditional), $needRedraw);
		$data .= $cellRange;
		$data .= pack('v', 1);
		$data .= $cellRange;
		$this->_append($header . $data);
	}
}

?>

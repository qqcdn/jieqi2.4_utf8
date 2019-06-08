<?php

class PHPExcel_Writer_HTML extends PHPExcel_Writer_Abstract implements PHPExcel_Writer_IWriter
{
	/**
	 * PHPExcel object
	 *
	 * @var PHPExcel
	 */
	protected $_phpExcel;
	/**
	 * Sheet index to write
	 *
	 * @var int
	 */
	private $_sheetIndex = 0;
	/**
	 * Images root
	 *
	 * @var string
	 */
	private $_imagesRoot = '.';
	/**
	 * embed images, or link to images
	 *
	 * @var boolean
	 */
	private $_embedImages = false;
	/**
	 * Use inline CSS?
	 *
	 * @var boolean
	 */
	private $_useInlineCss = false;
	/**
	 * Array of CSS styles
	 *
	 * @var array
	 */
	private $_cssStyles;
	/**
	 * Array of column widths in points
	 *
	 * @var array
	 */
	private $_columnWidths;
	/**
	 * Default font
	 *
	 * @var PHPExcel_Style_Font
	 */
	private $_defaultFont;
	/**
	 * Flag whether spans have been calculated
	 *
	 * @var boolean
	 */
	private $_spansAreCalculated = false;
	/**
	 * Excel cells that should not be written as HTML cells
	 *
	 * @var array
	 */
	private $_isSpannedCell = array();
	/**
	 * Excel cells that are upper-left corner in a cell merge
	 *
	 * @var array
	 */
	private $_isBaseCell = array();
	/**
	 * Excel rows that should not be written as HTML rows
	 *
	 * @var array
	 */
	private $_isSpannedRow = array();
	/**
	 * Is the current writer creating PDF?
	 *
	 * @var boolean
	 */
	protected $_isPdf = false;
	/**
	 * Generate the Navigation block
	 *
	 * @var boolean
	 */
	private $_generateSheetNavigationBlock = true;

	public function __construct(PHPExcel $phpExcel)
	{
		$this->_phpExcel = $phpExcel;
		$this->_defaultFont = $this->_phpExcel->getDefaultStyle()->getFont();
	}

	public function save($pFilename = NULL)
	{
		$this->_phpExcel->garbageCollect();
		$saveDebugLog = PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->getWriteDebugLog();
		PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->setWriteDebugLog(false);
		$saveArrayReturnType = PHPExcel_Calculation::getArrayReturnType();
		PHPExcel_Calculation::setArrayReturnType(PHPExcel_Calculation::RETURN_ARRAY_AS_VALUE);
		$this->buildCSS(!$this->_useInlineCss);
		$fileHandle = fopen($pFilename, 'wb+');

		if ($fileHandle === false) {
			throw new PHPExcel_Writer_Exception('Could not open file ' . $pFilename . ' for writing.');
		}

		fwrite($fileHandle, $this->generateHTMLHeader(!$this->_useInlineCss));
		if (!$this->_isPdf && $this->_generateSheetNavigationBlock) {
			fwrite($fileHandle, $this->generateNavigation());
		}

		fwrite($fileHandle, $this->generateSheetData());
		fwrite($fileHandle, $this->generateHTMLFooter());
		fclose($fileHandle);
		PHPExcel_Calculation::setArrayReturnType($saveArrayReturnType);
		PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->setWriteDebugLog($saveDebugLog);
	}

	private function _mapVAlign($vAlign)
	{
		switch ($vAlign) {
		case PHPExcel_Style_Alignment::VERTICAL_BOTTOM:
			return 'bottom';
		case PHPExcel_Style_Alignment::VERTICAL_TOP:
			return 'top';
		case PHPExcel_Style_Alignment::VERTICAL_CENTER:
		case PHPExcel_Style_Alignment::VERTICAL_JUSTIFY:
			return 'middle';
		default:
			return 'baseline';
		}
	}

	private function _mapHAlign($hAlign)
	{
		switch ($hAlign) {
		case PHPExcel_Style_Alignment::HORIZONTAL_GENERAL:
			return false;
		case PHPExcel_Style_Alignment::HORIZONTAL_LEFT:
			return 'left';
		case PHPExcel_Style_Alignment::HORIZONTAL_RIGHT:
			return 'right';
		case PHPExcel_Style_Alignment::HORIZONTAL_CENTER:
		case PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS:
			return 'center';
		case PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY:
			return 'justify';
		default:
			return false;
		}
	}

	private function _mapBorderStyle($borderStyle)
	{
		switch ($borderStyle) {
		case PHPExcel_Style_Border::BORDER_NONE:
			return 'none';
		case PHPExcel_Style_Border::BORDER_DASHDOT:
			return '1px dashed';
		case PHPExcel_Style_Border::BORDER_DASHDOTDOT:
			return '1px dotted';
		case PHPExcel_Style_Border::BORDER_DASHED:
			return '1px dashed';
		case PHPExcel_Style_Border::BORDER_DOTTED:
			return '1px dotted';
		case PHPExcel_Style_Border::BORDER_DOUBLE:
			return '3px double';
		case PHPExcel_Style_Border::BORDER_HAIR:
			return '1px solid';
		case PHPExcel_Style_Border::BORDER_MEDIUM:
			return '2px solid';
		case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT:
			return '2px dashed';
		case PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT:
			return '2px dotted';
		case PHPExcel_Style_Border::BORDER_MEDIUMDASHED:
			return '2px dashed';
		case PHPExcel_Style_Border::BORDER_SLANTDASHDOT:
			return '2px dashed';
		case PHPExcel_Style_Border::BORDER_THICK:
			return '3px solid';
		case PHPExcel_Style_Border::BORDER_THIN:
			return '1px solid';
		default:
			return '1px solid';
		}
	}

	public function getSheetIndex()
	{
		return $this->_sheetIndex;
	}

	public function setSheetIndex($pValue = 0)
	{
		$this->_sheetIndex = $pValue;
		return $this;
	}

	public function getGenerateSheetNavigationBlock()
	{
		return $this->_generateSheetNavigationBlock;
	}

	public function setGenerateSheetNavigationBlock($pValue = true)
	{
		$this->_generateSheetNavigationBlock = (bool) $pValue;
		return $this;
	}

	public function writeAllSheets()
	{
		$this->_sheetIndex = NULL;
		return $this;
	}

	public function generateHTMLHeader($pIncludeStyles = false)
	{
		if (is_null($this->_phpExcel)) {
			throw new PHPExcel_Writer_Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		$properties = $this->_phpExcel->getProperties();
		$html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">' . PHP_EOL;
		$html .= '<!-- Generated by PHPExcel - http://www.phpexcel.net -->' . PHP_EOL;
		$html .= '<html>' . PHP_EOL;
		$html .= '  <head>' . PHP_EOL;
		$html .= '	  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . PHP_EOL;

		if ('' < $properties->getTitle()) {
			$html .= '	  <title>' . htmlspecialchars($properties->getTitle()) . '</title>' . PHP_EOL;
		}

		if ('' < $properties->getCreator()) {
			$html .= '	  <meta name="author" content="' . htmlspecialchars($properties->getCreator()) . '" />' . PHP_EOL;
		}

		if ('' < $properties->getTitle()) {
			$html .= '	  <meta name="title" content="' . htmlspecialchars($properties->getTitle()) . '" />' . PHP_EOL;
		}

		if ('' < $properties->getDescription()) {
			$html .= '	  <meta name="description" content="' . htmlspecialchars($properties->getDescription()) . '" />' . PHP_EOL;
		}

		if ('' < $properties->getSubject()) {
			$html .= '	  <meta name="subject" content="' . htmlspecialchars($properties->getSubject()) . '" />' . PHP_EOL;
		}

		if ('' < $properties->getKeywords()) {
			$html .= '	  <meta name="keywords" content="' . htmlspecialchars($properties->getKeywords()) . '" />' . PHP_EOL;
		}

		if ('' < $properties->getCategory()) {
			$html .= '	  <meta name="category" content="' . htmlspecialchars($properties->getCategory()) . '" />' . PHP_EOL;
		}

		if ('' < $properties->getCompany()) {
			$html .= '	  <meta name="company" content="' . htmlspecialchars($properties->getCompany()) . '" />' . PHP_EOL;
		}

		if ('' < $properties->getManager()) {
			$html .= '	  <meta name="manager" content="' . htmlspecialchars($properties->getManager()) . '" />' . PHP_EOL;
		}

		if ($pIncludeStyles) {
			$html .= $this->generateStyles(true);
		}

		$html .= '  </head>' . PHP_EOL;
		$html .= '' . PHP_EOL;
		$html .= '  <body>' . PHP_EOL;
		return $html;
	}

	public function generateSheetData()
	{
		if (is_null($this->_phpExcel)) {
			throw new PHPExcel_Writer_Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		if (!$this->_spansAreCalculated) {
			$this->_calculateSpans();
		}

		$sheets = array();

		if (is_null($this->_sheetIndex)) {
			$sheets = $this->_phpExcel->getAllSheets();
		}
		else {
			$sheets[] = $this->_phpExcel->getSheet($this->_sheetIndex);
		}

		$html = '';
		$sheetId = 0;

		foreach ($sheets as $sheet) {
			$html .= $this->_generateTableHeader($sheet);
			$dimension = explode(':', $sheet->calculateWorksheetDimension());
			$dimension[0] = PHPExcel_Cell::coordinateFromString($dimension[0]);
			$dimension[0][0] = PHPExcel_Cell::columnIndexFromString($dimension[0][0]) - 1;
			$dimension[1] = PHPExcel_Cell::coordinateFromString($dimension[1]);
			$dimension[1][0] = PHPExcel_Cell::columnIndexFromString($dimension[1][0]) - 1;
			$rowMin = $dimension[0][1];
			$rowMax = $dimension[1][1];
			$tbodyStart = $rowMin;
			$theadStart = $theadEnd = 0;

			if ($sheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
				$rowsToRepeatAtTop = $sheet->getPageSetup()->getRowsToRepeatAtTop();

				if ($rowsToRepeatAtTop[0] == 1) {
					$theadStart = $rowsToRepeatAtTop[0];
					$theadEnd = $rowsToRepeatAtTop[1];
					$tbodyStart = $rowsToRepeatAtTop[1] + 1;
				}
			}

			$row = $rowMin - 1;

			while ($row++ < $rowMax) {
				if ($row == $theadStart) {
					$html .= '		<thead>' . PHP_EOL;
				}

				if ($row == $tbodyStart) {
					$html .= '		<tbody>' . PHP_EOL;
				}

				if (!isset($this->_isSpannedRow[$sheet->getParent()->getIndex($sheet)][$row])) {
					$rowData = array();
					$column = $dimension[0][0] - 1;

					while ($column++ < $dimension[1][0]) {
						if ($sheet->cellExistsByColumnAndRow($column, $row)) {
							$rowData[$column] = PHPExcel_Cell::stringFromColumnIndex($column) . $row;
						}
						else {
							$rowData[$column] = '';
						}
					}

					$html .= $this->_generateRow($sheet, $rowData, $row - 1);
				}

				if ($row == $theadEnd) {
					$html .= '		</thead>' . PHP_EOL;
				}
			}

			$html .= $this->_extendRowsForChartsAndImages($sheet, $row);
			$html .= '		</tbody>' . PHP_EOL;
			$html .= $this->_generateTableFooter();

			if ($this->_isPdf) {
				if (is_null($this->_sheetIndex) && (($sheetId + 1) < $this->_phpExcel->getSheetCount())) {
					$html .= '<div style="page-break-before:always" />';
				}
			}

			++$sheetId;
		}

		return $html;
	}

	public function generateNavigation()
	{
		if (is_null($this->_phpExcel)) {
			throw new PHPExcel_Writer_Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		$sheets = array();

		if (is_null($this->_sheetIndex)) {
			$sheets = $this->_phpExcel->getAllSheets();
		}
		else {
			$sheets[] = $this->_phpExcel->getSheet($this->_sheetIndex);
		}

		$html = '';

		if (1 < count($sheets)) {
			$sheetId = 0;
			$html .= '<ul class="navigation">' . PHP_EOL;

			foreach ($sheets as $sheet) {
				$html .= '  <li class="sheet' . $sheetId . '"><a href="#sheet' . $sheetId . '">' . $sheet->getTitle() . '</a></li>' . PHP_EOL;
				++$sheetId;
			}

			$html .= '</ul>' . PHP_EOL;
		}

		return $html;
	}

	private function _extendRowsForChartsAndImages(PHPExcel_Worksheet $pSheet, $row)
	{
		$rowMax = $row;
		$colMax = 'A';

		if ($this->_includeCharts) {
			foreach ($pSheet->getChartCollection() as $chart) {
				if ($chart instanceof PHPExcel_Chart) {
					$chartCoordinates = $chart->getTopLeftPosition();
					$chartTL = PHPExcel_Cell::coordinateFromString($chartCoordinates['cell']);
					$chartCol = PHPExcel_Cell::columnIndexFromString($chartTL[0]);

					if ($rowMax < $chartTL[1]) {
						$rowMax = $chartTL[1];

						if (PHPExcel_Cell::columnIndexFromString($colMax) < $chartCol) {
							$colMax = $chartTL[0];
						}
					}
				}
			}
		}

		foreach ($pSheet->getDrawingCollection() as $drawing) {
			if ($drawing instanceof PHPExcel_Worksheet_Drawing) {
				$imageTL = PHPExcel_Cell::coordinateFromString($drawing->getCoordinates());
				$imageCol = PHPExcel_Cell::columnIndexFromString($imageTL[0]);

				if ($rowMax < $imageTL[1]) {
					$rowMax = $imageTL[1];

					if (PHPExcel_Cell::columnIndexFromString($colMax) < $imageCol) {
						$colMax = $imageTL[0];
					}
				}
			}
		}

		$html = '';
		$colMax++;

		while ($row < $rowMax) {
			$html .= '<tr>';

			for ($col = 'A'; $col != $colMax; ++$col) {
				$html .= '<td>';
				$html .= $this->_writeImageInCell($pSheet, $col . $row);

				if ($this->_includeCharts) {
					$html .= $this->_writeChartInCell($pSheet, $col . $row);
				}

				$html .= '</td>';
			}

			++$row;
			$html .= '</tr>';
		}

		return $html;
	}

	private function _writeImageInCell(PHPExcel_Worksheet $pSheet, $coordinates)
	{
		$html = '';

		foreach ($pSheet->getDrawingCollection() as $drawing) {
			if ($drawing instanceof PHPExcel_Worksheet_Drawing) {
				if ($drawing->getCoordinates() == $coordinates) {
					$filename = $drawing->getPath();

					if (substr($filename, 0, 1) == '.') {
						$filename = substr($filename, 1);
					}

					$filename = $this->getImagesRoot() . $filename;
					if ((substr($filename, 0, 1) == '.') && (substr($filename, 0, 2) != './')) {
						$filename = substr($filename, 1);
					}

					$filename = htmlspecialchars($filename);
					$html .= PHP_EOL;
					if (!$this->_embedImages || $this->_isPdf) {
						$imageData = $filename;
					}
					else {
						$imageDetails = getimagesize($filename);

						if ($fp = fopen($filename, 'rb', 0)) {
							$picture = fread($fp, filesize($filename));
							fclose($fp);
							$base64 = chunk_split(base64_encode($picture));
							$imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;
						}
						else {
							$imageData = $filename;
						}
					}

					$html .= '<div style="position: relative;">';
					$html .= '<img style="position: absolute; z-index: 1; left: ' . $drawing->getOffsetX() . 'px; top: ' . $drawing->getOffsetY() . 'px; width: ' . $drawing->getWidth() . 'px; height: ' . $drawing->getHeight() . 'px;" src="' . $imageData . '" border="0" />';
					$html .= '</div>';
				}
			}
		}

		return $html;
	}

	private function _writeChartInCell(PHPExcel_Worksheet $pSheet, $coordinates)
	{
		$html = '';

		foreach ($pSheet->getChartCollection() as $chart) {
			if ($chart instanceof PHPExcel_Chart) {
				$chartCoordinates = $chart->getTopLeftPosition();

				if ($chartCoordinates['cell'] == $coordinates) {
					$chartFileName = PHPExcel_Shared_File::sys_get_temp_dir() . '/' . uniqid() . '.png';

					if (!$chart->render($chartFileName)) {
						return NULL;
					}

					$html .= PHP_EOL;
					$imageDetails = getimagesize($chartFileName);

					if ($fp = fopen($chartFileName, 'rb', 0)) {
						$picture = fread($fp, filesize($chartFileName));
						fclose($fp);
						$base64 = chunk_split(base64_encode($picture));
						$imageData = 'data:' . $imageDetails['mime'] . ';base64,' . $base64;
						$html .= '<div style="position: relative;">';
						$html .= '<img style="position: absolute; z-index: 1; left: ' . $chartCoordinates['xOffset'] . 'px; top: ' . $chartCoordinates['yOffset'] . 'px; width: ' . $imageDetails[0] . 'px; height: ' . $imageDetails[1] . 'px;" src="' . $imageData . '" border="0" />' . PHP_EOL;
						$html .= '</div>';
						unlink($chartFileName);
					}
				}
			}
		}

		return $html;
	}

	public function generateStyles($generateSurroundingHTML = true)
	{
		if (is_null($this->_phpExcel)) {
			throw new PHPExcel_Writer_Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		$css = $this->buildCSS($generateSurroundingHTML);
		$html = '';

		if ($generateSurroundingHTML) {
			$html .= '	<style type="text/css">' . PHP_EOL;
			$html .= '	  html { ' . $this->_assembleCSS($css['html']) . ' }' . PHP_EOL;
		}

		foreach ($css as $styleName => $styleDefinition) {
			if ($styleName != 'html') {
				$html .= '	  ' . $styleName . ' { ' . $this->_assembleCSS($styleDefinition) . ' }' . PHP_EOL;
			}
		}

		if ($generateSurroundingHTML) {
			$html .= '	</style>' . PHP_EOL;
		}

		return $html;
	}

	public function buildCSS($generateSurroundingHTML = true)
	{
		if (is_null($this->_phpExcel)) {
			throw new PHPExcel_Writer_Exception('Internal PHPExcel object not set to an instance of an object.');
		}

		if (!is_null($this->_cssStyles)) {
			return $this->_cssStyles;
		}

		if (!$this->_spansAreCalculated) {
			$this->_calculateSpans();
		}

		$css = array();

		if ($generateSurroundingHTML) {
			$css['html']['font-family'] = 'Calibri, Arial, Helvetica, sans-serif';
			$css['html']['font-size'] = '11pt';
			$css['html']['background-color'] = 'white';
		}

		$css['table']['border-collapse'] = 'collapse';

		if (!$this->_isPdf) {
			$css['table']['page-break-after'] = 'always';
		}

		$css['.gridlines td']['border'] = '1px dotted black';
		$css['.b']['text-align'] = 'center';
		$css['.e']['text-align'] = 'center';
		$css['.f']['text-align'] = 'right';
		$css['.inlineStr']['text-align'] = 'left';
		$css['.n']['text-align'] = 'right';
		$css['.s']['text-align'] = 'left';

		foreach ($this->_phpExcel->getCellXfCollection() as $index => $style) {
			$css['td.style' . $index] = $this->_createCSSStyle($style);
		}

		$sheets = array();

		if (is_null($this->_sheetIndex)) {
			$sheets = $this->_phpExcel->getAllSheets();
		}
		else {
			$sheets[] = $this->_phpExcel->getSheet($this->_sheetIndex);
		}

		foreach ($sheets as $sheet) {
			$sheetIndex = $sheet->getParent()->getIndex($sheet);
			$sheet->calculateColumnWidths();
			$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()) - 1;
			$column = -1;

			while ($column++ < $highestColumnIndex) {
				$this->_columnWidths[$sheetIndex][$column] = 42;
				$css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = '42pt';
			}

			foreach ($sheet->getColumnDimensions() as $columnDimension) {
				if (0 <= $width = PHPExcel_Shared_Drawing::cellDimensionToPixels($columnDimension->getWidth(), $this->_defaultFont)) {
					$width = PHPExcel_Shared_Drawing::pixelsToPoints($width);
					$column = PHPExcel_Cell::columnIndexFromString($columnDimension->getColumnIndex()) - 1;
					$this->_columnWidths[$sheetIndex][$column] = $width;
					$css['table.sheet' . $sheetIndex . ' col.col' . $column]['width'] = $width . 'pt';

					if ($columnDimension->getVisible() === false) {
						$css['table.sheet' . $sheetIndex . ' col.col' . $column]['visibility'] = 'collapse';
						$css['table.sheet' . $sheetIndex . ' col.col' . $column]['*display'] = 'none';
					}
				}
			}

			$rowDimension = $sheet->getDefaultRowDimension();
			$css['table.sheet' . $sheetIndex . ' tr'] = array();

			if ($rowDimension->getRowHeight() == -1) {
				$pt_height = PHPExcel_Shared_Font::getDefaultRowHeightByFont($this->_phpExcel->getDefaultStyle()->getFont());
			}
			else {
				$pt_height = $rowDimension->getRowHeight();
			}

			$css['table.sheet' . $sheetIndex . ' tr']['height'] = $pt_height . 'pt';

			if ($rowDimension->getVisible() === false) {
				$css['table.sheet' . $sheetIndex . ' tr']['display'] = 'none';
				$css['table.sheet' . $sheetIndex . ' tr']['visibility'] = 'hidden';
			}

			foreach ($sheet->getRowDimensions() as $rowDimension) {
				$row = $rowDimension->getRowIndex() - 1;
				$css['table.sheet' . $sheetIndex . ' tr.row' . $row] = array();

				if ($rowDimension->getRowHeight() == -1) {
					$pt_height = PHPExcel_Shared_Font::getDefaultRowHeightByFont($this->_phpExcel->getDefaultStyle()->getFont());
				}
				else {
					$pt_height = $rowDimension->getRowHeight();
				}

				$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['height'] = $pt_height . 'pt';

				if ($rowDimension->getVisible() === false) {
					$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['display'] = 'none';
					$css['table.sheet' . $sheetIndex . ' tr.row' . $row]['visibility'] = 'hidden';
				}
			}
		}

		if (is_null($this->_cssStyles)) {
			$this->_cssStyles = $css;
		}

		return $css;
	}

	private function _createCSSStyle(PHPExcel_Style $pStyle)
	{
		$css = '';
		$css = array_merge($this->_createCSSStyleAlignment($pStyle->getAlignment()), $this->_createCSSStyleBorders($pStyle->getBorders()), $this->_createCSSStyleFont($pStyle->getFont()), $this->_createCSSStyleFill($pStyle->getFill()));
		return $css;
	}

	private function _createCSSStyleAlignment(PHPExcel_Style_Alignment $pStyle)
	{
		$css = array();
		$css['vertical-align'] = $this->_mapVAlign($pStyle->getVertical());

		if ($textAlign = $this->_mapHAlign($pStyle->getHorizontal())) {
			$css['text-align'] = $textAlign;

			if (in_array($textAlign, array('left', 'right'))) {
				$css['padding-' . $textAlign] = (string) ((int) $pStyle->getIndent() * 9) . 'px';
			}
		}

		return $css;
	}

	private function _createCSSStyleFont(PHPExcel_Style_Font $pStyle)
	{
		$css = array();

		if ($pStyle->getBold()) {
			$css['font-weight'] = 'bold';
		}

		if (($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE) && $pStyle->getStrikethrough()) {
			$css['text-decoration'] = 'underline line-through';
		}
		else if ($pStyle->getUnderline() != PHPExcel_Style_Font::UNDERLINE_NONE) {
			$css['text-decoration'] = 'underline';
		}
		else if ($pStyle->getStrikethrough()) {
			$css['text-decoration'] = 'line-through';
		}

		if ($pStyle->getItalic()) {
			$css['font-style'] = 'italic';
		}

		$css['color'] = '#' . $pStyle->getColor()->getRGB();
		$css['font-family'] = '\'' . $pStyle->getName() . '\'';
		$css['font-size'] = $pStyle->getSize() . 'pt';
		return $css;
	}

	private function _createCSSStyleBorders(PHPExcel_Style_Borders $pStyle)
	{
		$css = array();
		$css['border-bottom'] = $this->_createCSSStyleBorder($pStyle->getBottom());
		$css['border-top'] = $this->_createCSSStyleBorder($pStyle->getTop());
		$css['border-left'] = $this->_createCSSStyleBorder($pStyle->getLeft());
		$css['border-right'] = $this->_createCSSStyleBorder($pStyle->getRight());
		return $css;
	}

	private function _createCSSStyleBorder(PHPExcel_Style_Border $pStyle)
	{
		$borderStyle = $this->_mapBorderStyle($pStyle->getBorderStyle());
		$css = $borderStyle . ' #' . $pStyle->getColor()->getRGB() . ($borderStyle == 'none' ? '' : ' !important');
		return $css;
	}

	private function _createCSSStyleFill(PHPExcel_Style_Fill $pStyle)
	{
		$css = array();
		$value = ($pStyle->getFillType() == PHPExcel_Style_Fill::FILL_NONE ? 'white' : '#' . $pStyle->getStartColor()->getRGB());
		$css['background-color'] = $value;
		return $css;
	}

	public function generateHTMLFooter()
	{
		$html = '';
		$html .= '  </body>' . PHP_EOL;
		$html .= '</html>' . PHP_EOL;
		return $html;
	}

	private function _generateTableHeader($pSheet)
	{
		$sheetIndex = $pSheet->getParent()->getIndex($pSheet);
		$html = '';
		$html .= $this->_setMargins($pSheet);

		if (!$this->_useInlineCss) {
			$gridlines = ($pSheet->getShowGridlines() ? ' gridlines' : '');
			$html .= '	<table border="0" cellpadding="0" cellspacing="0" id="sheet' . $sheetIndex . '" class="sheet' . $sheetIndex . $gridlines . '">' . PHP_EOL;
		}
		else {
			$style = (isset($this->_cssStyles['table']) ? $this->_assembleCSS($this->_cssStyles['table']) : '');
			if ($this->_isPdf && $pSheet->getShowGridlines()) {
				$html .= '	<table border="1" cellpadding="1" id="sheet' . $sheetIndex . '" cellspacing="1" style="' . $style . '">' . PHP_EOL;
			}
			else {
				$html .= '	<table border="0" cellpadding="1" id="sheet' . $sheetIndex . '" cellspacing="0" style="' . $style . '">' . PHP_EOL;
			}
		}

		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($pSheet->getHighestColumn()) - 1;
		$i = -1;

		while ($i++ < $highestColumnIndex) {
			if (!$this->_isPdf) {
				if (!$this->_useInlineCss) {
					$html .= '		<col class="col' . $i . '">' . PHP_EOL;
				}
				else {
					$style = (isset($this->_cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) ? $this->_assembleCSS($this->_cssStyles['table.sheet' . $sheetIndex . ' col.col' . $i]) : '');
					$html .= '		<col style="' . $style . '">' . PHP_EOL;
				}
			}
		}

		return $html;
	}

	private function _generateTableFooter()
	{
		$html = '';
		$html .= '	</table>' . PHP_EOL;
		return $html;
	}

	private function _generateRow(PHPExcel_Worksheet $pSheet, $pValues = NULL, $pRow = 0)
	{
		if (is_array($pValues)) {
			$html = '';
			$sheetIndex = $pSheet->getParent()->getIndex($pSheet);
			if ($this->_isPdf && (0 < count($pSheet->getBreaks()))) {
				$breaks = $pSheet->getBreaks();

				if (isset($breaks['A' . $pRow])) {
					$html .= $this->_generateTableFooter();
					$html .= '<div style="page-break-before:always" />';
					$html .= $this->_generateTableHeader($pSheet);
				}
			}

			if (!$this->_useInlineCss) {
				$html .= '		  <tr class="row' . $pRow . '">' . PHP_EOL;
			}
			else {
				$style = (isset($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]) ? $this->_assembleCSS($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]) : '');
				$html .= '		  <tr style="' . $style . '">' . PHP_EOL;
			}

			$colNum = 0;

			foreach ($pValues as $cellAddress) {
				$cell = ('' < $cellAddress ? $pSheet->getCell($cellAddress) : '');
				$coordinate = PHPExcel_Cell::stringFromColumnIndex($colNum) . ($pRow + 1);

				if (!$this->_useInlineCss) {
					$cssClass = '';
					$cssClass = 'column' . $colNum;
				}
				else {
					$cssClass = array();

					if (isset($this->_cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum])) {
						$this->_cssStyles['table.sheet' . $sheetIndex . ' td.column' . $colNum];
					}
				}

				$colSpan = 1;
				$rowSpan = 1;
				$cellData = '&nbsp;';

				if ($cell instanceof PHPExcel_Cell) {
					$cellData = '';

					if (is_null($cell->getParent())) {
						$cell->attach($pSheet);
					}

					if ($cell->getValue() instanceof PHPExcel_RichText) {
						$elements = $cell->getValue()->getRichTextElements();

						foreach ($elements as $element) {
							if ($element instanceof PHPExcel_RichText_Run) {
								$cellData .= '<span style="' . $this->_assembleCSS($this->_createCSSStyleFont($element->getFont())) . '">';

								if ($element->getFont()->getSuperScript()) {
									$cellData .= '<sup>';
								}
								else if ($element->getFont()->getSubScript()) {
									$cellData .= '<sub>';
								}
							}

							$cellText = $element->getText();
							$cellData .= htmlspecialchars($cellText);

							if ($element instanceof PHPExcel_RichText_Run) {
								if ($element->getFont()->getSuperScript()) {
									$cellData .= '</sup>';
								}
								else if ($element->getFont()->getSubScript()) {
									$cellData .= '</sub>';
								}

								$cellData .= '</span>';
							}
						}
					}
					else {
						if ($this->_preCalculateFormulas) {
							$cellData = PHPExcel_Style_NumberFormat::toFormattedString($cell->getCalculatedValue(), $pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode(), array($this, 'formatColor'));
						}
						else {
							$cellData = PHPExcel_Style_NumberFormat::ToFormattedString($cell->getValue(), $pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode(), array($this, 'formatColor'));
						}

						$cellData = htmlspecialchars($cellData);

						if ($pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont()->getSuperScript()) {
							$cellData = '<sup>' . $cellData . '</sup>';
						}
						else if ($pSheet->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont()->getSubScript()) {
							$cellData = '<sub>' . $cellData . '</sub>';
						}
					}

					$cellData = preg_replace('/(?m)(?:^|\\G) /', '&nbsp;', $cellData);
					$cellData = nl2br($cellData);

					if (!$this->_useInlineCss) {
						$cssClass .= ' style' . $cell->getXfIndex();
						$cssClass .= ' ' . $cell->getDataType();
					}
					else {
						if (isset($this->_cssStyles['td.style' . $cell->getXfIndex()])) {
							$cssClass = array_merge($cssClass, $this->_cssStyles['td.style' . $cell->getXfIndex()]);
						}

						$sharedStyle = $pSheet->getParent()->getCellXfByIndex($cell->getXfIndex());
						if (($sharedStyle->getAlignment()->getHorizontal() == PHPExcel_Style_Alignment::HORIZONTAL_GENERAL) && isset($this->_cssStyles['.' . $cell->getDataType()]['text-align'])) {
							$cssClass['text-align'] = $this->_cssStyles['.' . $cell->getDataType()]['text-align'];
						}
					}
				}

				if ($pSheet->hyperlinkExists($coordinate) && !$pSheet->getHyperlink($coordinate)->isInternal()) {
					$cellData = '<a href="' . htmlspecialchars($pSheet->getHyperlink($coordinate)->getUrl()) . '" title="' . htmlspecialchars($pSheet->getHyperlink($coordinate)->getTooltip()) . '">' . $cellData . '</a>';
				}

				$writeCell = !(isset($this->_isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum]) && $this->_isSpannedCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum]);
				$colspan = 1;
				$rowspan = 1;

				if (isset($this->_isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum])) {
					$spans = $this->_isBaseCell[$pSheet->getParent()->getIndex($pSheet)][$pRow + 1][$colNum];
					$rowSpan = $spans['rowspan'];
					$colSpan = $spans['colspan'];
					$endCellCoord = PHPExcel_Cell::stringFromColumnIndex(($colNum + $colSpan) - 1) . ($pRow + $rowSpan);

					if (!$this->_useInlineCss) {
						$cssClass .= ' style' . $pSheet->getCell($endCellCoord)->getXfIndex();
					}
				}

				if ($writeCell) {
					$html .= '			<td';

					if (!$this->_useInlineCss) {
						$html .= ' class="' . $cssClass . '"';
					}
					else {
						$width = 0;
						$i = $colNum - 1;
						$e = ($colNum + $colSpan) - 1;

						while ($i++ < $e) {
							if (isset($this->_columnWidths[$sheetIndex][$i])) {
								$width += $this->_columnWidths[$sheetIndex][$i];
							}
						}

						$cssClass['width'] = $width . 'pt';

						if (isset($this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'])) {
							$height = $this->_cssStyles['table.sheet' . $sheetIndex . ' tr.row' . $pRow]['height'];
							$cssClass['height'] = $height;
						}

						$html .= ' style="' . $this->_assembleCSS($cssClass) . '"';
					}

					if (1 < $colSpan) {
						$html .= ' colspan="' . $colSpan . '"';
					}

					if (1 < $rowSpan) {
						$html .= ' rowspan="' . $rowSpan . '"';
					}

					$html .= '>';
					$html .= $this->_writeImageInCell($pSheet, $coordinate);

					if ($this->_includeCharts) {
						$html .= $this->_writeChartInCell($pSheet, $coordinate);
					}

					$html .= $cellData;
					$html .= '</td>' . PHP_EOL;
				}

				++$colNum;
			}

			$html .= '		  </tr>' . PHP_EOL;
			return $html;
		}
		else {
			throw new PHPExcel_Writer_Exception('Invalid parameters passed.');
		}
	}

	private function _assembleCSS($pValue = array())
	{
		$pairs = array();

		foreach ($pValue as $property => $value) {
			$pairs[] = $property . ':' . $value;
		}

		$string = implode('; ', $pairs);
		return $string;
	}

	public function getImagesRoot()
	{
		return $this->_imagesRoot;
	}

	public function setImagesRoot($pValue = '.')
	{
		$this->_imagesRoot = $pValue;
		return $this;
	}

	public function getEmbedImages()
	{
		return $this->_embedImages;
	}

	public function setEmbedImages($pValue = '.')
	{
		$this->_embedImages = $pValue;
		return $this;
	}

	public function getUseInlineCss()
	{
		return $this->_useInlineCss;
	}

	public function setUseInlineCss($pValue = false)
	{
		$this->_useInlineCss = $pValue;
		return $this;
	}

	public function formatColor($pValue, $pFormat)
	{
		$color = NULL;
		$matches = array();
		$color_regex = '/^\\[[a-zA-Z]+\\]/';

		if (preg_match($color_regex, $pFormat, $matches)) {
			$color = str_replace('[', '', $matches[0]);
			$color = str_replace(']', '', $color);
			$color = strtolower($color);
		}

		$value = htmlspecialchars($pValue);

		if ($color !== NULL) {
			$value = '<span style="color:' . $color . '">' . $value . '</span>';
		}

		return $value;
	}

	private function _calculateSpans()
	{
		$sheetIndexes = ($this->_sheetIndex !== NULL ? array($this->_sheetIndex) : range(0, $this->_phpExcel->getSheetCount() - 1));

		foreach ($sheetIndexes as $sheetIndex) {
			$sheet = $this->_phpExcel->getSheet($sheetIndex);
			$candidateSpannedRow = array();

			foreach ($sheet->getMergeCells() as $cells) {
				list($cells) = PHPExcel_Cell::splitRange($cells);
				$first = $cells[0];
				$last = $cells[1];
				list($fc, $fr) = PHPExcel_Cell::coordinateFromString($first);
				$fc = PHPExcel_Cell::columnIndexFromString($fc) - 1;
				list($lc, $lr) = PHPExcel_Cell::coordinateFromString($last);
				$lc = PHPExcel_Cell::columnIndexFromString($lc) - 1;
				$r = $fr - 1;

				while ($r++ < $lr) {
					$candidateSpannedRow[$r] = $r;
					$c = $fc - 1;

					while ($c++ < $lc) {
						if (!(($c == $fc) && ($r == $fr))) {
							$this->_isSpannedCell[$sheetIndex][$r][$c] = array(
	'baseCell' => array($fr, $fc)
	);
						}
						else {
							$this->_isBaseCell[$sheetIndex][$r][$c] = array('xlrowspan' => ($lr - $fr) + 1, 'rowspan' => ($lr - $fr) + 1, 'xlcolspan' => ($lc - $fc) + 1, 'colspan' => ($lc - $fc) + 1);
						}
					}
				}
			}

			$countColumns = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn());

			foreach ($candidateSpannedRow as $rowIndex) {
				if (isset($this->_isSpannedCell[$sheetIndex][$rowIndex])) {
					if (count($this->_isSpannedCell[$sheetIndex][$rowIndex]) == $countColumns) {
						$this->_isSpannedRow[$sheetIndex][$rowIndex] = $rowIndex;
					}
				}
			}

			if (isset($this->_isSpannedRow[$sheetIndex])) {
				foreach ($this->_isSpannedRow[$sheetIndex] as $rowIndex) {
					$adjustedBaseCells = array();
					$c = -1;
					$e = $countColumns - 1;

					while ($c++ < $e) {
						$baseCell = $this->_isSpannedCell[$sheetIndex][$rowIndex][$c]['baseCell'];

						if (!in_array($baseCell, $adjustedBaseCells)) {
							--$this->_isBaseCell[$sheetIndex][$baseCell[0]][$baseCell[1]]['rowspan'];
							$adjustedBaseCells[] = $baseCell;
						}
					}
				}
			}
		}

		$this->_spansAreCalculated = true;
	}

	private function _setMargins(PHPExcel_Worksheet $pSheet)
	{
		$htmlPage = '@page { ';
		$htmlBody = 'body { ';
		$left = PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getLeft()) . 'in; ';
		$htmlPage .= 'left-margin: ' . $left;
		$htmlBody .= 'left-margin: ' . $left;
		$right = PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getRight()) . 'in; ';
		$htmlPage .= 'right-margin: ' . $right;
		$htmlBody .= 'right-margin: ' . $right;
		$top = PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getTop()) . 'in; ';
		$htmlPage .= 'top-margin: ' . $top;
		$htmlBody .= 'top-margin: ' . $top;
		$bottom = PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getBottom()) . 'in; ';
		$htmlPage .= 'bottom-margin: ' . $bottom;
		$htmlBody .= 'bottom-margin: ' . $bottom;
		$htmlPage .= "}\n";
		$htmlBody .= "}\n";
		return "<style>\n" . $htmlPage . $htmlBody . "</style>\n";
	}
}

?>

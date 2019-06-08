<?php

class PHPExcel_Writer_Excel2007_Worksheet extends PHPExcel_Writer_Excel2007_WriterPart
{
	public function writeWorksheet($pSheet = NULL, $pStringTable = NULL, $includeCharts = false)
	{
		if (!is_null($pSheet)) {
			$objWriter = NULL;

			if ($this->getParentWriter()->getUseDiskCaching()) {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
			}
			else {
				$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
			}

			$objWriter->startDocument('1.0', 'UTF-8', 'yes');
			$objWriter->startElement('worksheet');
			$objWriter->writeAttribute('xml:space', 'preserve');
			$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
			$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
			$this->_writeSheetPr($objWriter, $pSheet);
			$this->_writeDimension($objWriter, $pSheet);
			$this->_writeSheetViews($objWriter, $pSheet);
			$this->_writeSheetFormatPr($objWriter, $pSheet);
			$this->_writeCols($objWriter, $pSheet);
			$this->_writeSheetData($objWriter, $pSheet, $pStringTable);
			$this->_writeSheetProtection($objWriter, $pSheet);
			$this->_writeProtectedRanges($objWriter, $pSheet);
			$this->_writeAutoFilter($objWriter, $pSheet);
			$this->_writeMergeCells($objWriter, $pSheet);
			$this->_writeConditionalFormatting($objWriter, $pSheet);
			$this->_writeDataValidations($objWriter, $pSheet);
			$this->_writeHyperlinks($objWriter, $pSheet);
			$this->_writePrintOptions($objWriter, $pSheet);
			$this->_writePageMargins($objWriter, $pSheet);
			$this->_writePageSetup($objWriter, $pSheet);
			$this->_writeHeaderFooter($objWriter, $pSheet);
			$this->_writeBreaks($objWriter, $pSheet);
			$this->_writeDrawings($objWriter, $pSheet, $includeCharts);
			$this->_writeLegacyDrawing($objWriter, $pSheet);
			$this->_writeLegacyDrawingHF($objWriter, $pSheet);
			$objWriter->endElement();
			return $objWriter->getData();
		}
		else {
			throw new PHPExcel_Writer_Exception('Invalid PHPExcel_Worksheet object passed.');
		}
	}

	private function _writeSheetPr(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('sheetPr');

		if ($pSheet->getParent()->hasMacros()) {
			if ($pSheet->hasCodeName() == false) {
				$pSheet->setCodeName($pSheet->getTitle());
			}

			$objWriter->writeAttribute('codeName', $pSheet->getCodeName());
		}

		$autoFilterRange = $pSheet->getAutoFilter()->getRange();

		if (!empty($autoFilterRange)) {
			$objWriter->writeAttribute('filterMode', 1);
			$pSheet->getAutoFilter()->showHideRows();
		}

		if ($pSheet->isTabColorSet()) {
			$objWriter->startElement('tabColor');
			$objWriter->writeAttribute('rgb', $pSheet->getTabColor()->getARGB());
			$objWriter->endElement();
		}

		$objWriter->startElement('outlinePr');
		$objWriter->writeAttribute('summaryBelow', $pSheet->getShowSummaryBelow() ? '1' : '0');
		$objWriter->writeAttribute('summaryRight', $pSheet->getShowSummaryRight() ? '1' : '0');
		$objWriter->endElement();

		if ($pSheet->getPageSetup()->getFitToPage()) {
			$objWriter->startElement('pageSetUpPr');
			$objWriter->writeAttribute('fitToPage', '1');
			$objWriter->endElement();
		}

		$objWriter->endElement();
	}

	private function _writeDimension(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('dimension');
		$objWriter->writeAttribute('ref', $pSheet->calculateWorksheetDimension());
		$objWriter->endElement();
	}

	private function _writeSheetViews(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('sheetViews');
		$sheetSelected = false;

		if ($this->getParentWriter()->getPHPExcel()->getIndex($pSheet) == $this->getParentWriter()->getPHPExcel()->getActiveSheetIndex()) {
			$sheetSelected = true;
		}

		$objWriter->startElement('sheetView');
		$objWriter->writeAttribute('tabSelected', $sheetSelected ? '1' : '0');
		$objWriter->writeAttribute('workbookViewId', '0');

		if ($pSheet->getSheetView()->getZoomScale() != 100) {
			$objWriter->writeAttribute('zoomScale', $pSheet->getSheetView()->getZoomScale());
		}

		if ($pSheet->getSheetView()->getZoomScaleNormal() != 100) {
			$objWriter->writeAttribute('zoomScaleNormal', $pSheet->getSheetView()->getZoomScaleNormal());
		}

		if ($pSheet->getSheetView()->getView() !== PHPExcel_Worksheet_SheetView::SHEETVIEW_NORMAL) {
			$objWriter->writeAttribute('view', $pSheet->getSheetView()->getView());
		}

		if ($pSheet->getShowGridlines()) {
			$objWriter->writeAttribute('showGridLines', 'true');
		}
		else {
			$objWriter->writeAttribute('showGridLines', 'false');
		}

		if ($pSheet->getShowRowColHeaders()) {
			$objWriter->writeAttribute('showRowColHeaders', '1');
		}
		else {
			$objWriter->writeAttribute('showRowColHeaders', '0');
		}

		if ($pSheet->getRightToLeft()) {
			$objWriter->writeAttribute('rightToLeft', 'true');
		}

		$activeCell = $pSheet->getActiveCell();
		$pane = '';
		$topLeftCell = $pSheet->getFreezePane();
		if (($topLeftCell != '') && ($topLeftCell != 'A1')) {
			$activeCell = $topLeftCell;
			$xSplit = $ySplit = 0;
			list($xSplit, $ySplit) = PHPExcel_Cell::coordinateFromString($topLeftCell);
			$xSplit = PHPExcel_Cell::columnIndexFromString($xSplit);
			$pane = 'topRight';
			$objWriter->startElement('pane');

			if (1 < $xSplit) {
				$objWriter->writeAttribute('xSplit', $xSplit - 1);
			}

			if (1 < $ySplit) {
				$objWriter->writeAttribute('ySplit', $ySplit - 1);
				$pane = (1 < $xSplit ? 'bottomRight' : 'bottomLeft');
			}

			$objWriter->writeAttribute('topLeftCell', $topLeftCell);
			$objWriter->writeAttribute('activePane', $pane);
			$objWriter->writeAttribute('state', 'frozen');
			$objWriter->endElement();
			if ((1 < $xSplit) && (1 < $ySplit)) {
				$objWriter->startElement('selection');
				$objWriter->writeAttribute('pane', 'topRight');
				$objWriter->endElement();
				$objWriter->startElement('selection');
				$objWriter->writeAttribute('pane', 'bottomLeft');
				$objWriter->endElement();
			}
		}

		$objWriter->startElement('selection');

		if ($pane != '') {
			$objWriter->writeAttribute('pane', $pane);
		}

		$objWriter->writeAttribute('activeCell', $activeCell);
		$objWriter->writeAttribute('sqref', $activeCell);
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
	}

	private function _writeSheetFormatPr(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('sheetFormatPr');

		if (0 <= $pSheet->getDefaultRowDimension()->getRowHeight()) {
			$objWriter->writeAttribute('customHeight', 'true');
			$objWriter->writeAttribute('defaultRowHeight', PHPExcel_Shared_String::FormatNumber($pSheet->getDefaultRowDimension()->getRowHeight()));
		}
		else {
			$objWriter->writeAttribute('defaultRowHeight', '14.4');
		}

		if (((string) $pSheet->getDefaultRowDimension()->getzeroHeight() == '1') || (strtolower((string) $pSheet->getDefaultRowDimension()->getzeroHeight()) == 'true')) {
			$objWriter->writeAttribute('zeroHeight', '1');
		}

		if (0 <= $pSheet->getDefaultColumnDimension()->getWidth()) {
			$objWriter->writeAttribute('defaultColWidth', PHPExcel_Shared_String::FormatNumber($pSheet->getDefaultColumnDimension()->getWidth()));
		}

		$outlineLevelRow = 0;

		foreach ($pSheet->getRowDimensions() as $dimension) {
			if ($outlineLevelRow < $dimension->getOutlineLevel()) {
				$outlineLevelRow = $dimension->getOutlineLevel();
			}
		}

		$objWriter->writeAttribute('outlineLevelRow', (int) $outlineLevelRow);
		$outlineLevelCol = 0;

		foreach ($pSheet->getColumnDimensions() as $dimension) {
			if ($outlineLevelCol < $dimension->getOutlineLevel()) {
				$outlineLevelCol = $dimension->getOutlineLevel();
			}
		}

		$objWriter->writeAttribute('outlineLevelCol', (int) $outlineLevelCol);
		$objWriter->endElement();
	}

	private function _writeCols(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		if (0 < count($pSheet->getColumnDimensions())) {
			$objWriter->startElement('cols');
			$pSheet->calculateColumnWidths();

			foreach ($pSheet->getColumnDimensions() as $colDimension) {
				$objWriter->startElement('col');
				$objWriter->writeAttribute('min', PHPExcel_Cell::columnIndexFromString($colDimension->getColumnIndex()));
				$objWriter->writeAttribute('max', PHPExcel_Cell::columnIndexFromString($colDimension->getColumnIndex()));

				if ($colDimension->getWidth() < 0) {
					$objWriter->writeAttribute('width', '9.10');
				}
				else {
					$objWriter->writeAttribute('width', PHPExcel_Shared_String::FormatNumber($colDimension->getWidth()));
				}

				if ($colDimension->getVisible() == false) {
					$objWriter->writeAttribute('hidden', 'true');
				}

				if ($colDimension->getAutoSize()) {
					$objWriter->writeAttribute('bestFit', 'true');
				}

				if ($colDimension->getWidth() != $pSheet->getDefaultColumnDimension()->getWidth()) {
					$objWriter->writeAttribute('customWidth', 'true');
				}

				if ($colDimension->getCollapsed() == true) {
					$objWriter->writeAttribute('collapsed', 'true');
				}

				if (0 < $colDimension->getOutlineLevel()) {
					$objWriter->writeAttribute('outlineLevel', $colDimension->getOutlineLevel());
				}

				$objWriter->writeAttribute('style', $colDimension->getXfIndex());
				$objWriter->endElement();
			}

			$objWriter->endElement();
		}
	}

	private function _writeSheetProtection(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('sheetProtection');

		if ($pSheet->getProtection()->getPassword() != '') {
			$objWriter->writeAttribute('password', $pSheet->getProtection()->getPassword());
		}

		$objWriter->writeAttribute('sheet', $pSheet->getProtection()->getSheet() ? 'true' : 'false');
		$objWriter->writeAttribute('objects', $pSheet->getProtection()->getObjects() ? 'true' : 'false');
		$objWriter->writeAttribute('scenarios', $pSheet->getProtection()->getScenarios() ? 'true' : 'false');
		$objWriter->writeAttribute('formatCells', $pSheet->getProtection()->getFormatCells() ? 'true' : 'false');
		$objWriter->writeAttribute('formatColumns', $pSheet->getProtection()->getFormatColumns() ? 'true' : 'false');
		$objWriter->writeAttribute('formatRows', $pSheet->getProtection()->getFormatRows() ? 'true' : 'false');
		$objWriter->writeAttribute('insertColumns', $pSheet->getProtection()->getInsertColumns() ? 'true' : 'false');
		$objWriter->writeAttribute('insertRows', $pSheet->getProtection()->getInsertRows() ? 'true' : 'false');
		$objWriter->writeAttribute('insertHyperlinks', $pSheet->getProtection()->getInsertHyperlinks() ? 'true' : 'false');
		$objWriter->writeAttribute('deleteColumns', $pSheet->getProtection()->getDeleteColumns() ? 'true' : 'false');
		$objWriter->writeAttribute('deleteRows', $pSheet->getProtection()->getDeleteRows() ? 'true' : 'false');
		$objWriter->writeAttribute('selectLockedCells', $pSheet->getProtection()->getSelectLockedCells() ? 'true' : 'false');
		$objWriter->writeAttribute('sort', $pSheet->getProtection()->getSort() ? 'true' : 'false');
		$objWriter->writeAttribute('autoFilter', $pSheet->getProtection()->getAutoFilter() ? 'true' : 'false');
		$objWriter->writeAttribute('pivotTables', $pSheet->getProtection()->getPivotTables() ? 'true' : 'false');
		$objWriter->writeAttribute('selectUnlockedCells', $pSheet->getProtection()->getSelectUnlockedCells() ? 'true' : 'false');
		$objWriter->endElement();
	}

	private function _writeConditionalFormatting(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$id = 1;

		foreach ($pSheet->getConditionalStylesCollection() as $cellCoordinate => $conditionalStyles) {
			foreach ($conditionalStyles as $conditional) {
				if ($conditional->getConditionType() != PHPExcel_Style_Conditional::CONDITION_NONE) {
					$objWriter->startElement('conditionalFormatting');
					$objWriter->writeAttribute('sqref', $cellCoordinate);
					$objWriter->startElement('cfRule');
					$objWriter->writeAttribute('type', $conditional->getConditionType());
					$objWriter->writeAttribute('dxfId', $this->getParentWriter()->getStylesConditionalHashTable()->getIndexForHashCode($conditional->getHashCode()));
					$objWriter->writeAttribute('priority', $id++);
					if ((($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CELLIS) || ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT)) && ($conditional->getOperatorType() != PHPExcel_Style_Conditional::OPERATOR_NONE)) {
						$objWriter->writeAttribute('operator', $conditional->getOperatorType());
					}

					if (($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT) && !is_null($conditional->getText())) {
						$objWriter->writeAttribute('text', $conditional->getText());
					}

					if (($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT) && ($conditional->getOperatorType() == PHPExcel_Style_Conditional::OPERATOR_CONTAINSTEXT) && !is_null($conditional->getText())) {
						$objWriter->writeElement('formula', 'NOT(ISERROR(SEARCH("' . $conditional->getText() . '",' . $cellCoordinate . ')))');
					}
					else {
						if (($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT) && ($conditional->getOperatorType() == PHPExcel_Style_Conditional::OPERATOR_BEGINSWITH) && !is_null($conditional->getText())) {
							$objWriter->writeElement('formula', 'LEFT(' . $cellCoordinate . ',' . strlen($conditional->getText()) . ')="' . $conditional->getText() . '"');
						}
						else {
							if (($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT) && ($conditional->getOperatorType() == PHPExcel_Style_Conditional::OPERATOR_ENDSWITH) && !is_null($conditional->getText())) {
								$objWriter->writeElement('formula', 'RIGHT(' . $cellCoordinate . ',' . strlen($conditional->getText()) . ')="' . $conditional->getText() . '"');
							}
							else {
								if (($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT) && ($conditional->getOperatorType() == PHPExcel_Style_Conditional::OPERATOR_NOTCONTAINS) && !is_null($conditional->getText())) {
									$objWriter->writeElement('formula', 'ISERROR(SEARCH("' . $conditional->getText() . '",' . $cellCoordinate . '))');
								}
								else {
									if (($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CELLIS) || ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_CONTAINSTEXT) || ($conditional->getConditionType() == PHPExcel_Style_Conditional::CONDITION_EXPRESSION)) {
										foreach ($conditional->getConditions() as $formula) {
											$objWriter->writeElement('formula', $formula);
										}
									}
								}
							}
						}
					}

					$objWriter->endElement();
					$objWriter->endElement();
				}
			}
		}
	}

	private function _writeDataValidations(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$dataValidationCollection = $pSheet->getDataValidationCollection();

		if (!empty($dataValidationCollection)) {
			$objWriter->startElement('dataValidations');
			$objWriter->writeAttribute('count', count($dataValidationCollection));

			foreach ($dataValidationCollection as $coordinate => $dv) {
				$objWriter->startElement('dataValidation');

				if ($dv->getType() != '') {
					$objWriter->writeAttribute('type', $dv->getType());
				}

				if ($dv->getErrorStyle() != '') {
					$objWriter->writeAttribute('errorStyle', $dv->getErrorStyle());
				}

				if ($dv->getOperator() != '') {
					$objWriter->writeAttribute('operator', $dv->getOperator());
				}

				$objWriter->writeAttribute('allowBlank', $dv->getAllowBlank() ? '1' : '0');
				$objWriter->writeAttribute('showDropDown', !$dv->getShowDropDown() ? '1' : '0');
				$objWriter->writeAttribute('showInputMessage', $dv->getShowInputMessage() ? '1' : '0');
				$objWriter->writeAttribute('showErrorMessage', $dv->getShowErrorMessage() ? '1' : '0');

				if ($dv->getErrorTitle() !== '') {
					$objWriter->writeAttribute('errorTitle', $dv->getErrorTitle());
				}

				if ($dv->getError() !== '') {
					$objWriter->writeAttribute('error', $dv->getError());
				}

				if ($dv->getPromptTitle() !== '') {
					$objWriter->writeAttribute('promptTitle', $dv->getPromptTitle());
				}

				if ($dv->getPrompt() !== '') {
					$objWriter->writeAttribute('prompt', $dv->getPrompt());
				}

				$objWriter->writeAttribute('sqref', $coordinate);

				if ($dv->getFormula1() !== '') {
					$objWriter->writeElement('formula1', $dv->getFormula1());
				}

				if ($dv->getFormula2() !== '') {
					$objWriter->writeElement('formula2', $dv->getFormula2());
				}

				$objWriter->endElement();
			}

			$objWriter->endElement();
		}
	}

	private function _writeHyperlinks(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$hyperlinkCollection = $pSheet->getHyperlinkCollection();
		$relationId = 1;

		if (!empty($hyperlinkCollection)) {
			$objWriter->startElement('hyperlinks');

			foreach ($hyperlinkCollection as $coordinate => $hyperlink) {
				$objWriter->startElement('hyperlink');
				$objWriter->writeAttribute('ref', $coordinate);

				if (!$hyperlink->isInternal()) {
					$objWriter->writeAttribute('r:id', 'rId_hyperlink_' . $relationId);
					++$relationId;
				}
				else {
					$objWriter->writeAttribute('location', str_replace('sheet://', '', $hyperlink->getUrl()));
				}

				if ($hyperlink->getTooltip() != '') {
					$objWriter->writeAttribute('tooltip', $hyperlink->getTooltip());
				}

				$objWriter->endElement();
			}

			$objWriter->endElement();
		}
	}

	private function _writeProtectedRanges(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		if (0 < count($pSheet->getProtectedCells())) {
			$objWriter->startElement('protectedRanges');

			foreach ($pSheet->getProtectedCells() as $protectedCell => $passwordHash) {
				$objWriter->startElement('protectedRange');
				$objWriter->writeAttribute('name', 'p' . md5($protectedCell));
				$objWriter->writeAttribute('sqref', $protectedCell);

				if (!empty($passwordHash)) {
					$objWriter->writeAttribute('password', $passwordHash);
				}

				$objWriter->endElement();
			}

			$objWriter->endElement();
		}
	}

	private function _writeMergeCells(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		if (0 < count($pSheet->getMergeCells())) {
			$objWriter->startElement('mergeCells');

			foreach ($pSheet->getMergeCells() as $mergeCell) {
				$objWriter->startElement('mergeCell');
				$objWriter->writeAttribute('ref', $mergeCell);
				$objWriter->endElement();
			}

			$objWriter->endElement();
		}
	}

	private function _writePrintOptions(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('printOptions');
		$objWriter->writeAttribute('gridLines', $pSheet->getPrintGridlines() ? 'true' : 'false');
		$objWriter->writeAttribute('gridLinesSet', 'true');

		if ($pSheet->getPageSetup()->getHorizontalCentered()) {
			$objWriter->writeAttribute('horizontalCentered', 'true');
		}

		if ($pSheet->getPageSetup()->getVerticalCentered()) {
			$objWriter->writeAttribute('verticalCentered', 'true');
		}

		$objWriter->endElement();
	}

	private function _writePageMargins(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('pageMargins');
		$objWriter->writeAttribute('left', PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getLeft()));
		$objWriter->writeAttribute('right', PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getRight()));
		$objWriter->writeAttribute('top', PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getTop()));
		$objWriter->writeAttribute('bottom', PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getBottom()));
		$objWriter->writeAttribute('header', PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getHeader()));
		$objWriter->writeAttribute('footer', PHPExcel_Shared_String::FormatNumber($pSheet->getPageMargins()->getFooter()));
		$objWriter->endElement();
	}

	private function _writeAutoFilter(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$autoFilterRange = $pSheet->getAutoFilter()->getRange();

		if (!empty($autoFilterRange)) {
			$objWriter->startElement('autoFilter');
			$range = PHPExcel_Cell::splitRange($autoFilterRange);
			$range = $range[0];

			if (strpos($range[0], '!') !== false) {
				list($ws, $range[0]) = explode('!', $range[0]);
			}

			$range = implode(':', $range);
			$objWriter->writeAttribute('ref', str_replace('$', '', $range));
			$columns = $pSheet->getAutoFilter()->getColumns();

			if (count(0 < $columns)) {
				foreach ($columns as $columnID => $column) {
					$rules = $column->getRules();

					if (count(0 < $rules)) {
						$objWriter->startElement('filterColumn');
						$objWriter->writeAttribute('colId', $pSheet->getAutoFilter()->getColumnOffset($columnID));
						$objWriter->startElement($column->getFilterType());

						if ($column->getJoin() == PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND) {
							$objWriter->writeAttribute('and', 1);
						}

						foreach ($rules as $rule) {
							if (($column->getFilterType() === PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_FILTER) && ($rule->getOperator() === PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL) && ($rule->getValue() === '')) {
								$objWriter->writeAttribute('blank', 1);
							}
							else if ($rule->getRuleType() === PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMICFILTER) {
								$objWriter->writeAttribute('type', $rule->getGrouping());
								$val = $column->getAttribute('val');

								if ($val !== NULL) {
									$objWriter->writeAttribute('val', $val);
								}

								$maxVal = $column->getAttribute('maxVal');

								if ($maxVal !== NULL) {
									$objWriter->writeAttribute('maxVal', $maxVal);
								}
							}
							else if ($rule->getRuleType() === PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_TOPTENFILTER) {
								$objWriter->writeAttribute('val', $rule->getValue());
								$objWriter->writeAttribute('percent', $rule->getOperator() === PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT ? '1' : '0');
								$objWriter->writeAttribute('top', $rule->getGrouping() === PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP ? '1' : '0');
							}
							else {
								$objWriter->startElement($rule->getRuleType());

								if ($rule->getOperator() !== PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL) {
									$objWriter->writeAttribute('operator', $rule->getOperator());
								}

								if ($rule->getRuleType() === PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP) {
									foreach ($rule->getValue() as $key => $value) {
										if ('' < $value) {
											$objWriter->writeAttribute($key, $value);
										}
									}

									$objWriter->writeAttribute('dateTimeGrouping', $rule->getGrouping());
								}
								else {
									$objWriter->writeAttribute('val', $rule->getValue());
								}

								$objWriter->endElement();
							}
						}

						$objWriter->endElement();
						$objWriter->endElement();
					}
				}
			}

			$objWriter->endElement();
		}
	}

	private function _writePageSetup(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('pageSetup');
		$objWriter->writeAttribute('paperSize', $pSheet->getPageSetup()->getPaperSize());
		$objWriter->writeAttribute('orientation', $pSheet->getPageSetup()->getOrientation());

		if (!is_null($pSheet->getPageSetup()->getScale())) {
			$objWriter->writeAttribute('scale', $pSheet->getPageSetup()->getScale());
		}

		if (!is_null($pSheet->getPageSetup()->getFitToHeight())) {
			$objWriter->writeAttribute('fitToHeight', $pSheet->getPageSetup()->getFitToHeight());
		}
		else {
			$objWriter->writeAttribute('fitToHeight', '0');
		}

		if (!is_null($pSheet->getPageSetup()->getFitToWidth())) {
			$objWriter->writeAttribute('fitToWidth', $pSheet->getPageSetup()->getFitToWidth());
		}
		else {
			$objWriter->writeAttribute('fitToWidth', '0');
		}

		if (!is_null($pSheet->getPageSetup()->getFirstPageNumber())) {
			$objWriter->writeAttribute('firstPageNumber', $pSheet->getPageSetup()->getFirstPageNumber());
			$objWriter->writeAttribute('useFirstPageNumber', '1');
		}

		$objWriter->endElement();
	}

	private function _writeHeaderFooter(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$objWriter->startElement('headerFooter');
		$objWriter->writeAttribute('differentOddEven', $pSheet->getHeaderFooter()->getDifferentOddEven() ? 'true' : 'false');
		$objWriter->writeAttribute('differentFirst', $pSheet->getHeaderFooter()->getDifferentFirst() ? 'true' : 'false');
		$objWriter->writeAttribute('scaleWithDoc', $pSheet->getHeaderFooter()->getScaleWithDocument() ? 'true' : 'false');
		$objWriter->writeAttribute('alignWithMargins', $pSheet->getHeaderFooter()->getAlignWithMargins() ? 'true' : 'false');
		$objWriter->writeElement('oddHeader', $pSheet->getHeaderFooter()->getOddHeader());
		$objWriter->writeElement('oddFooter', $pSheet->getHeaderFooter()->getOddFooter());
		$objWriter->writeElement('evenHeader', $pSheet->getHeaderFooter()->getEvenHeader());
		$objWriter->writeElement('evenFooter', $pSheet->getHeaderFooter()->getEvenFooter());
		$objWriter->writeElement('firstHeader', $pSheet->getHeaderFooter()->getFirstHeader());
		$objWriter->writeElement('firstFooter', $pSheet->getHeaderFooter()->getFirstFooter());
		$objWriter->endElement();
	}

	private function _writeBreaks(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		$aRowBreaks = array();
		$aColumnBreaks = array();

		foreach ($pSheet->getBreaks() as $cell => $breakType) {
			if ($breakType == PHPExcel_Worksheet::BREAK_ROW) {
				$aRowBreaks[] = $cell;
			}
			else if ($breakType == PHPExcel_Worksheet::BREAK_COLUMN) {
				$aColumnBreaks[] = $cell;
			}
		}

		if (!empty($aRowBreaks)) {
			$objWriter->startElement('rowBreaks');
			$objWriter->writeAttribute('count', count($aRowBreaks));
			$objWriter->writeAttribute('manualBreakCount', count($aRowBreaks));

			foreach ($aRowBreaks as $cell) {
				$coords = PHPExcel_Cell::coordinateFromString($cell);
				$objWriter->startElement('brk');
				$objWriter->writeAttribute('id', $coords[1]);
				$objWriter->writeAttribute('man', '1');
				$objWriter->endElement();
			}

			$objWriter->endElement();
		}

		if (!empty($aColumnBreaks)) {
			$objWriter->startElement('colBreaks');
			$objWriter->writeAttribute('count', count($aColumnBreaks));
			$objWriter->writeAttribute('manualBreakCount', count($aColumnBreaks));

			foreach ($aColumnBreaks as $cell) {
				$coords = PHPExcel_Cell::coordinateFromString($cell);
				$objWriter->startElement('brk');
				$objWriter->writeAttribute('id', PHPExcel_Cell::columnIndexFromString($coords[0]) - 1);
				$objWriter->writeAttribute('man', '1');
				$objWriter->endElement();
			}

			$objWriter->endElement();
		}
	}

	private function _writeSheetData(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL, $pStringTable = NULL)
	{
		if (is_array($pStringTable)) {
			$aFlippedStringTable = $this->getParentWriter()->getWriterPart('stringtable')->flipStringTable($pStringTable);
			$objWriter->startElement('sheetData');
			$colCount = PHPExcel_Cell::columnIndexFromString($pSheet->getHighestColumn());
			$highestRow = $pSheet->getHighestRow();
			$cellsByRow = array();

			foreach ($pSheet->getCellCollection() as $cellID) {
				$cellAddress = PHPExcel_Cell::coordinateFromString($cellID);
				$cellsByRow[$cellAddress[1]][] = $cellID;
			}

			$currentRow = 0;

			while ($currentRow++ < $highestRow) {
				$rowDimension = $pSheet->getRowDimension($currentRow);
				$writeCurrentRow = isset($cellsByRow[$currentRow]) || (0 <= $rowDimension->getRowHeight()) || ($rowDimension->getVisible() == false) || ($rowDimension->getCollapsed() == true) || (0 < $rowDimension->getOutlineLevel()) || ($rowDimension->getXfIndex() !== NULL);

				if ($writeCurrentRow) {
					$objWriter->startElement('row');
					$objWriter->writeAttribute('r', $currentRow);
					$objWriter->writeAttribute('spans', '1:' . $colCount);

					if (0 <= $rowDimension->getRowHeight()) {
						$objWriter->writeAttribute('customHeight', '1');
						$objWriter->writeAttribute('ht', PHPExcel_Shared_String::FormatNumber($rowDimension->getRowHeight()));
					}

					if ($rowDimension->getVisible() == false) {
						$objWriter->writeAttribute('hidden', 'true');
					}

					if ($rowDimension->getCollapsed() == true) {
						$objWriter->writeAttribute('collapsed', 'true');
					}

					if (0 < $rowDimension->getOutlineLevel()) {
						$objWriter->writeAttribute('outlineLevel', $rowDimension->getOutlineLevel());
					}

					if ($rowDimension->getXfIndex() !== NULL) {
						$objWriter->writeAttribute('s', $rowDimension->getXfIndex());
						$objWriter->writeAttribute('customFormat', '1');
					}

					if (isset($cellsByRow[$currentRow])) {
						foreach ($cellsByRow[$currentRow] as $cellAddress) {
							$this->_writeCell($objWriter, $pSheet, $cellAddress, $pStringTable, $aFlippedStringTable);
						}
					}

					$objWriter->endElement();
				}
			}

			$objWriter->endElement();
		}
		else {
			throw new PHPExcel_Writer_Exception('Invalid parameters passed.');
		}
	}

	private function _writeCell(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL, $pCellAddress = NULL, $pStringTable = NULL, $pFlippedStringTable = NULL)
	{
		if (is_array($pStringTable) && is_array($pFlippedStringTable)) {
			$pCell = $pSheet->getCell($pCellAddress);
			$objWriter->startElement('c');
			$objWriter->writeAttribute('r', $pCellAddress);

			if ($pCell->getXfIndex() != '') {
				$objWriter->writeAttribute('s', $pCell->getXfIndex());
			}

			$cellValue = $pCell->getValue();
			if (is_object($cellValue) || ($cellValue !== '')) {
				$mappedType = $pCell->getDataType();

				switch (strtolower($mappedType)) {
				case 'inlinestr':
				case 's':
				case 'b':
					$objWriter->writeAttribute('t', $mappedType);
					break;

				case 'f':
					$calculatedValue = ($this->getParentWriter()->getPreCalculateFormulas() ? $pCell->getCalculatedValue() : $cellValue);

					if (is_string($calculatedValue)) {
						$objWriter->writeAttribute('t', 'str');
					}

					break;

				case 'e':
					$objWriter->writeAttribute('t', $mappedType);
				}

				switch (strtolower($mappedType)) {
				case 'inlinestr':
					if (!$cellValue instanceof PHPExcel_RichText) {
						$objWriter->writeElement('t', PHPExcel_Shared_String::ControlCharacterPHP2OOXML(htmlspecialchars($cellValue)));
					}
					else if ($cellValue instanceof PHPExcel_RichText) {
						$objWriter->startElement('is');
						$this->getParentWriter()->getWriterPart('stringtable')->writeRichText($objWriter, $cellValue);
						$objWriter->endElement();
					}

					break;

				case 's':
					if (!$cellValue instanceof PHPExcel_RichText) {
						if (isset($pFlippedStringTable[$cellValue])) {
							$objWriter->writeElement('v', $pFlippedStringTable[$cellValue]);
						}
					}
					else if ($cellValue instanceof PHPExcel_RichText) {
						$objWriter->writeElement('v', $pFlippedStringTable[$cellValue->getHashCode()]);
					}

					break;

				case 'f':
					$attributes = $pCell->getFormulaAttributes();

					if ($attributes['t'] == 'array') {
						$objWriter->startElement('f');
						$objWriter->writeAttribute('t', 'array');
						$objWriter->writeAttribute('ref', $pCellAddress);
						$objWriter->writeAttribute('aca', '1');
						$objWriter->writeAttribute('ca', '1');
						$objWriter->text(substr($cellValue, 1));
						$objWriter->endElement();
					}
					else {
						$objWriter->writeElement('f', substr($cellValue, 1));
					}

					if ($this->getParentWriter()->getOffice2003Compatibility() === false) {
						if ($this->getParentWriter()->getPreCalculateFormulas()) {
							if (!is_array($calculatedValue) && (substr($calculatedValue, 0, 1) != '#')) {
								$objWriter->writeElement('v', PHPExcel_Shared_String::FormatNumber($calculatedValue));
							}
							else {
								$objWriter->writeElement('v', '0');
							}
						}
						else {
							$objWriter->writeElement('v', '0');
						}
					}

					break;

				case 'n':
					$objWriter->writeElement('v', str_replace(',', '.', $cellValue));
					break;

				case 'b':
					$objWriter->writeElement('v', $cellValue ? '1' : '0');
					break;

				case 'e':
					if (substr($cellValue, 0, 1) == '=') {
						$objWriter->writeElement('f', substr($cellValue, 1));
						$objWriter->writeElement('v', substr($cellValue, 1));
					}
					else {
						$objWriter->writeElement('v', $cellValue);
					}

					break;
				}
			}

			$objWriter->endElement();
		}
		else {
			throw new PHPExcel_Writer_Exception('Invalid parameters passed.');
		}
	}

	private function _writeDrawings(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL, $includeCharts = false)
	{
		$chartCount = ($includeCharts ? $pSheet->getChartCollection()->count() : 0);
		if ((0 < $pSheet->getDrawingCollection()->count()) || (0 < $chartCount)) {
			$objWriter->startElement('drawing');
			$objWriter->writeAttribute('r:id', 'rId1');
			$objWriter->endElement();
		}
	}

	private function _writeLegacyDrawing(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		if (0 < count($pSheet->getComments())) {
			$objWriter->startElement('legacyDrawing');
			$objWriter->writeAttribute('r:id', 'rId_comments_vml1');
			$objWriter->endElement();
		}
	}

	private function _writeLegacyDrawingHF(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL)
	{
		if (0 < count($pSheet->getHeaderFooter()->getImages())) {
			$objWriter->startElement('legacyDrawingHF');
			$objWriter->writeAttribute('r:id', 'rId_headerfooter_vml1');
			$objWriter->endElement();
		}
	}
}

?>

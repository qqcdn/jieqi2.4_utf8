<?php

class PHPExcel_Writer_Excel2007_Workbook extends PHPExcel_Writer_Excel2007_WriterPart
{
	public function writeWorkbook(PHPExcel $pPHPExcel = NULL, $recalcRequired = false)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$objWriter->startElement('workbook');
		$objWriter->writeAttribute('xml:space', 'preserve');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
		$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
		$this->_writeFileVersion($objWriter);
		$this->_writeWorkbookPr($objWriter);
		$this->_writeWorkbookProtection($objWriter, $pPHPExcel);

		if ($this->getParentWriter()->getOffice2003Compatibility() === false) {
			$this->_writeBookViews($objWriter, $pPHPExcel);
		}

		$this->_writeSheets($objWriter, $pPHPExcel);
		$this->_writeDefinedNames($objWriter, $pPHPExcel);
		$this->_writeCalcPr($objWriter, $recalcRequired);
		$objWriter->endElement();
		return $objWriter->getData();
	}

	private function _writeFileVersion(PHPExcel_Shared_XMLWriter $objWriter = NULL)
	{
		$objWriter->startElement('fileVersion');
		$objWriter->writeAttribute('appName', 'xl');
		$objWriter->writeAttribute('lastEdited', '4');
		$objWriter->writeAttribute('lowestEdited', '4');
		$objWriter->writeAttribute('rupBuild', '4505');
		$objWriter->endElement();
	}

	private function _writeWorkbookPr(PHPExcel_Shared_XMLWriter $objWriter = NULL)
	{
		$objWriter->startElement('workbookPr');

		if (PHPExcel_Shared_Date::getExcelCalendar() == PHPExcel_Shared_Date::CALENDAR_MAC_1904) {
			$objWriter->writeAttribute('date1904', '1');
		}

		$objWriter->writeAttribute('codeName', 'ThisWorkbook');
		$objWriter->endElement();
	}

	private function _writeBookViews(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel $pPHPExcel = NULL)
	{
		$objWriter->startElement('bookViews');
		$objWriter->startElement('workbookView');
		$objWriter->writeAttribute('activeTab', $pPHPExcel->getActiveSheetIndex());
		$objWriter->writeAttribute('autoFilterDateGrouping', '1');
		$objWriter->writeAttribute('firstSheet', '0');
		$objWriter->writeAttribute('minimized', '0');
		$objWriter->writeAttribute('showHorizontalScroll', '1');
		$objWriter->writeAttribute('showSheetTabs', '1');
		$objWriter->writeAttribute('showVerticalScroll', '1');
		$objWriter->writeAttribute('tabRatio', '600');
		$objWriter->writeAttribute('visibility', 'visible');
		$objWriter->endElement();
		$objWriter->endElement();
	}

	private function _writeWorkbookProtection(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel $pPHPExcel = NULL)
	{
		if ($pPHPExcel->getSecurity()->isSecurityEnabled()) {
			$objWriter->startElement('workbookProtection');
			$objWriter->writeAttribute('lockRevision', $pPHPExcel->getSecurity()->getLockRevision() ? 'true' : 'false');
			$objWriter->writeAttribute('lockStructure', $pPHPExcel->getSecurity()->getLockStructure() ? 'true' : 'false');
			$objWriter->writeAttribute('lockWindows', $pPHPExcel->getSecurity()->getLockWindows() ? 'true' : 'false');

			if ($pPHPExcel->getSecurity()->getRevisionsPassword() != '') {
				$objWriter->writeAttribute('revisionsPassword', $pPHPExcel->getSecurity()->getRevisionsPassword());
			}

			if ($pPHPExcel->getSecurity()->getWorkbookPassword() != '') {
				$objWriter->writeAttribute('workbookPassword', $pPHPExcel->getSecurity()->getWorkbookPassword());
			}

			$objWriter->endElement();
		}
	}

	private function _writeCalcPr(PHPExcel_Shared_XMLWriter $objWriter = NULL, $recalcRequired = true)
	{
		$objWriter->startElement('calcPr');
		$objWriter->writeAttribute('calcId', '999999');
		$objWriter->writeAttribute('calcMode', 'auto');
		$objWriter->writeAttribute('calcCompleted', $recalcRequired ? 1 : 0);
		$objWriter->writeAttribute('fullCalcOnLoad', $recalcRequired ? 0 : 1);
		$objWriter->endElement();
	}

	private function _writeSheets(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel $pPHPExcel = NULL)
	{
		$objWriter->startElement('sheets');
		$sheetCount = $pPHPExcel->getSheetCount();

		for ($i = 0; $i < $sheetCount; ++$i) {
			$this->_writeSheet($objWriter, $pPHPExcel->getSheet($i)->getTitle(), $i + 1, $i + 1 + 3, $pPHPExcel->getSheet($i)->getSheetState());
		}

		$objWriter->endElement();
	}

	private function _writeSheet(PHPExcel_Shared_XMLWriter $objWriter = NULL, $pSheetname = '', $pSheetId = 1, $pRelId = 1, $sheetState = 'visible')
	{
		if ($pSheetname != '') {
			$objWriter->startElement('sheet');
			$objWriter->writeAttribute('name', $pSheetname);
			$objWriter->writeAttribute('sheetId', $pSheetId);
			if (($sheetState != 'visible') && ($sheetState != '')) {
				$objWriter->writeAttribute('state', $sheetState);
			}

			$objWriter->writeAttribute('r:id', 'rId' . $pRelId);
			$objWriter->endElement();
		}
		else {
			throw new PHPExcel_Writer_Exception('Invalid parameters passed.');
		}
	}

	private function _writeDefinedNames(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel $pPHPExcel = NULL)
	{
		$objWriter->startElement('definedNames');

		if (0 < count($pPHPExcel->getNamedRanges())) {
			$this->_writeNamedRanges($objWriter, $pPHPExcel);
		}

		$sheetCount = $pPHPExcel->getSheetCount();

		for ($i = 0; $i < $sheetCount; ++$i) {
			$this->_writeDefinedNameForAutofilter($objWriter, $pPHPExcel->getSheet($i), $i);
			$this->_writeDefinedNameForPrintTitles($objWriter, $pPHPExcel->getSheet($i), $i);
			$this->_writeDefinedNameForPrintArea($objWriter, $pPHPExcel->getSheet($i), $i);
		}

		$objWriter->endElement();
	}

	private function _writeNamedRanges(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel $pPHPExcel)
	{
		$namedRanges = $pPHPExcel->getNamedRanges();

		foreach ($namedRanges as $namedRange) {
			$this->_writeDefinedNameForNamedRange($objWriter, $namedRange);
		}
	}

	private function _writeDefinedNameForNamedRange(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_NamedRange $pNamedRange)
	{
		$objWriter->startElement('definedName');
		$objWriter->writeAttribute('name', $pNamedRange->getName());

		if ($pNamedRange->getLocalOnly()) {
			$objWriter->writeAttribute('localSheetId', $pNamedRange->getScope()->getParent()->getIndex($pNamedRange->getScope()));
		}

		$range = PHPExcel_Cell::splitRange($pNamedRange->getRange());

		for ($i = 0; $i < count($range); $i++) {
			$range[$i][0] = '\'' . str_replace('\'', '\'\'', $pNamedRange->getWorksheet()->getTitle()) . '\'!' . PHPExcel_Cell::absoluteReference($range[$i][0]);

			if (isset($range[$i][1])) {
				$range[$i][1] = PHPExcel_Cell::absoluteReference($range[$i][1]);
			}
		}

		$range = PHPExcel_Cell::buildRange($range);
		$objWriter->writeRawData($range);
		$objWriter->endElement();
	}

	private function _writeDefinedNameForAutofilter(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL, $pSheetId = 0)
	{
		$autoFilterRange = $pSheet->getAutoFilter()->getRange();

		if (!empty($autoFilterRange)) {
			$objWriter->startElement('definedName');
			$objWriter->writeAttribute('name', '_xlnm._FilterDatabase');
			$objWriter->writeAttribute('localSheetId', $pSheetId);
			$objWriter->writeAttribute('hidden', '1');
			$range = PHPExcel_Cell::splitRange($autoFilterRange);
			$range = $range[0];

			if (strpos($range[0], '!') !== false) {
				list($ws, $range[0]) = explode('!', $range[0]);
			}

			$range[0] = PHPExcel_Cell::absoluteCoordinate($range[0]);
			$range[1] = PHPExcel_Cell::absoluteCoordinate($range[1]);
			$range = implode(':', $range);
			$objWriter->writeRawData('\'' . str_replace('\'', '\'\'', $pSheet->getTitle()) . '\'!' . $range);
			$objWriter->endElement();
		}
	}

	private function _writeDefinedNameForPrintTitles(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL, $pSheetId = 0)
	{
		if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet() || $pSheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
			$objWriter->startElement('definedName');
			$objWriter->writeAttribute('name', '_xlnm.Print_Titles');
			$objWriter->writeAttribute('localSheetId', $pSheetId);
			$settingString = '';

			if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
				$repeat = $pSheet->getPageSetup()->getColumnsToRepeatAtLeft();
				$settingString .= '\'' . str_replace('\'', '\'\'', $pSheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
			}

			if ($pSheet->getPageSetup()->isRowsToRepeatAtTopSet()) {
				if ($pSheet->getPageSetup()->isColumnsToRepeatAtLeftSet()) {
					$settingString .= ',';
				}

				$repeat = $pSheet->getPageSetup()->getRowsToRepeatAtTop();
				$settingString .= '\'' . str_replace('\'', '\'\'', $pSheet->getTitle()) . '\'!$' . $repeat[0] . ':$' . $repeat[1];
			}

			$objWriter->writeRawData($settingString);
			$objWriter->endElement();
		}
	}

	private function _writeDefinedNameForPrintArea(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet $pSheet = NULL, $pSheetId = 0)
	{
		if ($pSheet->getPageSetup()->isPrintAreaSet()) {
			$objWriter->startElement('definedName');
			$objWriter->writeAttribute('name', '_xlnm.Print_Area');
			$objWriter->writeAttribute('localSheetId', $pSheetId);
			$settingString = '';
			$printArea = PHPExcel_Cell::splitRange($pSheet->getPageSetup()->getPrintArea());
			$chunks = array();

			foreach ($printArea as $printAreaRect) {
				$printAreaRect[0] = PHPExcel_Cell::absoluteReference($printAreaRect[0]);
				$printAreaRect[1] = PHPExcel_Cell::absoluteReference($printAreaRect[1]);
				$chunks[] = '\'' . str_replace('\'', '\'\'', $pSheet->getTitle()) . '\'!' . implode(':', $printAreaRect);
			}

			$objWriter->writeRawData(implode(',', $chunks));
			$objWriter->endElement();
		}
	}
}

?>

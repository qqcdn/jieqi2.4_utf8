<?php

class PHPExcel_Writer_Excel2007_Rels extends PHPExcel_Writer_Excel2007_WriterPart
{
	public function writeRelationships(PHPExcel $pPHPExcel = NULL)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');
		$customPropertyList = $pPHPExcel->getProperties()->getCustomProperties();

		if (!empty($customPropertyList)) {
			$this->_writeRelationship($objWriter, 4, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/custom-properties', 'docProps/custom.xml');
		}

		$this->_writeRelationship($objWriter, 3, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties', 'docProps/app.xml');
		$this->_writeRelationship($objWriter, 2, 'http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties', 'docProps/core.xml');
		$this->_writeRelationship($objWriter, 1, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument', 'xl/workbook.xml');

		if ($pPHPExcel->hasRibbon()) {
			$this->_writeRelationShip($objWriter, 5, 'http://schemas.microsoft.com/office/2006/relationships/ui/extensibility', $pPHPExcel->getRibbonXMLData('target'));
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}

	public function writeWorkbookRelationships(PHPExcel $pPHPExcel = NULL)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');
		$this->_writeRelationship($objWriter, 1, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles', 'styles.xml');
		$this->_writeRelationship($objWriter, 2, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/theme', 'theme/theme1.xml');
		$this->_writeRelationship($objWriter, 3, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings', 'sharedStrings.xml');
		$sheetCount = $pPHPExcel->getSheetCount();

		for ($i = 0; $i < $sheetCount; ++$i) {
			$this->_writeRelationship($objWriter, $i + 1 + 3, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet', 'worksheets/sheet' . ($i + 1) . '.xml');
		}

		if ($pPHPExcel->hasMacros()) {
			$this->_writeRelationShip($objWriter, $i + 1 + 3, 'http://schemas.microsoft.com/office/2006/relationships/vbaProject', 'vbaProject.bin');
			++$i;
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}

	public function writeWorksheetRelationships(PHPExcel_Worksheet $pWorksheet = NULL, $pWorksheetId = 1, $includeCharts = false)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');
		$d = 0;

		if ($includeCharts) {
			$charts = $pWorksheet->getChartCollection();
		}
		else {
			$charts = array();
		}

		if ((0 < $pWorksheet->getDrawingCollection()->count()) || (0 < count($charts))) {
			$this->_writeRelationship($objWriter, ++$d, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/drawing', '../drawings/drawing' . $pWorksheetId . '.xml');
		}

		$i = 1;

		foreach ($pWorksheet->getHyperlinkCollection() as $hyperlink) {
			if (!$hyperlink->isInternal()) {
				$this->_writeRelationship($objWriter, '_hyperlink_' . $i, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/hyperlink', $hyperlink->getUrl(), 'External');
				++$i;
			}
		}

		$i = 1;

		if (0 < count($pWorksheet->getComments())) {
			$this->_writeRelationship($objWriter, '_comments_vml' . $i, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing', '../drawings/vmlDrawing' . $pWorksheetId . '.vml');
			$this->_writeRelationship($objWriter, '_comments' . $i, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/comments', '../comments' . $pWorksheetId . '.xml');
		}

		$i = 1;

		if (0 < count($pWorksheet->getHeaderFooter()->getImages())) {
			$this->_writeRelationship($objWriter, '_headerfooter_vml' . $i, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/vmlDrawing', '../drawings/vmlDrawingHF' . $pWorksheetId . '.vml');
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}

	public function writeDrawingRelationships(PHPExcel_Worksheet $pWorksheet = NULL, &$chartRef, $includeCharts = false)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');
		$i = 1;
		$iterator = $pWorksheet->getDrawingCollection()->getIterator();

		while ($iterator->valid()) {
			if ($iterator->current() instanceof PHPExcel_Worksheet_Drawing || $iterator->current() instanceof PHPExcel_Worksheet_MemoryDrawing) {
				$this->_writeRelationship($objWriter, $i, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image', '../media/' . str_replace(' ', '', $iterator->current()->getIndexedFilename()));
			}

			$iterator->next();
			++$i;
		}

		if ($includeCharts) {
			$chartCount = $pWorksheet->getChartCount();

			if (0 < $chartCount) {
				for ($c = 0; $c < $chartCount; ++$c) {
					$this->_writeRelationship($objWriter, $i++, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/chart', '../charts/chart' . ++$chartRef . '.xml');
				}
			}
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}

	public function writeHeaderFooterDrawingRelationships(PHPExcel_Worksheet $pWorksheet = NULL)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$objWriter->startElement('Relationships');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/package/2006/relationships');

		foreach ($pWorksheet->getHeaderFooter()->getImages() as $key => $value) {
			$this->_writeRelationship($objWriter, $key, 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image', '../media/' . $value->getIndexedFilename());
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}

	private function _writeRelationship(PHPExcel_Shared_XMLWriter $objWriter = NULL, $pId = 1, $pType = '', $pTarget = '', $pTargetMode = '')
	{
		if (($pType != '') && ($pTarget != '')) {
			$objWriter->startElement('Relationship');
			$objWriter->writeAttribute('Id', 'rId' . $pId);
			$objWriter->writeAttribute('Type', $pType);
			$objWriter->writeAttribute('Target', $pTarget);

			if ($pTargetMode != '') {
				$objWriter->writeAttribute('TargetMode', $pTargetMode);
			}

			$objWriter->endElement();
		}
		else {
			throw new PHPExcel_Writer_Exception('Invalid parameters passed.');
		}
	}
}

?>

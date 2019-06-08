<?php

class PHPExcel_Writer_Excel2007_RelsRibbon extends PHPExcel_Writer_Excel2007_WriterPart
{
	public function writeRibbonRelationships(PHPExcel $pPHPExcel = NULL)
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
		$localRels = $pPHPExcel->getRibbonBinObjects('names');

		if (is_array($localRels)) {
			foreach ($localRels as $aId => $aTarget) {
				$objWriter->startElement('Relationship');
				$objWriter->writeAttribute('Id', $aId);
				$objWriter->writeAttribute('Type', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships/image');
				$objWriter->writeAttribute('Target', $aTarget);
				$objWriter->endElement();
			}
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}
}

?>

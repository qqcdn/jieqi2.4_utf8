<?php

class PHPExcel_Writer_Excel2007_RelsVBA extends PHPExcel_Writer_Excel2007_WriterPart
{
	public function writeVBARelationships(PHPExcel $pPHPExcel = NULL)
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
		$objWriter->startElement('Relationship');
		$objWriter->writeAttribute('Id', 'rId1');
		$objWriter->writeAttribute('Type', 'http://schemas.microsoft.com/office/2006/relationships/vbaProjectSignature');
		$objWriter->writeAttribute('Target', 'vbaProjectSignature.bin');
		$objWriter->endElement();
		$objWriter->endElement();
		return $objWriter->getData();
	}
}

?>

<?php

class PHPExcel_Writer_Excel2007_Comments extends PHPExcel_Writer_Excel2007_WriterPart
{
	public function writeComments(PHPExcel_Worksheet $pWorksheet = NULL)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$comments = $pWorksheet->getComments();
		$authors = array();
		$authorId = 0;

		foreach ($comments as $comment) {
			if (!isset($authors[$comment->getAuthor()])) {
				$authors[$comment->getAuthor()] = $authorId++;
			}
		}

		$objWriter->startElement('comments');
		$objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
		$objWriter->startElement('authors');

		foreach ($authors as $author => $index) {
			$objWriter->writeElement('author', $author);
		}

		$objWriter->endElement();
		$objWriter->startElement('commentList');

		foreach ($comments as $key => $value) {
			$this->_writeComment($objWriter, $key, $value, $authors);
		}

		$objWriter->endElement();
		$objWriter->endElement();
		return $objWriter->getData();
	}

	public function _writeComment(PHPExcel_Shared_XMLWriter $objWriter = NULL, $pCellReference = 'A1', PHPExcel_Comment $pComment = NULL, $pAuthors = NULL)
	{
		$objWriter->startElement('comment');
		$objWriter->writeAttribute('ref', $pCellReference);
		$objWriter->writeAttribute('authorId', $pAuthors[$pComment->getAuthor()]);
		$objWriter->startElement('text');
		$this->getParentWriter()->getWriterPart('stringtable')->writeRichText($objWriter, $pComment->getText());
		$objWriter->endElement();
		$objWriter->endElement();
	}

	public function writeVMLComments(PHPExcel_Worksheet $pWorksheet = NULL)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$comments = $pWorksheet->getComments();
		$objWriter->startElement('xml');
		$objWriter->writeAttribute('xmlns:v', 'urn:schemas-microsoft-com:vml');
		$objWriter->writeAttribute('xmlns:o', 'urn:schemas-microsoft-com:office:office');
		$objWriter->writeAttribute('xmlns:x', 'urn:schemas-microsoft-com:office:excel');
		$objWriter->startElement('o:shapelayout');
		$objWriter->writeAttribute('v:ext', 'edit');
		$objWriter->startElement('o:idmap');
		$objWriter->writeAttribute('v:ext', 'edit');
		$objWriter->writeAttribute('data', '1');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('v:shapetype');
		$objWriter->writeAttribute('id', '_x0000_t202');
		$objWriter->writeAttribute('coordsize', '21600,21600');
		$objWriter->writeAttribute('o:spt', '202');
		$objWriter->writeAttribute('path', 'm,l,21600r21600,l21600,xe');
		$objWriter->startElement('v:stroke');
		$objWriter->writeAttribute('joinstyle', 'miter');
		$objWriter->endElement();
		$objWriter->startElement('v:path');
		$objWriter->writeAttribute('gradientshapeok', 't');
		$objWriter->writeAttribute('o:connecttype', 'rect');
		$objWriter->endElement();
		$objWriter->endElement();

		foreach ($comments as $key => $value) {
			$this->_writeVMLComment($objWriter, $key, $value);
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}

	public function _writeVMLComment(PHPExcel_Shared_XMLWriter $objWriter = NULL, $pCellReference = 'A1', PHPExcel_Comment $pComment = NULL)
	{
		list($column, $row) = PHPExcel_Cell::coordinateFromString($pCellReference);
		$column = PHPExcel_Cell::columnIndexFromString($column);
		$id = 1024 + $column + $row;
		$id = substr($id, 0, 4);
		$objWriter->startElement('v:shape');
		$objWriter->writeAttribute('id', '_x0000_s' . $id);
		$objWriter->writeAttribute('type', '#_x0000_t202');
		$objWriter->writeAttribute('style', 'position:absolute;margin-left:' . $pComment->getMarginLeft() . ';margin-top:' . $pComment->getMarginTop() . ';width:' . $pComment->getWidth() . ';height:' . $pComment->getHeight() . ';z-index:1;visibility:' . ($pComment->getVisible() ? 'visible' : 'hidden'));
		$objWriter->writeAttribute('fillcolor', '#' . $pComment->getFillColor()->getRGB());
		$objWriter->writeAttribute('o:insetmode', 'auto');
		$objWriter->startElement('v:fill');
		$objWriter->writeAttribute('color2', '#' . $pComment->getFillColor()->getRGB());
		$objWriter->endElement();
		$objWriter->startElement('v:shadow');
		$objWriter->writeAttribute('on', 't');
		$objWriter->writeAttribute('color', 'black');
		$objWriter->writeAttribute('obscured', 't');
		$objWriter->endElement();
		$objWriter->startElement('v:path');
		$objWriter->writeAttribute('o:connecttype', 'none');
		$objWriter->endElement();
		$objWriter->startElement('v:textbox');
		$objWriter->writeAttribute('style', 'mso-direction-alt:auto');
		$objWriter->startElement('div');
		$objWriter->writeAttribute('style', 'text-align:left');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('x:ClientData');
		$objWriter->writeAttribute('ObjectType', 'Note');
		$objWriter->writeElement('x:MoveWithCells', '');
		$objWriter->writeElement('x:SizeWithCells', '');
		$objWriter->writeElement('x:AutoFill', 'False');
		$objWriter->writeElement('x:Row', $row - 1);
		$objWriter->writeElement('x:Column', $column - 1);
		$objWriter->endElement();
		$objWriter->endElement();
	}
}

?>

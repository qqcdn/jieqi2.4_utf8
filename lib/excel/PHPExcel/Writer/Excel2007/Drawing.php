<?php

class PHPExcel_Writer_Excel2007_Drawing extends PHPExcel_Writer_Excel2007_WriterPart
{
	public function writeDrawings(PHPExcel_Worksheet $pWorksheet = NULL, &$chartRef, $includeCharts = false)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$objWriter->startElement('xdr:wsDr');
		$objWriter->writeAttribute('xmlns:xdr', 'http://schemas.openxmlformats.org/drawingml/2006/spreadsheetDrawing');
		$objWriter->writeAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
		$i = 1;
		$iterator = $pWorksheet->getDrawingCollection()->getIterator();

		while ($iterator->valid()) {
			$this->_writeDrawing($objWriter, $iterator->current(), $i);
			$iterator->next();
			++$i;
		}

		if ($includeCharts) {
			$chartCount = $pWorksheet->getChartCount();

			if (0 < $chartCount) {
				for ($c = 0; $c < $chartCount; ++$c) {
					$this->_writeChart($objWriter, $pWorksheet->getChartByIndex($c), $c + $i);
				}
			}
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}

	public function _writeChart(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Chart $pChart = NULL, $pRelationId = -1)
	{
		$tl = $pChart->getTopLeftPosition();
		$tl['colRow'] = PHPExcel_Cell::coordinateFromString($tl['cell']);
		$br = $pChart->getBottomRightPosition();
		$br['colRow'] = PHPExcel_Cell::coordinateFromString($br['cell']);
		$objWriter->startElement('xdr:twoCellAnchor');
		$objWriter->startElement('xdr:from');
		$objWriter->writeElement('xdr:col', PHPExcel_Cell::columnIndexFromString($tl['colRow'][0]) - 1);
		$objWriter->writeElement('xdr:colOff', PHPExcel_Shared_Drawing::pixelsToEMU($tl['xOffset']));
		$objWriter->writeElement('xdr:row', $tl['colRow'][1] - 1);
		$objWriter->writeElement('xdr:rowOff', PHPExcel_Shared_Drawing::pixelsToEMU($tl['yOffset']));
		$objWriter->endElement();
		$objWriter->startElement('xdr:to');
		$objWriter->writeElement('xdr:col', PHPExcel_Cell::columnIndexFromString($br['colRow'][0]) - 1);
		$objWriter->writeElement('xdr:colOff', PHPExcel_Shared_Drawing::pixelsToEMU($br['xOffset']));
		$objWriter->writeElement('xdr:row', $br['colRow'][1] - 1);
		$objWriter->writeElement('xdr:rowOff', PHPExcel_Shared_Drawing::pixelsToEMU($br['yOffset']));
		$objWriter->endElement();
		$objWriter->startElement('xdr:graphicFrame');
		$objWriter->writeAttribute('macro', '');
		$objWriter->startElement('xdr:nvGraphicFramePr');
		$objWriter->startElement('xdr:cNvPr');
		$objWriter->writeAttribute('name', 'Chart ' . $pRelationId);
		$objWriter->writeAttribute('id', 1025 * $pRelationId);
		$objWriter->endElement();
		$objWriter->startElement('xdr:cNvGraphicFramePr');
		$objWriter->startElement('a:graphicFrameLocks');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('xdr:xfrm');
		$objWriter->startElement('a:off');
		$objWriter->writeAttribute('x', '0');
		$objWriter->writeAttribute('y', '0');
		$objWriter->endElement();
		$objWriter->startElement('a:ext');
		$objWriter->writeAttribute('cx', '0');
		$objWriter->writeAttribute('cy', '0');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('a:graphic');
		$objWriter->startElement('a:graphicData');
		$objWriter->writeAttribute('uri', 'http://schemas.openxmlformats.org/drawingml/2006/chart');
		$objWriter->startElement('c:chart');
		$objWriter->writeAttribute('xmlns:c', 'http://schemas.openxmlformats.org/drawingml/2006/chart');
		$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
		$objWriter->writeAttribute('r:id', 'rId' . $pRelationId);
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('xdr:clientData');
		$objWriter->endElement();
		$objWriter->endElement();
	}

	public function _writeDrawing(PHPExcel_Shared_XMLWriter $objWriter = NULL, PHPExcel_Worksheet_BaseDrawing $pDrawing = NULL, $pRelationId = -1)
	{
		if (0 <= $pRelationId) {
			$objWriter->startElement('xdr:oneCellAnchor');
			$aCoordinates = PHPExcel_Cell::coordinateFromString($pDrawing->getCoordinates());
			$aCoordinates[0] = PHPExcel_Cell::columnIndexFromString($aCoordinates[0]);
			$objWriter->startElement('xdr:from');
			$objWriter->writeElement('xdr:col', $aCoordinates[0] - 1);
			$objWriter->writeElement('xdr:colOff', PHPExcel_Shared_Drawing::pixelsToEMU($pDrawing->getOffsetX()));
			$objWriter->writeElement('xdr:row', $aCoordinates[1] - 1);
			$objWriter->writeElement('xdr:rowOff', PHPExcel_Shared_Drawing::pixelsToEMU($pDrawing->getOffsetY()));
			$objWriter->endElement();
			$objWriter->startElement('xdr:ext');
			$objWriter->writeAttribute('cx', PHPExcel_Shared_Drawing::pixelsToEMU($pDrawing->getWidth()));
			$objWriter->writeAttribute('cy', PHPExcel_Shared_Drawing::pixelsToEMU($pDrawing->getHeight()));
			$objWriter->endElement();
			$objWriter->startElement('xdr:pic');
			$objWriter->startElement('xdr:nvPicPr');
			$objWriter->startElement('xdr:cNvPr');
			$objWriter->writeAttribute('id', $pRelationId);
			$objWriter->writeAttribute('name', $pDrawing->getName());
			$objWriter->writeAttribute('descr', $pDrawing->getDescription());
			$objWriter->endElement();
			$objWriter->startElement('xdr:cNvPicPr');
			$objWriter->startElement('a:picLocks');
			$objWriter->writeAttribute('noChangeAspect', '1');
			$objWriter->endElement();
			$objWriter->endElement();
			$objWriter->endElement();
			$objWriter->startElement('xdr:blipFill');
			$objWriter->startElement('a:blip');
			$objWriter->writeAttribute('xmlns:r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
			$objWriter->writeAttribute('r:embed', 'rId' . $pRelationId);
			$objWriter->endElement();
			$objWriter->startElement('a:stretch');
			$objWriter->writeElement('a:fillRect', NULL);
			$objWriter->endElement();
			$objWriter->endElement();
			$objWriter->startElement('xdr:spPr');
			$objWriter->startElement('a:xfrm');
			$objWriter->writeAttribute('rot', PHPExcel_Shared_Drawing::degreesToAngle($pDrawing->getRotation()));
			$objWriter->endElement();
			$objWriter->startElement('a:prstGeom');
			$objWriter->writeAttribute('prst', 'rect');
			$objWriter->writeElement('a:avLst', NULL);
			$objWriter->endElement();

			if ($pDrawing->getShadow()->getVisible()) {
				$objWriter->startElement('a:effectLst');
				$objWriter->startElement('a:outerShdw');
				$objWriter->writeAttribute('blurRad', PHPExcel_Shared_Drawing::pixelsToEMU($pDrawing->getShadow()->getBlurRadius()));
				$objWriter->writeAttribute('dist', PHPExcel_Shared_Drawing::pixelsToEMU($pDrawing->getShadow()->getDistance()));
				$objWriter->writeAttribute('dir', PHPExcel_Shared_Drawing::degreesToAngle($pDrawing->getShadow()->getDirection()));
				$objWriter->writeAttribute('algn', $pDrawing->getShadow()->getAlignment());
				$objWriter->writeAttribute('rotWithShape', '0');
				$objWriter->startElement('a:srgbClr');
				$objWriter->writeAttribute('val', $pDrawing->getShadow()->getColor()->getRGB());
				$objWriter->startElement('a:alpha');
				$objWriter->writeAttribute('val', $pDrawing->getShadow()->getAlpha() * 1000);
				$objWriter->endElement();
				$objWriter->endElement();
				$objWriter->endElement();
				$objWriter->endElement();
			}

			$objWriter->endElement();
			$objWriter->endElement();
			$objWriter->writeElement('xdr:clientData', NULL);
			$objWriter->endElement();
		}
		else {
			throw new PHPExcel_Writer_Exception('Invalid parameters passed.');
		}
	}

	public function writeVMLHeaderFooterImages(PHPExcel_Worksheet $pWorksheet = NULL)
	{
		$objWriter = NULL;

		if ($this->getParentWriter()->getUseDiskCaching()) {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
		}
		else {
			$objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
		}

		$objWriter->startDocument('1.0', 'UTF-8', 'yes');
		$images = $pWorksheet->getHeaderFooter()->getImages();
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
		$objWriter->writeAttribute('id', '_x0000_t75');
		$objWriter->writeAttribute('coordsize', '21600,21600');
		$objWriter->writeAttribute('o:spt', '75');
		$objWriter->writeAttribute('o:preferrelative', 't');
		$objWriter->writeAttribute('path', 'm@4@5l@4@11@9@11@9@5xe');
		$objWriter->writeAttribute('filled', 'f');
		$objWriter->writeAttribute('stroked', 'f');
		$objWriter->startElement('v:stroke');
		$objWriter->writeAttribute('joinstyle', 'miter');
		$objWriter->endElement();
		$objWriter->startElement('v:formulas');
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'if lineDrawn pixelLineWidth 0');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'sum @0 1 0');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'sum 0 0 @1');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'prod @2 1 2');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'prod @3 21600 pixelWidth');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'prod @3 21600 pixelHeight');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'sum @0 0 1');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'prod @6 1 2');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'prod @7 21600 pixelWidth');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'sum @8 21600 0');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'prod @7 21600 pixelHeight');
		$objWriter->endElement();
		$objWriter->startElement('v:f');
		$objWriter->writeAttribute('eqn', 'sum @10 21600 0');
		$objWriter->endElement();
		$objWriter->endElement();
		$objWriter->startElement('v:path');
		$objWriter->writeAttribute('o:extrusionok', 'f');
		$objWriter->writeAttribute('gradientshapeok', 't');
		$objWriter->writeAttribute('o:connecttype', 'rect');
		$objWriter->endElement();
		$objWriter->startElement('o:lock');
		$objWriter->writeAttribute('v:ext', 'edit');
		$objWriter->writeAttribute('aspectratio', 't');
		$objWriter->endElement();
		$objWriter->endElement();

		foreach ($images as $key => $value) {
			$this->_writeVMLHeaderFooterImage($objWriter, $key, $value);
		}

		$objWriter->endElement();
		return $objWriter->getData();
	}

	public function _writeVMLHeaderFooterImage(PHPExcel_Shared_XMLWriter $objWriter = NULL, $pReference = '', PHPExcel_Worksheet_HeaderFooterDrawing $pImage = NULL)
	{
		preg_match('{(\\d+)}', md5($pReference), $m);
		$id = 1500 + (substr($m[1], 0, 2) * 1);
		$width = $pImage->getWidth();
		$height = $pImage->getHeight();
		$marginLeft = $pImage->getOffsetX();
		$marginTop = $pImage->getOffsetY();
		$objWriter->startElement('v:shape');
		$objWriter->writeAttribute('id', $pReference);
		$objWriter->writeAttribute('o:spid', '_x0000_s' . $id);
		$objWriter->writeAttribute('type', '#_x0000_t75');
		$objWriter->writeAttribute('style', 'position:absolute;margin-left:' . $marginLeft . 'px;margin-top:' . $marginTop . 'px;width:' . $width . 'px;height:' . $height . 'px;z-index:1');
		$objWriter->startElement('v:imagedata');
		$objWriter->writeAttribute('o:relid', 'rId' . $pReference);
		$objWriter->writeAttribute('o:title', $pImage->getName());
		$objWriter->endElement();
		$objWriter->startElement('o:lock');
		$objWriter->writeAttribute('v:ext', 'edit');
		$objWriter->writeAttribute('rotation', 't');
		$objWriter->endElement();
		$objWriter->endElement();
	}

	public function allDrawings(PHPExcel $pPHPExcel = NULL)
	{
		$aDrawings = array();
		$sheetCount = $pPHPExcel->getSheetCount();

		for ($i = 0; $i < $sheetCount; ++$i) {
			$iterator = $pPHPExcel->getSheet($i)->getDrawingCollection()->getIterator();

			while ($iterator->valid()) {
				$aDrawings[] = $iterator->current();
				$iterator->next();
			}
		}

		return $aDrawings;
	}
}

?>

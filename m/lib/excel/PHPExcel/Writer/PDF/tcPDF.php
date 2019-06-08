<?php

$pdfRendererClassFile = PHPExcel_Settings::getPdfRendererPath() . '/tcpdf.php';

if (file_exists($pdfRendererClassFile)) {
	$k_path_url = PHPExcel_Settings::getPdfRendererPath();
	require_once $pdfRendererClassFile;
}
else {
	throw new PHPExcel_Writer_Exception('Unable to load PDF Rendering library');
}

class PHPExcel_Writer_PDF_tcPDF extends PHPExcel_Writer_PDF_Core implements PHPExcel_Writer_IWriter
{
	public function __construct(PHPExcel $phpExcel)
	{
		parent::__construct($phpExcel);
	}

	public function save($pFilename = NULL)
	{
		$fileHandle = parent::prepareForSave($pFilename);
		$paperSize = 'LETTER';

		if (is_null($this->getSheetIndex())) {
			$orientation = ($this->_phpExcel->getSheet(0)->getPageSetup()->getOrientation() == PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE ? 'L' : 'P');
			$printPaperSize = $this->_phpExcel->getSheet(0)->getPageSetup()->getPaperSize();
			$printMargins = $this->_phpExcel->getSheet(0)->getPageMargins();
		}
		else {
			$orientation = ($this->_phpExcel->getSheet($this->getSheetIndex())->getPageSetup()->getOrientation() == PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE ? 'L' : 'P');
			$printPaperSize = $this->_phpExcel->getSheet($this->getSheetIndex())->getPageSetup()->getPaperSize();
			$printMargins = $this->_phpExcel->getSheet($this->getSheetIndex())->getPageMargins();
		}

		if (!is_null($this->getOrientation())) {
			$orientation = ($this->getOrientation() == PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE ? 'L' : 'P');
		}

		if (!is_null($this->getPaperSize())) {
			$printPaperSize = $this->getPaperSize();
		}

		if (isset(self::$_paperSizes[$printPaperSize])) {
			$paperSize = self::$_paperSizes[$printPaperSize];
		}

		$pdf = new TCPDF($orientation, 'pt', $paperSize);
		$pdf->setFontSubsetting(false);
		$pdf->SetMargins($printMargins->getLeft() * 72, $printMargins->getTop() * 72, $printMargins->getRight() * 72);
		$pdf->SetAutoPageBreak(true, $printMargins->getBottom() * 72);
		$pdf->setPrintHeader(false);
		$pdf->setPrintFooter(false);
		$pdf->AddPage();
		$pdf->SetFont($this->getFont());
		$pdf->writeHTML($this->generateHTMLHeader(false) . $this->generateSheetData() . $this->generateHTMLFooter());
		$pdf->SetTitle($this->_phpExcel->getProperties()->getTitle());
		$pdf->SetAuthor($this->_phpExcel->getProperties()->getCreator());
		$pdf->SetSubject($this->_phpExcel->getProperties()->getSubject());
		$pdf->SetKeywords($this->_phpExcel->getProperties()->getKeywords());
		$pdf->SetCreator($this->_phpExcel->getProperties()->getCreator());
		fwrite($fileHandle, $pdf->output($pFilename, 'S'));
		parent::restoreStateAfterSave($fileHandle);
	}
}

?>

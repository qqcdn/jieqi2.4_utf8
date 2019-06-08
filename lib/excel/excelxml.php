<?php

class Excel_XML
{
    /**
     * Header (of document)
     * @var string
     */
    private $header = '<?xml version="1.0" encoding="%s"?\\>' . "\n" . '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">';
    /**
     * Footer (of document)
     * @var string
     */
    private $footer = '</Workbook>';
    /**
     * Lines to output in the excel document
     * @var array
     */
    private $lines = array();
    /**
     * Used encoding
     * @var string
     */
    private $sEncoding;
    /**
     * Convert variable types
     * @var boolean
     */
    private $bConvertTypes;
    /**
     * Worksheet title
     * @var string
     */
    private $sWorksheetTitle;
    public function __construct($sEncoding = 'UTF-8', $bConvertTypes = false, $sWorksheetTitle = 'Table1')
    {
        $this->bConvertTypes = $bConvertTypes;
        $this->setEncoding($sEncoding);
        $this->setWorksheetTitle($sWorksheetTitle);
    }
    public function setEncoding($sEncoding)
    {
        $this->sEncoding = $sEncoding;
    }
    public function setWorksheetTitle($title)
    {
        $title = preg_replace('/[\\\\|:|\\/|\\?|\\*|\\[|\\]]/', '', $title);
        $title = substr($title, 0, 31);
        $this->sWorksheetTitle = $title;
    }
    private function addRow($array)
    {
        $cells = '';
        foreach ($array as $k => $v) {
            $type = 'String';
            if ($this->bConvertTypes === true && is_numeric($v)) {
                $type = 'Number';
            }
            $v = htmlentities($v, ENT_COMPAT, $this->sEncoding);
            $cells .= '<Cell><Data ss:Type="' . $type . '">' . $v . '</Data></Cell>' . "\n" . '';
        }
        $this->lines[] = '<Row>' . "\n" . '' . $cells . '</Row>' . "\n" . '';
    }
    public function addArray($array)
    {
        foreach ($array as $k => $v) {
            $this->addRow($v);
        }
    }
    public function generateXML($filename = 'excel-export')
    {
        $filename = preg_replace('/[^aA-zZ0-9\\_\\-]/', '', $filename);
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Type: application/vnd.ms-excel; charset=' . $this->sEncoding);
        header('Content-Disposition: attachment; filename="' . $filename . '.xls"');
        echo stripslashes(sprintf($this->header, $this->sEncoding));
        echo '' . "\n" . '<Worksheet ss:Name="' . $this->sWorksheetTitle . '">' . "\n" . '<Table>' . "\n" . '';
        foreach ($this->lines as $line) {
            echo $line;
        }
        echo '</Table>' . "\n" . '</Worksheet>' . "\n" . '';
        echo $this->footer;
    }
}
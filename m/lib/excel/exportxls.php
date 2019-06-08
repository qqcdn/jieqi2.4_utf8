<?php

class ExportXLS
{
    private $filename;
    private $headerArray;
    private $bodyArray;
    private $rowNo = 0;
    public function __construct($filename)
    {
        $this->filename = $filename;
    }
    public function addHeader($header)
    {
        if (is_array($header)) {
            $this->headerArray[] = $header;
        } else {
            $this->headerArray[][0] = $header;
        }
    }
    public function addRow($row)
    {
        if (is_array($row)) {
            if (is_array($row[0])) {
                foreach ($row as $key => $array) {
                    $this->bodyArray[] = $array;
                }
            } else {
                $this->bodyArray[] = $row;
            }
        } else {
            $this->bodyArray[][0] = $row;
        }
    }
    public function returnSheet()
    {
        return $this->buildXLS();
    }
    public function sendFile()
    {
        $xls = $this->buildXLS();
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Disposition: attachment;filename=' . $this->filename);
        header('Content-Transfer-Encoding: binary ');
        echo $xls;
        exit;
    }
    private function buildXLS()
    {
        $xls = pack('ssssss', 2057, 8, 0, 16, 0, 0);
        if (is_array($this->headerArray)) {
            $xls .= $this->build($this->headerArray);
        }
        if (is_array($this->bodyArray)) {
            $xls .= $this->build($this->bodyArray);
        }
        $xls .= pack('ss', 10, 0);
        return $xls;
    }
    private function build($array)
    {
        foreach ($array as $key => $row) {
            $colNo = 0;
            foreach ($row as $key2 => $field) {
                if (is_numeric($field)) {
                    $build .= $this->numFormat($this->rowNo, $colNo, $field);
                } else {
                    $build .= $this->textFormat($this->rowNo, $colNo, $field);
                }
                $colNo++;
            }
            $this->rowNo++;
        }
        return $build;
    }
    private function textFormat($row, $col, $data)
    {
        $data = $data;
        $length = strlen($data);
        $field = pack('ssssss', 516, 8 + $length, $row, $col, 0, $length);
        $field .= $data;
        return $field;
    }
    private function numFormat($row, $col, $data)
    {
        $field = pack('sssss', 515, 14, $row, $col, 0);
        $field .= pack('d', $data);
        return $field;
    }
}
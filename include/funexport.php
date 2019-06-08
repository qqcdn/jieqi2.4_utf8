<?php

function jieqi_system_exportfile($params)
{
    global $jieqiModules;
    global $query;
    if (!isset($query) || !is_a($query, 'JieqiQueryHandler')) {
        jieqi_includedb();
        $query = JieqiQueryHandler::getInstance('JieqiQueryHandler');
    }
    if (empty($params['funrow'])) {
        $params['funrow'] = 'jieqi_query_rowvars';
    }
    $formatmap = array(1 => 'exceltxt', 2 => 'excel5', 3 => 'excel2007', 'exceltxt' => 'exceltxt', 'excel5' => 'excel5', 'excel2007' => 'excel2007', 'txt' => 'exceltxt', 'excel' => 'excel5');
    $format = isset($formatmap[$params['format']]) ? $formatmap[$params['format']] : 'exceltxt';
    if ($format == 'exceltxt') {
        $titlefields = array();
        foreach ($params['fields'] as $k => $v) {
            if (!isset($v['display']) || 0 < $v['display']) {
                $titlefields[$k] = $v['caption'];
            }
        }
        if (empty($titlefields)) {
            return false;
        }
        $outputFileName = $params['filename'];
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Disposition:attachment;filename="' . jieqi_headstr($outputFileName) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('content-Type:application/vnd.ms-excel;charset=utf8');
        echo jieqi_excel_txtline($titlefields, true);
        while ($row = $query->getRow($params['res'])) {
            $row = call_user_func($params['funrow'], $row);
            $line = array();
            foreach ($titlefields as $k => $v) {
                $line[] = isset($row[$k]) ? $row[$k] : '';
            }
            echo jieqi_excel_txtline($line, false);
        }
        if (is_array($params['footrow']) && !empty($params['footrow'])) {
            echo jieqi_excel_txtline($params['footrow'], false);
        }
    } else {
        include_once JIEQI_ROOT_PATH . '/header.php';
        include_once JIEQI_ROOT_PATH . '/lib/excel/PHPExcel.php';
        if ($format == 'excel2007') {
            include_once JIEQI_ROOT_PATH . '/lib/excel/PHPExcel/Writer/Excel2007.php';
        } else {
            include_once JIEQI_ROOT_PATH . '/lib/excel/PHPExcel/Writer/Excel5.php';
        }
        $objExcel = new PHPExcel();
        if ($format == 'excel2007') {
            $objWriter = new PHPExcel_Writer_Excel2007($objExcel);
            $objWriter->setOffice2003Compatibility(true);
        } else {
            $objWriter = new PHPExcel_Writer_Excel5($objExcel);
        }
        $objProps = $objExcel->getProperties();
        $excel_creator = isset($params['excelproperties']['creator']) ? $params['excelproperties']['creator'] : 'JIEQI CMS';
        $objProps->setCreator($excel_creator);
        $excel_lastmodifiedby = isset($params['excelproperties']['lastmodifiedby']) ? $params['excelproperties']['lastmodifiedby'] : 'JIEQI CMS';
        $objProps->setLastModifiedBy($excel_lastmodifiedby);
        $excel_title = isset($params['excelproperties']['title']) ? $params['excelproperties']['title'] : '';
        $objProps->setTitle($excel_title);
        $excel_subject = isset($params['excelproperties']['subject']) ? $params['excelproperties']['subject'] : '';
        $objProps->setSubject($excel_subject);
        $excel_description = isset($params['excelproperties']['description']) ? $params['excelproperties']['description'] : '';
        $objProps->setDescription($excel_description);
        $excel_keywords = isset($params['excelproperties']['keywords']) ? $params['excelproperties']['keywords'] : '';
        $objProps->setKeywords($excel_keywords);
        $excel_category = isset($params['excelproperties']['category']) ? $params['excelproperties']['category'] : '';
        $objProps->setCategory($excel_category);
        $objExcel->setActiveSheetIndex(0);
        $objActSheet = $objExcel->getActiveSheet();
        $sheet_category = isset($params['sheetproperties']['title']) ? $params['sheetproperties']['title'] : 'Sheet1';
        $objActSheet->setTitle($sheet_category);
        $titlefields = array();
        $titlewidth = array();
        foreach ($params['fields'] as $k => $v) {
            if (!isset($v['display']) || 0 < $v['display']) {
                $titlefields[$k] = $v['caption'];
                $titlewidth[$k] = $v['width'];
            }
        }
        if (empty($titlefields)) {
            return false;
        }
        $line = array_values($titlefields);
        jieqi_excel_addrow($line, $objActSheet);
        jieqi_excel_setwidth($titlewidth, $objActSheet);
        while ($row = $query->getRow()) {
            $row = call_user_func($params['funrow'], $row);
            $line = array();
            foreach ($titlefields as $k => $v) {
                $line[] = isset($row[$k]) ? $row[$k] : '';
            }
            jieqi_excel_addrow($line, $objActSheet);
        }
        if (is_array($params['footrow']) && !empty($params['footrow'])) {
            jieqi_excel_addrow($params['footrow'], $objActSheet);
        }
        $outputFileName = $params['filename'];
        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');
        header('Content-Disposition:attachment;filename="' . jieqi_headstr($outputFileName) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('content-Type:application/vnd.ms-excel;charset=utf8');
        $objWriter->save('php://output');
    }
    return true;
}
function jieqi_excel_addrow($row, $sheet)
{
    static $excel_roworder = 1;
    $k = 1;
    foreach ($row as $v) {
        $cellcode = jieqi_excel_colcode($k) . $excel_roworder;
        $sheet->setCellValue($cellcode, iconv('GBK', 'UTF-8//IGNORE', $v));
        $sheet->getStyle($cellcode)->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle($cellcode)->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle($cellcode)->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle($cellcode)->getBorders()->getBottom()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        if ($excel_roworder == 1) {
            $sheet->getStyle($cellcode)->getFont()->setBold(true);
            $sheet->getStyle($cellcode)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        }
        $k++;
    }
    $excel_roworder++;
}
function jieqi_excel_setwidth($row, $sheet)
{
    $k = 1;
    foreach ($row as $v) {
        $sheet->getColumnDimension(jieqi_excel_colcode($k))->setWidth(intval($v));
        $k++;
    }
}
function jieqi_excel_colcode($k)
{
    $ret = '';
    $k = intval($k);
    if (0 < $k) {
        while (26 < $k) {
            $ret = chr(64 + $k % 26) . $ret;
            $k = floor($k / 26);
        }
        $ret = chr(64 + $k) . $ret;
    }
    return $ret;
}
function jieqi_excel_txtline($line, $first = false)
{
    $ret = '';
    if (!$first) {
        $ret .= "\n";
    }
    foreach ($line as $v) {
        $ret .= str_replace(array('	', "\n", "\r"), array(' ', ' ', ''), $v) . '	';
    }
    return $ret;
}
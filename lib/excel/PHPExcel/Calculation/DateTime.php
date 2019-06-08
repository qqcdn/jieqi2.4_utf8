<?php

class PHPExcel_Calculation_DateTime
{
    public static function _isLeapYear($year)
    {
        return $year % 4 == 0 && $year % 100 != 0 || $year % 400 == 0;
    }
    private static function _dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, $methodUS)
    {
        if ($startDay == 31) {
            --$startDay;
        } else {
            if ($methodUS && $startMonth == 2 && ($startDay == 29 || $startDay == 28 && !self::_isLeapYear($startYear))) {
                $startDay = 30;
            }
        }
        if ($endDay == 31) {
            if ($methodUS && $startDay != 30) {
                $endDay = 1;
                if ($endMonth == 12) {
                    ++$endYear;
                    $endMonth = 1;
                } else {
                    ++$endMonth;
                }
            } else {
                $endDay = 30;
            }
        }
        return $endDay + $endMonth * 30 + $endYear * 360 - $startDay - $startMonth * 30 - $startYear * 360;
    }
    public static function _getDateValue($dateValue)
    {
        if (!is_numeric($dateValue)) {
            if (is_string($dateValue) && PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            if (is_object($dateValue) && $dateValue instanceof DateTime) {
                $dateValue = PHPExcel_Shared_Date::PHPToExcel($dateValue);
            } else {
                $saveReturnDateType = PHPExcel_Calculation_Functions::getReturnDateType();
                PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
                $dateValue = self::DATEVALUE($dateValue);
                PHPExcel_Calculation_Functions::setReturnDateType($saveReturnDateType);
            }
        }
        return $dateValue;
    }
    private static function _getTimeValue($timeValue)
    {
        $saveReturnDateType = PHPExcel_Calculation_Functions::getReturnDateType();
        PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);
        $timeValue = self::TIMEVALUE($timeValue);
        PHPExcel_Calculation_Functions::setReturnDateType($saveReturnDateType);
        return $timeValue;
    }
    private static function _adjustDateByMonths($dateValue = 0, $adjustmentMonths = 0)
    {
        $PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
        $oMonth = (int) $PHPDateObject->format('m');
        $oYear = (int) $PHPDateObject->format('Y');
        $adjustmentMonthsString = (string) $adjustmentMonths;
        if (0 < $adjustmentMonths) {
            $adjustmentMonthsString = '+' . $adjustmentMonths;
        }
        if ($adjustmentMonths != 0) {
            $PHPDateObject->modify($adjustmentMonthsString . ' months');
        }
        $nMonth = (int) $PHPDateObject->format('m');
        $nYear = (int) $PHPDateObject->format('Y');
        $monthDiff = $nMonth - $oMonth + ($nYear - $oYear) * 12;
        if ($monthDiff != $adjustmentMonths) {
            $adjustDays = (int) $PHPDateObject->format('d');
            $adjustDaysString = '-' . $adjustDays . ' days';
            $PHPDateObject->modify($adjustDaysString);
        }
        return $PHPDateObject;
    }
    public static function DATETIMENOW()
    {
        $saveTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $retValue = false;
        switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
            case PHPExcel_Calculation_Functions:
                $retValue = (double) PHPExcel_Shared_Date::PHPToExcel(time());
                break;
            case PHPExcel_Calculation_Functions:
                $retValue = (int) time();
                break;
            case PHPExcel_Calculation_Functions:
                $retValue = new DateTime();
                break;
        }
        date_default_timezone_set($saveTimeZone);
        return $retValue;
    }
    public static function DATENOW()
    {
        $saveTimeZone = date_default_timezone_get();
        date_default_timezone_set('UTC');
        $retValue = false;
        $excelDateTime = floor(PHPExcel_Shared_Date::PHPToExcel(time()));
        switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
            case PHPExcel_Calculation_Functions:
                $retValue = (double) $excelDateTime;
                break;
            case PHPExcel_Calculation_Functions:
                $retValue = (int) PHPExcel_Shared_Date::ExcelToPHP($excelDateTime);
                break;
            case PHPExcel_Calculation_Functions:
                $retValue = PHPExcel_Shared_Date::ExcelToPHPObject($excelDateTime);
                break;
        }
        date_default_timezone_set($saveTimeZone);
        return $retValue;
    }
    public static function DATE($year = 0, $month = 1, $day = 1)
    {
        $year = PHPExcel_Calculation_Functions::flattenSingleValue($year);
        $month = PHPExcel_Calculation_Functions::flattenSingleValue($month);
        $day = PHPExcel_Calculation_Functions::flattenSingleValue($day);
        if ($month !== NULL && !is_numeric($month)) {
            $month = PHPExcel_Shared_Date::monthStringToNumber($month);
        }
        if ($day !== NULL && !is_numeric($day)) {
            $day = PHPExcel_Shared_Date::dayStringToNumber($day);
        }
        $year = $year !== NULL ? PHPExcel_Shared_String::testStringAsNumeric($year) : 0;
        $month = $month !== NULL ? PHPExcel_Shared_String::testStringAsNumeric($month) : 0;
        $day = $day !== NULL ? PHPExcel_Shared_String::testStringAsNumeric($day) : 0;
        if (!is_numeric($year) || !is_numeric($month) || !is_numeric($day)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $year = (int) $year;
        $month = (int) $month;
        $day = (int) $day;
        $baseYear = PHPExcel_Shared_Date::getExcelCalendar();
        if ($year < $baseYear - 1900) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($baseYear - 1900 != 0 && $year < $baseYear && 1900 <= $year) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($year < $baseYear && $baseYear - 1900 <= $year) {
            $year += 1900;
        }
        if ($month < 1) {
            --$month;
            $year += ceil($month / 12) - 1;
            $month = 13 - abs($month % 12);
        } else {
            if (12 < $month) {
                $year += floor($month / 12);
                $month = $month % 12;
            }
        }
        if ($year < $baseYear || 10000 <= $year) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel($year, $month, $day);
        switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
            case PHPExcel_Calculation_Functions:
                return (double) $excelDateValue;
            case PHPExcel_Calculation_Functions:
                return (int) PHPExcel_Shared_Date::ExcelToPHP($excelDateValue);
            case PHPExcel_Calculation_Functions:
                return PHPExcel_Shared_Date::ExcelToPHPObject($excelDateValue);
        }
    }
    public static function TIME($hour = 0, $minute = 0, $second = 0)
    {
        $hour = PHPExcel_Calculation_Functions::flattenSingleValue($hour);
        $minute = PHPExcel_Calculation_Functions::flattenSingleValue($minute);
        $second = PHPExcel_Calculation_Functions::flattenSingleValue($second);
        if ($hour == '') {
            $hour = 0;
        }
        if ($minute == '') {
            $minute = 0;
        }
        if ($second == '') {
            $second = 0;
        }
        if (!is_numeric($hour) || !is_numeric($minute) || !is_numeric($second)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $hour = (int) $hour;
        $minute = (int) $minute;
        $second = (int) $second;
        if ($second < 0) {
            $minute += floor($second / 60);
            $second = 60 - abs($second % 60);
            if ($second == 60) {
                $second = 0;
            }
        } else {
            if (60 <= $second) {
                $minute += floor($second / 60);
                $second = $second % 60;
            }
        }
        if ($minute < 0) {
            $hour += floor($minute / 60);
            $minute = 60 - abs($minute % 60);
            if ($minute == 60) {
                $minute = 0;
            }
        } else {
            if (60 <= $minute) {
                $hour += floor($minute / 60);
                $minute = $minute % 60;
            }
        }
        if (23 < $hour) {
            $hour = $hour % 24;
        } else {
            if ($hour < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
            case PHPExcel_Calculation_Functions:
                $date = 0;
                $calendar = PHPExcel_Shared_Date::getExcelCalendar();
                if ($calendar != PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900) {
                    $date = 1;
                }
                return (double) PHPExcel_Shared_Date::FormattedPHPToExcel($calendar, 1, $date, $hour, $minute, $second);
            case PHPExcel_Calculation_Functions:
                return (int) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::FormattedPHPToExcel(1970, 1, 1, $hour, $minute, $second));
            case PHPExcel_Calculation_Functions:
                $dayAdjust = 0;
                if ($hour < 0) {
                    $dayAdjust = floor($hour / 24);
                    $hour = 24 - abs($hour % 24);
                    if ($hour == 24) {
                        $hour = 0;
                    }
                } else {
                    if (24 <= $hour) {
                        $dayAdjust = floor($hour / 24);
                        $hour = $hour % 24;
                    }
                }
                $phpDateObject = new DateTime('1900-01-01 ' . $hour . ':' . $minute . ':' . $second);
                if ($dayAdjust != 0) {
                    $phpDateObject->modify($dayAdjust . ' days');
                }
                return $phpDateObject;
        }
    }
    public static function DATEVALUE($dateValue = 1)
    {
        $dateValue = trim(PHPExcel_Calculation_Functions::flattenSingleValue($dateValue), '"');
        $dateValue = preg_replace('/(\\d)(st|nd|rd|th)([ -\\/])/Ui', '$1$3', $dateValue);
        $dateValue = str_replace(array('/', '.', '-', '  '), array(' ', ' ', ' ', ' '), $dateValue);
        $yearFound = false;
        $t1 = explode(' ', $dateValue);
        foreach ($t1 as &$t) {
            if (is_numeric($t) && 31 < $t) {
                if ($yearFound) {
                    return PHPExcel_Calculation_Functions::VALUE();
                } else {
                    if ($t < 100) {
                        $t += 1900;
                    }
                    $yearFound = true;
                }
            }
        }
        if (count($t1) == 1 && strpos($t, ':') != false) {
            return 0;
        } else {
            if (count($t1) == 2) {
                if ($yearFound) {
                    array_unshift($t1, 1);
                } else {
                    array_push($t1, date('Y'));
                }
            }
        }
        unset($t);
        $dateValue = implode(' ', $t1);
        $PHPDateArray = date_parse($dateValue);
        if ($PHPDateArray === false || 0 < $PHPDateArray['error_count']) {
            $testVal1 = strtok($dateValue, '- ');
            if ($testVal1 !== false) {
                $testVal2 = strtok('- ');
                if ($testVal2 !== false) {
                    $testVal3 = strtok('- ');
                    if ($testVal3 === false) {
                        $testVal3 = strftime('%Y');
                    }
                } else {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            $PHPDateArray = date_parse($testVal1 . '-' . $testVal2 . '-' . $testVal3);
            if ($PHPDateArray === false || 0 < $PHPDateArray['error_count']) {
                $PHPDateArray = date_parse($testVal2 . '-' . $testVal1 . '-' . $testVal3);
                if ($PHPDateArray === false || 0 < $PHPDateArray['error_count']) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
            }
        }
        if ($PHPDateArray !== false && $PHPDateArray['error_count'] == 0) {
            if ($PHPDateArray['year'] == '') {
                $PHPDateArray['year'] = strftime('%Y');
            }
            if ($PHPDateArray['year'] < 1900) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            if ($PHPDateArray['month'] == '') {
                $PHPDateArray['month'] = strftime('%m');
            }
            if ($PHPDateArray['day'] == '') {
                $PHPDateArray['day'] = strftime('%d');
            }
            $excelDateValue = floor(PHPExcel_Shared_Date::FormattedPHPToExcel($PHPDateArray['year'], $PHPDateArray['month'], $PHPDateArray['day'], $PHPDateArray['hour'], $PHPDateArray['minute'], $PHPDateArray['second']));
            switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
                case PHPExcel_Calculation_Functions:
                    return (double) $excelDateValue;
                case PHPExcel_Calculation_Functions:
                    return (int) PHPExcel_Shared_Date::ExcelToPHP($excelDateValue);
                case PHPExcel_Calculation_Functions:
                    return new DateTime($PHPDateArray['year'] . '-' . $PHPDateArray['month'] . '-' . $PHPDateArray['day'] . ' 00:00:00');
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function TIMEVALUE($timeValue)
    {
        $timeValue = trim(PHPExcel_Calculation_Functions::flattenSingleValue($timeValue), '"');
        $timeValue = str_replace(array('/', '.'), array('-', '-'), $timeValue);
        $PHPDateArray = date_parse($timeValue);
        if ($PHPDateArray !== false && $PHPDateArray['error_count'] == 0) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                $excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel($PHPDateArray['year'], $PHPDateArray['month'], $PHPDateArray['day'], $PHPDateArray['hour'], $PHPDateArray['minute'], $PHPDateArray['second']);
            } else {
                $excelDateValue = PHPExcel_Shared_Date::FormattedPHPToExcel(1900, 1, 1, $PHPDateArray['hour'], $PHPDateArray['minute'], $PHPDateArray['second']) - 1;
            }
            switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
                case PHPExcel_Calculation_Functions:
                    return (double) $excelDateValue;
                case PHPExcel_Calculation_Functions:
                    return (int) ($phpDateValue = PHPExcel_Shared_Date::ExcelToPHP($excelDateValue + 25569) - 3600);
                case PHPExcel_Calculation_Functions:
                    return new DateTime('1900-01-01 ' . $PHPDateArray['hour'] . ':' . $PHPDateArray['minute'] . ':' . $PHPDateArray['second']);
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function DATEDIF($startDate = 0, $endDate = 0, $unit = 'D')
    {
        $startDate = PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
        $endDate = PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
        $unit = strtoupper(PHPExcel_Calculation_Functions::flattenSingleValue($unit));
        if (is_string($startDate = self::_getDateValue($startDate))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($endDate = self::_getDateValue($endDate))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($endDate <= $startDate) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $difference = $endDate - $startDate;
        $PHPStartDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($startDate);
        $startDays = $PHPStartDateObject->format('j');
        $startMonths = $PHPStartDateObject->format('n');
        $startYears = $PHPStartDateObject->format('Y');
        $PHPEndDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
        $endDays = $PHPEndDateObject->format('j');
        $endMonths = $PHPEndDateObject->format('n');
        $endYears = $PHPEndDateObject->format('Y');
        $retVal = PHPExcel_Calculation_Functions::NaN();
        switch ($unit) {
            case 'D':
                $retVal = intval($difference);
                break;
            case 'M':
                $retVal = intval($endMonths - $startMonths) + intval($endYears - $startYears) * 12;
                if ($endDays < $startDays) {
                    --$retVal;
                }
                break;
            case 'Y':
                $retVal = intval($endYears - $startYears);
                if ($endMonths < $startMonths) {
                    --$retVal;
                } else {
                    if ($endMonths == $startMonths && $endDays < $startDays) {
                        --$retVal;
                    }
                }
                break;
            case 'MD':
                if ($endDays < $startDays) {
                    $retVal = $endDays;
                    $PHPEndDateObject->modify('-' . $endDays . ' days');
                    $adjustDays = $PHPEndDateObject->format('j');
                    if ($startDays < $adjustDays) {
                        $retVal += $adjustDays - $startDays;
                    }
                } else {
                    $retVal = $endDays - $startDays;
                }
                break;
            case 'YM':
                $retVal = intval($endMonths - $startMonths);
                if ($retVal < 0) {
                    $retVal = 12 + $retVal;
                }
                if ($endDays < $startDays) {
                    --$retVal;
                }
                break;
            case 'YD':
                $retVal = intval($difference);
                if ($startYears < $endYears) {
                    while ($startYears < $endYears) {
                        $PHPEndDateObject->modify('-1 year');
                        $endYears = $PHPEndDateObject->format('Y');
                    }
                    $retVal = $PHPEndDateObject->format('z') - $PHPStartDateObject->format('z');
                    if ($retVal < 0) {
                        $retVal += 365;
                    }
                }
                break;
            default:
                $retVal = PHPExcel_Calculation_Functions::NaN();
        }
        return $retVal;
    }
    public static function DAYS360($startDate = 0, $endDate = 0, $method = false)
    {
        $startDate = PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
        $endDate = PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
        if (is_string($startDate = self::_getDateValue($startDate))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($endDate = self::_getDateValue($endDate))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (!is_bool($method)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $PHPStartDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($startDate);
        $startDay = $PHPStartDateObject->format('j');
        $startMonth = $PHPStartDateObject->format('n');
        $startYear = $PHPStartDateObject->format('Y');
        $PHPEndDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
        $endDay = $PHPEndDateObject->format('j');
        $endMonth = $PHPEndDateObject->format('n');
        $endYear = $PHPEndDateObject->format('Y');
        return self::_dateDiff360($startDay, $startMonth, $startYear, $endDay, $endMonth, $endYear, !$method);
    }
    public static function YEARFRAC($startDate = 0, $endDate = 0, $method = 0)
    {
        $startDate = PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
        $endDate = PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
        $method = PHPExcel_Calculation_Functions::flattenSingleValue($method);
        if (is_string($startDate = self::_getDateValue($startDate))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($endDate = self::_getDateValue($endDate))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_numeric($method) && !is_string($method) || $method == '') {
            switch ($method) {
                case 0:
                    return self::DAYS360($startDate, $endDate) / 360;
                case 1:
                    $days = self::DATEDIF($startDate, $endDate);
                    $startYear = self::YEAR($startDate);
                    $endYear = self::YEAR($endDate);
                    $years = $endYear - $startYear + 1;
                    $leapDays = 0;
                    if ($years == 1) {
                        if (self::_isLeapYear($endYear)) {
                            $startMonth = self::MONTHOFYEAR($startDate);
                            $endMonth = self::MONTHOFYEAR($endDate);
                            $endDay = self::DAYOFMONTH($endDate);
                            if ($startMonth < 3 || 2 * 100 + 29 <= $endMonth * 100 + $endDay) {
                                $leapDays += 1;
                            }
                        }
                    } else {
                        for ($year = $startYear; $year <= $endYear; ++$year) {
                            if ($year == $startYear) {
                                $startMonth = self::MONTHOFYEAR($startDate);
                                $startDay = self::DAYOFMONTH($startDate);
                                if ($startMonth < 3) {
                                    $leapDays += self::_isLeapYear($year) ? 1 : 0;
                                }
                            } else {
                                if ($year == $endYear) {
                                    $endMonth = self::MONTHOFYEAR($endDate);
                                    $endDay = self::DAYOFMONTH($endDate);
                                    if (2 * 100 + 29 <= $endMonth * 100 + $endDay) {
                                        $leapDays += self::_isLeapYear($year) ? 1 : 0;
                                    }
                                } else {
                                    $leapDays += self::_isLeapYear($year) ? 1 : 0;
                                }
                            }
                        }
                        if ($years == 2) {
                            if ($leapDays == 0 && self::_isLeapYear($startYear) && 365 < $days) {
                                $leapDays = 1;
                            } else {
                                if ($days < 366) {
                                    $years = 1;
                                }
                            }
                        }
                        $leapDays /= $years;
                    }
                    return $days / (365 + $leapDays);
                case 2:
                    return self::DATEDIF($startDate, $endDate) / 360;
                case 3:
                    return self::DATEDIF($startDate, $endDate) / 365;
                case 4:
                    return self::DAYS360($startDate, $endDate, true) / 360;
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function NETWORKDAYS($startDate, $endDate)
    {
        $startDate = PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
        $endDate = PHPExcel_Calculation_Functions::flattenSingleValue($endDate);
        $dateArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        array_shift($dateArgs);
        array_shift($dateArgs);
        if (is_string($startDate = $sDate = self::_getDateValue($startDate))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $startDate = (double) floor($startDate);
        if (is_string($endDate = $eDate = self::_getDateValue($endDate))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $endDate = (double) floor($endDate);
        if ($eDate < $sDate) {
            $startDate = $eDate;
            $endDate = $sDate;
        }
        $startDoW = 6 - self::DAYOFWEEK($startDate, 2);
        if ($startDoW < 0) {
            $startDoW = 0;
        }
        $endDoW = self::DAYOFWEEK($endDate, 2);
        if (6 <= $endDoW) {
            $endDoW = 0;
        }
        $wholeWeekDays = floor(($endDate - $startDate) / 7) * 5;
        $partWeekDays = $endDoW + $startDoW;
        if (5 < $partWeekDays) {
            $partWeekDays -= 5;
        }
        $holidayCountedArray = array();
        foreach ($dateArgs as $holidayDate) {
            if (is_string($holidayDate = self::_getDateValue($holidayDate))) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            if ($startDate <= $holidayDate && $holidayDate <= $endDate) {
                if (self::DAYOFWEEK($holidayDate, 2) < 6 && !in_array($holidayDate, $holidayCountedArray)) {
                    --$partWeekDays;
                    $holidayCountedArray[] = $holidayDate;
                }
            }
        }
        if ($eDate < $sDate) {
            return -($wholeWeekDays + $partWeekDays);
        }
        return $wholeWeekDays + $partWeekDays;
    }
    public static function WORKDAY($startDate, $endDays)
    {
        $startDate = PHPExcel_Calculation_Functions::flattenSingleValue($startDate);
        $endDays = PHPExcel_Calculation_Functions::flattenSingleValue($endDays);
        $dateArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        array_shift($dateArgs);
        array_shift($dateArgs);
        if (is_string($startDate = self::_getDateValue($startDate)) || !is_numeric($endDays)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $startDate = (double) floor($startDate);
        $endDays = (int) floor($endDays);
        if ($endDays == 0) {
            return $startDate;
        }
        $decrementing = $endDays < 0 ? true : false;
        $startDoW = self::DAYOFWEEK($startDate, 3);
        if (5 <= self::DAYOFWEEK($startDate, 3)) {
            $startDate += $decrementing ? -$startDoW + 4 : 7 - $startDoW;
            $decrementing ? $endDays++ : $endDays--;
        }
        $endDate = (double) $startDate + intval($endDays / 5) * 7 + $endDays % 5;
        $endDoW = self::DAYOFWEEK($endDate, 3);
        if (5 <= $endDoW) {
            $endDate += $decrementing ? -$endDoW + 4 : 7 - $endDoW;
        }
        if (!empty($dateArgs)) {
            $holidayCountedArray = $holidayDates = array();
            foreach ($dateArgs as $holidayDate) {
                if ($holidayDate !== NULL && '' < trim($holidayDate)) {
                    if (is_string($holidayDate = self::_getDateValue($holidayDate))) {
                        return PHPExcel_Calculation_Functions::VALUE();
                    }
                    if (self::DAYOFWEEK($holidayDate, 3) < 5) {
                        $holidayDates[] = $holidayDate;
                    }
                }
            }
            if ($decrementing) {
                rsort($holidayDates, SORT_NUMERIC);
            } else {
                sort($holidayDates, SORT_NUMERIC);
            }
            foreach ($holidayDates as $holidayDate) {
                if ($decrementing) {
                    if ($holidayDate <= $startDate && $endDate <= $holidayDate) {
                        if (!in_array($holidayDate, $holidayCountedArray)) {
                            --$endDate;
                            $holidayCountedArray[] = $holidayDate;
                        }
                    }
                } else {
                    if ($startDate <= $holidayDate && $holidayDate <= $endDate) {
                        if (!in_array($holidayDate, $holidayCountedArray)) {
                            ++$endDate;
                            $holidayCountedArray[] = $holidayDate;
                        }
                    }
                }
                $endDoW = self::DAYOFWEEK($endDate, 3);
                if (5 <= $endDoW) {
                    $endDate += $decrementing ? -$endDoW + 4 : 7 - $endDoW;
                }
            }
        }
        switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
            case PHPExcel_Calculation_Functions:
                return (double) $endDate;
            case PHPExcel_Calculation_Functions:
                return (int) PHPExcel_Shared_Date::ExcelToPHP($endDate);
            case PHPExcel_Calculation_Functions:
                return PHPExcel_Shared_Date::ExcelToPHPObject($endDate);
        }
    }
    public static function DAYOFMONTH($dateValue = 1)
    {
        $dateValue = PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
        if (is_string($dateValue = self::_getDateValue($dateValue))) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($dateValue == 0) {
                return 0;
            } else {
                if ($dateValue < 0) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }
        }
        $PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
        return (int) $PHPDateObject->format('j');
    }
    public static function DAYOFWEEK($dateValue = 1, $style = 1)
    {
        $dateValue = PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
        $style = PHPExcel_Calculation_Functions::flattenSingleValue($style);
        if (!is_numeric($style)) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($style < 1 || 3 < $style) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $style = floor($style);
        if (is_string($dateValue = self::_getDateValue($dateValue))) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($dateValue < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
        $DoW = $PHPDateObject->format('w');
        $firstDay = 1;
        switch ($style) {
            case 1:
                ++$DoW;
                break;
            case 2:
                if ($DoW == 0) {
                    $DoW = 7;
                }
                break;
            case 3:
                if ($DoW == 0) {
                    $DoW = 7;
                }
                $firstDay = 0;
                --$DoW;
                break;
        }
        if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_EXCEL) {
            if ($PHPDateObject->format('Y') == 1900 && $PHPDateObject->format('n') <= 2) {
                --$DoW;
                if ($DoW < $firstDay) {
                    $DoW += 7;
                }
            }
        }
        return (int) $DoW;
    }
    public static function WEEKOFYEAR($dateValue = 1, $method = 1)
    {
        $dateValue = PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
        $method = PHPExcel_Calculation_Functions::flattenSingleValue($method);
        if (!is_numeric($method)) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($method < 1 || 2 < $method) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $method = floor($method);
        if (is_string($dateValue = self::_getDateValue($dateValue))) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($dateValue < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
        $dayOfYear = $PHPDateObject->format('z');
        $dow = $PHPDateObject->format('w');
        $PHPDateObject->modify('-' . $dayOfYear . ' days');
        $dow = $PHPDateObject->format('w');
        $daysInFirstWeek = 7 - ($dow + (2 - $method)) % 7;
        $dayOfYear -= $daysInFirstWeek;
        $weekOfYear = ceil($dayOfYear / 7) + 1;
        return (int) $weekOfYear;
    }
    public static function MONTHOFYEAR($dateValue = 1)
    {
        $dateValue = PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
        if (is_string($dateValue = self::_getDateValue($dateValue))) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($dateValue < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
        return (int) $PHPDateObject->format('n');
    }
    public static function YEAR($dateValue = 1)
    {
        $dateValue = PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
        if (is_string($dateValue = self::_getDateValue($dateValue))) {
            return PHPExcel_Calculation_Functions::VALUE();
        } else {
            if ($dateValue < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $PHPDateObject = PHPExcel_Shared_Date::ExcelToPHPObject($dateValue);
        return (int) $PHPDateObject->format('Y');
    }
    public static function HOUROFDAY($timeValue = 0)
    {
        $timeValue = PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);
        if (!is_numeric($timeValue)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
            }
            $timeValue = self::_getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        if (1 <= $timeValue) {
            $timeValue = fmod($timeValue, 1);
        } else {
            if ($timeValue < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);
        return (int) gmdate('G', $timeValue);
    }
    public static function MINUTEOFHOUR($timeValue = 0)
    {
        $timeValue = $timeTester = PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);
        if (!is_numeric($timeValue)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
            }
            $timeValue = self::_getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        if (1 <= $timeValue) {
            $timeValue = fmod($timeValue, 1);
        } else {
            if ($timeValue < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);
        return (int) gmdate('i', $timeValue);
    }
    public static function SECONDOFMINUTE($timeValue = 0)
    {
        $timeValue = PHPExcel_Calculation_Functions::flattenSingleValue($timeValue);
        if (!is_numeric($timeValue)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                $testVal = strtok($timeValue, '/-: ');
                if (strlen($testVal) < strlen($timeValue)) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
            }
            $timeValue = self::_getTimeValue($timeValue);
            if (is_string($timeValue)) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        if (1 <= $timeValue) {
            $timeValue = fmod($timeValue, 1);
        } else {
            if ($timeValue < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        $timeValue = PHPExcel_Shared_Date::ExcelToPHP($timeValue);
        return (int) gmdate('s', $timeValue);
    }
    public static function EDATE($dateValue = 1, $adjustmentMonths = 0)
    {
        $dateValue = PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
        $adjustmentMonths = PHPExcel_Calculation_Functions::flattenSingleValue($adjustmentMonths);
        if (!is_numeric($adjustmentMonths)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $adjustmentMonths = floor($adjustmentMonths);
        if (is_string($dateValue = self::_getDateValue($dateValue))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $PHPDateObject = self::_adjustDateByMonths($dateValue, $adjustmentMonths);
        switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
            case PHPExcel_Calculation_Functions:
                return (double) PHPExcel_Shared_Date::PHPToExcel($PHPDateObject);
            case PHPExcel_Calculation_Functions:
                return (int) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::PHPToExcel($PHPDateObject));
            case PHPExcel_Calculation_Functions:
                return $PHPDateObject;
        }
    }
    public static function EOMONTH($dateValue = 1, $adjustmentMonths = 0)
    {
        $dateValue = PHPExcel_Calculation_Functions::flattenSingleValue($dateValue);
        $adjustmentMonths = PHPExcel_Calculation_Functions::flattenSingleValue($adjustmentMonths);
        if (!is_numeric($adjustmentMonths)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $adjustmentMonths = floor($adjustmentMonths);
        if (is_string($dateValue = self::_getDateValue($dateValue))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $PHPDateObject = self::_adjustDateByMonths($dateValue, $adjustmentMonths + 1);
        $adjustDays = (int) $PHPDateObject->format('d');
        $adjustDaysString = '-' . $adjustDays . ' days';
        $PHPDateObject->modify($adjustDaysString);
        switch (PHPExcel_Calculation_Functions::getReturnDateType()) {
            case PHPExcel_Calculation_Functions:
                return (double) PHPExcel_Shared_Date::PHPToExcel($PHPDateObject);
            case PHPExcel_Calculation_Functions:
                return (int) PHPExcel_Shared_Date::ExcelToPHP(PHPExcel_Shared_Date::PHPToExcel($PHPDateObject));
            case PHPExcel_Calculation_Functions:
                return $PHPDateObject;
        }
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
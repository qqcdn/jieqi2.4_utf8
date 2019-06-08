<?php

class PHPExcel_Calculation_MathTrig
{
    private static function _factors($value)
    {
        $startVal = floor(sqrt($value));
        $factorArray = array();
        for ($i = $startVal; 1 < $i; --$i) {
            if ($value % $i == 0) {
                $factorArray = array_merge($factorArray, self::_factors($value / $i));
                $factorArray = array_merge($factorArray, self::_factors($i));
                if ($i <= sqrt($value)) {
                    break;
                }
            }
        }
        if (!empty($factorArray)) {
            rsort($factorArray);
            return $factorArray;
        } else {
            return array((int) $value);
        }
    }
    private static function _romanCut($num, $n)
    {
        return ($num - $num % $n) / $n;
    }
    public static function ATAN2($xCoordinate = NULL, $yCoordinate = NULL)
    {
        $xCoordinate = PHPExcel_Calculation_Functions::flattenSingleValue($xCoordinate);
        $yCoordinate = PHPExcel_Calculation_Functions::flattenSingleValue($yCoordinate);
        $xCoordinate = $xCoordinate !== NULL ? $xCoordinate : 0;
        $yCoordinate = $yCoordinate !== NULL ? $yCoordinate : 0;
        if ((is_numeric($xCoordinate) || is_bool($xCoordinate)) && is_numeric($yCoordinate) || is_bool($yCoordinate)) {
            $xCoordinate = (double) $xCoordinate;
            $yCoordinate = (double) $yCoordinate;
            if ($xCoordinate == 0 && $yCoordinate == 0) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
            return atan2($yCoordinate, $xCoordinate);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function CEILING($number, $significance = NULL)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $significance = PHPExcel_Calculation_Functions::flattenSingleValue($significance);
        if (is_null($significance) && PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
            $significance = $number / abs($number);
        }
        if (is_numeric($number) && is_numeric($significance)) {
            if ($significance == 0) {
                return 0;
            } else {
                if (self::SIGN($number) == self::SIGN($significance)) {
                    return ceil($number / $significance) * $significance;
                } else {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function COMBIN($numObjs, $numInSet)
    {
        $numObjs = PHPExcel_Calculation_Functions::flattenSingleValue($numObjs);
        $numInSet = PHPExcel_Calculation_Functions::flattenSingleValue($numInSet);
        if (is_numeric($numObjs) && is_numeric($numInSet)) {
            if ($numObjs < $numInSet) {
                return PHPExcel_Calculation_Functions::NaN();
            } else {
                if ($numInSet < 0) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }
            return round(self::FACT($numObjs) / self::FACT($numObjs - $numInSet)) / self::FACT($numInSet);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function EVEN($number)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        if (is_null($number)) {
            return 0;
        } else {
            if (is_bool($number)) {
                $number = (int) $number;
            }
        }
        if (is_numeric($number)) {
            $significance = 2 * self::SIGN($number);
            return (int) self::CEILING($number, $significance);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function FACT($factVal)
    {
        $factVal = PHPExcel_Calculation_Functions::flattenSingleValue($factVal);
        if (is_numeric($factVal)) {
            if ($factVal < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $factLoop = floor($factVal);
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                if ($factLoop < $factVal) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }
            $factorial = 1;
            while (1 < $factLoop) {
                $factorial *= $factLoop--;
            }
            return $factorial;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function FACTDOUBLE($factVal)
    {
        $factLoop = PHPExcel_Calculation_Functions::flattenSingleValue($factVal);
        if (is_numeric($factLoop)) {
            $factLoop = floor($factLoop);
            if ($factVal < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $factorial = 1;
            while (1 < $factLoop) {
                $factorial *= $factLoop--;
                --$factLoop;
            }
            return $factorial;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function FLOOR($number, $significance = NULL)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $significance = PHPExcel_Calculation_Functions::flattenSingleValue($significance);
        if (is_null($significance) && PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
            $significance = $number / abs($number);
        }
        if (is_numeric($number) && is_numeric($significance)) {
            if ((double) $significance == 0) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
            if (self::SIGN($number) == self::SIGN($significance)) {
                return floor($number / $significance) * $significance;
            } else {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function GCD()
    {
        $returnValue = 1;
        $allValuesFactors = array();
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $value) {
            if (!is_numeric($value)) {
                return PHPExcel_Calculation_Functions::VALUE();
            } else {
                if ($value == 0) {
                    continue;
                } else {
                    if ($value < 0) {
                        return PHPExcel_Calculation_Functions::NaN();
                    }
                }
            }
            $myFactors = self::_factors($value);
            $myCountedFactors = array_count_values($myFactors);
            $allValuesFactors[] = $myCountedFactors;
        }
        $allValuesCount = count($allValuesFactors);
        if ($allValuesCount == 0) {
            return 0;
        }
        $mergedArray = $allValuesFactors[0];
        for ($i = 1; $i < $allValuesCount; ++$i) {
            $mergedArray = array_intersect_key($mergedArray, $allValuesFactors[$i]);
        }
        $mergedArrayValues = count($mergedArray);
        if ($mergedArrayValues == 0) {
            return $returnValue;
        } else {
            if (1 < $mergedArrayValues) {
                foreach ($mergedArray as $mergedKey => $mergedValue) {
                    foreach ($allValuesFactors as $highestPowerTest) {
                        foreach ($highestPowerTest as $testKey => $testValue) {
                            if ($testKey == $mergedKey && $testValue < $mergedValue) {
                                $mergedArray[$mergedKey] = $testValue;
                                $mergedValue = $testValue;
                            }
                        }
                    }
                }
                $returnValue = 1;
                foreach ($mergedArray as $key => $value) {
                    $returnValue *= pow($key, $value);
                }
                return $returnValue;
            } else {
                $keys = array_keys($mergedArray);
                $key = $keys[0];
                $value = $mergedArray[$key];
                foreach ($allValuesFactors as $testValue) {
                    foreach ($testValue as $mergedKey => $mergedValue) {
                        if ($mergedKey == $key && $mergedValue < $value) {
                            $value = $mergedValue;
                        }
                    }
                }
                return pow($key, $value);
            }
        }
    }
    public static function INT($number)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        if (is_null($number)) {
            return 0;
        } else {
            if (is_bool($number)) {
                return (int) $number;
            }
        }
        if (is_numeric($number)) {
            return (int) floor($number);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function LCM()
    {
        $returnValue = 1;
        $allPoweredFactors = array();
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $value) {
            if (!is_numeric($value)) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            if ($value == 0) {
                return 0;
            } else {
                if ($value < 0) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }
            $myFactors = self::_factors(floor($value));
            $myCountedFactors = array_count_values($myFactors);
            $myPoweredFactors = array();
            foreach ($myCountedFactors as $myCountedFactor => $myCountedPower) {
                $myPoweredFactors[$myCountedFactor] = pow($myCountedFactor, $myCountedPower);
            }
            foreach ($myPoweredFactors as $myPoweredValue => $myPoweredFactor) {
                if (array_key_exists($myPoweredValue, $allPoweredFactors)) {
                    if ($allPoweredFactors[$myPoweredValue] < $myPoweredFactor) {
                        $allPoweredFactors[$myPoweredValue] = $myPoweredFactor;
                    }
                } else {
                    $allPoweredFactors[$myPoweredValue] = $myPoweredFactor;
                }
            }
        }
        foreach ($allPoweredFactors as $allPoweredFactor) {
            $returnValue *= (int) $allPoweredFactor;
        }
        return $returnValue;
    }
    public static function LOG_BASE($number = NULL, $base = 10)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $base = is_null($base) ? 10 : (double) PHPExcel_Calculation_Functions::flattenSingleValue($base);
        if (!is_numeric($base) || !is_numeric($number)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($base <= 0 || $number <= 0) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return log($number, $base);
    }
    public static function MDETERM($matrixValues)
    {
        $matrixData = array();
        if (!is_array($matrixValues)) {
            $matrixValues = array(array($matrixValues));
        }
        $row = $maxColumn = 0;
        foreach ($matrixValues as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = array($matrixRow);
            }
            $column = 0;
            foreach ($matrixRow as $matrixCell) {
                if (is_string($matrixCell) || $matrixCell === NULL) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
                $matrixData[$column][$row] = $matrixCell;
                ++$column;
            }
            if ($maxColumn < $column) {
                $maxColumn = $column;
            }
            ++$row;
        }
        if ($row != $maxColumn) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        try {
            $matrix = new PHPExcel_Shared_JAMA_Matrix($matrixData);
            return $matrix->det();
        } catch (PHPExcel_Exception $ex) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
    }
    public static function MINVERSE($matrixValues)
    {
        $matrixData = array();
        if (!is_array($matrixValues)) {
            $matrixValues = array(array($matrixValues));
        }
        $row = $maxColumn = 0;
        foreach ($matrixValues as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = array($matrixRow);
            }
            $column = 0;
            foreach ($matrixRow as $matrixCell) {
                if (is_string($matrixCell) || $matrixCell === NULL) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
                $matrixData[$column][$row] = $matrixCell;
                ++$column;
            }
            if ($maxColumn < $column) {
                $maxColumn = $column;
            }
            ++$row;
        }
        if ($row != $maxColumn) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        try {
            $matrix = new PHPExcel_Shared_JAMA_Matrix($matrixData);
            return $matrix->inverse()->getArray();
        } catch (PHPExcel_Exception $ex) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
    }
    public static function MMULT($matrixData1, $matrixData2)
    {
        $matrixAData = $matrixBData = array();
        if (!is_array($matrixData1)) {
            $matrixData1 = array(array($matrixData1));
        }
        if (!is_array($matrixData2)) {
            $matrixData2 = array(array($matrixData2));
        }
        $rowA = 0;
        foreach ($matrixData1 as $matrixRow) {
            if (!is_array($matrixRow)) {
                $matrixRow = array($matrixRow);
            }
            $columnA = 0;
            foreach ($matrixRow as $matrixCell) {
                if (is_string($matrixCell) || $matrixCell === NULL) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
                $matrixAData[$rowA][$columnA] = $matrixCell;
                ++$columnA;
            }
            ++$rowA;
        }
        try {
            $matrixA = new PHPExcel_Shared_JAMA_Matrix($matrixAData);
            $rowB = 0;
            foreach ($matrixData2 as $matrixRow) {
                if (!is_array($matrixRow)) {
                    $matrixRow = array($matrixRow);
                }
                $columnB = 0;
                foreach ($matrixRow as $matrixCell) {
                    if (is_string($matrixCell) || $matrixCell === NULL) {
                        return PHPExcel_Calculation_Functions::VALUE();
                    }
                    $matrixBData[$rowB][$columnB] = $matrixCell;
                    ++$columnB;
                }
                ++$rowB;
            }
            $matrixB = new PHPExcel_Shared_JAMA_Matrix($matrixBData);
            if ($rowA != $columnB || $rowB != $columnA) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            return $matrixA->times($matrixB)->getArray();
        } catch (PHPExcel_Exception $ex) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
    }
    public static function MOD($a = 1, $b = 1)
    {
        $a = PHPExcel_Calculation_Functions::flattenSingleValue($a);
        $b = PHPExcel_Calculation_Functions::flattenSingleValue($b);
        if ($b == 0) {
            return PHPExcel_Calculation_Functions::DIV0();
        } else {
            if ($a < 0 && 0 < $b) {
                return $b - fmod(abs($a), $b);
            } else {
                if (0 < $a && $b < 0) {
                    return $b + fmod($a, abs($b));
                }
            }
        }
        return fmod($a, $b);
    }
    public static function MROUND($number, $multiple)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $multiple = PHPExcel_Calculation_Functions::flattenSingleValue($multiple);
        if (is_numeric($number) && is_numeric($multiple)) {
            if ($multiple == 0) {
                return 0;
            }
            if (self::SIGN($number) == self::SIGN($multiple)) {
                $multiplier = 1 / $multiple;
                return round($number * $multiplier) / $multiplier;
            }
            return PHPExcel_Calculation_Functions::NaN();
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function MULTINOMIAL()
    {
        $summer = 0;
        $divisor = 1;
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            if (is_numeric($arg)) {
                if ($arg < 1) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
                $summer += floor($arg);
                $divisor *= self::FACT($arg);
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        if (0 < $summer) {
            $summer = self::FACT($summer);
            return $summer / $divisor;
        }
        return 0;
    }
    public static function ODD($number)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        if (is_null($number)) {
            return 1;
        } else {
            if (is_bool($number)) {
                $number = (int) $number;
            }
        }
        if (is_numeric($number)) {
            $significance = self::SIGN($number);
            if ($significance == 0) {
                return 1;
            }
            $result = self::CEILING($number, $significance);
            if ($result == self::EVEN($result)) {
                $result += $significance;
            }
            return (int) $result;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function POWER($x = 0, $y = 2)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $y = PHPExcel_Calculation_Functions::flattenSingleValue($y);
        if ($x == 0 && $y == 0) {
            return PHPExcel_Calculation_Functions::NaN();
        } else {
            if ($x == 0 && $y < 0) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
        }
        $result = pow($x, $y);
        return !is_nan($result) && !is_infinite($result) ? $result : PHPExcel_Calculation_Functions::NaN();
    }
    public static function PRODUCT()
    {
        $returnValue = NULL;
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                if (is_null($returnValue)) {
                    $returnValue = $arg;
                } else {
                    $returnValue *= $arg;
                }
            }
        }
        if (is_null($returnValue)) {
            return 0;
        }
        return $returnValue;
    }
    public static function QUOTIENT()
    {
        $returnValue = NULL;
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                if (is_null($returnValue)) {
                    $returnValue = $arg == 0 ? 0 : $arg;
                } else {
                    if ($returnValue == 0 || $arg == 0) {
                        $returnValue = 0;
                    } else {
                        $returnValue /= $arg;
                    }
                }
            }
        }
        return intval($returnValue);
    }
    public static function RAND($min = 0, $max = 0)
    {
        $min = PHPExcel_Calculation_Functions::flattenSingleValue($min);
        $max = PHPExcel_Calculation_Functions::flattenSingleValue($max);
        if ($min == 0 && $max == 0) {
            return rand(0, 10000000) / 10000000;
        } else {
            return rand($min, $max);
        }
    }
    public static function ROMAN($aValue, $style = 0)
    {
        $aValue = PHPExcel_Calculation_Functions::flattenSingleValue($aValue);
        $style = is_null($style) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($style);
        if (!is_numeric($aValue) || $aValue < 0 || 4000 <= $aValue) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $aValue = (int) $aValue;
        if ($aValue == 0) {
            return '';
        }
        $mill = array('', 'M', 'MM', 'MMM', 'MMMM', 'MMMMM');
        $cent = array('', 'C', 'CC', 'CCC', 'CD', 'D', 'DC', 'DCC', 'DCCC', 'CM');
        $tens = array('', 'X', 'XX', 'XXX', 'XL', 'L', 'LX', 'LXX', 'LXXX', 'XC');
        $ones = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX');
        $roman = '';
        while (5999 < $aValue) {
            $roman .= 'M';
            $aValue -= 1000;
        }
        $m = self::_romanCut($aValue, 1000);
        $aValue %= 1000;
        $c = self::_romanCut($aValue, 100);
        $aValue %= 100;
        $t = self::_romanCut($aValue, 10);
        $aValue %= 10;
        return $roman . $mill[$m] . $cent[$c] . $tens[$t] . $ones[$aValue];
    }
    public static function ROUNDUP($number, $digits)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $digits = PHPExcel_Calculation_Functions::flattenSingleValue($digits);
        if (is_numeric($number) && is_numeric($digits)) {
            $significance = pow(10, (int) $digits);
            if ($number < 0) {
                return floor($number * $significance) / $significance;
            } else {
                return ceil($number * $significance) / $significance;
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function ROUNDDOWN($number, $digits)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $digits = PHPExcel_Calculation_Functions::flattenSingleValue($digits);
        if (is_numeric($number) && is_numeric($digits)) {
            $significance = pow(10, (int) $digits);
            if ($number < 0) {
                return ceil($number * $significance) / $significance;
            } else {
                return floor($number * $significance) / $significance;
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function SERIESSUM()
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $x = array_shift($aArgs);
        $n = array_shift($aArgs);
        $m = array_shift($aArgs);
        if (is_numeric($x) && is_numeric($n) && is_numeric($m)) {
            $i = 0;
            foreach ($aArgs as $arg) {
                if (is_numeric($arg) && !is_string($arg)) {
                    $returnValue += $arg * pow($x, $n + $m * $i++);
                } else {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
            }
            return $returnValue;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function SIGN($number)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        if (is_bool($number)) {
            return (int) $number;
        }
        if (is_numeric($number)) {
            if ($number == 0) {
                return 0;
            }
            return $number / abs($number);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function SQRTPI($number)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        if (is_numeric($number)) {
            if ($number < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return sqrt($number * M_PI);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function SUBTOTAL()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $subtotal = array_shift($aArgs);
        if (is_numeric($subtotal) && !is_string($subtotal)) {
            switch ($subtotal) {
                case 1:
                    return PHPExcel_Calculation_Statistical::AVERAGE($aArgs);
                    break;
                case 2:
                    return PHPExcel_Calculation_Statistical::COUNT($aArgs);
                    break;
                case 3:
                    return PHPExcel_Calculation_Statistical::COUNTA($aArgs);
                    break;
                case 4:
                    return PHPExcel_Calculation_Statistical::MAX($aArgs);
                    break;
                case 5:
                    return PHPExcel_Calculation_Statistical::MIN($aArgs);
                    break;
                case 6:
                    return self::PRODUCT($aArgs);
                    break;
                case 7:
                    return PHPExcel_Calculation_Statistical::STDEV($aArgs);
                    break;
                case 8:
                    return PHPExcel_Calculation_Statistical::STDEVP($aArgs);
                    break;
                case 9:
                    return self::SUM($aArgs);
                    break;
                case 10:
                    return PHPExcel_Calculation_Statistical::VARFunc($aArgs);
                    break;
                case 11:
                    return PHPExcel_Calculation_Statistical::VARP($aArgs);
                    break;
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function SUM()
    {
        $returnValue = 0;
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                $returnValue += $arg;
            }
        }
        return $returnValue;
    }
    public static function SUMIF($aArgs, $condition, $sumArgs = array())
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray($aArgs);
        $sumArgs = PHPExcel_Calculation_Functions::flattenArray($sumArgs);
        if (empty($sumArgs)) {
            $sumArgs = $aArgs;
        }
        $condition = PHPExcel_Calculation_Functions::_ifCondition($condition);
        foreach ($aArgs as $key => $arg) {
            if (!is_numeric($arg)) {
                $arg = str_replace('"', '""', $arg);
                $arg = PHPExcel_Calculation::_wrapResult(strtoupper($arg));
            }
            $testCondition = '=' . $arg . $condition;
            if (PHPExcel_Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                $returnValue += $sumArgs[$key];
            }
        }
        return $returnValue;
    }
    public static function SUMPRODUCT()
    {
        $arrayList = func_get_args();
        $wrkArray = PHPExcel_Calculation_Functions::flattenArray(array_shift($arrayList));
        $wrkCellCount = count($wrkArray);
        for ($i = 0; $i < $wrkCellCount; ++$i) {
            if (!is_numeric($wrkArray[$i]) || is_string($wrkArray[$i])) {
                $wrkArray[$i] = 0;
            }
        }
        foreach ($arrayList as $matrixData) {
            $array2 = PHPExcel_Calculation_Functions::flattenArray($matrixData);
            $count = count($array2);
            if ($wrkCellCount != $count) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            foreach ($array2 as $i => $val) {
                if (!is_numeric($val) || is_string($val)) {
                    $val = 0;
                }
                $wrkArray[$i] *= $val;
            }
        }
        return array_sum($wrkArray);
    }
    public static function SUMSQ()
    {
        $returnValue = 0;
        foreach (PHPExcel_Calculation_Functions::flattenArray(func_get_args()) as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                $returnValue += $arg * $arg;
            }
        }
        return $returnValue;
    }
    public static function SUMX2MY2($matrixData1, $matrixData2)
    {
        $array1 = PHPExcel_Calculation_Functions::flattenArray($matrixData1);
        $array2 = PHPExcel_Calculation_Functions::flattenArray($matrixData2);
        $count1 = count($array1);
        $count2 = count($array2);
        if ($count1 < $count2) {
            $count = $count1;
        } else {
            $count = $count2;
        }
        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (is_numeric($array1[$i]) && !is_string($array1[$i]) && is_numeric($array2[$i]) && !is_string($array2[$i])) {
                $result += $array1[$i] * $array1[$i] - $array2[$i] * $array2[$i];
            }
        }
        return $result;
    }
    public static function SUMX2PY2($matrixData1, $matrixData2)
    {
        $array1 = PHPExcel_Calculation_Functions::flattenArray($matrixData1);
        $array2 = PHPExcel_Calculation_Functions::flattenArray($matrixData2);
        $count1 = count($array1);
        $count2 = count($array2);
        if ($count1 < $count2) {
            $count = $count1;
        } else {
            $count = $count2;
        }
        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (is_numeric($array1[$i]) && !is_string($array1[$i]) && is_numeric($array2[$i]) && !is_string($array2[$i])) {
                $result += $array1[$i] * $array1[$i] + $array2[$i] * $array2[$i];
            }
        }
        return $result;
    }
    public static function SUMXMY2($matrixData1, $matrixData2)
    {
        $array1 = PHPExcel_Calculation_Functions::flattenArray($matrixData1);
        $array2 = PHPExcel_Calculation_Functions::flattenArray($matrixData2);
        $count1 = count($array1);
        $count2 = count($array2);
        if ($count1 < $count2) {
            $count = $count1;
        } else {
            $count = $count2;
        }
        $result = 0;
        for ($i = 0; $i < $count; ++$i) {
            if (is_numeric($array1[$i]) && !is_string($array1[$i]) && is_numeric($array2[$i]) && !is_string($array2[$i])) {
                $result += ($array1[$i] - $array2[$i]) * ($array1[$i] - $array2[$i]);
            }
        }
        return $result;
    }
    public static function TRUNC($value = 0, $digits = 0)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $digits = PHPExcel_Calculation_Functions::flattenSingleValue($digits);
        if (!is_numeric($value) || !is_numeric($digits)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $digits = floor($digits);
        $adjust = pow(10, $digits);
        if (0 < $digits && rtrim(intval((abs($value) - abs(intval($value))) * $adjust), '0') < $adjust / 10) {
            return $value;
        }
        return intval($value * $adjust) / $adjust;
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
<?php

class PHPExcel_Calculation_Financial
{
    private static function _lastDayOfMonth($testDate)
    {
        return $testDate->format('d') == $testDate->format('t');
    }
    private static function _firstDayOfMonth($testDate)
    {
        return $testDate->format('d') == 1;
    }
    private static function _coupFirstPeriodDate($settlement, $maturity, $frequency, $next)
    {
        $months = 12 / $frequency;
        $result = PHPExcel_Shared_Date::ExcelToPHPObject($maturity);
        $eom = self::_lastDayOfMonth($result);
        while ($settlement < PHPExcel_Shared_Date::PHPToExcel($result)) {
            $result->modify('-' . $months . ' months');
        }
        if ($next) {
            $result->modify('+' . $months . ' months');
        }
        if ($eom) {
            $result->modify('-1 day');
        }
        return PHPExcel_Shared_Date::PHPToExcel($result);
    }
    private static function _validFrequency($frequency)
    {
        if ($frequency == 1 || $frequency == 2 || $frequency == 4) {
            return true;
        }
        if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC && ($frequency == 6 || $frequency == 12)) {
            return true;
        }
        return false;
    }
    private static function _daysPerYear($year, $basis = 0)
    {
        switch ($basis) {
            case 0:
            case 2:
            case 4:
                $daysPerYear = 360;
                break;
            case 3:
                $daysPerYear = 365;
                break;
            case 1:
                $daysPerYear = PHPExcel_Calculation_DateTime::_isLeapYear($year) ? 366 : 365;
                break;
            default:
                return PHPExcel_Calculation_Functions::NaN();
        }
        return $daysPerYear;
    }
    private static function _interestAndPrincipal($rate = 0, $per = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $pmt = self::PMT($rate, $nper, $pv, $fv, $type);
        $capital = $pv;
        for ($i = 1; $i <= $per; ++$i) {
            $interest = $type && $i == 1 ? 0 : -$capital * $rate;
            $principal = $pmt - $interest;
            $capital += $principal;
        }
        return array($interest, $principal);
    }
    public static function ACCRINT($issue, $firstinterest, $settlement, $rate, $par = 1000, $frequency = 1, $basis = 0)
    {
        $issue = PHPExcel_Calculation_Functions::flattenSingleValue($issue);
        $firstinterest = PHPExcel_Calculation_Functions::flattenSingleValue($firstinterest);
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $par = is_null($par) ? 1000 : PHPExcel_Calculation_Functions::flattenSingleValue($par);
        $frequency = is_null($frequency) ? 1 : PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
        $basis = is_null($basis) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($rate) && is_numeric($par)) {
            $rate = (double) $rate;
            $par = (double) $par;
            if ($rate <= 0 || $par <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysBetweenIssueAndSettlement = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $settlement, $basis);
            if (!is_numeric($daysBetweenIssueAndSettlement)) {
                return $daysBetweenIssueAndSettlement;
            }
            return $par * $rate * $daysBetweenIssueAndSettlement;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function ACCRINTM($issue, $settlement, $rate, $par = 1000, $basis = 0)
    {
        $issue = PHPExcel_Calculation_Functions::flattenSingleValue($issue);
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $par = is_null($par) ? 1000 : PHPExcel_Calculation_Functions::flattenSingleValue($par);
        $basis = is_null($basis) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($rate) && is_numeric($par)) {
            $rate = (double) $rate;
            $par = (double) $par;
            if ($rate <= 0 || $par <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysBetweenIssueAndSettlement = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $settlement, $basis);
            if (!is_numeric($daysBetweenIssueAndSettlement)) {
                return $daysBetweenIssueAndSettlement;
            }
            return $par * $rate * $daysBetweenIssueAndSettlement;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function AMORDEGRC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis = 0)
    {
        $cost = PHPExcel_Calculation_Functions::flattenSingleValue($cost);
        $purchased = PHPExcel_Calculation_Functions::flattenSingleValue($purchased);
        $firstPeriod = PHPExcel_Calculation_Functions::flattenSingleValue($firstPeriod);
        $salvage = PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
        $period = floor(PHPExcel_Calculation_Functions::flattenSingleValue($period));
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        $fUsePer = 1 / $rate;
        if ($fUsePer < 3) {
            $amortiseCoeff = 1;
        } else {
            if ($fUsePer < 5) {
                $amortiseCoeff = 1.5;
            } else {
                if ($fUsePer <= 6) {
                    $amortiseCoeff = 2;
                } else {
                    $amortiseCoeff = 2.5;
                }
            }
        }
        $rate *= $amortiseCoeff;
        $fNRate = round(PHPExcel_Calculation_DateTime::YEARFRAC($purchased, $firstPeriod, $basis) * $rate * $cost, 0);
        $cost -= $fNRate;
        $fRest = $cost - $salvage;
        for ($n = 0; $n < $period; ++$n) {
            $fNRate = round($rate * $cost, 0);
            $fRest -= $fNRate;
            if ($fRest < 0) {
                switch ($period - $n) {
                    case 0:
                    case 1:
                        $period - $n;
                        return round($cost * 0.5, 0);
                        break;
                    default:
                        return 0;
                        break;
                }
            }
            $cost -= $fNRate;
        }
        return $fNRate;
    }
    public static function AMORLINC($cost, $purchased, $firstPeriod, $salvage, $period, $rate, $basis = 0)
    {
        $cost = PHPExcel_Calculation_Functions::flattenSingleValue($cost);
        $purchased = PHPExcel_Calculation_Functions::flattenSingleValue($purchased);
        $firstPeriod = PHPExcel_Calculation_Functions::flattenSingleValue($firstPeriod);
        $salvage = PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
        $period = PHPExcel_Calculation_Functions::flattenSingleValue($period);
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        $fOneRate = $cost * $rate;
        $fCostDelta = $cost - $salvage;
        $purchasedYear = PHPExcel_Calculation_DateTime::YEAR($purchased);
        $yearFrac = PHPExcel_Calculation_DateTime::YEARFRAC($purchased, $firstPeriod, $basis);
        if ($basis == 1 && $yearFrac < 1 && PHPExcel_Calculation_DateTime::_isLeapYear($purchasedYear)) {
            $yearFrac *= 365 / 366;
        }
        $f0Rate = $yearFrac * $rate * $cost;
        $nNumOfFullPeriods = intval(($cost - $salvage - $f0Rate) / $fOneRate);
        if ($period == 0) {
            return $f0Rate;
        } else {
            if ($period <= $nNumOfFullPeriods) {
                return $fOneRate;
            } else {
                if ($period == $nNumOfFullPeriods + 1) {
                    return $fCostDelta - $fOneRate * $nNumOfFullPeriods - $f0Rate;
                } else {
                    return 0;
                }
            }
        }
    }
    public static function COUPDAYBS($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $frequency = (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($maturity < $settlement || !self::_validFrequency($frequency) || $basis < 0 || 4 < $basis) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement), $basis);
        $prev = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, false);
        return PHPExcel_Calculation_DateTime::YEARFRAC($prev, $settlement, $basis) * $daysPerYear;
    }
    public static function COUPDAYS($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $frequency = (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($maturity < $settlement || !self::_validFrequency($frequency) || $basis < 0 || 4 < $basis) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        switch ($basis) {
            case 3:
                return 365 / $frequency;
            case 1:
                if ($frequency == 1) {
                    $daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($maturity), $basis);
                    return $daysPerYear / $frequency;
                } else {
                    $prev = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, false);
                    $next = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, true);
                    return $next - $prev;
                }
            default:
                return 360 / $frequency;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function COUPDAYSNC($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $frequency = (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($maturity < $settlement || !self::_validFrequency($frequency) || $basis < 0 || 4 < $basis) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement), $basis);
        $next = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, true);
        return PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $next, $basis) * $daysPerYear;
    }
    public static function COUPNCD($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $frequency = (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($maturity < $settlement || !self::_validFrequency($frequency) || $basis < 0 || 4 < $basis) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return self::_coupFirstPeriodDate($settlement, $maturity, $frequency, true);
    }
    public static function COUPNUM($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $frequency = (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($maturity < $settlement || !self::_validFrequency($frequency) || $basis < 0 || 4 < $basis) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $settlement = self::_coupFirstPeriodDate($settlement, $maturity, $frequency, true);
        $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis) * 365;
        switch ($frequency) {
            case 1:
                return ceil($daysBetweenSettlementAndMaturity / 360);
            case 2:
                return ceil($daysBetweenSettlementAndMaturity / 180);
            case 4:
                return ceil($daysBetweenSettlementAndMaturity / 90);
            case 6:
                return ceil($daysBetweenSettlementAndMaturity / 60);
            case 12:
                return ceil($daysBetweenSettlementAndMaturity / 30);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function COUPPCD($settlement, $maturity, $frequency, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $frequency = (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($maturity < $settlement || !self::_validFrequency($frequency) || $basis < 0 || 4 < $basis) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return self::_coupFirstPeriodDate($settlement, $maturity, $frequency, false);
    }
    public static function CUMIPMT($rate, $nper, $pv, $start, $end, $type = 0)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $nper = (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
        $pv = PHPExcel_Calculation_Functions::flattenSingleValue($pv);
        $start = (int) PHPExcel_Calculation_Functions::flattenSingleValue($start);
        $end = (int) PHPExcel_Calculation_Functions::flattenSingleValue($end);
        $type = (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);
        if ($type != 0 && $type != 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($start < 1 || $end < $start) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $interest = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $interest += self::IPMT($rate, $per, $nper, $pv, 0, $type);
        }
        return $interest;
    }
    public static function CUMPRINC($rate, $nper, $pv, $start, $end, $type = 0)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $nper = (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
        $pv = PHPExcel_Calculation_Functions::flattenSingleValue($pv);
        $start = (int) PHPExcel_Calculation_Functions::flattenSingleValue($start);
        $end = (int) PHPExcel_Calculation_Functions::flattenSingleValue($end);
        $type = (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);
        if ($type != 0 && $type != 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($start < 1 || $end < $start) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $principal = 0;
        for ($per = $start; $per <= $end; ++$per) {
            $principal += self::PPMT($rate, $per, $nper, $pv, 0, $type);
        }
        return $principal;
    }
    public static function DB($cost, $salvage, $life, $period, $month = 12)
    {
        $cost = PHPExcel_Calculation_Functions::flattenSingleValue($cost);
        $salvage = PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
        $life = PHPExcel_Calculation_Functions::flattenSingleValue($life);
        $period = PHPExcel_Calculation_Functions::flattenSingleValue($period);
        $month = PHPExcel_Calculation_Functions::flattenSingleValue($month);
        if (is_numeric($cost) && is_numeric($salvage) && is_numeric($life) && is_numeric($period) && is_numeric($month)) {
            $cost = (double) $cost;
            $salvage = (double) $salvage;
            $life = (int) $life;
            $period = (int) $period;
            $month = (int) $month;
            if ($cost == 0) {
                return 0;
            } else {
                if ($cost < 0 || $salvage / $cost < 0 || $life <= 0 || $period < 1 || $month < 1) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }
            $fixedDepreciationRate = 1 - pow($salvage / $cost, 1 / $life);
            $fixedDepreciationRate = round($fixedDepreciationRate, 3);
            $previousDepreciation = 0;
            for ($per = 1; $per <= $period; ++$per) {
                if ($per == 1) {
                    $depreciation = $cost * $fixedDepreciationRate * $month / 12;
                } else {
                    if ($per == $life + 1) {
                        $depreciation = ($cost - $previousDepreciation) * $fixedDepreciationRate * (12 - $month) / 12;
                    } else {
                        $depreciation = ($cost - $previousDepreciation) * $fixedDepreciationRate;
                    }
                }
                $previousDepreciation += $depreciation;
            }
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                $depreciation = round($depreciation, 2);
            }
            return $depreciation;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function DDB($cost, $salvage, $life, $period, $factor = 2)
    {
        $cost = PHPExcel_Calculation_Functions::flattenSingleValue($cost);
        $salvage = PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
        $life = PHPExcel_Calculation_Functions::flattenSingleValue($life);
        $period = PHPExcel_Calculation_Functions::flattenSingleValue($period);
        $factor = PHPExcel_Calculation_Functions::flattenSingleValue($factor);
        if (is_numeric($cost) && is_numeric($salvage) && is_numeric($life) && is_numeric($period) && is_numeric($factor)) {
            $cost = (double) $cost;
            $salvage = (double) $salvage;
            $life = (int) $life;
            $period = (int) $period;
            $factor = (double) $factor;
            if ($cost <= 0 || $salvage / $cost < 0 || $life <= 0 || $period < 1 || $factor <= 0 || $life < $period) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $fixedDepreciationRate = 1 - pow($salvage / $cost, 1 / $life);
            $fixedDepreciationRate = round($fixedDepreciationRate, 3);
            $previousDepreciation = 0;
            for ($per = 1; $per <= $period; ++$per) {
                $depreciation = min(($cost - $previousDepreciation) * ($factor / $life), $cost - $salvage - $previousDepreciation);
                $previousDepreciation += $depreciation;
            }
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                $depreciation = round($depreciation, 2);
            }
            return $depreciation;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function DISC($settlement, $maturity, $price, $redemption, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $price = PHPExcel_Calculation_Functions::flattenSingleValue($price);
        $redemption = PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
        $basis = PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($price) && is_numeric($redemption) && is_numeric($basis)) {
            $price = (double) $price;
            $redemption = (double) $redemption;
            $basis = (int) $basis;
            if ($price <= 0 || $redemption <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                return $daysBetweenSettlementAndMaturity;
            }
            return (1 - $price / $redemption) / $daysBetweenSettlementAndMaturity;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function DOLLARDE($fractional_dollar = NULL, $fraction = 0)
    {
        $fractional_dollar = PHPExcel_Calculation_Functions::flattenSingleValue($fractional_dollar);
        $fraction = (int) PHPExcel_Calculation_Functions::flattenSingleValue($fraction);
        if (is_null($fractional_dollar) || $fraction < 0) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($fraction == 0) {
            return PHPExcel_Calculation_Functions::DIV0();
        }
        $dollars = floor($fractional_dollar);
        $cents = fmod($fractional_dollar, 1);
        $cents /= $fraction;
        $cents *= pow(10, ceil(log10($fraction)));
        return $dollars + $cents;
    }
    public static function DOLLARFR($decimal_dollar = NULL, $fraction = 0)
    {
        $decimal_dollar = PHPExcel_Calculation_Functions::flattenSingleValue($decimal_dollar);
        $fraction = (int) PHPExcel_Calculation_Functions::flattenSingleValue($fraction);
        if (is_null($decimal_dollar) || $fraction < 0) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($fraction == 0) {
            return PHPExcel_Calculation_Functions::DIV0();
        }
        $dollars = floor($decimal_dollar);
        $cents = fmod($decimal_dollar, 1);
        $cents *= $fraction;
        $cents *= pow(10, -ceil(log10($fraction)));
        return $dollars + $cents;
    }
    public static function EFFECT($nominal_rate = 0, $npery = 0)
    {
        $nominal_rate = PHPExcel_Calculation_Functions::flattenSingleValue($nominal_rate);
        $npery = (int) PHPExcel_Calculation_Functions::flattenSingleValue($npery);
        if ($nominal_rate <= 0 || $npery < 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return pow(1 + $nominal_rate / $npery, $npery) - 1;
    }
    public static function FV($rate = 0, $nper = 0, $pmt = 0, $pv = 0, $type = 0)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $nper = PHPExcel_Calculation_Functions::flattenSingleValue($nper);
        $pmt = PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
        $pv = PHPExcel_Calculation_Functions::flattenSingleValue($pv);
        $type = PHPExcel_Calculation_Functions::flattenSingleValue($type);
        if ($type != 0 && $type != 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if (!is_null($rate) && $rate != 0) {
            return -$pv * pow(1 + $rate, $nper) - $pmt * (1 + $rate * $type) * (pow(1 + $rate, $nper) - 1) / $rate;
        } else {
            return -$pv - $pmt * $nper;
        }
    }
    public static function FVSCHEDULE($principal, $schedule)
    {
        $principal = PHPExcel_Calculation_Functions::flattenSingleValue($principal);
        $schedule = PHPExcel_Calculation_Functions::flattenArray($schedule);
        foreach ($schedule as $rate) {
            $principal *= 1 + $rate;
        }
        return $principal;
    }
    public static function INTRATE($settlement, $maturity, $investment, $redemption, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $investment = PHPExcel_Calculation_Functions::flattenSingleValue($investment);
        $redemption = PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
        $basis = PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($investment) && is_numeric($redemption) && is_numeric($basis)) {
            $investment = (double) $investment;
            $redemption = (double) $redemption;
            $basis = (int) $basis;
            if ($investment <= 0 || $redemption <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                return $daysBetweenSettlementAndMaturity;
            }
            return ($redemption / $investment - 1) / $daysBetweenSettlementAndMaturity;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function IPMT($rate, $per, $nper, $pv, $fv = 0, $type = 0)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $per = (int) PHPExcel_Calculation_Functions::flattenSingleValue($per);
        $nper = (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
        $pv = PHPExcel_Calculation_Functions::flattenSingleValue($pv);
        $fv = PHPExcel_Calculation_Functions::flattenSingleValue($fv);
        $type = (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);
        if ($type != 0 && $type != 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($per <= 0 || $nper < $per) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $interestAndPrincipal = self::_interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);
        return $interestAndPrincipal[0];
    }
    public static function IRR($values, $guess = 0.1)
    {
        if (!is_array($values)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $values = PHPExcel_Calculation_Functions::flattenArray($values);
        $guess = PHPExcel_Calculation_Functions::flattenSingleValue($guess);
        $x1 = 0;
        $x2 = $guess;
        $f1 = self::NPV($x1, $values);
        $f2 = self::NPV($x2, $values);
        for ($i = 0; $i < FINANCIAL_MAX_ITERATIONS; ++$i) {
            if ($f1 * $f2 < 0) {
                break;
            }
            if (abs($f1) < abs($f2)) {
                $f1 = self::NPV($x1 += 1.6 * ($x1 - $x2), $values);
            } else {
                $f2 = self::NPV($x2 += 1.6 * ($x2 - $x1), $values);
            }
        }
        if (0 < $f1 * $f2) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $f = self::NPV($x1, $values);
        if ($f < 0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }
        for ($i = 0; $i < FINANCIAL_MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::NPV($x_mid, $values);
            if ($f_mid <= 0) {
                $rtb = $x_mid;
            }
            if (abs($f_mid) < FINANCIAL_PRECISION || abs($dx) < FINANCIAL_PRECISION) {
                return $x_mid;
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function ISPMT()
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $interestRate = array_shift($aArgs);
        $period = array_shift($aArgs);
        $numberPeriods = array_shift($aArgs);
        $principleRemaining = array_shift($aArgs);
        $principlePayment = $principleRemaining * 1 / ($numberPeriods * 1);
        for ($i = 0; $i <= $period; ++$i) {
            $returnValue = $interestRate * $principleRemaining * -1;
            $principleRemaining -= $principlePayment;
            if ($i == $numberPeriods) {
                $returnValue = 0;
            }
        }
        return $returnValue;
    }
    public static function MIRR($values, $finance_rate, $reinvestment_rate)
    {
        if (!is_array($values)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $values = PHPExcel_Calculation_Functions::flattenArray($values);
        $finance_rate = PHPExcel_Calculation_Functions::flattenSingleValue($finance_rate);
        $reinvestment_rate = PHPExcel_Calculation_Functions::flattenSingleValue($reinvestment_rate);
        $n = count($values);
        $rr = 1 + $reinvestment_rate;
        $fr = 1 + $finance_rate;
        $npv_pos = $npv_neg = 0;
        foreach ($values as $i => $v) {
            if (0 <= $v) {
                $npv_pos += $v / pow($rr, $i);
            } else {
                $npv_neg += $v / pow($fr, $i);
            }
        }
        if ($npv_neg == 0 || $npv_pos == 0 || $reinvestment_rate <= -1) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $mirr = pow(-$npv_pos * pow($rr, $n) / ($npv_neg * $rr), 1 / ($n - 1)) - 1;
        return is_finite($mirr) ? $mirr : PHPExcel_Calculation_Functions::VALUE();
    }
    public static function NOMINAL($effect_rate = 0, $npery = 0)
    {
        $effect_rate = PHPExcel_Calculation_Functions::flattenSingleValue($effect_rate);
        $npery = (int) PHPExcel_Calculation_Functions::flattenSingleValue($npery);
        if ($effect_rate <= 0 || $npery < 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return $npery * (pow($effect_rate + 1, 1 / $npery) - 1);
    }
    public static function NPER($rate = 0, $pmt = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $pmt = PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
        $pv = PHPExcel_Calculation_Functions::flattenSingleValue($pv);
        $fv = PHPExcel_Calculation_Functions::flattenSingleValue($fv);
        $type = PHPExcel_Calculation_Functions::flattenSingleValue($type);
        if ($type != 0 && $type != 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if (!is_null($rate) && $rate != 0) {
            if ($pmt == 0 && $pv == 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return log(($pmt * (1 + $rate * $type) / $rate - $fv) / ($pv + $pmt * (1 + $rate * $type) / $rate)) / log(1 + $rate);
        } else {
            if ($pmt == 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return (-$pv - $fv) / $pmt;
        }
    }
    public static function NPV()
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $rate = array_shift($aArgs);
        for ($i = 1; $i <= count($aArgs); ++$i) {
            if (is_numeric($aArgs[$i - 1])) {
                $returnValue += $aArgs[$i - 1] / pow(1 + $rate, $i);
            }
        }
        return $returnValue;
    }
    public static function PMT($rate = 0, $nper = 0, $pv = 0, $fv = 0, $type = 0)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $nper = PHPExcel_Calculation_Functions::flattenSingleValue($nper);
        $pv = PHPExcel_Calculation_Functions::flattenSingleValue($pv);
        $fv = PHPExcel_Calculation_Functions::flattenSingleValue($fv);
        $type = PHPExcel_Calculation_Functions::flattenSingleValue($type);
        if ($type != 0 && $type != 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if (!is_null($rate) && $rate != 0) {
            return (-$fv - $pv * pow(1 + $rate, $nper)) / (1 + $rate * $type) / (pow(1 + $rate, $nper) - 1) / $rate;
        } else {
            return (-$pv - $fv) / $nper;
        }
    }
    public static function PPMT($rate, $per, $nper, $pv, $fv = 0, $type = 0)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $per = (int) PHPExcel_Calculation_Functions::flattenSingleValue($per);
        $nper = (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
        $pv = PHPExcel_Calculation_Functions::flattenSingleValue($pv);
        $fv = PHPExcel_Calculation_Functions::flattenSingleValue($fv);
        $type = (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);
        if ($type != 0 && $type != 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($per <= 0 || $nper < $per) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $interestAndPrincipal = self::_interestAndPrincipal($rate, $per, $nper, $pv, $fv, $type);
        return $interestAndPrincipal[1];
    }
    public static function PRICE($settlement, $maturity, $rate, $yield, $redemption, $frequency, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $rate = (double) PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $yield = (double) PHPExcel_Calculation_Functions::flattenSingleValue($yield);
        $redemption = (double) PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
        $frequency = (int) PHPExcel_Calculation_Functions::flattenSingleValue($frequency);
        $basis = is_null($basis) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_string($settlement = PHPExcel_Calculation_DateTime::_getDateValue($settlement))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if ($maturity < $settlement || !self::_validFrequency($frequency) || $basis < 0 || 4 < $basis) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $dsc = self::COUPDAYSNC($settlement, $maturity, $frequency, $basis);
        $e = self::COUPDAYS($settlement, $maturity, $frequency, $basis);
        $n = self::COUPNUM($settlement, $maturity, $frequency, $basis);
        $a = self::COUPDAYBS($settlement, $maturity, $frequency, $basis);
        $baseYF = 1 + $yield / $frequency;
        $rfp = 100 * ($rate / $frequency);
        $de = $dsc / $e;
        $result = $redemption / pow($baseYF, --$n + $de);
        for ($k = 0; $k <= $n; ++$k) {
            $result += $rfp / pow($baseYF, $k + $de);
        }
        $result -= $rfp * ($a / $e);
        return $result;
    }
    public static function PRICEDISC($settlement, $maturity, $discount, $redemption, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $discount = (double) PHPExcel_Calculation_Functions::flattenSingleValue($discount);
        $redemption = (double) PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
        $basis = (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($discount) && is_numeric($redemption) && is_numeric($basis)) {
            if ($discount <= 0 || $redemption <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                return $daysBetweenSettlementAndMaturity;
            }
            return $redemption * (1 - $discount * $daysBetweenSettlementAndMaturity);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function PRICEMAT($settlement, $maturity, $issue, $rate, $yield, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $issue = PHPExcel_Calculation_Functions::flattenSingleValue($issue);
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $yield = PHPExcel_Calculation_Functions::flattenSingleValue($yield);
        $basis = (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($rate) && is_numeric($yield)) {
            if ($rate <= 0 || $yield <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement), $basis);
            if (!is_numeric($daysPerYear)) {
                return $daysPerYear;
            }
            $daysBetweenIssueAndSettlement = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $settlement, $basis);
            if (!is_numeric($daysBetweenIssueAndSettlement)) {
                return $daysBetweenIssueAndSettlement;
            }
            $daysBetweenIssueAndSettlement *= $daysPerYear;
            $daysBetweenIssueAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $maturity, $basis);
            if (!is_numeric($daysBetweenIssueAndMaturity)) {
                return $daysBetweenIssueAndMaturity;
            }
            $daysBetweenIssueAndMaturity *= $daysPerYear;
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                return $daysBetweenSettlementAndMaturity;
            }
            $daysBetweenSettlementAndMaturity *= $daysPerYear;
            return (100 + $daysBetweenIssueAndMaturity / $daysPerYear * $rate * 100) / (1 + $daysBetweenSettlementAndMaturity / $daysPerYear * $yield) - $daysBetweenIssueAndSettlement / $daysPerYear * $rate * 100;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function PV($rate = 0, $nper = 0, $pmt = 0, $fv = 0, $type = 0)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $nper = PHPExcel_Calculation_Functions::flattenSingleValue($nper);
        $pmt = PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
        $fv = PHPExcel_Calculation_Functions::flattenSingleValue($fv);
        $type = PHPExcel_Calculation_Functions::flattenSingleValue($type);
        if ($type != 0 && $type != 1) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if (!is_null($rate) && $rate != 0) {
            return (-$pmt * (1 + $rate * $type) * ((pow(1 + $rate, $nper) - 1) / $rate) - $fv) / pow(1 + $rate, $nper);
        } else {
            return -$fv - $pmt * $nper;
        }
    }
    public static function RATE($nper, $pmt, $pv, $fv = 0, $type = 0, $guess = 0.1)
    {
        $nper = (int) PHPExcel_Calculation_Functions::flattenSingleValue($nper);
        $pmt = PHPExcel_Calculation_Functions::flattenSingleValue($pmt);
        $pv = PHPExcel_Calculation_Functions::flattenSingleValue($pv);
        $fv = is_null($fv) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($fv);
        $type = is_null($type) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($type);
        $guess = is_null($guess) ? 0.1 : PHPExcel_Calculation_Functions::flattenSingleValue($guess);
        $rate = $guess;
        if (abs($rate) < FINANCIAL_PRECISION) {
            $y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
        } else {
            $f = exp($nper * log(1 + $rate));
            $y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
        }
        $y0 = $pv + $pmt * $nper + $fv;
        $y1 = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
        $i = $x0 = 0;
        $x1 = $rate;
        while (FINANCIAL_PRECISION < abs($y0 - $y1) && $i < FINANCIAL_MAX_ITERATIONS) {
            $rate = ($y1 * $x0 - $y0 * $x1) / ($y1 - $y0);
            $x0 = $x1;
            $x1 = $rate;
            if ($pv - $fv < $nper * abs($pmt)) {
                $x1 = abs($x1);
            }
            if (abs($rate) < FINANCIAL_PRECISION) {
                $y = $pv * (1 + $nper * $rate) + $pmt * (1 + $rate * $type) * $nper + $fv;
            } else {
                $f = exp($nper * log(1 + $rate));
                $y = $pv * $f + $pmt * (1 / $rate + $type) * ($f - 1) + $fv;
            }
            $y0 = $y1;
            $y1 = $y;
            ++$i;
        }
        return $rate;
    }
    public static function RECEIVED($settlement, $maturity, $investment, $discount, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $investment = (double) PHPExcel_Calculation_Functions::flattenSingleValue($investment);
        $discount = (double) PHPExcel_Calculation_Functions::flattenSingleValue($discount);
        $basis = (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($investment) && is_numeric($discount) && is_numeric($basis)) {
            if ($investment <= 0 || $discount <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                return $daysBetweenSettlementAndMaturity;
            }
            return $investment / (1 - $discount * $daysBetweenSettlementAndMaturity);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function SLN($cost, $salvage, $life)
    {
        $cost = PHPExcel_Calculation_Functions::flattenSingleValue($cost);
        $salvage = PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
        $life = PHPExcel_Calculation_Functions::flattenSingleValue($life);
        if (is_numeric($cost) && is_numeric($salvage) && is_numeric($life)) {
            if ($life < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return ($cost - $salvage) / $life;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function SYD($cost, $salvage, $life, $period)
    {
        $cost = PHPExcel_Calculation_Functions::flattenSingleValue($cost);
        $salvage = PHPExcel_Calculation_Functions::flattenSingleValue($salvage);
        $life = PHPExcel_Calculation_Functions::flattenSingleValue($life);
        $period = PHPExcel_Calculation_Functions::flattenSingleValue($period);
        if (is_numeric($cost) && is_numeric($salvage) && is_numeric($life) && is_numeric($period)) {
            if ($life < 1 || $life < $period) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return ($cost - $salvage) * ($life - $period + 1) * 2 / ($life * ($life + 1));
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function TBILLEQ($settlement, $maturity, $discount)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $discount = PHPExcel_Calculation_Functions::flattenSingleValue($discount);
        $testValue = self::TBILLPRICE($settlement, $maturity, $discount);
        if (is_string($testValue)) {
            return $testValue;
        }
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
            ++$maturity;
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity) * 360;
        } else {
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity) - PHPExcel_Calculation_DateTime::_getDateValue($settlement);
        }
        return 365 * $discount / (360 - $discount * $daysBetweenSettlementAndMaturity);
    }
    public static function TBILLPRICE($settlement, $maturity, $discount)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $discount = PHPExcel_Calculation_Functions::flattenSingleValue($discount);
        if (is_string($maturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity))) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (is_numeric($discount)) {
            if ($discount <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                ++$maturity;
                $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity) * 360;
                if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                    return $daysBetweenSettlementAndMaturity;
                }
            } else {
                $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity) - PHPExcel_Calculation_DateTime::_getDateValue($settlement);
            }
            if (360 < $daysBetweenSettlementAndMaturity) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $price = 100 * (1 - $discount * $daysBetweenSettlementAndMaturity / 360);
            if ($price <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return $price;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function TBILLYIELD($settlement, $maturity, $price)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $price = PHPExcel_Calculation_Functions::flattenSingleValue($price);
        if (is_numeric($price)) {
            if ($price <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                ++$maturity;
                $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity) * 360;
                if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                    return $daysBetweenSettlementAndMaturity;
                }
            } else {
                $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::_getDateValue($maturity) - PHPExcel_Calculation_DateTime::_getDateValue($settlement);
            }
            if (360 < $daysBetweenSettlementAndMaturity) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return (100 - $price) / $price * (360 / $daysBetweenSettlementAndMaturity);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function XIRR($values, $dates, $guess = 0.1)
    {
        if (!is_array($values) && !is_array($dates)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $values = PHPExcel_Calculation_Functions::flattenArray($values);
        $dates = PHPExcel_Calculation_Functions::flattenArray($dates);
        $guess = PHPExcel_Calculation_Functions::flattenSingleValue($guess);
        if (count($values) != count($dates)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $x1 = 0;
        $x2 = $guess;
        $f1 = self::XNPV($x1, $values, $dates);
        $f2 = self::XNPV($x2, $values, $dates);
        for ($i = 0; $i < FINANCIAL_MAX_ITERATIONS; ++$i) {
            if ($f1 * $f2 < 0) {
                break;
            }
            if (abs($f1) < abs($f2)) {
                $f1 = self::XNPV($x1 += 1.6 * ($x1 - $x2), $values, $dates);
            } else {
                $f2 = self::XNPV($x2 += 1.6 * ($x2 - $x1), $values, $dates);
            }
        }
        if (0 < $f1 * $f2) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $f = self::XNPV($x1, $values, $dates);
        if ($f < 0) {
            $rtb = $x1;
            $dx = $x2 - $x1;
        } else {
            $rtb = $x2;
            $dx = $x1 - $x2;
        }
        for ($i = 0; $i < FINANCIAL_MAX_ITERATIONS; ++$i) {
            $dx *= 0.5;
            $x_mid = $rtb + $dx;
            $f_mid = self::XNPV($x_mid, $values, $dates);
            if ($f_mid <= 0) {
                $rtb = $x_mid;
            }
            if (abs($f_mid) < FINANCIAL_PRECISION || abs($dx) < FINANCIAL_PRECISION) {
                return $x_mid;
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function XNPV($rate, $values, $dates)
    {
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        if (!is_numeric($rate)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (!is_array($values) || !is_array($dates)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $values = PHPExcel_Calculation_Functions::flattenArray($values);
        $dates = PHPExcel_Calculation_Functions::flattenArray($dates);
        $valCount = count($values);
        if ($valCount != count($dates)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if (0 < min($values) || max($values) < 0) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $xnpv = 0;
        for ($i = 0; $i < $valCount; ++$i) {
            if (!is_numeric($values[$i])) {
                return PHPExcel_Calculation_Functions::VALUE();
            }
            $xnpv += $values[$i] / pow(1 + $rate, PHPExcel_Calculation_DateTime::DATEDIF($dates[0], $dates[$i], 'd') / 365);
        }
        return is_finite($xnpv) ? $xnpv : PHPExcel_Calculation_Functions::VALUE();
    }
    public static function YIELDDISC($settlement, $maturity, $price, $redemption, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $price = PHPExcel_Calculation_Functions::flattenSingleValue($price);
        $redemption = PHPExcel_Calculation_Functions::flattenSingleValue($redemption);
        $basis = (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($price) && is_numeric($redemption)) {
            if ($price <= 0 || $redemption <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement), $basis);
            if (!is_numeric($daysPerYear)) {
                return $daysPerYear;
            }
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                return $daysBetweenSettlementAndMaturity;
            }
            $daysBetweenSettlementAndMaturity *= $daysPerYear;
            return ($redemption - $price) / $price * ($daysPerYear / $daysBetweenSettlementAndMaturity);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function YIELDMAT($settlement, $maturity, $issue, $rate, $price, $basis = 0)
    {
        $settlement = PHPExcel_Calculation_Functions::flattenSingleValue($settlement);
        $maturity = PHPExcel_Calculation_Functions::flattenSingleValue($maturity);
        $issue = PHPExcel_Calculation_Functions::flattenSingleValue($issue);
        $rate = PHPExcel_Calculation_Functions::flattenSingleValue($rate);
        $price = PHPExcel_Calculation_Functions::flattenSingleValue($price);
        $basis = (int) PHPExcel_Calculation_Functions::flattenSingleValue($basis);
        if (is_numeric($rate) && is_numeric($price)) {
            if ($rate <= 0 || $price <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $daysPerYear = self::_daysPerYear(PHPExcel_Calculation_DateTime::YEAR($settlement), $basis);
            if (!is_numeric($daysPerYear)) {
                return $daysPerYear;
            }
            $daysBetweenIssueAndSettlement = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $settlement, $basis);
            if (!is_numeric($daysBetweenIssueAndSettlement)) {
                return $daysBetweenIssueAndSettlement;
            }
            $daysBetweenIssueAndSettlement *= $daysPerYear;
            $daysBetweenIssueAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($issue, $maturity, $basis);
            if (!is_numeric($daysBetweenIssueAndMaturity)) {
                return $daysBetweenIssueAndMaturity;
            }
            $daysBetweenIssueAndMaturity *= $daysPerYear;
            $daysBetweenSettlementAndMaturity = PHPExcel_Calculation_DateTime::YEARFRAC($settlement, $maturity, $basis);
            if (!is_numeric($daysBetweenSettlementAndMaturity)) {
                return $daysBetweenSettlementAndMaturity;
            }
            $daysBetweenSettlementAndMaturity *= $daysPerYear;
            return (1 + $daysBetweenIssueAndMaturity / $daysPerYear * $rate - ($price / 100 + $daysBetweenIssueAndSettlement / $daysPerYear * $rate)) / ($price / 100 + $daysBetweenIssueAndSettlement / $daysPerYear * $rate) * ($daysPerYear / $daysBetweenSettlementAndMaturity);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
define('FINANCIAL_MAX_ITERATIONS', 128);
define('FINANCIAL_PRECISION', 1.0E-8);
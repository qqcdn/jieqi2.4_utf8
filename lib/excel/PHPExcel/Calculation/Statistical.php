<?php

class PHPExcel_Calculation_Statistical
{
    private static $_logBetaCache_p = 0;
    private static $_logBetaCache_q = 0;
    private static $_logBetaCache_result = 0;
    /**
     * logGamma function
     *
     * @version 1.1
     * @author Jaco van Kooten
     *
     * Original author was Jaco van Kooten. Ported to PHP by Paul Meagher.
     *
     * The natural logarithm of the gamma function. <br />
     * Based on public domain NETLIB (Fortran) code by W. J. Cody and L. Stoltz <br />
     * Applied Mathematics Division <br />
     * Argonne National Laboratory <br />
     * Argonne, IL 60439 <br />
     * <p>
     * References:
     * <ol>
     * <li>W. J. Cody and K. E. Hillstrom, 'Chebyshev Approximations for the Natural
     *	 Logarithm of the Gamma Function,' Math. Comp. 21, 1967, pp. 198-203.</li>
     * <li>K. E. Hillstrom, ANL/AMD Program ANLC366S, DGAMMA/DLGAMA, May, 1969.</li>
     * <li>Hart, Et. Al., Computer Approximations, Wiley and sons, New York, 1968.</li>
     * </ol>
     * </p>
     * <p>
     * From the original documentation:
     * </p>
     * <p>
     * This routine calculates the LOG(GAMMA) function for a positive real argument X.
     * Computation is based on an algorithm outlined in references 1 and 2.
     * The program uses rational functions that theoretically approximate LOG(GAMMA)
     * to at least 18 significant decimal digits. The approximation for X > 12 is from
     * reference 3, while approximations for X < 12.0 are similar to those in reference
     * 1, but are unpublished. The accuracy achieved depends on the arithmetic system,
     * the compiler, the intrinsic functions, and proper selection of the
     * machine-dependent constants.
     * </p>
     * <p>
     * Error returns: <br />
     * The program returns the value XINF for X .LE. 0.0 or when overflow would occur.
     * The computation is believed to be free of underflow and overflow.
     * </p>
     * @return MAX_VALUE for x < 0.0 or when overflow would occur, i.e. x > 2.55E305
     */
    private static $_logGammaCache_result = 0;
    private static $_logGammaCache_x = 0;
    private static function _checkTrendArrays(&$array1, &$array2)
    {
        if (!is_array($array1)) {
            $array1 = array($array1);
        }
        if (!is_array($array2)) {
            $array2 = array($array2);
        }
        $array1 = PHPExcel_Calculation_Functions::flattenArray($array1);
        $array2 = PHPExcel_Calculation_Functions::flattenArray($array2);
        foreach ($array1 as $key => $value) {
            if (is_bool($value) || is_string($value) || is_null($value)) {
                unset($array1[$key]);
                unset($array2[$key]);
            }
        }
        foreach ($array2 as $key => $value) {
            if (is_bool($value) || is_string($value) || is_null($value)) {
                unset($array1[$key]);
                unset($array2[$key]);
            }
        }
        $array1 = array_merge($array1);
        $array2 = array_merge($array2);
        return true;
    }
    private static function _beta($p, $q)
    {
        if ($p <= 0 || $q <= 0 || LOG_GAMMA_X_MAX_VALUE < $p + $q) {
            return 0;
        } else {
            return exp(self::_logBeta($p, $q));
        }
    }
    private static function _incompleteBeta($x, $p, $q)
    {
        if ($x <= 0) {
            return 0;
        } else {
            if (1 <= $x) {
                return 1;
            } else {
                if ($p <= 0 || $q <= 0 || LOG_GAMMA_X_MAX_VALUE < $p + $q) {
                    return 0;
                }
            }
        }
        $beta_gam = exp(-self::_logBeta($p, $q) + $p * log($x) + $q * log(1 - $x));
        if ($x < ($p + 1) / ($p + $q + 2)) {
            return $beta_gam * self::_betaFraction($x, $p, $q) / $p;
        } else {
            return 1 - $beta_gam * self::_betaFraction(1 - $x, $q, $p) / $q;
        }
    }
    private static function _logBeta($p, $q)
    {
        if ($p != self::$_logBetaCache_p || $q != self::$_logBetaCache_q) {
            self::$_logBetaCache_p = $p;
            self::$_logBetaCache_q = $q;
            if ($p <= 0 || $q <= 0 || LOG_GAMMA_X_MAX_VALUE < $p + $q) {
                self::$_logBetaCache_result = 0;
            } else {
                self::$_logBetaCache_result = self::_logGamma($p) + self::_logGamma($q) - self::_logGamma($p + $q);
            }
        }
        return self::$_logBetaCache_result;
    }
    private static function _betaFraction($x, $p, $q)
    {
        $c = 1;
        $sum_pq = $p + $q;
        $p_plus = $p + 1;
        $p_minus = $p - 1;
        $h = 1 - $sum_pq * $x / $p_plus;
        if (abs($h) < XMININ) {
            $h = XMININ;
        }
        $h = 1 / $h;
        $frac = $h;
        $m = 1;
        $delta = 0;
        while ($m <= MAX_ITERATIONS && PRECISION < abs($delta - 1)) {
            $m2 = 2 * $m;
            $d = $m * ($q - $m) * $x / (($p_minus + $m2) * ($p + $m2));
            $h = 1 + $d * $h;
            if (abs($h) < XMININ) {
                $h = XMININ;
            }
            $h = 1 / $h;
            $c = 1 + $d / $c;
            if (abs($c) < XMININ) {
                $c = XMININ;
            }
            $frac *= $h * $c;
            $d = -($p + $m) * ($sum_pq + $m) * $x / (($p + $m2) * ($p_plus + $m2));
            $h = 1 + $d * $h;
            if (abs($h) < XMININ) {
                $h = XMININ;
            }
            $h = 1 / $h;
            $c = 1 + $d / $c;
            if (abs($c) < XMININ) {
                $c = XMININ;
            }
            $delta = $h * $c;
            $frac *= $delta;
            ++$m;
        }
        return $frac;
    }
    private static function _logGamma($x)
    {
        static $lg_d1 = -0.57721566490153;
        static $lg_d2 = 0.42278433509847;
        static $lg_d4 = 1.7917594692281;
        static $lg_p1 = array(4.9452353592967, 201.81126208568, 2290.8383738313, 11319.672059034, 28557.246356716, 38484.962284438, 26377.487876242, 7225.8139797003);
        static $lg_p2 = array(4.9746078455689, 542.4138599891101, 15506.938649784, 184793.29044456, 1088204.7694688, 3338152.967987, 5106661.6789274, 3074109.0548505);
        static $lg_p4 = array(14745.021660599, 2426813.3694867, 121475557.40451, 2663432449.631, 29403789566.346, 170266573776.54, 492612579337.74, 560625185622.4);
        static $lg_q1 = array(67.48212550303801, 1113.3323938572, 7738.7570569354, 27639.870744033, 54993.102062262, 61611.22180066, 36351.275915019, 8785.536302431001);
        static $lg_q2 = array(183.03283993706, 7765.049321445, 133190.38279661, 1136705.821322, 5267964.1174379, 13467014.543111, 17827365.303533, 9533095.5918444);
        static $lg_q4 = array(2690.5301758709, 639388.56543001, 41355999.302414, 1120872109.6161, 14886137286.788, 101680358627.24, 341747634550.74, 446315818741.97);
        static $lg_c = array(-0.001910444077728, 0.0008417138778129501, -0.0005952379913043, 0.00079365079350035, -0.0027777777777777, 0.083333333333333, 0.0057083835261);
        static $lg_frtbig = 2.25E+76;
        static $pnt68 = 0.6796875;
        if ($x == self::$_logGammaCache_x) {
            return self::$_logGammaCache_result;
        }
        $y = $x;
        if (0 < $y && $y <= LOG_GAMMA_X_MAX_VALUE) {
            if ($y <= EPS) {
                $res = -log(y);
            } else {
                if ($y <= 1.5) {
                    if ($y < $pnt68) {
                        $corr = -log($y);
                        $xm1 = $y;
                    } else {
                        $corr = 0;
                        $xm1 = $y - 1;
                    }
                    if ($y <= 0.5 || $pnt68 <= $y) {
                        $xden = 1;
                        $xnum = 0;
                        for ($i = 0; $i < 8; ++$i) {
                            $xnum = $xnum * $xm1 + $lg_p1[$i];
                            $xden = $xden * $xm1 + $lg_q1[$i];
                        }
                        $res = $corr + $xm1 * ($lg_d1 + $xm1 * ($xnum / $xden));
                    } else {
                        $xm2 = $y - 1;
                        $xden = 1;
                        $xnum = 0;
                        for ($i = 0; $i < 8; ++$i) {
                            $xnum = $xnum * $xm2 + $lg_p2[$i];
                            $xden = $xden * $xm2 + $lg_q2[$i];
                        }
                        $res = $corr + $xm2 * ($lg_d2 + $xm2 * ($xnum / $xden));
                    }
                } else {
                    if ($y <= 4) {
                        $xm2 = $y - 2;
                        $xden = 1;
                        $xnum = 0;
                        for ($i = 0; $i < 8; ++$i) {
                            $xnum = $xnum * $xm2 + $lg_p2[$i];
                            $xden = $xden * $xm2 + $lg_q2[$i];
                        }
                        $res = $xm2 * ($lg_d2 + $xm2 * ($xnum / $xden));
                    } else {
                        if ($y <= 12) {
                            $xm4 = $y - 4;
                            $xden = -1;
                            $xnum = 0;
                            for ($i = 0; $i < 8; ++$i) {
                                $xnum = $xnum * $xm4 + $lg_p4[$i];
                                $xden = $xden * $xm4 + $lg_q4[$i];
                            }
                            $res = $lg_d4 + $xm4 * ($xnum / $xden);
                        } else {
                            $res = 0;
                            if ($y <= $lg_frtbig) {
                                $res = $lg_c[6];
                                $ysq = $y * $y;
                                for ($i = 0; $i < 6; ++$i) {
                                    $res = $res / $ysq + $lg_c[$i];
                                }
                            }
                            $res /= $y;
                            $corr = log($y);
                            $res = $res + log(SQRT2PI) - 0.5 * $corr;
                            $res += $y * ($corr - 1);
                        }
                    }
                }
            }
        } else {
            $res = MAX_VALUE;
        }
        self::$_logGammaCache_x = $x;
        self::$_logGammaCache_result = $res;
        return $res;
    }
    private static function _incompleteGamma($a, $x)
    {
        static $max = 32;
        $summer = 0;
        for ($n = 0; $n <= $max; ++$n) {
            $divisor = $a;
            for ($i = 1; $i <= $n; ++$i) {
                $divisor *= $a + $i;
            }
            $summer += pow($x, $n) / $divisor;
        }
        return pow($x, $a) * exp(-$x) * $summer;
    }
    private static function _gamma($data)
    {
        if ($data == 0) {
            return 0;
        }
        static $p0 = 1.00000000019;
        static $p = array(1 => 76.180091729471, 2 => -86.505320329417, 3 => 24.014098240831, 4 => -1.2317395724502, 5 => 0.0012086509738662, 6 => -5.395239384953E-6);
        $y = $x = $data;
        $tmp = $x + 5.5;
        $tmp -= ($x + 0.5) * log($tmp);
        $summer = $p0;
        for ($j = 1; $j <= 6; ++$j) {
            $summer += $p[$j] / ++$y;
        }
        return exp(-$tmp + log(SQRT2PI * $summer / $x));
    }
    private static function _inverse_ncdf($p)
    {
        static $a = array(1 => -39.696830286654, 2 => 220.94609842452, 3 => -275.92851044697, 4 => 138.35775186727, 5 => -30.664798066147, 6 => 2.5066282774592);
        static $b = array(1 => -54.476098798224, 2 => 161.58583685804, 3 => -155.69897985989, 4 => 66.80131188772, 5 => -13.280681552886);
        static $c = array(1 => -0.0077848940024303, 2 => -0.32239645804114, 3 => -2.4007582771618, 4 => -2.5497325393437, 5 => 4.374664141465, 6 => 2.9381639826988);
        static $d = array(1 => 0.0077846957090415, 2 => 0.32246712907004, 3 => 2.445134137143, 4 => 3.7544086619074);
        $p_low = 0.02425;
        $p_high = 1 - $p_low;
        if (0 < $p && $p < $p_low) {
            $q = sqrt(-2 * log($p));
            return ((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) / (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
        } else {
            if ($p_low <= $p && $p <= $p_high) {
                $q = $p - 0.5;
                $r = $q * $q;
                return ((((($a[1] * $r + $a[2]) * $r + $a[3]) * $r + $a[4]) * $r + $a[5]) * $r + $a[6]) * $q / ((((($b[1] * $r + $b[2]) * $r + $b[3]) * $r + $b[4]) * $r + $b[5]) * $r + 1);
            } else {
                if ($p_high < $p && $p < 1) {
                    $q = sqrt(-2 * log(1 - $p));
                    return -((((($c[1] * $q + $c[2]) * $q + $c[3]) * $q + $c[4]) * $q + $c[5]) * $q + $c[6]) / (((($d[1] * $q + $d[2]) * $q + $d[3]) * $q + $d[4]) * $q + 1);
                }
            }
        }
        return PHPExcel_Calculation_Functions::NULL();
    }
    private static function _inverse_ncdf2($prob)
    {
        $a1 = 2.50662823884;
        $a2 = -18.61500062529;
        $a3 = 41.39119773534;
        $a4 = -25.44106049637;
        $b1 = -8.4735109309;
        $b2 = 23.08336743743;
        $b3 = -21.06224101826;
        $b4 = 3.13082909833;
        $c1 = 0.33747548227261;
        $c2 = 0.97616901909172;
        $c3 = 0.16079797149182;
        $c4 = 0.027643881033386;
        $c5 = 0.0038405729373609;
        $c6 = 0.0003951896511919;
        $c7 = 3.21767881768E-5;
        $c8 = 2.888167364E-7;
        $c9 = 3.960315187E-7;
        $y = $prob - 0.5;
        if (abs($y) < 0.42) {
            $z = $y * $y;
            $z = $y * ((($a4 * $z + $a3) * $z + $a2) * $z + $a1) / (((($b4 * $z + $b3) * $z + $b2) * $z + $b1) * $z + 1);
        } else {
            if (0 < $y) {
                $z = log(-log(1 - $prob));
            } else {
                $z = log(-log($prob));
            }
            $z = $c1 + $z * ($c2 + $z * ($c3 + $z * ($c4 + $z * ($c5 + $z * ($c6 + $z * ($c7 + $z * ($c8 + $z * $c9)))))));
            if ($y < 0) {
                $z = -$z;
            }
        }
        return $z;
    }
    private static function _inverse_ncdf3($p)
    {
        $split1 = 0.425;
        $split2 = 5;
        $const1 = 0.180625;
        $const2 = 1.6;
        $a0 = 3.3871328727964;
        $a1 = 133.14166789178;
        $a2 = 1971.5909503066;
        $a3 = 13731.693765509;
        $a4 = 45921.95393155;
        $a5 = 67265.770927009;
        $a6 = 33430.575583588;
        $a7 = 2509.0809287301;
        $b1 = 42.313330701601;
        $b2 = 687.18700749206;
        $b3 = 5394.1960214248;
        $b4 = 21213.794301587;
        $b5 = 39307.895800093;
        $b6 = 28729.085735722;
        $b7 = 5226.4952788529;
        $c0 = 1.4234371107497;
        $c1 = 4.6303378461565;
        $c2 = 5.7694972214607;
        $c3 = 3.6478483247632;
        $c4 = 1.2704582524524;
        $c5 = 0.24178072517745;
        $c6 = 0.022723844989269;
        $c7 = 0.00077454501427834;
        $d1 = 2.0531916266378;
        $d2 = 1.6763848301838;
        $d3 = 0.6897673349851;
        $d4 = 0.14810397642748;
        $d5 = 0.015198666563616;
        $d6 = 0.00054759380849953;
        $d7 = 1.0507500716444E-9;
        $e0 = 6.6579046435011;
        $e1 = 5.4637849111641;
        $e2 = 1.7848265399173;
        $e3 = 0.2965605718285;
        $e4 = 0.026532189526576;
        $e5 = 0.0012426609473881;
        $e6 = 2.7115555687435E-5;
        $e7 = 2.0103343992923E-7;
        $f1 = 0.59983220655589;
        $f2 = 0.13692988092274;
        $f3 = 0.014875361290851;
        $f4 = 0.00078686913114561;
        $f5 = 1.8463183175101E-5;
        $f6 = 1.4215117583164E-7;
        $f7 = 2.0442631033899E-15;
        $q = $p - 0.5;
        if (abs($q) <= split1) {
            $R = $const1 - $q * $q;
            $z = $q * ((((((($a7 * $R + $a6) * $R + $a5) * $R + $a4) * $R + $a3) * $R + $a2) * $R + $a1) * $R + $a0) / ((((((($b7 * $R + $b6) * $R + $b5) * $R + $b4) * $R + $b3) * $R + $b2) * $R + $b1) * $R + 1);
        } else {
            if ($q < 0) {
                $R = $p;
            } else {
                $R = 1 - $p;
            }
            $R = pow(-log($R), 2);
            if ($R <= $split2) {
                $R = $R - $const2;
                $z = ((((((($c7 * $R + $c6) * $R + $c5) * $R + $c4) * $R + $c3) * $R + $c2) * $R + $c1) * $R + $c0) / ((((((($d7 * $R + $d6) * $R + $d5) * $R + $d4) * $R + $d3) * $R + $d2) * $R + $d1) * $R + 1);
            } else {
                $R = $R - $split2;
                $z = ((((((($e7 * $R + $e6) * $R + $e5) * $R + $e4) * $R + $e3) * $R + $e2) * $R + $e1) * $R + $e0) / ((((((($f7 * $R + $f6) * $R + $f5) * $R + $f4) * $R + $f3) * $R + $f2) * $R + $f1) * $R + 1);
            }
            if ($q < 0) {
                $z = -$z;
            }
        }
        return $z;
    }
    public static function AVEDEV()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $returnValue = NULL;
        $aMean = self::AVERAGE($aArgs);
        if ($aMean != PHPExcel_Calculation_Functions::DIV0()) {
            $aCount = 0;
            foreach ($aArgs as $k => $arg) {
                if (is_bool($arg) && (!PHPExcel_Calculation_Functions::isCellValue($k) || PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE)) {
                    $arg = (int) $arg;
                }
                if (is_numeric($arg) && !is_string($arg)) {
                    if (is_null($returnValue)) {
                        $returnValue = abs($arg - $aMean);
                    } else {
                        $returnValue += abs($arg - $aMean);
                    }
                    ++$aCount;
                }
            }
            if ($aCount == 0) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
            return $returnValue / $aCount;
        }
        return PHPExcel_Calculation_Functions::NaN();
    }
    public static function AVERAGE()
    {
        $returnValue = $aCount = 0;
        foreach (PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args()) as $k => $arg) {
            if (is_bool($arg) && (!PHPExcel_Calculation_Functions::isCellValue($k) || PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE)) {
                $arg = (int) $arg;
            }
            if (is_numeric($arg) && !is_string($arg)) {
                if (is_null($returnValue)) {
                    $returnValue = $arg;
                } else {
                    $returnValue += $arg;
                }
                ++$aCount;
            }
        }
        if (0 < $aCount) {
            return $returnValue / $aCount;
        } else {
            return PHPExcel_Calculation_Functions::DIV0();
        }
    }
    public static function AVERAGEA()
    {
        $returnValue = NULL;
        $aCount = 0;
        foreach (PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args()) as $k => $arg) {
            if (is_bool($arg) && !PHPExcel_Calculation_Functions::isMatrixValue($k)) {
            } else {
                if (is_numeric($arg) || is_bool($arg) || is_string($arg) && $arg != '') {
                    if (is_bool($arg)) {
                        $arg = (int) $arg;
                    } else {
                        if (is_string($arg)) {
                            $arg = 0;
                        }
                    }
                    if (is_null($returnValue)) {
                        $returnValue = $arg;
                    } else {
                        $returnValue += $arg;
                    }
                    ++$aCount;
                }
            }
        }
        if (0 < $aCount) {
            return $returnValue / $aCount;
        } else {
            return PHPExcel_Calculation_Functions::DIV0();
        }
    }
    public static function AVERAGEIF($aArgs, $condition, $averageArgs = array())
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray($aArgs);
        $averageArgs = PHPExcel_Calculation_Functions::flattenArray($averageArgs);
        if (empty($averageArgs)) {
            $averageArgs = $aArgs;
        }
        $condition = PHPExcel_Calculation_Functions::_ifCondition($condition);
        $aCount = 0;
        foreach ($aArgs as $key => $arg) {
            if (!is_numeric($arg)) {
                $arg = PHPExcel_Calculation::_wrapResult(strtoupper($arg));
            }
            $testCondition = '=' . $arg . $condition;
            if (PHPExcel_Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                if (is_null($returnValue) || $returnValue < $arg) {
                    $returnValue += $arg;
                    ++$aCount;
                }
            }
        }
        if (0 < $aCount) {
            return $returnValue / $aCount;
        } else {
            return PHPExcel_Calculation_Functions::DIV0();
        }
    }
    public static function BETADIST($value, $alpha, $beta, $rMin = 0, $rMax = 1)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $alpha = PHPExcel_Calculation_Functions::flattenSingleValue($alpha);
        $beta = PHPExcel_Calculation_Functions::flattenSingleValue($beta);
        $rMin = PHPExcel_Calculation_Functions::flattenSingleValue($rMin);
        $rMax = PHPExcel_Calculation_Functions::flattenSingleValue($rMax);
        if (is_numeric($value) && is_numeric($alpha) && is_numeric($beta) && is_numeric($rMin) && is_numeric($rMax)) {
            if ($value < $rMin || $rMax < $value || $alpha <= 0 || $beta <= 0 || $rMin == $rMax) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($rMax < $rMin) {
                $tmp = $rMin;
                $rMin = $rMax;
                $rMax = $tmp;
            }
            $value -= $rMin;
            $value /= $rMax - $rMin;
            return self::_incompleteBeta($value, $alpha, $beta);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function BETAINV($probability, $alpha, $beta, $rMin = 0, $rMax = 1)
    {
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        $alpha = PHPExcel_Calculation_Functions::flattenSingleValue($alpha);
        $beta = PHPExcel_Calculation_Functions::flattenSingleValue($beta);
        $rMin = PHPExcel_Calculation_Functions::flattenSingleValue($rMin);
        $rMax = PHPExcel_Calculation_Functions::flattenSingleValue($rMax);
        if (is_numeric($probability) && is_numeric($alpha) && is_numeric($beta) && is_numeric($rMin) && is_numeric($rMax)) {
            if ($alpha <= 0 || $beta <= 0 || $rMin == $rMax || $probability <= 0 || 1 < $probability) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($rMax < $rMin) {
                $tmp = $rMin;
                $rMin = $rMax;
                $rMax = $tmp;
            }
            $a = 0;
            $b = 2;
            $i = 0;
            while (PRECISION < $b - $a && $i++ < MAX_ITERATIONS) {
                $guess = ($a + $b) / 2;
                $result = self::BETADIST($guess, $alpha, $beta);
                if ($result == $probability || $result == 0) {
                    $b = $a;
                } else {
                    if ($probability < $result) {
                        $b = $guess;
                    } else {
                        $a = $guess;
                    }
                }
            }
            if ($i == MAX_ITERATIONS) {
                return PHPExcel_Calculation_Functions::NA();
            }
            return round($rMin + $guess * ($rMax - $rMin), 12);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function BINOMDIST($value, $trials, $probability, $cumulative)
    {
        $value = floor(PHPExcel_Calculation_Functions::flattenSingleValue($value));
        $trials = floor(PHPExcel_Calculation_Functions::flattenSingleValue($trials));
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        if (is_numeric($value) && is_numeric($trials) && is_numeric($probability)) {
            if ($value < 0 || $trials < $value) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($probability < 0 || 1 < $probability) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (is_numeric($cumulative) || is_bool($cumulative)) {
                if ($cumulative) {
                    $summer = 0;
                    for ($i = 0; $i <= $value; ++$i) {
                        $summer += PHPExcel_Calculation_MathTrig::COMBIN($trials, $i) * pow($probability, $i) * pow(1 - $probability, $trials - $i);
                    }
                    return $summer;
                } else {
                    return PHPExcel_Calculation_MathTrig::COMBIN($trials, $value) * pow($probability, $value) * pow(1 - $probability, $trials - $value);
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function CHIDIST($value, $degrees)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $degrees = floor(PHPExcel_Calculation_Functions::flattenSingleValue($degrees));
        if (is_numeric($value) && is_numeric($degrees)) {
            if ($degrees < 1) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($value < 0) {
                if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                    return 1;
                }
                return PHPExcel_Calculation_Functions::NaN();
            }
            return 1 - self::_incompleteGamma($degrees / 2, $value / 2) / self::_gamma($degrees / 2);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function CHIINV($probability, $degrees)
    {
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        $degrees = floor(PHPExcel_Calculation_Functions::flattenSingleValue($degrees));
        if (is_numeric($probability) && is_numeric($degrees)) {
            $xLo = 100;
            $xHi = 0;
            $x = $xNew = 1;
            $dx = 1;
            $i = 0;
            while (PRECISION < abs($dx) && $i++ < MAX_ITERATIONS) {
                $result = self::CHIDIST($x, $degrees);
                $error = $result - $probability;
                if ($error == 0) {
                    $dx = 0;
                } else {
                    if ($error < 0) {
                        $xLo = $x;
                    } else {
                        $xHi = $x;
                    }
                }
                if ($result != 0) {
                    $dx = $error / $result;
                    $xNew = $x - $dx;
                }
                if ($xNew < $xLo || $xHi < $xNew || $result == 0) {
                    $xNew = ($xLo + $xHi) / 2;
                    $dx = $xNew - $x;
                }
                $x = $xNew;
            }
            if ($i == MAX_ITERATIONS) {
                return PHPExcel_Calculation_Functions::NA();
            }
            return round($x, 12);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function CONFIDENCE($alpha, $stdDev, $size)
    {
        $alpha = PHPExcel_Calculation_Functions::flattenSingleValue($alpha);
        $stdDev = PHPExcel_Calculation_Functions::flattenSingleValue($stdDev);
        $size = floor(PHPExcel_Calculation_Functions::flattenSingleValue($size));
        if (is_numeric($alpha) && is_numeric($stdDev) && is_numeric($size)) {
            if ($alpha <= 0 || 1 <= $alpha) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($stdDev <= 0 || $size < 1) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return self::NORMSINV(1 - $alpha / 2) * $stdDev / sqrt($size);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function CORREL($yValues, $xValues = NULL)
    {
        if (is_null($xValues) || !is_array($yValues) || !is_array($xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
        }
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues);
        return $bestFitLinear->getCorrelation();
    }
    public static function COUNT()
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        foreach ($aArgs as $k => $arg) {
            if (is_bool($arg) && (!PHPExcel_Calculation_Functions::isCellValue($k) || PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE)) {
                $arg = (int) $arg;
            }
            if (is_numeric($arg) && !is_string($arg)) {
                ++$returnValue;
            }
        }
        return $returnValue;
    }
    public static function COUNTA()
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            if (is_numeric($arg) || is_bool($arg) || is_string($arg) && $arg != '') {
                ++$returnValue;
            }
        }
        return $returnValue;
    }
    public static function COUNTBLANK()
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            if (is_null($arg) || is_string($arg) && $arg == '') {
                ++$returnValue;
            }
        }
        return $returnValue;
    }
    public static function COUNTIF($aArgs, $condition)
    {
        $returnValue = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray($aArgs);
        $condition = PHPExcel_Calculation_Functions::_ifCondition($condition);
        foreach ($aArgs as $arg) {
            if (!is_numeric($arg)) {
                $arg = PHPExcel_Calculation::_wrapResult(strtoupper($arg));
            }
            $testCondition = '=' . $arg . $condition;
            if (PHPExcel_Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                ++$returnValue;
            }
        }
        return $returnValue;
    }
    public static function COVAR($yValues, $xValues)
    {
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
        }
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues);
        return $bestFitLinear->getCovariance();
    }
    public static function CRITBINOM($trials, $probability, $alpha)
    {
        $trials = floor(PHPExcel_Calculation_Functions::flattenSingleValue($trials));
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        $alpha = PHPExcel_Calculation_Functions::flattenSingleValue($alpha);
        if (is_numeric($trials) && is_numeric($probability) && is_numeric($alpha)) {
            if ($trials < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($probability < 0 || 1 < $probability) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($alpha < 0 || 1 < $alpha) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($alpha <= 0.5) {
                $t = sqrt(log(1 / ($alpha * $alpha)));
                $trialsApprox = -($t + (2.515517 + 0.802853 * $t + 0.010328 * $t * $t) / (1 + 1.432788 * $t + 0.189269 * $t * $t + 0.001308 * $t * $t * $t));
            } else {
                $t = sqrt(log(1 / pow(1 - $alpha, 2)));
                $trialsApprox = $t - (2.515517 + 0.802853 * $t + 0.010328 * $t * $t) / (1 + 1.432788 * $t + 0.189269 * $t * $t + 0.001308 * $t * $t * $t);
            }
            $Guess = floor($trials * $probability + $trialsApprox * sqrt($trials * $probability * (1 - $probability)));
            if ($Guess < 0) {
                $Guess = 0;
            } else {
                if ($trials < $Guess) {
                    $Guess = $trials;
                }
            }
            $TotalUnscaledProbability = $UnscaledPGuess = $UnscaledCumPGuess = 0;
            $EssentiallyZero = 9.999999999999999E-12;
            $m = floor($trials * $probability);
            ++$TotalUnscaledProbability;
            if ($m == $Guess) {
                ++$UnscaledPGuess;
            }
            if ($m <= $Guess) {
                ++$UnscaledCumPGuess;
            }
            $PreviousValue = 1;
            $Done = false;
            $k = $m + 1;
            while (!$Done && $k <= $trials) {
                $CurrentValue = $PreviousValue * ($trials - $k + 1) * $probability / ($k * (1 - $probability));
                $TotalUnscaledProbability += $CurrentValue;
                if ($k == $Guess) {
                    $UnscaledPGuess += $CurrentValue;
                }
                if ($k <= $Guess) {
                    $UnscaledCumPGuess += $CurrentValue;
                }
                if ($CurrentValue <= $EssentiallyZero) {
                    $Done = true;
                }
                $PreviousValue = $CurrentValue;
                ++$k;
            }
            $PreviousValue = 1;
            $Done = false;
            $k = $m - 1;
            while (!$Done && 0 <= $k) {
                $CurrentValue = $PreviousValue * $k + 1 * (1 - $probability) / (($trials - $k) * $probability);
                $TotalUnscaledProbability += $CurrentValue;
                if ($k == $Guess) {
                    $UnscaledPGuess += $CurrentValue;
                }
                if ($k <= $Guess) {
                    $UnscaledCumPGuess += $CurrentValue;
                }
                if ($CurrentValue <= $EssentiallyZero) {
                    $Done = true;
                }
                $PreviousValue = $CurrentValue;
                --$k;
            }
            $PGuess = $UnscaledPGuess / $TotalUnscaledProbability;
            $CumPGuess = $UnscaledCumPGuess / $TotalUnscaledProbability;
            $CumPGuessMinus1 = $CumPGuess - 1;
            while (true) {
                if ($CumPGuessMinus1 < $alpha && $alpha <= $CumPGuess) {
                    return $Guess;
                } else {
                    if ($CumPGuessMinus1 < $alpha && $CumPGuess < $alpha) {
                        $PGuessPlus1 = $PGuess * ($trials - $Guess) * $probability / $Guess / (1 - $probability);
                        $CumPGuessMinus1 = $CumPGuess;
                        $CumPGuess = $CumPGuess + $PGuessPlus1;
                        $PGuess = $PGuessPlus1;
                        ++$Guess;
                    } else {
                        if ($alpha <= $CumPGuessMinus1 && $alpha <= $CumPGuess) {
                            $PGuessMinus1 = $PGuess * $Guess * (1 - $probability) / ($trials - $Guess + 1) / $probability;
                            $CumPGuess = $CumPGuessMinus1;
                            $CumPGuessMinus1 = $CumPGuessMinus1 - $PGuess;
                            $PGuess = $PGuessMinus1;
                            --$Guess;
                        }
                    }
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function DEVSQ()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $returnValue = NULL;
        $aMean = self::AVERAGE($aArgs);
        if ($aMean != PHPExcel_Calculation_Functions::DIV0()) {
            $aCount = -1;
            foreach ($aArgs as $k => $arg) {
                if (is_bool($arg) && (!PHPExcel_Calculation_Functions::isCellValue($k) || PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE)) {
                    $arg = (int) $arg;
                }
                if (is_numeric($arg) && !is_string($arg)) {
                    if (is_null($returnValue)) {
                        $returnValue = pow($arg - $aMean, 2);
                    } else {
                        $returnValue += pow($arg - $aMean, 2);
                    }
                    ++$aCount;
                }
            }
            if (is_null($returnValue)) {
                return PHPExcel_Calculation_Functions::NaN();
            } else {
                return $returnValue;
            }
        }
        return self::NA();
    }
    public static function EXPONDIST($value, $lambda, $cumulative)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $lambda = PHPExcel_Calculation_Functions::flattenSingleValue($lambda);
        $cumulative = PHPExcel_Calculation_Functions::flattenSingleValue($cumulative);
        if (is_numeric($value) && is_numeric($lambda)) {
            if ($value < 0 || $lambda < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (is_numeric($cumulative) || is_bool($cumulative)) {
                if ($cumulative) {
                    return 1 - exp(-($value * $lambda));
                } else {
                    return $lambda * exp(-($value * $lambda));
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function FISHER($value)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        if (is_numeric($value)) {
            if ($value <= -1 || 1 <= $value) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return 0.5 * log((1 + $value) / (1 - $value));
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function FISHERINV($value)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        if (is_numeric($value)) {
            return (exp(2 * $value) - 1) / (exp(2 * $value) + 1);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function FORECAST($xValue, $yValues, $xValues)
    {
        $xValue = PHPExcel_Calculation_Functions::flattenSingleValue($xValue);
        if (!is_numeric($xValue)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
        }
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues);
        return $bestFitLinear->getValueOfYForX($xValue);
    }
    public static function GAMMADIST($value, $a, $b, $cumulative)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $a = PHPExcel_Calculation_Functions::flattenSingleValue($a);
        $b = PHPExcel_Calculation_Functions::flattenSingleValue($b);
        if (is_numeric($value) && is_numeric($a) && is_numeric($b)) {
            if ($value < 0 || $a <= 0 || $b <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (is_numeric($cumulative) || is_bool($cumulative)) {
                if ($cumulative) {
                    return self::_incompleteGamma($a, $value / $b) / self::_gamma($a);
                } else {
                    return 1 / (pow($b, $a) * self::_gamma($a)) * pow($value, $a - 1) * exp(-($value / $b));
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function GAMMAINV($probability, $alpha, $beta)
    {
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        $alpha = PHPExcel_Calculation_Functions::flattenSingleValue($alpha);
        $beta = PHPExcel_Calculation_Functions::flattenSingleValue($beta);
        if (is_numeric($probability) && is_numeric($alpha) && is_numeric($beta)) {
            if ($alpha <= 0 || $beta <= 0 || $probability < 0 || 1 < $probability) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $xLo = 0;
            $xHi = $alpha * $beta * 5;
            $x = $xNew = 1;
            $error = $pdf = 0;
            $dx = 1024;
            $i = 0;
            while (PRECISION < abs($dx) && $i++ < MAX_ITERATIONS) {
                $error = self::GAMMADIST($x, $alpha, $beta, true) - $probability;
                if ($error < 0) {
                    $xLo = $x;
                } else {
                    $xHi = $x;
                }
                $pdf = self::GAMMADIST($x, $alpha, $beta, false);
                if ($pdf != 0) {
                    $dx = $error / $pdf;
                    $xNew = $x - $dx;
                }
                if ($xNew < $xLo || $xHi < $xNew || $pdf == 0) {
                    $xNew = ($xLo + $xHi) / 2;
                    $dx = $xNew - $x;
                }
                $x = $xNew;
            }
            if ($i == MAX_ITERATIONS) {
                return PHPExcel_Calculation_Functions::NA();
            }
            return $x;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function GAMMALN($value)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        if (is_numeric($value)) {
            if ($value <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return log(self::_gamma($value));
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function GEOMEAN()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $aMean = PHPExcel_Calculation_MathTrig::PRODUCT($aArgs);
        if (is_numeric($aMean) && 0 < $aMean) {
            $aCount = self::COUNT($aArgs);
            if (0 < self::MIN($aArgs)) {
                return pow($aMean, 1 / $aCount);
            }
        }
        return PHPExcel_Calculation_Functions::NaN();
    }
    public static function GROWTH($yValues, $xValues = array(), $newValues = array(), $const = true)
    {
        $yValues = PHPExcel_Calculation_Functions::flattenArray($yValues);
        $xValues = PHPExcel_Calculation_Functions::flattenArray($xValues);
        $newValues = PHPExcel_Calculation_Functions::flattenArray($newValues);
        $const = is_null($const) ? true : (bool) PHPExcel_Calculation_Functions::flattenSingleValue($const);
        $bestFitExponential = trendClass::calculate(trendClass::TREND_EXPONENTIAL, $yValues, $xValues, $const);
        if (empty($newValues)) {
            $newValues = $bestFitExponential->getXValues();
        }
        $returnArray = array();
        foreach ($newValues as $xValue) {
            $returnArray[0][] = $bestFitExponential->getValueOfYForX($xValue);
        }
        return $returnArray;
    }
    public static function HARMEAN()
    {
        $returnValue = PHPExcel_Calculation_Functions::NA();
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        if (self::MIN($aArgs) < 0) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $aCount = 0;
        foreach ($aArgs as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                if ($arg <= 0) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
                if (is_null($returnValue)) {
                    $returnValue = 1 / $arg;
                } else {
                    $returnValue += 1 / $arg;
                }
                ++$aCount;
            }
        }
        if (0 < $aCount) {
            return 1 / $returnValue / $aCount;
        } else {
            return $returnValue;
        }
    }
    public static function HYPGEOMDIST($sampleSuccesses, $sampleNumber, $populationSuccesses, $populationNumber)
    {
        $sampleSuccesses = floor(PHPExcel_Calculation_Functions::flattenSingleValue($sampleSuccesses));
        $sampleNumber = floor(PHPExcel_Calculation_Functions::flattenSingleValue($sampleNumber));
        $populationSuccesses = floor(PHPExcel_Calculation_Functions::flattenSingleValue($populationSuccesses));
        $populationNumber = floor(PHPExcel_Calculation_Functions::flattenSingleValue($populationNumber));
        if (is_numeric($sampleSuccesses) && is_numeric($sampleNumber) && is_numeric($populationSuccesses) && is_numeric($populationNumber)) {
            if ($sampleSuccesses < 0 || $sampleNumber < $sampleSuccesses || $populationSuccesses < $sampleSuccesses) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($sampleNumber <= 0 || $populationNumber < $sampleNumber) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($populationSuccesses <= 0 || $populationNumber < $populationSuccesses) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return PHPExcel_Calculation_MathTrig::COMBIN($populationSuccesses, $sampleSuccesses) * PHPExcel_Calculation_MathTrig::COMBIN($populationNumber - $populationSuccesses, $sampleNumber - $sampleSuccesses) / PHPExcel_Calculation_MathTrig::COMBIN($populationNumber, $sampleNumber);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function INTERCEPT($yValues, $xValues)
    {
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
        }
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues);
        return $bestFitLinear->getIntersect();
    }
    public static function KURT()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $mean = self::AVERAGE($aArgs);
        $stdDev = self::STDEV($aArgs);
        if (0 < $stdDev) {
            $count = $summer = 0;
            foreach ($aArgs as $k => $arg) {
                if (is_bool($arg) && !PHPExcel_Calculation_Functions::isMatrixValue($k)) {
                } else {
                    if (is_numeric($arg) && !is_string($arg)) {
                        $summer += pow(($arg - $mean) / $stdDev, 4);
                        ++$count;
                    }
                }
            }
            if (3 < $count) {
                return $summer * ($count * ($count + 1) / (($count - 1) * ($count - 2) * ($count - 3))) - 3 * pow($count - 1, 2) / (($count - 2) * ($count - 3));
            }
        }
        return PHPExcel_Calculation_Functions::DIV0();
    }
    public static function LARGE()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $entry = floor(array_pop($aArgs));
        if (is_numeric($entry) && !is_string($entry)) {
            $mArgs = array();
            foreach ($aArgs as $arg) {
                if (is_numeric($arg) && !is_string($arg)) {
                    $mArgs[] = $arg;
                }
            }
            $count = self::COUNT($mArgs);
            $entry = floor(--$entry);
            if ($entry < 0 || $count <= $entry || $count == 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            rsort($mArgs);
            return $mArgs[$entry];
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function LINEST($yValues, $xValues = NULL, $const = true, $stats = false)
    {
        $const = is_null($const) ? true : (bool) PHPExcel_Calculation_Functions::flattenSingleValue($const);
        $stats = is_null($stats) ? false : (bool) PHPExcel_Calculation_Functions::flattenSingleValue($stats);
        if (is_null($xValues)) {
            $xValues = range(1, count(PHPExcel_Calculation_Functions::flattenArray($yValues)));
        }
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return 0;
            }
        }
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues, $const);
        if ($stats) {
            return array(array($bestFitLinear->getSlope(), $bestFitLinear->getSlopeSE(), $bestFitLinear->getGoodnessOfFit(), $bestFitLinear->getF(), $bestFitLinear->getSSRegression()), array($bestFitLinear->getIntersect(), $bestFitLinear->getIntersectSE(), $bestFitLinear->getStdevOfResiduals(), $bestFitLinear->getDFResiduals(), $bestFitLinear->getSSResiduals()));
        } else {
            return array($bestFitLinear->getSlope(), $bestFitLinear->getIntersect());
        }
    }
    public static function LOGEST($yValues, $xValues = NULL, $const = true, $stats = false)
    {
        $const = is_null($const) ? true : (bool) PHPExcel_Calculation_Functions::flattenSingleValue($const);
        $stats = is_null($stats) ? false : (bool) PHPExcel_Calculation_Functions::flattenSingleValue($stats);
        if (is_null($xValues)) {
            $xValues = range(1, count(PHPExcel_Calculation_Functions::flattenArray($yValues)));
        }
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        foreach ($yValues as $value) {
            if ($value <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return 1;
            }
        }
        $bestFitExponential = trendClass::calculate(trendClass::TREND_EXPONENTIAL, $yValues, $xValues, $const);
        if ($stats) {
            return array(array($bestFitExponential->getSlope(), $bestFitExponential->getSlopeSE(), $bestFitExponential->getGoodnessOfFit(), $bestFitExponential->getF(), $bestFitExponential->getSSRegression()), array($bestFitExponential->getIntersect(), $bestFitExponential->getIntersectSE(), $bestFitExponential->getStdevOfResiduals(), $bestFitExponential->getDFResiduals(), $bestFitExponential->getSSResiduals()));
        } else {
            return array($bestFitExponential->getSlope(), $bestFitExponential->getIntersect());
        }
    }
    public static function LOGINV($probability, $mean, $stdDev)
    {
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        $mean = PHPExcel_Calculation_Functions::flattenSingleValue($mean);
        $stdDev = PHPExcel_Calculation_Functions::flattenSingleValue($stdDev);
        if (is_numeric($probability) && is_numeric($mean) && is_numeric($stdDev)) {
            if ($probability < 0 || 1 < $probability || $stdDev <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return exp($mean + $stdDev * self::NORMSINV($probability));
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function LOGNORMDIST($value, $mean, $stdDev)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $mean = PHPExcel_Calculation_Functions::flattenSingleValue($mean);
        $stdDev = PHPExcel_Calculation_Functions::flattenSingleValue($stdDev);
        if (is_numeric($value) && is_numeric($mean) && is_numeric($stdDev)) {
            if ($value <= 0 || $stdDev <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return self::NORMSDIST((log($value) - $mean) / $stdDev);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function MAX()
    {
        $returnValue = NULL;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                if (is_null($returnValue) || $returnValue < $arg) {
                    $returnValue = $arg;
                }
            }
        }
        if (is_null($returnValue)) {
            return 0;
        }
        return $returnValue;
    }
    public static function MAXA()
    {
        $returnValue = NULL;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            if (is_numeric($arg) || is_bool($arg) || is_string($arg) && $arg != '') {
                if (is_bool($arg)) {
                    $arg = (int) $arg;
                } else {
                    if (is_string($arg)) {
                        $arg = 0;
                    }
                }
                if (is_null($returnValue) || $returnValue < $arg) {
                    $returnValue = $arg;
                }
            }
        }
        if (is_null($returnValue)) {
            return 0;
        }
        return $returnValue;
    }
    public static function MAXIF($aArgs, $condition, $sumArgs = array())
    {
        $returnValue = NULL;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray($aArgs);
        $sumArgs = PHPExcel_Calculation_Functions::flattenArray($sumArgs);
        if (empty($sumArgs)) {
            $sumArgs = $aArgs;
        }
        $condition = PHPExcel_Calculation_Functions::_ifCondition($condition);
        foreach ($aArgs as $key => $arg) {
            if (!is_numeric($arg)) {
                $arg = PHPExcel_Calculation::_wrapResult(strtoupper($arg));
            }
            $testCondition = '=' . $arg . $condition;
            if (PHPExcel_Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                if (is_null($returnValue) || $returnValue < $arg) {
                    $returnValue = $arg;
                }
            }
        }
        return $returnValue;
    }
    public static function MEDIAN()
    {
        $returnValue = PHPExcel_Calculation_Functions::NaN();
        $mArgs = array();
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                $mArgs[] = $arg;
            }
        }
        $mValueCount = count($mArgs);
        if (0 < $mValueCount) {
            sort($mArgs, SORT_NUMERIC);
            $mValueCount = $mValueCount / 2;
            if ($mValueCount == floor($mValueCount)) {
                $returnValue = ($mArgs[$mValueCount--] + $mArgs[$mValueCount]) / 2;
            } else {
                $mValueCount == floor($mValueCount);
                $returnValue = $mArgs[$mValueCount];
            }
        }
        return $returnValue;
    }
    public static function MIN()
    {
        $returnValue = NULL;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                if (is_null($returnValue) || $arg < $returnValue) {
                    $returnValue = $arg;
                }
            }
        }
        if (is_null($returnValue)) {
            return 0;
        }
        return $returnValue;
    }
    public static function MINA()
    {
        $returnValue = NULL;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            if (is_numeric($arg) || is_bool($arg) || is_string($arg) && $arg != '') {
                if (is_bool($arg)) {
                    $arg = (int) $arg;
                } else {
                    if (is_string($arg)) {
                        $arg = 0;
                    }
                }
                if (is_null($returnValue) || $arg < $returnValue) {
                    $returnValue = $arg;
                }
            }
        }
        if (is_null($returnValue)) {
            return 0;
        }
        return $returnValue;
    }
    public static function MINIF($aArgs, $condition, $sumArgs = array())
    {
        $returnValue = NULL;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray($aArgs);
        $sumArgs = PHPExcel_Calculation_Functions::flattenArray($sumArgs);
        if (empty($sumArgs)) {
            $sumArgs = $aArgs;
        }
        $condition = PHPExcel_Calculation_Functions::_ifCondition($condition);
        foreach ($aArgs as $key => $arg) {
            if (!is_numeric($arg)) {
                $arg = PHPExcel_Calculation::_wrapResult(strtoupper($arg));
            }
            $testCondition = '=' . $arg . $condition;
            if (PHPExcel_Calculation::getInstance()->_calculateFormulaValue($testCondition)) {
                if (is_null($returnValue) || $arg < $returnValue) {
                    $returnValue = $arg;
                }
            }
        }
        return $returnValue;
    }
    private static function _modeCalc($data)
    {
        $frequencyArray = array();
        foreach ($data as $datum) {
            $found = false;
            foreach ($frequencyArray as $key => $value) {
                if ((string) $value['value'] == (string) $datum) {
                    ++$frequencyArray[$key]['frequency'];
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $frequencyArray[] = array('value' => $datum, 'frequency' => 1);
            }
        }
        foreach ($frequencyArray as $key => $value) {
            $frequencyList[$key] = $value['frequency'];
            $valueList[$key] = $value['value'];
        }
        array_multisort($frequencyList, SORT_DESC, $valueList, SORT_ASC, SORT_NUMERIC, $frequencyArray);
        if ($frequencyArray[0]['frequency'] == 1) {
            return PHPExcel_Calculation_Functions::NA();
        }
        return $frequencyArray[0]['value'];
    }
    public static function MODE()
    {
        $returnValue = PHPExcel_Calculation_Functions::NA();
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $mArgs = array();
        foreach ($aArgs as $arg) {
            if (is_numeric($arg) && !is_string($arg)) {
                $mArgs[] = $arg;
            }
        }
        if (!empty($mArgs)) {
            return self::_modeCalc($mArgs);
        }
        return $returnValue;
    }
    public static function NEGBINOMDIST($failures, $successes, $probability)
    {
        $failures = floor(PHPExcel_Calculation_Functions::flattenSingleValue($failures));
        $successes = floor(PHPExcel_Calculation_Functions::flattenSingleValue($successes));
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        if (is_numeric($failures) && is_numeric($successes) && is_numeric($probability)) {
            if ($failures < 0 || $successes < 1) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($probability < 0 || 1 < $probability) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
                if ($failures + $successes - 1 <= 0) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }
            return PHPExcel_Calculation_MathTrig::COMBIN($failures + $successes - 1, $successes - 1) * pow($probability, $successes) * pow(1 - $probability, $failures);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function NORMDIST($value, $mean, $stdDev, $cumulative)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $mean = PHPExcel_Calculation_Functions::flattenSingleValue($mean);
        $stdDev = PHPExcel_Calculation_Functions::flattenSingleValue($stdDev);
        if (is_numeric($value) && is_numeric($mean) && is_numeric($stdDev)) {
            if ($stdDev < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (is_numeric($cumulative) || is_bool($cumulative)) {
                if ($cumulative) {
                    return 0.5 * (1 + PHPExcel_Calculation_Engineering::_erfVal(($value - $mean) / ($stdDev * sqrt(2))));
                } else {
                    return 1 / (SQRT2PI * $stdDev) * exp(-(pow($value - $mean, 2) / (2 * $stdDev * $stdDev)));
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function NORMINV($probability, $mean, $stdDev)
    {
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        $mean = PHPExcel_Calculation_Functions::flattenSingleValue($mean);
        $stdDev = PHPExcel_Calculation_Functions::flattenSingleValue($stdDev);
        if (is_numeric($probability) && is_numeric($mean) && is_numeric($stdDev)) {
            if ($probability < 0 || 1 < $probability) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if ($stdDev < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return self::_inverse_ncdf($probability) * $stdDev + $mean;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function NORMSDIST($value)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        return self::NORMDIST($value, 0, 1, true);
    }
    public static function NORMSINV($value)
    {
        return self::NORMINV($value, 0, 1);
    }
    public static function PERCENTILE()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $entry = array_pop($aArgs);
        if (is_numeric($entry) && !is_string($entry)) {
            if ($entry < 0 || 1 < $entry) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $mArgs = array();
            foreach ($aArgs as $arg) {
                if (is_numeric($arg) && !is_string($arg)) {
                    $mArgs[] = $arg;
                }
            }
            $mValueCount = count($mArgs);
            if (0 < $mValueCount) {
                sort($mArgs);
                $count = self::COUNT($mArgs);
                $index = $entry * ($count - 1);
                $iBase = floor($index);
                if ($index == $iBase) {
                    return $mArgs[$index];
                } else {
                    $iNext = $iBase + 1;
                    $iProportion = $index - $iBase;
                    return $mArgs[$iBase] + ($mArgs[$iNext] - $mArgs[$iBase]) * $iProportion;
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function PERCENTRANK($valueSet, $value, $significance = 3)
    {
        $valueSet = PHPExcel_Calculation_Functions::flattenArray($valueSet);
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $significance = is_null($significance) ? 3 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($significance);
        foreach ($valueSet as $key => $valueEntry) {
            if (!is_numeric($valueEntry)) {
                unset($valueSet[$key]);
            }
        }
        sort($valueSet, SORT_NUMERIC);
        $valueCount = count($valueSet);
        if ($valueCount == 0) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $valueAdjustor = $valueCount - 1;
        if ($value < $valueSet[0] || $valueSet[$valueAdjustor] < $value) {
            return PHPExcel_Calculation_Functions::NA();
        }
        $pos = array_search($value, $valueSet);
        if ($pos === false) {
            $pos = 0;
            $testValue = $valueSet[0];
            while ($testValue < $value) {
                $testValue = $valueSet[++$pos];
            }
            --$pos;
            $pos += ($value - $valueSet[$pos]) / ($testValue - $valueSet[$pos]);
        }
        return round($pos / $valueAdjustor, $significance);
    }
    public static function PERMUT($numObjs, $numInSet)
    {
        $numObjs = PHPExcel_Calculation_Functions::flattenSingleValue($numObjs);
        $numInSet = PHPExcel_Calculation_Functions::flattenSingleValue($numInSet);
        if (is_numeric($numObjs) && is_numeric($numInSet)) {
            $numInSet = floor($numInSet);
            if ($numObjs < $numInSet) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return round(PHPExcel_Calculation_MathTrig::FACT($numObjs) / PHPExcel_Calculation_MathTrig::FACT($numObjs - $numInSet));
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function POISSON($value, $mean, $cumulative)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $mean = PHPExcel_Calculation_Functions::flattenSingleValue($mean);
        if (is_numeric($value) && is_numeric($mean)) {
            if ($value <= 0 || $mean <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (is_numeric($cumulative) || is_bool($cumulative)) {
                if ($cumulative) {
                    $summer = 0;
                    for ($i = 0; $i <= floor($value); ++$i) {
                        $summer += pow($mean, $i) / PHPExcel_Calculation_MathTrig::FACT($i);
                    }
                    return exp(-$mean) * $summer;
                } else {
                    return exp(-$mean) * pow($mean, $value) / PHPExcel_Calculation_MathTrig::FACT($value);
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function QUARTILE()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $entry = floor(array_pop($aArgs));
        if (is_numeric($entry) && !is_string($entry)) {
            $entry /= 4;
            if ($entry < 0 || 1 < $entry) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return self::PERCENTILE($aArgs, $entry);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function RANK($value, $valueSet, $order = 0)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $valueSet = PHPExcel_Calculation_Functions::flattenArray($valueSet);
        $order = is_null($order) ? 0 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($order);
        foreach ($valueSet as $key => $valueEntry) {
            if (!is_numeric($valueEntry)) {
                unset($valueSet[$key]);
            }
        }
        if ($order == 0) {
            rsort($valueSet, SORT_NUMERIC);
        } else {
            sort($valueSet, SORT_NUMERIC);
        }
        $pos = array_search($value, $valueSet);
        if ($pos === false) {
            return PHPExcel_Calculation_Functions::NA();
        }
        return ++$pos;
    }
    public static function RSQ($yValues, $xValues)
    {
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
        }
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues);
        return $bestFitLinear->getGoodnessOfFit();
    }
    public static function SKEW()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $mean = self::AVERAGE($aArgs);
        $stdDev = self::STDEV($aArgs);
        $count = $summer = 0;
        foreach ($aArgs as $k => $arg) {
            if (is_bool($arg) && !PHPExcel_Calculation_Functions::isMatrixValue($k)) {
            } else {
                if (is_numeric($arg) && !is_string($arg)) {
                    $summer += pow(($arg - $mean) / $stdDev, 3);
                    ++$count;
                }
            }
        }
        if (2 < $count) {
            return $summer * ($count / (($count - 1) * ($count - 2)));
        }
        return PHPExcel_Calculation_Functions::DIV0();
    }
    public static function SLOPE($yValues, $xValues)
    {
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
        }
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues);
        return $bestFitLinear->getSlope();
    }
    public static function SMALL()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $entry = array_pop($aArgs);
        if (is_numeric($entry) && !is_string($entry)) {
            $mArgs = array();
            foreach ($aArgs as $arg) {
                if (is_numeric($arg) && !is_string($arg)) {
                    $mArgs[] = $arg;
                }
            }
            $count = self::COUNT($mArgs);
            $entry = floor(--$entry);
            if ($entry < 0 || $count <= $entry || $count == 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            sort($mArgs);
            return $mArgs[$entry];
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function STANDARDIZE($value, $mean, $stdDev)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $mean = PHPExcel_Calculation_Functions::flattenSingleValue($mean);
        $stdDev = PHPExcel_Calculation_Functions::flattenSingleValue($stdDev);
        if (is_numeric($value) && is_numeric($mean) && is_numeric($stdDev)) {
            if ($stdDev <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            return ($value - $mean) / $stdDev;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function STDEV()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $returnValue = NULL;
        $aMean = self::AVERAGE($aArgs);
        if (!is_null($aMean)) {
            $aCount = -1;
            foreach ($aArgs as $k => $arg) {
                if (is_bool($arg) && (!PHPExcel_Calculation_Functions::isCellValue($k) || PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE)) {
                    $arg = (int) $arg;
                }
                if (is_numeric($arg) && !is_string($arg)) {
                    if (is_null($returnValue)) {
                        $returnValue = pow($arg - $aMean, 2);
                    } else {
                        $returnValue += pow($arg - $aMean, 2);
                    }
                    ++$aCount;
                }
            }
            if (0 < $aCount && 0 <= $returnValue) {
                return sqrt($returnValue / $aCount);
            }
        }
        return PHPExcel_Calculation_Functions::DIV0();
    }
    public static function STDEVA()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $returnValue = NULL;
        $aMean = self::AVERAGEA($aArgs);
        if (!is_null($aMean)) {
            $aCount = -1;
            foreach ($aArgs as $k => $arg) {
                if (is_bool($arg) && !PHPExcel_Calculation_Functions::isMatrixValue($k)) {
                } else {
                    if (is_numeric($arg) || is_bool($arg) || is_string($arg) & $arg != '') {
                        if (is_bool($arg)) {
                            $arg = (int) $arg;
                        } else {
                            if (is_string($arg)) {
                                $arg = 0;
                            }
                        }
                        if (is_null($returnValue)) {
                            $returnValue = pow($arg - $aMean, 2);
                        } else {
                            $returnValue += pow($arg - $aMean, 2);
                        }
                        ++$aCount;
                    }
                }
            }
            if (0 < $aCount && 0 <= $returnValue) {
                return sqrt($returnValue / $aCount);
            }
        }
        return PHPExcel_Calculation_Functions::DIV0();
    }
    public static function STDEVP()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $returnValue = NULL;
        $aMean = self::AVERAGE($aArgs);
        if (!is_null($aMean)) {
            $aCount = 0;
            foreach ($aArgs as $k => $arg) {
                if (is_bool($arg) && (!PHPExcel_Calculation_Functions::isCellValue($k) || PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE)) {
                    $arg = (int) $arg;
                }
                if (is_numeric($arg) && !is_string($arg)) {
                    if (is_null($returnValue)) {
                        $returnValue = pow($arg - $aMean, 2);
                    } else {
                        $returnValue += pow($arg - $aMean, 2);
                    }
                    ++$aCount;
                }
            }
            if (0 < $aCount && 0 <= $returnValue) {
                return sqrt($returnValue / $aCount);
            }
        }
        return PHPExcel_Calculation_Functions::DIV0();
    }
    public static function STDEVPA()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $returnValue = NULL;
        $aMean = self::AVERAGEA($aArgs);
        if (!is_null($aMean)) {
            $aCount = 0;
            foreach ($aArgs as $k => $arg) {
                if (is_bool($arg) && !PHPExcel_Calculation_Functions::isMatrixValue($k)) {
                } else {
                    if (is_numeric($arg) || is_bool($arg) || is_string($arg) & $arg != '') {
                        if (is_bool($arg)) {
                            $arg = (int) $arg;
                        } else {
                            if (is_string($arg)) {
                                $arg = 0;
                            }
                        }
                        if (is_null($returnValue)) {
                            $returnValue = pow($arg - $aMean, 2);
                        } else {
                            $returnValue += pow($arg - $aMean, 2);
                        }
                        ++$aCount;
                    }
                }
            }
            if (0 < $aCount && 0 <= $returnValue) {
                return sqrt($returnValue / $aCount);
            }
        }
        return PHPExcel_Calculation_Functions::DIV0();
    }
    public static function STEYX($yValues, $xValues)
    {
        if (!self::_checkTrendArrays($yValues, $xValues)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $yValueCount = count($yValues);
        $xValueCount = count($xValues);
        if ($yValueCount == 0 || $yValueCount != $xValueCount) {
            return PHPExcel_Calculation_Functions::NA();
        } else {
            if ($yValueCount == 1) {
                return PHPExcel_Calculation_Functions::DIV0();
            }
        }
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues);
        return $bestFitLinear->getStdevOfResiduals();
    }
    public static function TDIST($value, $degrees, $tails)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $degrees = floor(PHPExcel_Calculation_Functions::flattenSingleValue($degrees));
        $tails = floor(PHPExcel_Calculation_Functions::flattenSingleValue($tails));
        if (is_numeric($value) && is_numeric($degrees) && is_numeric($tails)) {
            if ($value < 0 || $degrees < 1 || $tails < 1 || 2 < $tails) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $tterm = $degrees;
            $ttheta = atan2($value, sqrt($tterm));
            $tc = cos($ttheta);
            $ts = sin($ttheta);
            $tsum = 0;
            if ($degrees % 2 == 1) {
                $ti = 3;
                $tterm = $tc;
            } else {
                $ti = 2;
                $tterm = 1;
            }
            $tsum = $tterm;
            while ($ti < $degrees) {
                $tterm *= $tc * $tc * ($ti - 1) / $ti;
                $tsum += $tterm;
                $ti += 2;
            }
            $tsum *= $ts;
            if ($degrees % 2 == 1) {
                $tsum = M_2DIVPI * ($tsum + $ttheta);
            }
            $tValue = 0.5 * (1 + $tsum);
            if ($tails == 1) {
                return 1 - abs($tValue);
            } else {
                return 1 - abs(1 - $tValue - $tValue);
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function TINV($probability, $degrees)
    {
        $probability = PHPExcel_Calculation_Functions::flattenSingleValue($probability);
        $degrees = floor(PHPExcel_Calculation_Functions::flattenSingleValue($degrees));
        if (is_numeric($probability) && is_numeric($degrees)) {
            $xLo = 100;
            $xHi = 0;
            $x = $xNew = 1;
            $dx = 1;
            $i = 0;
            while (PRECISION < abs($dx) && $i++ < MAX_ITERATIONS) {
                $result = self::TDIST($x, $degrees, 2);
                $error = $result - $probability;
                if ($error == 0) {
                    $dx = 0;
                } else {
                    if ($error < 0) {
                        $xLo = $x;
                    } else {
                        $xHi = $x;
                    }
                }
                if ($result != 0) {
                    $dx = $error / $result;
                    $xNew = $x - $dx;
                }
                if ($xNew < $xLo || $xHi < $xNew || $result == 0) {
                    $xNew = ($xLo + $xHi) / 2;
                    $dx = $xNew - $x;
                }
                $x = $xNew;
            }
            if ($i == MAX_ITERATIONS) {
                return PHPExcel_Calculation_Functions::NA();
            }
            return round($x, 12);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function TREND($yValues, $xValues = array(), $newValues = array(), $const = true)
    {
        $yValues = PHPExcel_Calculation_Functions::flattenArray($yValues);
        $xValues = PHPExcel_Calculation_Functions::flattenArray($xValues);
        $newValues = PHPExcel_Calculation_Functions::flattenArray($newValues);
        $const = is_null($const) ? true : (bool) PHPExcel_Calculation_Functions::flattenSingleValue($const);
        $bestFitLinear = trendClass::calculate(trendClass::TREND_LINEAR, $yValues, $xValues, $const);
        if (empty($newValues)) {
            $newValues = $bestFitLinear->getXValues();
        }
        $returnArray = array();
        foreach ($newValues as $xValue) {
            $returnArray[0][] = $bestFitLinear->getValueOfYForX($xValue);
        }
        return $returnArray;
    }
    public static function TRIMMEAN()
    {
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $percent = array_pop($aArgs);
        if (is_numeric($percent) && !is_string($percent)) {
            if ($percent < 0 || 1 < $percent) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $mArgs = array();
            foreach ($aArgs as $arg) {
                if (is_numeric($arg) && !is_string($arg)) {
                    $mArgs[] = $arg;
                }
            }
            $discard = floor(self::COUNT($mArgs) * $percent / 2);
            sort($mArgs);
            for ($i = 0; $i < $discard; ++$i) {
                array_pop($mArgs);
                array_shift($mArgs);
            }
            return self::AVERAGE($mArgs);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function VARFunc()
    {
        $returnValue = PHPExcel_Calculation_Functions::DIV0();
        $summerA = $summerB = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $aCount = 0;
        foreach ($aArgs as $arg) {
            if (is_bool($arg)) {
                $arg = (int) $arg;
            }
            if (is_numeric($arg) && !is_string($arg)) {
                $summerA += $arg * $arg;
                $summerB += $arg;
                ++$aCount;
            }
        }
        if (1 < $aCount) {
            $summerA *= $aCount;
            $summerB *= $summerB;
            $returnValue = ($summerA - $summerB) / ($aCount * ($aCount - 1));
        }
        return $returnValue;
    }
    public static function VARA()
    {
        $returnValue = PHPExcel_Calculation_Functions::DIV0();
        $summerA = $summerB = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $aCount = 0;
        foreach ($aArgs as $k => $arg) {
            if (is_string($arg) && PHPExcel_Calculation_Functions::isValue($k)) {
                return PHPExcel_Calculation_Functions::VALUE();
            } else {
                if (is_string($arg) && !PHPExcel_Calculation_Functions::isMatrixValue($k)) {
                } else {
                    if (is_numeric($arg) || is_bool($arg) || is_string($arg) & $arg != '') {
                        if (is_bool($arg)) {
                            $arg = (int) $arg;
                        } else {
                            if (is_string($arg)) {
                                $arg = 0;
                            }
                        }
                        $summerA += $arg * $arg;
                        $summerB += $arg;
                        ++$aCount;
                    }
                }
            }
        }
        if (1 < $aCount) {
            $summerA *= $aCount;
            $summerB *= $summerB;
            $returnValue = ($summerA - $summerB) / ($aCount * ($aCount - 1));
        }
        return $returnValue;
    }
    public static function VARP()
    {
        $returnValue = PHPExcel_Calculation_Functions::DIV0();
        $summerA = $summerB = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        $aCount = 0;
        foreach ($aArgs as $arg) {
            if (is_bool($arg)) {
                $arg = (int) $arg;
            }
            if (is_numeric($arg) && !is_string($arg)) {
                $summerA += $arg * $arg;
                $summerB += $arg;
                ++$aCount;
            }
        }
        if (0 < $aCount) {
            $summerA *= $aCount;
            $summerB *= $summerB;
            $returnValue = ($summerA - $summerB) / ($aCount * $aCount);
        }
        return $returnValue;
    }
    public static function VARPA()
    {
        $returnValue = PHPExcel_Calculation_Functions::DIV0();
        $summerA = $summerB = 0;
        $aArgs = PHPExcel_Calculation_Functions::flattenArrayIndexed(func_get_args());
        $aCount = 0;
        foreach ($aArgs as $k => $arg) {
            if (is_string($arg) && PHPExcel_Calculation_Functions::isValue($k)) {
                return PHPExcel_Calculation_Functions::VALUE();
            } else {
                if (is_string($arg) && !PHPExcel_Calculation_Functions::isMatrixValue($k)) {
                } else {
                    if (is_numeric($arg) || is_bool($arg) || is_string($arg) & $arg != '') {
                        if (is_bool($arg)) {
                            $arg = (int) $arg;
                        } else {
                            if (is_string($arg)) {
                                $arg = 0;
                            }
                        }
                        $summerA += $arg * $arg;
                        $summerB += $arg;
                        ++$aCount;
                    }
                }
            }
        }
        if (0 < $aCount) {
            $summerA *= $aCount;
            $summerB *= $summerB;
            $returnValue = ($summerA - $summerB) / ($aCount * $aCount);
        }
        return $returnValue;
    }
    public static function WEIBULL($value, $alpha, $beta, $cumulative)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $alpha = PHPExcel_Calculation_Functions::flattenSingleValue($alpha);
        $beta = PHPExcel_Calculation_Functions::flattenSingleValue($beta);
        if (is_numeric($value) && is_numeric($alpha) && is_numeric($beta)) {
            if ($value < 0 || $alpha <= 0 || $beta <= 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (is_numeric($cumulative) || is_bool($cumulative)) {
                if ($cumulative) {
                    return 1 - exp(-pow($value / $beta, $alpha));
                } else {
                    return $alpha / pow($beta, $alpha) * pow($value, $alpha - 1) * exp(-pow($value / $beta, $alpha));
                }
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function ZTEST($dataSet, $m0, $sigma = NULL)
    {
        $dataSet = PHPExcel_Calculation_Functions::flattenArrayIndexed($dataSet);
        $m0 = PHPExcel_Calculation_Functions::flattenSingleValue($m0);
        $sigma = PHPExcel_Calculation_Functions::flattenSingleValue($sigma);
        if (is_null($sigma)) {
            $sigma = self::STDEV($dataSet);
        }
        $n = count($dataSet);
        return 1 - self::NORMSDIST((self::AVERAGE($dataSet) - $m0) / $sigma / sqrt($n));
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/trend/trendClass.php';
define('LOG_GAMMA_X_MAX_VALUE', 2.55E+305);
define('XMININ', 2.23E-308);
define('EPS', 2.22E-16);
define('SQRT2PI', 2.506628274631);
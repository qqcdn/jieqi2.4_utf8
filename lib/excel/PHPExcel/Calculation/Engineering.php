<?php

class PHPExcel_Calculation_Engineering
{
    /**
     * Details of the Units of measure that can be used in CONVERTUOM()
     *
     * @var mixed[]
     */
    private static $_conversionUnits = array('g' => array('Group' => 'Mass', 'Unit Name' => 'Gram', 'AllowPrefix' => true), 'sg' => array('Group' => 'Mass', 'Unit Name' => 'Slug', 'AllowPrefix' => false), 'lbm' => array('Group' => 'Mass', 'Unit Name' => 'Pound mass (avoirdupois)', 'AllowPrefix' => false), 'u' => array('Group' => 'Mass', 'Unit Name' => 'U (atomic mass unit)', 'AllowPrefix' => true), 'ozm' => array('Group' => 'Mass', 'Unit Name' => 'Ounce mass (avoirdupois)', 'AllowPrefix' => false), 'm' => array('Group' => 'Distance', 'Unit Name' => 'Meter', 'AllowPrefix' => true), 'mi' => array('Group' => 'Distance', 'Unit Name' => 'Statute mile', 'AllowPrefix' => false), 'Nmi' => array('Group' => 'Distance', 'Unit Name' => 'Nautical mile', 'AllowPrefix' => false), 'in' => array('Group' => 'Distance', 'Unit Name' => 'Inch', 'AllowPrefix' => false), 'ft' => array('Group' => 'Distance', 'Unit Name' => 'Foot', 'AllowPrefix' => false), 'yd' => array('Group' => 'Distance', 'Unit Name' => 'Yard', 'AllowPrefix' => false), 'ang' => array('Group' => 'Distance', 'Unit Name' => 'Angstrom', 'AllowPrefix' => true), 'Pica' => array('Group' => 'Distance', 'Unit Name' => 'Pica (1/72 in)', 'AllowPrefix' => false), 'yr' => array('Group' => 'Time', 'Unit Name' => 'Year', 'AllowPrefix' => false), 'day' => array('Group' => 'Time', 'Unit Name' => 'Day', 'AllowPrefix' => false), 'hr' => array('Group' => 'Time', 'Unit Name' => 'Hour', 'AllowPrefix' => false), 'mn' => array('Group' => 'Time', 'Unit Name' => 'Minute', 'AllowPrefix' => false), 'sec' => array('Group' => 'Time', 'Unit Name' => 'Second', 'AllowPrefix' => true), 'Pa' => array('Group' => 'Pressure', 'Unit Name' => 'Pascal', 'AllowPrefix' => true), 'p' => array('Group' => 'Pressure', 'Unit Name' => 'Pascal', 'AllowPrefix' => true), 'atm' => array('Group' => 'Pressure', 'Unit Name' => 'Atmosphere', 'AllowPrefix' => true), 'at' => array('Group' => 'Pressure', 'Unit Name' => 'Atmosphere', 'AllowPrefix' => true), 'mmHg' => array('Group' => 'Pressure', 'Unit Name' => 'mm of Mercury', 'AllowPrefix' => true), 'N' => array('Group' => 'Force', 'Unit Name' => 'Newton', 'AllowPrefix' => true), 'dyn' => array('Group' => 'Force', 'Unit Name' => 'Dyne', 'AllowPrefix' => true), 'dy' => array('Group' => 'Force', 'Unit Name' => 'Dyne', 'AllowPrefix' => true), 'lbf' => array('Group' => 'Force', 'Unit Name' => 'Pound force', 'AllowPrefix' => false), 'J' => array('Group' => 'Energy', 'Unit Name' => 'Joule', 'AllowPrefix' => true), 'e' => array('Group' => 'Energy', 'Unit Name' => 'Erg', 'AllowPrefix' => true), 'c' => array('Group' => 'Energy', 'Unit Name' => 'Thermodynamic calorie', 'AllowPrefix' => true), 'cal' => array('Group' => 'Energy', 'Unit Name' => 'IT calorie', 'AllowPrefix' => true), 'eV' => array('Group' => 'Energy', 'Unit Name' => 'Electron volt', 'AllowPrefix' => true), 'ev' => array('Group' => 'Energy', 'Unit Name' => 'Electron volt', 'AllowPrefix' => true), 'HPh' => array('Group' => 'Energy', 'Unit Name' => 'Horsepower-hour', 'AllowPrefix' => false), 'hh' => array('Group' => 'Energy', 'Unit Name' => 'Horsepower-hour', 'AllowPrefix' => false), 'Wh' => array('Group' => 'Energy', 'Unit Name' => 'Watt-hour', 'AllowPrefix' => true), 'wh' => array('Group' => 'Energy', 'Unit Name' => 'Watt-hour', 'AllowPrefix' => true), 'flb' => array('Group' => 'Energy', 'Unit Name' => 'Foot-pound', 'AllowPrefix' => false), 'BTU' => array('Group' => 'Energy', 'Unit Name' => 'BTU', 'AllowPrefix' => false), 'btu' => array('Group' => 'Energy', 'Unit Name' => 'BTU', 'AllowPrefix' => false), 'HP' => array('Group' => 'Power', 'Unit Name' => 'Horsepower', 'AllowPrefix' => false), 'h' => array('Group' => 'Power', 'Unit Name' => 'Horsepower', 'AllowPrefix' => false), 'W' => array('Group' => 'Power', 'Unit Name' => 'Watt', 'AllowPrefix' => true), 'w' => array('Group' => 'Power', 'Unit Name' => 'Watt', 'AllowPrefix' => true), 'T' => array('Group' => 'Magnetism', 'Unit Name' => 'Tesla', 'AllowPrefix' => true), 'ga' => array('Group' => 'Magnetism', 'Unit Name' => 'Gauss', 'AllowPrefix' => true), 'C' => array('Group' => 'Temperature', 'Unit Name' => 'Celsius', 'AllowPrefix' => false), 'cel' => array('Group' => 'Temperature', 'Unit Name' => 'Celsius', 'AllowPrefix' => false), 'F' => array('Group' => 'Temperature', 'Unit Name' => 'Fahrenheit', 'AllowPrefix' => false), 'fah' => array('Group' => 'Temperature', 'Unit Name' => 'Fahrenheit', 'AllowPrefix' => false), 'K' => array('Group' => 'Temperature', 'Unit Name' => 'Kelvin', 'AllowPrefix' => false), 'kel' => array('Group' => 'Temperature', 'Unit Name' => 'Kelvin', 'AllowPrefix' => false), 'tsp' => array('Group' => 'Liquid', 'Unit Name' => 'Teaspoon', 'AllowPrefix' => false), 'tbs' => array('Group' => 'Liquid', 'Unit Name' => 'Tablespoon', 'AllowPrefix' => false), 'oz' => array('Group' => 'Liquid', 'Unit Name' => 'Fluid Ounce', 'AllowPrefix' => false), 'cup' => array('Group' => 'Liquid', 'Unit Name' => 'Cup', 'AllowPrefix' => false), 'pt' => array('Group' => 'Liquid', 'Unit Name' => 'U.S. Pint', 'AllowPrefix' => false), 'us_pt' => array('Group' => 'Liquid', 'Unit Name' => 'U.S. Pint', 'AllowPrefix' => false), 'uk_pt' => array('Group' => 'Liquid', 'Unit Name' => 'U.K. Pint', 'AllowPrefix' => false), 'qt' => array('Group' => 'Liquid', 'Unit Name' => 'Quart', 'AllowPrefix' => false), 'gal' => array('Group' => 'Liquid', 'Unit Name' => 'Gallon', 'AllowPrefix' => false), 'l' => array('Group' => 'Liquid', 'Unit Name' => 'Litre', 'AllowPrefix' => true), 'lt' => array('Group' => 'Liquid', 'Unit Name' => 'Litre', 'AllowPrefix' => true));
    /**
     * Details of the Multiplier prefixes that can be used with Units of Measure in CONVERTUOM()
     *
     * @var mixed[]
     */
    private static $_conversionMultipliers = array('Y' => array('multiplier' => 1.0E+24, 'name' => 'yotta'), 'Z' => array('multiplier' => 1.0E+21, 'name' => 'zetta'), 'E' => array('multiplier' => 1.0E+18, 'name' => 'exa'), 'P' => array('multiplier' => 1000000000000000.0, 'name' => 'peta'), 'T' => array('multiplier' => 1000000000000.0, 'name' => 'tera'), 'G' => array('multiplier' => 1000000000, 'name' => 'giga'), 'M' => array('multiplier' => 1000000, 'name' => 'mega'), 'k' => array('multiplier' => 1000, 'name' => 'kilo'), 'h' => array('multiplier' => 100, 'name' => 'hecto'), 'e' => array('multiplier' => 10, 'name' => 'deka'), 'd' => array('multiplier' => 0.1, 'name' => 'deci'), 'c' => array('multiplier' => 0.01, 'name' => 'centi'), 'm' => array('multiplier' => 0.001, 'name' => 'milli'), 'u' => array('multiplier' => 1.0E-6, 'name' => 'micro'), 'n' => array('multiplier' => 1.0E-9, 'name' => 'nano'), 'p' => array('multiplier' => 1.0E-12, 'name' => 'pico'), 'f' => array('multiplier' => 1.0E-15, 'name' => 'femto'), 'a' => array('multiplier' => 1.0E-18, 'name' => 'atto'), 'z' => array('multiplier' => 9.999999999999999E-22, 'name' => 'zepto'), 'y' => array('multiplier' => 9.999999999999999E-25, 'name' => 'yocto'));
    /**
     * Details of the Units of measure conversion factors, organised by group
     *
     * @var mixed[]
     */
    private static $_unitConversions = array('Mass' => array('g' => array('g' => 1, 'sg' => 6.8522050005348E-5, 'lbm' => 0.0022046229146913, 'u' => 6.02217E+23, 'ozm' => 0.035273971800363), 'sg' => array('g' => 14593.842418929, 'sg' => 1, 'lbm' => 32.173919410165, 'u' => 8.788659999999999E+27, 'ozm' => 514.78278594423), 'lbm' => array('g' => 453.59230974881, 'sg' => 0.031081074930649, 'lbm' => 1, 'u' => 2.73161E+26, 'ozm' => 16.000002342941), 'u' => array('g' => 1.6605310046047E-24, 'sg' => 1.1378298853295E-28, 'lbm' => 3.6608447033068E-27, 'u' => 1, 'ozm' => 5.8573523830052E-26), 'ozm' => array('g' => 28.349515207973, 'sg' => 0.0019425668987081, 'lbm' => 0.062499990847888, 'u' => 1.707256E+25, 'ozm' => 1)), 'Distance' => array('m' => array('m' => 1, 'mi' => 0.00062137119223733, 'Nmi' => 0.00053995680345572, 'in' => 39.370078740158, 'ft' => 3.2808398950131, 'yd' => 1.0936132979789, 'ang' => 10000000000.0, 'Pica' => 2834.6456692912), 'mi' => array('m' => 1609.344, 'mi' => 1, 'Nmi' => 0.86897624190065, 'in' => 63360, 'ft' => 5280, 'yd' => 1760, 'ang' => 16093440000000.0, 'Pica' => 4561919.9999997), 'Nmi' => array('m' => 1852, 'mi' => 1.1507794480235, 'Nmi' => 1, 'in' => 72913.38582677201, 'ft' => 6076.1154855643, 'yd' => 2025.3718278569, 'ang' => 18520000000000.0, 'Pica' => 5249763.7795272), 'in' => array('m' => 0.0254, 'mi' => 1.5782828282828E-5, 'Nmi' => 1.3714902807775E-5, 'in' => 1, 'ft' => 0.083333333333333, 'yd' => 0.027777777768664, 'ang' => 254000000, 'Pica' => 71.999999999995), 'ft' => array('m' => 0.3048, 'mi' => 0.00018939393939394, 'Nmi' => 0.00016457883369331, 'in' => 12, 'ft' => 1, 'yd' => 0.33333333322397, 'ang' => 3048000000.0, 'Pica' => 863.99999999995), 'yd' => array('m' => 0.9144000003, 'mi' => 0.00056818181836823, 'Nmi' => 0.0004937365012419, 'in' => 36.000000011811, 'ft' => 3, 'yd' => 1, 'ang' => 9144000003.0, 'Pica' => 2592.0000008502), 'ang' => array('m' => 1.0E-10, 'mi' => 6.2137119223733E-14, 'Nmi' => 5.3995680345572E-14, 'in' => 3.9370078740157E-9, 'ft' => 3.2808398950131E-10, 'yd' => 1.0936132979789E-10, 'ang' => 1, 'Pica' => 2.8346456692912E-7), 'Pica' => array('m' => 0.0003527777777778, 'mi' => 2.1920594837263E-7, 'Nmi' => 1.9048476121911E-7, 'in' => 0.01388888888889, 'ft' => 0.0011574074074075, 'yd' => 0.00038580246900925, 'ang' => 3527777.777778, 'Pica' => 1)), 'Time' => array('yr' => array('yr' => 1, 'day' => 365.25, 'hr' => 8766, 'mn' => 525960, 'sec' => 31557600), 'day' => array('yr' => 0.0027378507871321, 'day' => 1, 'hr' => 24, 'mn' => 1440, 'sec' => 86400), 'hr' => array('yr' => 0.0001140771161305, 'day' => 0.041666666666667, 'hr' => 1, 'mn' => 60, 'sec' => 3600), 'mn' => array('yr' => 1.9012852688417E-6, 'day' => 0.00069444444444444, 'hr' => 0.016666666666667, 'mn' => 1, 'sec' => 60), 'sec' => array('yr' => 3.1688087814029E-8, 'day' => 1.1574074074074E-5, 'hr' => 0.00027777777777778, 'mn' => 0.016666666666667, 'sec' => 1)), 'Pressure' => array('Pa' => array('Pa' => 1, 'p' => 1, 'atm' => 9.869232999981899E-6, 'at' => 9.869232999981899E-6, 'mmHg' => 0.0075006170799863), 'p' => array('Pa' => 1, 'p' => 1, 'atm' => 9.869232999981899E-6, 'at' => 9.869232999981899E-6, 'mmHg' => 0.0075006170799863), 'atm' => array('Pa' => 101324.996583, 'p' => 101324.996583, 'atm' => 1, 'at' => 1, 'mmHg' => 760), 'at' => array('Pa' => 101324.996583, 'p' => 101324.996583, 'atm' => 1, 'at' => 1, 'mmHg' => 760), 'mmHg' => array('Pa' => 133.322363925, 'p' => 133.322363925, 'atm' => 0.0013157894736842, 'at' => 0.0013157894736842, 'mmHg' => 1)), 'Force' => array('N' => array('N' => 1, 'dyn' => 100000, 'dy' => 100000, 'lbf' => 0.22480892365534), 'dyn' => array('N' => 1.0E-5, 'dyn' => 1, 'dy' => 1, 'lbf' => 2.2480892365534E-6), 'dy' => array('N' => 1.0E-5, 'dyn' => 1, 'dy' => 1, 'lbf' => 2.2480892365534E-6), 'lbf' => array('N' => 4.448222, 'dyn' => 444822.2, 'dy' => 444822.2, 'lbf' => 1)), 'Energy' => array('J' => array('J' => 1, 'e' => 9999995.193432299, 'c' => 0.23900624947347, 'cal' => 0.23884619064202, 'eV' => 6.241457E+18, 'ev' => 6.241457E+18, 'HPh' => 3.72506430801E-7, 'hh' => 3.72506430801E-7, 'Wh' => 0.00027777791623871, 'wh' => 0.00027777791623871, 'flb' => 23.730422219265, 'BTU' => 0.00094781506734902, 'btu' => 0.00094781506734902), 'e' => array('J' => 1.000000480657E-7, 'e' => 1, 'c' => 2.3900636435349E-8, 'cal' => 2.3884630544511E-8, 'eV' => 624146000000.0, 'ev' => 624146000000.0, 'HPh' => 3.7250660984882E-14, 'hh' => 3.7250660984882E-14, 'Wh' => 2.7777804975461E-11, 'wh' => 2.7777804975461E-11, 'flb' => 2.3730433625459E-6, 'BTU' => 9.4781552292296E-11, 'btu' => 9.4781552292296E-11), 'c' => array('J' => 4.1839910136367, 'e' => 41839890.025731, 'c' => 1, 'cal' => 0.99933031528756, 'eV' => 2.61142E+19, 'ev' => 2.61142E+19, 'HPh' => 1.5585635589933E-6, 'hh' => 1.5585635589933E-6, 'Wh' => 0.0011622203053295, 'wh' => 0.0011622203053295, 'flb' => 99.28787331521001, 'BTU' => 0.0039656497243778, 'btu' => 0.0039656497243778), 'cal' => array('J' => 4.1867948461393, 'e' => 41867928.33728, 'c' => 1.0006701334906, 'cal' => 1, 'eV' => 2.61317E+19, 'ev' => 2.61317E+19, 'HPh' => 1.5596080046314E-6, 'hh' => 1.5596080046314E-6, 'Wh' => 0.0011629991480795, 'wh' => 0.0011629991480795, 'flb' => 99.354409444328, 'BTU' => 0.00396830723907, 'btu' => 0.00396830723907), 'eV' => array('J' => 1.6021900014692E-19, 'e' => 1.6021892313657E-12, 'c' => 3.8293342319504E-20, 'cal' => 3.8267697853565E-20, 'eV' => 1, 'ev' => 1, 'HPh' => 5.9682607891234E-26, 'hh' => 5.9682607891234E-26, 'Wh' => 4.4505300002661E-23, 'wh' => 4.4505300002661E-23, 'flb' => 3.8020645210349E-18, 'BTU' => 1.5185798241485E-22, 'btu' => 1.5185798241485E-22), 'ev' => array('J' => 1.6021900014692E-19, 'e' => 1.6021892313657E-12, 'c' => 3.8293342319504E-20, 'cal' => 3.8267697853565E-20, 'eV' => 1, 'ev' => 1, 'HPh' => 5.9682607891234E-26, 'hh' => 5.9682607891234E-26, 'Wh' => 4.4505300002661E-23, 'wh' => 4.4505300002661E-23, 'flb' => 3.8020645210349E-18, 'BTU' => 1.5185798241485E-22, 'btu' => 1.5185798241485E-22), 'HPh' => array('J' => 2684517.4131617, 'e' => 26845161228302.0, 'c' => 641616.43856599, 'cal' => 641186.7578458301, 'eV' => 1.67553E+25, 'ev' => 1.67553E+25, 'HPh' => 1, 'hh' => 1, 'Wh' => 745.69965313459, 'wh' => 745.69965313459, 'flb' => 63704731.669296, 'BTU' => 2544.4260527555, 'btu' => 2544.4260527555), 'hh' => array('J' => 2684517.4131617, 'e' => 26845161228302.0, 'c' => 641616.43856599, 'cal' => 641186.7578458301, 'eV' => 1.67553E+25, 'ev' => 1.67553E+25, 'HPh' => 1, 'hh' => 1, 'Wh' => 745.69965313459, 'wh' => 745.69965313459, 'flb' => 63704731.669296, 'BTU' => 2544.4260527555, 'btu' => 2544.4260527555), 'Wh' => array('J' => 3599.9982055472, 'e' => 35999964751.837, 'c' => 860.4220692190499, 'cal' => 859.84585771305, 'eV' => 2.2469234E+22, 'ev' => 2.2469234E+22, 'HPh' => 0.0013410224824384, 'hh' => 0.0013410224824384, 'Wh' => 1, 'wh' => 1, 'flb' => 85429.47740623201, 'BTU' => 3.4121325416471, 'btu' => 3.4121325416471), 'wh' => array('J' => 3599.9982055472, 'e' => 35999964751.837, 'c' => 860.4220692190499, 'cal' => 859.84585771305, 'eV' => 2.2469234E+22, 'ev' => 2.2469234E+22, 'HPh' => 0.0013410224824384, 'hh' => 0.0013410224824384, 'Wh' => 1, 'wh' => 1, 'flb' => 85429.47740623201, 'BTU' => 3.4121325416471, 'btu' => 3.4121325416471), 'flb' => array('J' => 0.042140000323642, 'e' => 421399.80068766, 'c' => 0.010071723430164, 'cal' => 0.010064978550955, 'eV' => 2.63015E+17, 'ev' => 2.63015E+17, 'HPh' => 1.5697421114513E-8, 'hh' => 1.5697421114513E-8, 'Wh' => 1.17055614802E-5, 'wh' => 1.17055614802E-5, 'flb' => 1, 'BTU' => 3.9940927244841E-5, 'btu' => 3.9940927244841E-5), 'BTU' => array('J' => 1055.0581378675, 'e' => 10550576307.466, 'c' => 252.16548850817, 'cal' => 251.99661713551, 'eV' => 6.5851E+21, 'ev' => 6.5851E+21, 'HPh' => 0.00039301594122457, 'hh' => 0.00039301594122457, 'Wh' => 0.29307185104753, 'wh' => 0.29307185104753, 'flb' => 25036.975077467, 'BTU' => 1, 'btu' => 1), 'btu' => array('J' => 1055.0581378675, 'e' => 10550576307.466, 'c' => 252.16548850817, 'cal' => 251.99661713551, 'eV' => 6.5851E+21, 'ev' => 6.5851E+21, 'HPh' => 0.00039301594122457, 'hh' => 0.00039301594122457, 'Wh' => 0.29307185104753, 'wh' => 0.29307185104753, 'flb' => 25036.975077467, 'BTU' => 1, 'btu' => 1)), 'Power' => array('HP' => array('HP' => 1, 'h' => 1, 'W' => 745.701, 'w' => 745.701), 'h' => array('HP' => 1, 'h' => 1, 'W' => 745.701, 'w' => 745.701), 'W' => array('HP' => 0.0013410200603191, 'h' => 0.0013410200603191, 'W' => 1, 'w' => 1), 'w' => array('HP' => 0.0013410200603191, 'h' => 0.0013410200603191, 'W' => 1, 'w' => 1)), 'Magnetism' => array('T' => array('T' => 1, 'ga' => 10000), 'ga' => array('T' => 0.0001, 'ga' => 1)), 'Liquid' => array('tsp' => array('tsp' => 1, 'tbs' => 0.33333333333333, 'oz' => 0.16666666666667, 'cup' => 0.020833333333333, 'pt' => 0.010416666666667, 'us_pt' => 0.010416666666667, 'uk_pt' => 0.008675585168219599, 'qt' => 0.0052083333333333, 'gal' => 0.0013020833333333, 'l' => 0.0049299940840071, 'lt' => 0.0049299940840071), 'tbs' => array('tsp' => 3, 'tbs' => 1, 'oz' => 0.5, 'cup' => 0.0625, 'pt' => 0.03125, 'us_pt' => 0.03125, 'uk_pt' => 0.026026755504659, 'qt' => 0.015625, 'gal' => 0.00390625, 'l' => 0.014789982252021, 'lt' => 0.014789982252021), 'oz' => array('tsp' => 6, 'tbs' => 2, 'oz' => 1, 'cup' => 0.125, 'pt' => 0.0625, 'us_pt' => 0.0625, 'uk_pt' => 0.052053511009318, 'qt' => 0.03125, 'gal' => 0.0078125, 'l' => 0.029579964504043, 'lt' => 0.029579964504043), 'cup' => array('tsp' => 48, 'tbs' => 16, 'oz' => 8, 'cup' => 1, 'pt' => 0.5, 'us_pt' => 0.5, 'uk_pt' => 0.41642808807454, 'qt' => 0.25, 'gal' => 0.0625, 'l' => 0.23663971603234, 'lt' => 0.23663971603234), 'pt' => array('tsp' => 96, 'tbs' => 32, 'oz' => 16, 'cup' => 2, 'pt' => 1, 'us_pt' => 1, 'uk_pt' => 0.83285617614908, 'qt' => 0.5, 'gal' => 0.125, 'l' => 0.47327943206468, 'lt' => 0.47327943206468), 'us_pt' => array('tsp' => 96, 'tbs' => 32, 'oz' => 16, 'cup' => 2, 'pt' => 1, 'us_pt' => 1, 'uk_pt' => 0.83285617614908, 'qt' => 0.5, 'gal' => 0.125, 'l' => 0.47327943206468, 'lt' => 0.47327943206468), 'uk_pt' => array('tsp' => 115.266, 'tbs' => 38.422, 'oz' => 19.211, 'cup' => 2.401375, 'pt' => 1.2006875, 'us_pt' => 1.2006875, 'uk_pt' => 1, 'qt' => 0.60034375, 'gal' => 0.1500859375, 'l' => 0.56826069808716, 'lt' => 0.56826069808716), 'qt' => array('tsp' => 192, 'tbs' => 64, 'oz' => 32, 'cup' => 4, 'pt' => 2, 'us_pt' => 2, 'uk_pt' => 1.6657123522982, 'qt' => 1, 'gal' => 0.25, 'l' => 0.94655886412936, 'lt' => 0.94655886412936), 'gal' => array('tsp' => 768, 'tbs' => 256, 'oz' => 128, 'cup' => 16, 'pt' => 8, 'us_pt' => 8, 'uk_pt' => 6.6628494091927, 'qt' => 4, 'gal' => 1, 'l' => 3.7862354565175, 'lt' => 3.7862354565175), 'l' => array('tsp' => 202.84, 'tbs' => 67.613333333333, 'oz' => 33.806666666667, 'cup' => 4.2258333333333, 'pt' => 2.1129166666667, 'us_pt' => 2.1129166666667, 'uk_pt' => 1.7597556955217, 'qt' => 1.0564583333333, 'gal' => 0.26411458333333, 'l' => 1, 'lt' => 1), 'lt' => array('tsp' => 202.84, 'tbs' => 67.613333333333, 'oz' => 33.806666666667, 'cup' => 4.2258333333333, 'pt' => 2.1129166666667, 'us_pt' => 2.1129166666667, 'uk_pt' => 1.7597556955217, 'qt' => 1.0564583333333, 'gal' => 0.26411458333333, 'l' => 1, 'lt' => 1)));
    private static $_two_sqrtpi = 1.1283791670955;
    private static $_one_sqrtpi = 0.5641895835477601;
    public static function _parseComplex($complexNumber)
    {
        $workString = (string) $complexNumber;
        $realNumber = $imaginary = 0;
        $suffix = substr($workString, -1);
        if (!is_numeric($suffix)) {
            $workString = substr($workString, 0, -1);
        } else {
            $suffix = '';
        }
        $leadingSign = 0;
        if (0 < strlen($workString)) {
            $leadingSign = $workString[0] == '+' || $workString[0] == '-' ? 1 : 0;
        }
        $power = '';
        $realNumber = strtok($workString, '+-');
        if (strtoupper(substr($realNumber, -1)) == 'E') {
            $power = strtok('+-');
            ++$leadingSign;
        }
        $realNumber = substr($workString, 0, strlen($realNumber) + strlen($power) + $leadingSign);
        if ($suffix != '') {
            $imaginary = substr($workString, strlen($realNumber));
            if ($imaginary == '' && ($realNumber == '' || $realNumber == '+' || $realNumber == '-')) {
                $imaginary = $realNumber . '1';
                $realNumber = '0';
            } else {
                if ($imaginary == '') {
                    $imaginary = $realNumber;
                    $realNumber = '0';
                } else {
                    if ($imaginary == '+' || $imaginary == '-') {
                        $imaginary .= '1';
                    }
                }
            }
        }
        return array('real' => $realNumber, 'imaginary' => $imaginary, 'suffix' => $suffix);
    }
    private static function _cleanComplex($complexNumber)
    {
        if ($complexNumber[0] == '+') {
            $complexNumber = substr($complexNumber, 1);
        }
        if ($complexNumber[0] == '0') {
            $complexNumber = substr($complexNumber, 1);
        }
        if ($complexNumber[0] == '.') {
            $complexNumber = '0' . $complexNumber;
        }
        if ($complexNumber[0] == '+') {
            $complexNumber = substr($complexNumber, 1);
        }
        return $complexNumber;
    }
    private static function _nbrConversionFormat($xVal, $places)
    {
        if (!is_null($places)) {
            if (strlen($xVal) <= $places) {
                return substr(str_pad($xVal, $places, '0', STR_PAD_LEFT), -10);
            } else {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        return substr($xVal, -10);
    }
    public static function BESSELI($x, $ord)
    {
        $x = is_null($x) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $ord = is_null($ord) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($ord);
        if (is_numeric($x) && is_numeric($ord)) {
            $ord = floor($ord);
            if ($ord < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            if (abs($x) <= 30) {
                $fResult = $fTerm = pow($x / 2, $ord) / PHPExcel_Calculation_MathTrig::FACT($ord);
                $ordK = 1;
                $fSqrX = $x * $x / 4;
                do {
                    $fTerm *= $fSqrX;
                    $fTerm /= $ordK * ($ordK + $ord);
                    $fResult += $fTerm;
                } while (1.0E-12 < abs($fTerm) && ++$ordK < 100);
            } else {
                $f_2_PI = 2 * M_PI;
                $fXAbs = abs($x);
                $fResult = exp($fXAbs) / sqrt($f_2_PI * $fXAbs);
                if ($ord & 1 && $x < 0) {
                    $fResult = -$fResult;
                }
            }
            return is_nan($fResult) ? PHPExcel_Calculation_Functions::NaN() : $fResult;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function BESSELJ($x, $ord)
    {
        $x = is_null($x) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $ord = is_null($ord) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($ord);
        if (is_numeric($x) && is_numeric($ord)) {
            $ord = floor($ord);
            if ($ord < 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            $fResult = 0;
            if (abs($x) <= 30) {
                $fResult = $fTerm = pow($x / 2, $ord) / PHPExcel_Calculation_MathTrig::FACT($ord);
                $ordK = 1;
                $fSqrX = $x * $x / -4;
                do {
                    $fTerm *= $fSqrX;
                    $fTerm /= $ordK * ($ordK + $ord);
                    $fResult += $fTerm;
                } while (1.0E-12 < abs($fTerm) && ++$ordK < 100);
            } else {
                $f_PI_DIV_2 = M_PI / 2;
                $f_PI_DIV_4 = M_PI / 4;
                $fXAbs = abs($x);
                $fResult = sqrt(M_2DIVPI / $fXAbs) * cos($fXAbs - $ord * $f_PI_DIV_2 - $f_PI_DIV_4);
                if ($ord & 1 && $x < 0) {
                    $fResult = -$fResult;
                }
            }
            return is_nan($fResult) ? PHPExcel_Calculation_Functions::NaN() : $fResult;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    private static function _Besselk0($fNum)
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = $fNum2 * $fNum2;
            $fRet = -log($fNum2) * self::BESSELI($fNum, 0) + -0.57721566 + $y * (0.4227842 + $y * (0.23069756 + $y * (0.0348859 + $y * (0.00262698 + $y * (0.0001075 + $y * 7.4E-6)))));
        } else {
            $y = 2 / $fNum;
            $fRet = exp(-$fNum) / sqrt($fNum) * (1.25331414 + $y * (-0.07832358 + $y * (0.02189568 + $y * (-0.01062446 + $y * (0.00587872 + $y * (-0.0025154 + $y * 0.00053208))))));
        }
        return $fRet;
    }
    private static function _Besselk1($fNum)
    {
        if ($fNum <= 2) {
            $fNum2 = $fNum * 0.5;
            $y = $fNum2 * $fNum2;
            $fRet = log($fNum2) * self::BESSELI($fNum, 1) + (1 + $y * (0.15443144 + $y * (-0.6727857900000001 + $y * (-0.18156897 + $y * (-0.01919402 + $y * (-0.00110404 + $y * -4.686E-5)))))) / $fNum;
        } else {
            $y = 2 / $fNum;
            $fRet = exp(-$fNum) / sqrt($fNum) * (1.25331414 + $y * (0.23498619 + $y * (-0.0365562 + $y * (0.01504268 + $y * (-0.00780353 + $y * (0.00325614 + $y * -0.00068245))))));
        }
        return $fRet;
    }
    public static function BESSELK($x, $ord)
    {
        $x = is_null($x) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $ord = is_null($ord) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($ord);
        if (is_numeric($x) && is_numeric($ord)) {
            if ($ord < 0 || $x == 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            switch (floor($ord)) {
                case 0:
                    return self::_Besselk0($x);
                    break;
                case 1:
                    return self::_Besselk1($x);
                    break;
                default:
                    $fTox = 2 / $x;
                    $fBkm = self::_Besselk0($x);
                    $fBk = self::_Besselk1($x);
                    for ($n = 1; $n < $ord; ++$n) {
                        $fBkp = $fBkm + $n * $fTox * $fBk;
                        $fBkm = $fBk;
                        $fBk = $fBkp;
                    }
            }
            return is_nan($fBk) ? PHPExcel_Calculation_Functions::NaN() : $fBk;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    private static function _Bessely0($fNum)
    {
        if ($fNum < 8) {
            $y = $fNum * $fNum;
            $f1 = -2957821389.0 + $y * (7062834065.0 + $y * (-512359803.6 + $y * (10879881.29 + $y * (-86327.92757 + $y * 228.4622733))));
            $f2 = 40076544269.0 + $y * (745249964.8 + $y * (7189466.438 + $y * (47447.2647 + $y * (226.1030244 + $y))));
            $fRet = $f1 / $f2 + 0.636619772 * self::BESSELJ($fNum, 0) * log($fNum);
        } else {
            $z = 8 / $fNum;
            $y = $z * $z;
            $xx = $fNum - 0.785398164;
            $f1 = 1 + $y * (-0.001098628627 + $y * (2.734510407E-5 + $y * (-2.073370639E-6 + $y * 2.093887211E-7)));
            $f2 = -0.01562499995 + $y * (0.0001430488765 + $y * (-6.911147651E-6 + $y * (7.621095161000001E-7 + $y * -9.34945152E-8)));
            $fRet = sqrt(0.636619772 / $fNum) * (sin($xx) * $f1 + $z * cos($xx) * $f2);
        }
        return $fRet;
    }
    private static function _Bessely1($fNum)
    {
        if ($fNum < 8) {
            $y = $fNum * $fNum;
            $f1 = $fNum * (-4900604943000.0 + $y * (1275274390000.0 + $y * (-51534381390.0 + $y * (734926455.1 + $y * (-4237922.726 + $y * 8511.937935)))));
            $f2 = 24995805700000.0 + $y * (424441966400.0 + $y * (3733650367.0 + $y * (22459040.02 + $y * (102042.605 + $y * (354.9632885 + $y)))));
            $fRet = $f1 / $f2 + 0.636619772 * (self::BESSELJ($fNum, 1) * log($fNum) - 1 / $fNum);
        } else {
            $fRet = sqrt(0.636619772 / $fNum) * sin($fNum - 2.356194491);
        }
        return $fRet;
    }
    public static function BESSELY($x, $ord)
    {
        $x = is_null($x) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $ord = is_null($ord) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($ord);
        if (is_numeric($x) && is_numeric($ord)) {
            if ($ord < 0 || $x == 0) {
                return PHPExcel_Calculation_Functions::NaN();
            }
            switch (floor($ord)) {
                case 0:
                    return self::_Bessely0($x);
                    break;
                case 1:
                    return self::_Bessely1($x);
                    break;
                default:
                    $fTox = 2 / $x;
                    $fBym = self::_Bessely0($x);
                    $fBy = self::_Bessely1($x);
                    for ($n = 1; $n < $ord; ++$n) {
                        $fByp = $n * $fTox * $fBy - $fBym;
                        $fBym = $fBy;
                        $fBy = $fByp;
                    }
            }
            return is_nan($fBy) ? PHPExcel_Calculation_Functions::NaN() : $fBy;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function BINTODEC($x)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        if (is_bool($x)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (preg_match_all('/[01]/', $x, $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if (10 < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        } else {
            if (strlen($x) == 10) {
                $x = substr($x, -9);
                return '-' . (512 - bindec($x));
            }
        }
        return bindec($x);
    }
    public static function BINTOHEX($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (preg_match_all('/[01]/', $x, $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if (10 < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        } else {
            if (strlen($x) == 10) {
                return str_repeat('F', 8) . substr(strtoupper(dechex(bindec(substr($x, -9)))), -2);
            }
        }
        $hexVal = (string) strtoupper(dechex(bindec($x)));
        return self::_nbrConversionFormat($hexVal, $places);
    }
    public static function BINTOOCT($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_GNUMERIC) {
            $x = floor($x);
        }
        $x = (string) $x;
        if (preg_match_all('/[01]/', $x, $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if (10 < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        } else {
            if (strlen($x) == 10) {
                return str_repeat('7', 7) . substr(strtoupper(decoct(bindec(substr($x, -9)))), -3);
            }
        }
        $octVal = (string) decoct(bindec($x));
        return self::_nbrConversionFormat($octVal, $places);
    }
    public static function DECTOBIN($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (preg_match_all('/[-0123456789.]/', $x, $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) floor($x);
        $r = decbin($x);
        if (strlen($r) == 32) {
            $r = substr($r, -10);
        } else {
            if (11 < strlen($r)) {
                return PHPExcel_Calculation_Functions::NaN();
            }
        }
        return self::_nbrConversionFormat($r, $places);
    }
    public static function DECTOHEX($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (preg_match_all('/[-0123456789.]/', $x, $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) floor($x);
        $r = strtoupper(dechex($x));
        if (strlen($r) == 8) {
            $r = 'FF' . $r;
        }
        return self::_nbrConversionFormat($r, $places);
    }
    public static function DECTOOCT($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            if (PHPExcel_Calculation_Functions::getCompatibilityMode() == PHPExcel_Calculation_Functions::COMPATIBILITY_OPENOFFICE) {
                $x = (int) $x;
            } else {
                return PHPExcel_Calculation_Functions::VALUE();
            }
        }
        $x = (string) $x;
        if (preg_match_all('/[-0123456789.]/', $x, $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) floor($x);
        $r = decoct($x);
        if (strlen($r) == 11) {
            $r = substr($r, -10);
        }
        return self::_nbrConversionFormat($r, $places);
    }
    public static function HEXTOBIN($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $binVal = decbin(hexdec($x));
        return substr(self::_nbrConversionFormat($binVal, $places), -10);
    }
    public static function HEXTODEC($x)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        if (is_bool($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return hexdec($x);
    }
    public static function HEXTOOCT($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[0123456789ABCDEF]/', strtoupper($x), $out) < strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $octVal = decoct(hexdec($x));
        return self::_nbrConversionFormat($octVal, $places);
    }
    public static function OCTTOBIN($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $r = decbin(octdec($x));
        return self::_nbrConversionFormat($r, $places);
    }
    public static function OCTTODEC($x)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        if (is_bool($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        return octdec($x);
    }
    public static function OCTTOHEX($x, $places = NULL)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        $places = PHPExcel_Calculation_Functions::flattenSingleValue($places);
        if (is_bool($x)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $x = (string) $x;
        if (preg_match_all('/[01234567]/', $x, $out) != strlen($x)) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $hexVal = strtoupper(dechex(octdec($x)));
        return self::_nbrConversionFormat($hexVal, $places);
    }
    public static function COMPLEX($realNumber = 0, $imaginary = 0, $suffix = 'i')
    {
        $realNumber = is_null($realNumber) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($realNumber);
        $imaginary = is_null($imaginary) ? 0 : PHPExcel_Calculation_Functions::flattenSingleValue($imaginary);
        $suffix = is_null($suffix) ? 'i' : PHPExcel_Calculation_Functions::flattenSingleValue($suffix);
        if (is_numeric($realNumber) && is_numeric($imaginary) && ($suffix == 'i' || $suffix == 'j' || $suffix == '')) {
            $realNumber = (double) $realNumber;
            $imaginary = (double) $imaginary;
            if ($suffix == '') {
                $suffix = 'i';
            }
            if ($realNumber == 0) {
                if ($imaginary == 0) {
                    return (string) '0';
                } else {
                    if ($imaginary == 1) {
                        return (string) $suffix;
                    } else {
                        if ($imaginary == -1) {
                            return (string) '-' . $suffix;
                        }
                    }
                }
                return (string) $imaginary . $suffix;
            } else {
                if ($imaginary == 0) {
                    return (string) $realNumber;
                } else {
                    if ($imaginary == 1) {
                        return (string) $realNumber . '+' . $suffix;
                    } else {
                        if ($imaginary == -1) {
                            return (string) $realNumber . '-' . $suffix;
                        }
                    }
                }
            }
            if (0 < $imaginary) {
                $imaginary = (string) '+' . $imaginary;
            }
            return (string) $realNumber . $imaginary . $suffix;
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function IMAGINARY($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        return $parsedComplex['imaginary'];
    }
    public static function IMREAL($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        return $parsedComplex['real'];
    }
    public static function IMABS($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        return sqrt($parsedComplex['real'] * $parsedComplex['real'] + $parsedComplex['imaginary'] * $parsedComplex['imaginary']);
    }
    public static function IMARGUMENT($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        if ($parsedComplex['real'] == 0) {
            if ($parsedComplex['imaginary'] == 0) {
                return 0;
            } else {
                if ($parsedComplex['imaginary'] < 0) {
                    return M_PI / -2;
                } else {
                    return M_PI / 2;
                }
            }
        } else {
            if (0 < $parsedComplex['real']) {
                return atan($parsedComplex['imaginary'] / $parsedComplex['real']);
            } else {
                if ($parsedComplex['imaginary'] < 0) {
                    return -M_PI - atan(abs($parsedComplex['imaginary']) / abs($parsedComplex['real']));
                } else {
                    return M_PI - atan($parsedComplex['imaginary'] / abs($parsedComplex['real']));
                }
            }
        }
    }
    public static function IMCONJUGATE($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        if ($parsedComplex['imaginary'] == 0) {
            return $parsedComplex['real'];
        } else {
            return self::_cleanComplex(self::COMPLEX($parsedComplex['real'], -$parsedComplex['imaginary'], $parsedComplex['suffix']));
        }
    }
    public static function IMCOS($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        if ($parsedComplex['imaginary'] == 0) {
            return cos($parsedComplex['real']);
        } else {
            return self::IMCONJUGATE(self::COMPLEX(cos($parsedComplex['real']) * cosh($parsedComplex['imaginary']), sin($parsedComplex['real']) * sinh($parsedComplex['imaginary']), $parsedComplex['suffix']));
        }
    }
    public static function IMSIN($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        if ($parsedComplex['imaginary'] == 0) {
            return sin($parsedComplex['real']);
        } else {
            return self::COMPLEX(sin($parsedComplex['real']) * cosh($parsedComplex['imaginary']), cos($parsedComplex['real']) * sinh($parsedComplex['imaginary']), $parsedComplex['suffix']);
        }
    }
    public static function IMSQRT($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        $theta = self::IMARGUMENT($complexNumber);
        $d1 = cos($theta / 2);
        $d2 = sin($theta / 2);
        $r = sqrt(sqrt($parsedComplex['real'] * $parsedComplex['real'] + $parsedComplex['imaginary'] * $parsedComplex['imaginary']));
        if ($parsedComplex['suffix'] == '') {
            return self::COMPLEX($d1 * $r, $d2 * $r);
        } else {
            return self::COMPLEX($d1 * $r, $d2 * $r, $parsedComplex['suffix']);
        }
    }
    public static function IMLN($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        if ($parsedComplex['real'] == 0 && $parsedComplex['imaginary'] == 0) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        $logR = log(sqrt($parsedComplex['real'] * $parsedComplex['real'] + $parsedComplex['imaginary'] * $parsedComplex['imaginary']));
        $t = self::IMARGUMENT($complexNumber);
        if ($parsedComplex['suffix'] == '') {
            return self::COMPLEX($logR, $t);
        } else {
            return self::COMPLEX($logR, $t, $parsedComplex['suffix']);
        }
    }
    public static function IMLOG10($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        if ($parsedComplex['real'] == 0 && $parsedComplex['imaginary'] == 0) {
            return PHPExcel_Calculation_Functions::NaN();
        } else {
            if (0 < $parsedComplex['real'] && $parsedComplex['imaginary'] == 0) {
                return log10($parsedComplex['real']);
            }
        }
        return self::IMPRODUCT(log10(EULER), self::IMLN($complexNumber));
    }
    public static function IMLOG2($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        if ($parsedComplex['real'] == 0 && $parsedComplex['imaginary'] == 0) {
            return PHPExcel_Calculation_Functions::NaN();
        } else {
            if (0 < $parsedComplex['real'] && $parsedComplex['imaginary'] == 0) {
                return log($parsedComplex['real'], 2);
            }
        }
        return self::IMPRODUCT(log(EULER, 2), self::IMLN($complexNumber));
    }
    public static function IMEXP($complexNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $parsedComplex = self::_parseComplex($complexNumber);
        if ($parsedComplex['real'] == 0 && $parsedComplex['imaginary'] == 0) {
            return '1';
        }
        $e = exp($parsedComplex['real']);
        $eX = $e * cos($parsedComplex['imaginary']);
        $eY = $e * sin($parsedComplex['imaginary']);
        if ($parsedComplex['suffix'] == '') {
            return self::COMPLEX($eX, $eY);
        } else {
            return self::COMPLEX($eX, $eY, $parsedComplex['suffix']);
        }
    }
    public static function IMPOWER($complexNumber, $realNumber)
    {
        $complexNumber = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber);
        $realNumber = PHPExcel_Calculation_Functions::flattenSingleValue($realNumber);
        if (!is_numeric($realNumber)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $parsedComplex = self::_parseComplex($complexNumber);
        $r = sqrt($parsedComplex['real'] * $parsedComplex['real'] + $parsedComplex['imaginary'] * $parsedComplex['imaginary']);
        $rPower = pow($r, $realNumber);
        $theta = self::IMARGUMENT($complexNumber) * $realNumber;
        if ($theta == 0) {
            return 1;
        } else {
            if ($parsedComplex['imaginary'] == 0) {
                return self::COMPLEX($rPower * cos($theta), $rPower * sin($theta), $parsedComplex['suffix']);
            } else {
                return self::COMPLEX($rPower * cos($theta), $rPower * sin($theta), $parsedComplex['suffix']);
            }
        }
    }
    public static function IMDIV($complexDividend, $complexDivisor)
    {
        $complexDividend = PHPExcel_Calculation_Functions::flattenSingleValue($complexDividend);
        $complexDivisor = PHPExcel_Calculation_Functions::flattenSingleValue($complexDivisor);
        $parsedComplexDividend = self::_parseComplex($complexDividend);
        $parsedComplexDivisor = self::_parseComplex($complexDivisor);
        if ($parsedComplexDividend['suffix'] != '' && $parsedComplexDivisor['suffix'] != '' && $parsedComplexDividend['suffix'] != $parsedComplexDivisor['suffix']) {
            return PHPExcel_Calculation_Functions::NaN();
        }
        if ($parsedComplexDividend['suffix'] != '' && $parsedComplexDivisor['suffix'] == '') {
            $parsedComplexDivisor['suffix'] = $parsedComplexDividend['suffix'];
        }
        $d1 = $parsedComplexDividend['real'] * $parsedComplexDivisor['real'] + $parsedComplexDividend['imaginary'] * $parsedComplexDivisor['imaginary'];
        $d2 = $parsedComplexDividend['imaginary'] * $parsedComplexDivisor['real'] - $parsedComplexDividend['real'] * $parsedComplexDivisor['imaginary'];
        $d3 = $parsedComplexDivisor['real'] * $parsedComplexDivisor['real'] + $parsedComplexDivisor['imaginary'] * $parsedComplexDivisor['imaginary'];
        $r = $d1 / $d3;
        $i = $d2 / $d3;
        if (0 < $i) {
            return self::_cleanComplex($r . '+' . $i . $parsedComplexDivisor['suffix']);
        } else {
            if ($i < 0) {
                return self::_cleanComplex($r . $i . $parsedComplexDivisor['suffix']);
            } else {
                return $r;
            }
        }
    }
    public static function IMSUB($complexNumber1, $complexNumber2)
    {
        $complexNumber1 = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber1);
        $complexNumber2 = PHPExcel_Calculation_Functions::flattenSingleValue($complexNumber2);
        $parsedComplex1 = self::_parseComplex($complexNumber1);
        $parsedComplex2 = self::_parseComplex($complexNumber2);
        if ($parsedComplex1['suffix'] != '' && $parsedComplex2['suffix'] != '' && $parsedComplex1['suffix'] != $parsedComplex2['suffix']) {
            return PHPExcel_Calculation_Functions::NaN();
        } else {
            if ($parsedComplex1['suffix'] == '' && $parsedComplex2['suffix'] != '') {
                $parsedComplex1['suffix'] = $parsedComplex2['suffix'];
            }
        }
        $d1 = $parsedComplex1['real'] - $parsedComplex2['real'];
        $d2 = $parsedComplex1['imaginary'] - $parsedComplex2['imaginary'];
        return self::COMPLEX($d1, $d2, $parsedComplex1['suffix']);
    }
    public static function IMSUM()
    {
        $returnValue = self::_parseComplex('0');
        $activeSuffix = '';
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            $parsedComplex = self::_parseComplex($arg);
            if ($activeSuffix == '') {
                $activeSuffix = $parsedComplex['suffix'];
            } else {
                if ($parsedComplex['suffix'] != '' && $activeSuffix != $parsedComplex['suffix']) {
                    return PHPExcel_Calculation_Functions::VALUE();
                }
            }
            $returnValue['real'] += $parsedComplex['real'];
            $returnValue['imaginary'] += $parsedComplex['imaginary'];
        }
        if ($returnValue['imaginary'] == 0) {
            $activeSuffix = '';
        }
        return self::COMPLEX($returnValue['real'], $returnValue['imaginary'], $activeSuffix);
    }
    public static function IMPRODUCT()
    {
        $returnValue = self::_parseComplex('1');
        $activeSuffix = '';
        $aArgs = PHPExcel_Calculation_Functions::flattenArray(func_get_args());
        foreach ($aArgs as $arg) {
            $parsedComplex = self::_parseComplex($arg);
            $workValue = $returnValue;
            if ($parsedComplex['suffix'] != '' && $activeSuffix == '') {
                $activeSuffix = $parsedComplex['suffix'];
            } else {
                if ($parsedComplex['suffix'] != '' && $activeSuffix != $parsedComplex['suffix']) {
                    return PHPExcel_Calculation_Functions::NaN();
                }
            }
            $returnValue['real'] = $workValue['real'] * $parsedComplex['real'] - $workValue['imaginary'] * $parsedComplex['imaginary'];
            $returnValue['imaginary'] = $workValue['real'] * $parsedComplex['imaginary'] + $workValue['imaginary'] * $parsedComplex['real'];
        }
        if ($returnValue['imaginary'] == 0) {
            $activeSuffix = '';
        }
        return self::COMPLEX($returnValue['real'], $returnValue['imaginary'], $activeSuffix);
    }
    public static function DELTA($a, $b = 0)
    {
        $a = PHPExcel_Calculation_Functions::flattenSingleValue($a);
        $b = PHPExcel_Calculation_Functions::flattenSingleValue($b);
        return (int) $a == $b;
    }
    public static function GESTEP($number, $step = 0)
    {
        $number = PHPExcel_Calculation_Functions::flattenSingleValue($number);
        $step = PHPExcel_Calculation_Functions::flattenSingleValue($step);
        return (int) $step <= $number;
    }
    public static function _erfVal($x)
    {
        if (2.2 < abs($x)) {
            return 1 - self::_erfcVal($x);
        }
        $sum = $term = $x;
        $xsqr = $x * $x;
        $j = 1;
        do {
            $term *= $xsqr / $j;
            $sum -= $term / (2 * $j + 1);
            ++$j;
            $term *= $xsqr / $j;
            $sum += $term / (2 * $j + 1);
            ++$j;
            if ($sum == 0) {
                break;
            }
        } while (PRECISION < abs($term / $sum));
        return self::$_two_sqrtpi * $sum;
    }
    public static function ERF($lower, $upper = NULL)
    {
        $lower = PHPExcel_Calculation_Functions::flattenSingleValue($lower);
        $upper = PHPExcel_Calculation_Functions::flattenSingleValue($upper);
        if (is_numeric($lower)) {
            if (is_null($upper)) {
                return self::_erfVal($lower);
            }
            if (is_numeric($upper)) {
                return self::_erfVal($upper) - self::_erfVal($lower);
            }
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    private static function _erfcVal($x)
    {
        if (abs($x) < 2.2) {
            return 1 - self::_erfVal($x);
        }
        if ($x < 0) {
            return 2 - self::ERFC(-$x);
        }
        $a = $n = 1;
        $b = $c = $x;
        $d = $x * $x + 0.5;
        $q1 = $q2 = $b / $d;
        $t = 0;
        do {
            $t = $a * $n + $b * $x;
            $a = $b;
            $b = $t;
            $t = $c * $n + $d * $x;
            $c = $d;
            $d = $t;
            $n += 0.5;
            $q1 = $q2;
            $q2 = $b / $d;
        } while (PRECISION < abs($q1 - $q2) / $q2);
        return self::$_one_sqrtpi * exp(-$x * $x) * $q2;
    }
    public static function ERFC($x)
    {
        $x = PHPExcel_Calculation_Functions::flattenSingleValue($x);
        if (is_numeric($x)) {
            return self::_erfcVal($x);
        }
        return PHPExcel_Calculation_Functions::VALUE();
    }
    public static function getConversionGroups()
    {
        $conversionGroups = array();
        foreach (self::$_conversionUnits as $conversionUnit) {
            $conversionGroups[] = $conversionUnit['Group'];
        }
        return array_merge(array_unique($conversionGroups));
    }
    public static function getConversionGroupUnits($group = NULL)
    {
        $conversionGroups = array();
        foreach (self::$_conversionUnits as $conversionUnit => $conversionGroup) {
            if (is_null($group) || $conversionGroup['Group'] == $group) {
                $conversionGroups[$conversionGroup['Group']][] = $conversionUnit;
            }
        }
        return $conversionGroups;
    }
    public static function getConversionGroupUnitDetails($group = NULL)
    {
        $conversionGroups = array();
        foreach (self::$_conversionUnits as $conversionUnit => $conversionGroup) {
            if (is_null($group) || $conversionGroup['Group'] == $group) {
                $conversionGroups[$conversionGroup['Group']][] = array('unit' => $conversionUnit, 'description' => $conversionGroup['Unit Name']);
            }
        }
        return $conversionGroups;
    }
    public static function getConversionMultipliers()
    {
        return self::$_conversionMultipliers;
    }
    public static function CONVERTUOM($value, $fromUOM, $toUOM)
    {
        $value = PHPExcel_Calculation_Functions::flattenSingleValue($value);
        $fromUOM = PHPExcel_Calculation_Functions::flattenSingleValue($fromUOM);
        $toUOM = PHPExcel_Calculation_Functions::flattenSingleValue($toUOM);
        if (!is_numeric($value)) {
            return PHPExcel_Calculation_Functions::VALUE();
        }
        $fromMultiplier = 1;
        if (isset(self::$_conversionUnits[$fromUOM])) {
            $unitGroup1 = self::$_conversionUnits[$fromUOM]['Group'];
        } else {
            $fromMultiplier = substr($fromUOM, 0, 1);
            $fromUOM = substr($fromUOM, 1);
            if (isset(self::$_conversionMultipliers[$fromMultiplier])) {
                $fromMultiplier = self::$_conversionMultipliers[$fromMultiplier]['multiplier'];
            } else {
                return PHPExcel_Calculation_Functions::NA();
            }
            if (isset(self::$_conversionUnits[$fromUOM]) && self::$_conversionUnits[$fromUOM]['AllowPrefix']) {
                $unitGroup1 = self::$_conversionUnits[$fromUOM]['Group'];
            } else {
                return PHPExcel_Calculation_Functions::NA();
            }
        }
        $value *= $fromMultiplier;
        $toMultiplier = 1;
        if (isset(self::$_conversionUnits[$toUOM])) {
            $unitGroup2 = self::$_conversionUnits[$toUOM]['Group'];
        } else {
            $toMultiplier = substr($toUOM, 0, 1);
            $toUOM = substr($toUOM, 1);
            if (isset(self::$_conversionMultipliers[$toMultiplier])) {
                $toMultiplier = self::$_conversionMultipliers[$toMultiplier]['multiplier'];
            } else {
                return PHPExcel_Calculation_Functions::NA();
            }
            if (isset(self::$_conversionUnits[$toUOM]) && self::$_conversionUnits[$toUOM]['AllowPrefix']) {
                $unitGroup2 = self::$_conversionUnits[$toUOM]['Group'];
            } else {
                return PHPExcel_Calculation_Functions::NA();
            }
        }
        if ($unitGroup1 != $unitGroup2) {
            return PHPExcel_Calculation_Functions::NA();
        }
        if ($fromUOM == $toUOM && $fromMultiplier == $toMultiplier) {
            return $value / $fromMultiplier;
        } else {
            if ($unitGroup1 == 'Temperature') {
                if ($fromUOM == 'F' || $fromUOM == 'fah') {
                    if ($toUOM == 'F' || $toUOM == 'fah') {
                        return $value;
                    } else {
                        $value = ($value - 32) / 1.8;
                        if ($toUOM == 'K' || $toUOM == 'kel') {
                            $value += 273.15;
                        }
                        return $value;
                    }
                } else {
                    if (($fromUOM == 'K' || $fromUOM == 'kel') && ($toUOM == 'K' || $toUOM == 'kel')) {
                        return $value;
                    } else {
                        if (($fromUOM == 'C' || $fromUOM == 'cel') && ($toUOM == 'C' || $toUOM == 'cel')) {
                            return $value;
                        }
                    }
                }
                if ($toUOM == 'F' || $toUOM == 'fah') {
                    if ($fromUOM == 'K' || $fromUOM == 'kel') {
                        $value -= 273.15;
                    }
                    return $value * 1.8 + 32;
                }
                if ($toUOM == 'C' || $toUOM == 'cel') {
                    return $value - 273.15;
                }
                return $value + 273.15;
            }
        }
        return $value * self::$_unitConversions[$unitGroup1][$fromUOM][$toUOM] / $toMultiplier;
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
define('EULER', 2.718281828459);
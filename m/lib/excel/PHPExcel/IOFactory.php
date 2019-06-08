<?php

class PHPExcel_IOFactory
{
    /**
     * Search locations
     *
     * @var	array
     * @access	private
     * @static
     */
    private static $_searchLocations = array(array('type' => 'IWriter', 'path' => 'PHPExcel/Writer/{0}.php', 'class' => 'PHPExcel_Writer_{0}'), array('type' => 'IReader', 'path' => 'PHPExcel/Reader/{0}.php', 'class' => 'PHPExcel_Reader_{0}'));
    /**
     * Autoresolve classes
     *
     * @var	array
     * @access	private
     * @static
     */
    private static $_autoResolveClasses = array('Excel2007', 'Excel5', 'Excel2003XML', 'OOCalc', 'SYLK', 'Gnumeric', 'HTML', 'CSV');
    private function __construct()
    {
    }
    public static function getSearchLocations()
    {
        return self::$_searchLocations;
    }
    public static function setSearchLocations($value)
    {
        if (is_array($value)) {
            self::$_searchLocations = $value;
        } else {
            throw new PHPExcel_Reader_Exception('Invalid parameter passed.');
        }
    }
    public static function addSearchLocation($type = '', $location = '', $classname = '')
    {
        self::$_searchLocations[] = array('type' => $type, 'path' => $location, 'class' => $classname);
    }
    public static function createWriter(PHPExcel $phpExcel, $writerType = '')
    {
        $searchType = 'IWriter';
        foreach (self::$_searchLocations as $searchLocation) {
            if ($searchLocation['type'] == $searchType) {
                $className = str_replace('{0}', $writerType, $searchLocation['class']);
                $instance = new $className($phpExcel);
                if ($instance !== NULL) {
                    return $instance;
                }
            }
        }
        throw new PHPExcel_Reader_Exception('No ' . $searchType . ' found for type ' . $writerType);
    }
    public static function createReader($readerType = '')
    {
        $searchType = 'IReader';
        foreach (self::$_searchLocations as $searchLocation) {
            if ($searchLocation['type'] == $searchType) {
                $className = str_replace('{0}', $readerType, $searchLocation['class']);
                $instance = new $className();
                if ($instance !== NULL) {
                    return $instance;
                }
            }
        }
        throw new PHPExcel_Reader_Exception('No ' . $searchType . ' found for type ' . $readerType);
    }
    public static function load($pFilename)
    {
        $reader = self::createReaderForFile($pFilename);
        return $reader->load($pFilename);
    }
    public static function identify($pFilename)
    {
        $reader = self::createReaderForFile($pFilename);
        $className = get_class($reader);
        $classType = explode('_', $className);
        unset($reader);
        return array_pop($classType);
    }
    public static function createReaderForFile($pFilename)
    {
        $pathinfo = pathinfo($pFilename);
        $extensionType = NULL;
        if (isset($pathinfo['extension'])) {
            switch (strtolower($pathinfo['extension'])) {
                case 'xlsx':
                case 'xlsm':
                case 'xltx':
                case 'xltm':
                    $extensionType = 'Excel2007';
                    break;
                case 'xls':
                case 'xlt':
                    $extensionType = 'Excel5';
                    break;
                case 'ods':
                case 'ots':
                    $extensionType = 'OOCalc';
                    break;
                case 'slk':
                    $extensionType = 'SYLK';
                    break;
                case 'xml':
                    $extensionType = 'Excel2003XML';
                    break;
                case 'gnumeric':
                    $extensionType = 'Gnumeric';
                    break;
                case 'htm':
                case 'html':
                    $extensionType = 'HTML';
                    break;
                case 'csv':
                    break;
                default:
                    break;
            }
            if ($extensionType !== NULL) {
                $reader = self::createReader($extensionType);
                if (isset($reader) && $reader->canRead($pFilename)) {
                    return $reader;
                }
            }
        }
        foreach (self::$_autoResolveClasses as $autoResolveClass) {
            if ($autoResolveClass !== $extensionType) {
                $reader = self::createReader($autoResolveClass);
                if ($reader->canRead($pFilename)) {
                    return $reader;
                }
            }
        }
        throw new PHPExcel_Reader_Exception('Unable to identify a reader for this file');
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
<?php

class PHPExcel_Settings
{
    const PCLZIP = 'PHPExcel_Shared_ZipArchive';
    const ZIPARCHIVE = 'ZipArchive';
    const CHART_RENDERER_JPGRAPH = 'jpgraph';
    const PDF_RENDERER_TCPDF = 'tcPDF';
    const PDF_RENDERER_DOMPDF = 'DomPDF';
    const PDF_RENDERER_MPDF = 'mPDF';
    private static $_chartRenderers = array(self::CHART_RENDERER_JPGRAPH);
    private static $_pdfRenderers = array(self::PDF_RENDERER_TCPDF, self::PDF_RENDERER_DOMPDF, self::PDF_RENDERER_MPDF);
    /**
     * Name of the class used for Zip file management
     *	e.g.
     *		ZipArchive
     *
     * @var string
     */
    private static $_zipClass = self::ZIPARCHIVE;
    /**
     * Name of the external Library used for rendering charts
     *	e.g.
     *		jpgraph
     *
     * @var string
     */
    private static $_chartRendererName;
    /**
     * Directory Path to the external Library used for rendering charts
     *
     * @var string
     */
    private static $_chartRendererPath;
    /**
     * Name of the external Library used for rendering PDF files
     *	e.g.
     * 		mPDF
     *
     * @var string
     */
    private static $_pdfRendererName;
    /**
     * Directory Path to the external Library used for rendering PDF files
     *
     * @var string
     */
    private static $_pdfRendererPath;
    /**
     * Default options for libxml loader
     *
     * @var int
     */
    private static $_libXmlLoaderOptions;
    public static function setZipClass($zipClass)
    {
        if ($zipClass === self::PCLZIP || $zipClass === self::ZIPARCHIVE) {
            self::$_zipClass = $zipClass;
            return true;
        }
        return false;
    }
    public static function getZipClass()
    {
        return self::$_zipClass;
    }
    public static function getCacheStorageMethod()
    {
        return PHPExcel_CachedObjectStorageFactory::getCacheStorageMethod();
    }
    public static function getCacheStorageClass()
    {
        return PHPExcel_CachedObjectStorageFactory::getCacheStorageClass();
    }
    public static function setCacheStorageMethod($method = PHPExcel_CachedObjectStorageFactory::cache_in_memory, $arguments = array())
    {
        return PHPExcel_CachedObjectStorageFactory::initialize($method, $arguments);
    }
    public static function setLocale($locale = 'en_us')
    {
        return PHPExcel_Calculation::getInstance()->setLocale($locale);
    }
    public static function setChartRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setChartRendererName($libraryName)) {
            return false;
        }
        return self::setChartRendererPath($libraryBaseDir);
    }
    public static function setChartRendererName($libraryName)
    {
        if (!in_array($libraryName, self::$_chartRenderers)) {
            return false;
        }
        self::$_chartRendererName = $libraryName;
        return true;
    }
    public static function setChartRendererPath($libraryBaseDir)
    {
        if (file_exists($libraryBaseDir) === false || is_readable($libraryBaseDir) === false) {
            return false;
        }
        self::$_chartRendererPath = $libraryBaseDir;
        return true;
    }
    public static function getChartRendererName()
    {
        return self::$_chartRendererName;
    }
    public static function getChartRendererPath()
    {
        return self::$_chartRendererPath;
    }
    public static function setPdfRenderer($libraryName, $libraryBaseDir)
    {
        if (!self::setPdfRendererName($libraryName)) {
            return false;
        }
        return self::setPdfRendererPath($libraryBaseDir);
    }
    public static function setPdfRendererName($libraryName)
    {
        if (!in_array($libraryName, self::$_pdfRenderers)) {
            return false;
        }
        self::$_pdfRendererName = $libraryName;
        return true;
    }
    public static function setPdfRendererPath($libraryBaseDir)
    {
        if (file_exists($libraryBaseDir) === false || is_readable($libraryBaseDir) === false) {
            return false;
        }
        self::$_pdfRendererPath = $libraryBaseDir;
        return true;
    }
    public static function getPdfRendererName()
    {
        return self::$_pdfRendererName;
    }
    public static function getPdfRendererPath()
    {
        return self::$_pdfRendererPath;
    }
    public static function setLibXmlLoaderOptions($options = NULL)
    {
        if (is_null($options)) {
            $options = LIBXML_DTDLOAD | LIBXML_DTDATTR;
        }
        @libxml_disable_entity_loader($options == LIBXML_DTDLOAD | LIBXML_DTDATTR);
        self::$_libXmlLoaderOptions = $options;
    }
    public static function getLibXmlLoaderOptions()
    {
        if (is_null(self::$_libXmlLoaderOptions)) {
            self::setLibXmlLoaderOptions(LIBXML_DTDLOAD | LIBXML_DTDATTR);
        }
        @libxml_disable_entity_loader($options == LIBXML_DTDLOAD | LIBXML_DTDATTR);
        return self::$_libXmlLoaderOptions;
    }
}
if (!defined('PHPEXCEL_ROOT')) {
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../');
    require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}
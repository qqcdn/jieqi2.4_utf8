<?php

abstract class PHPExcel_Writer_Abstract implements PHPExcel_Writer_IWriter
{
	/**
	 * Write charts that are defined in the workbook?
	 * Identifies whether the Writer should write definitions for any charts that exist in the PHPExcel object;
	 *
	 * @var	boolean
	 */
	protected $_includeCharts = false;
	/**
	 * Pre-calculate formulas
	 * Forces PHPExcel to recalculate all formulae in a workbook when saving, so that the pre-calculated values are
	 *    immediately available to MS Excel or other office spreadsheet viewer when opening the file
	 *
	 * @var boolean
	 */
	protected $_preCalculateFormulas = true;
	/**
	 * Use disk caching where possible?
	 *
	 * @var boolean
	 */
	protected $_useDiskCaching = false;
	/**
	 * Disk caching directory
	 *
	 * @var string
	 */
	protected $_diskCachingDirectory = './';

	public function getIncludeCharts()
	{
		return $this->_includeCharts;
	}

	public function setIncludeCharts($pValue = false)
	{
		$this->_includeCharts = (bool) $pValue;
		return $this;
	}

	public function getPreCalculateFormulas()
	{
		return $this->_preCalculateFormulas;
	}

	public function setPreCalculateFormulas($pValue = true)
	{
		$this->_preCalculateFormulas = (bool) $pValue;
		return $this;
	}

	public function getUseDiskCaching()
	{
		return $this->_useDiskCaching;
	}

	public function setUseDiskCaching($pValue = false, $pDirectory = NULL)
	{
		$this->_useDiskCaching = $pValue;

		if ($pDirectory !== NULL) {
			if (is_dir($pDirectory)) {
				$this->_diskCachingDirectory = $pDirectory;
			}
			else {
				throw new PHPExcel_Writer_Exception('Directory does not exist: ' . $pDirectory);
			}
		}

		return $this;
	}

	public function getDiskCachingDirectory()
	{
		return $this->_diskCachingDirectory;
	}
}

?>

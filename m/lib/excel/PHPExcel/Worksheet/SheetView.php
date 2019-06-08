<?php

class PHPExcel_Worksheet_SheetView
{
	const SHEETVIEW_NORMAL = 'normal';
	const SHEETVIEW_PAGE_LAYOUT = 'pageLayout';
	const SHEETVIEW_PAGE_BREAK_PREVIEW = 'pageBreakPreview';

	static private $_sheetViewTypes = array(self::SHEETVIEW_NORMAL, self::SHEETVIEW_PAGE_LAYOUT, self::SHEETVIEW_PAGE_BREAK_PREVIEW);
	/**
	 * ZoomScale
	 *
	 * Valid values range from 10 to 400.
	 *
	 * @var int
	 */
	private $_zoomScale = 100;
	/**
	 * ZoomScaleNormal
	 *
	 * Valid values range from 10 to 400.
	 *
	 * @var int
	 */
	private $_zoomScaleNormal = 100;
	/**
	 * View
	 *
	 * Valid values range from 10 to 400.
	 *
	 * @var string
	 */
	private $_sheetviewType = self::SHEETVIEW_NORMAL;

	public function __construct()
	{
	}

	public function getZoomScale()
	{
		return $this->_zoomScale;
	}

	public function setZoomScale($pValue = 100)
	{
		if ((1 <= $pValue) || is_null($pValue)) {
			$this->_zoomScale = $pValue;
		}
		else {
			throw new PHPExcel_Exception('Scale must be greater than or equal to 1.');
		}

		return $this;
	}

	public function getZoomScaleNormal()
	{
		return $this->_zoomScaleNormal;
	}

	public function setZoomScaleNormal($pValue = 100)
	{
		if ((1 <= $pValue) || is_null($pValue)) {
			$this->_zoomScaleNormal = $pValue;
		}
		else {
			throw new PHPExcel_Exception('Scale must be greater than or equal to 1.');
		}

		return $this;
	}

	public function getView()
	{
		return $this->_sheetviewType;
	}

	public function setView($pValue = NULL)
	{
		if ($pValue === NULL) {
			$pValue = self::SHEETVIEW_NORMAL;
		}

		if (in_array($pValue, self::$_sheetViewTypes)) {
			$this->_sheetviewType = $pValue;
		}
		else {
			throw new PHPExcel_Exception('Invalid sheetview layout type.');
		}

		return $this;
	}

	public function __clone()
	{
		$vars = get_object_vars($this);

		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			}
			else {
				$this->$key = $value;
			}
		}
	}
}


?>

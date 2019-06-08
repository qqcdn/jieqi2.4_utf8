<?php

class PHPExcel_Worksheet_HeaderFooterDrawing extends PHPExcel_Worksheet_Drawing implements PHPExcel_IComparable
{
	/**
	 * Path
	 *
	 * @var string
	 */
	private $_path;
	/**
	 * Name
	 *
	 * @var string
	 */
	protected $_name;
	/**
	 * Offset X
	 *
	 * @var int
	 */
	protected $_offsetX;
	/**
	 * Offset Y
	 *
	 * @var int
	 */
	protected $_offsetY;
	/**
	 * Width
	 *
	 * @var int
	 */
	protected $_width;
	/**
	 * Height
	 *
	 * @var int
	 */
	protected $_height;
	/**
	 * Proportional resize
	 *
	 * @var boolean
	 */
	protected $_resizeProportional;

	public function __construct()
	{
		$this->_path = '';
		$this->_name = '';
		$this->_offsetX = 0;
		$this->_offsetY = 0;
		$this->_width = 0;
		$this->_height = 0;
		$this->_resizeProportional = true;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setName($pValue = '')
	{
		$this->_name = $pValue;
		return $this;
	}

	public function getOffsetX()
	{
		return $this->_offsetX;
	}

	public function setOffsetX($pValue = 0)
	{
		$this->_offsetX = $pValue;
		return $this;
	}

	public function getOffsetY()
	{
		return $this->_offsetY;
	}

	public function setOffsetY($pValue = 0)
	{
		$this->_offsetY = $pValue;
		return $this;
	}

	public function getWidth()
	{
		return $this->_width;
	}

	public function setWidth($pValue = 0)
	{
		if ($this->_resizeProportional && ($pValue != 0)) {
			$ratio = $this->_width / $this->_height;
			$this->_height = round($ratio * $pValue);
		}

		$this->_width = $pValue;
		return $this;
	}

	public function getHeight()
	{
		return $this->_height;
	}

	public function setHeight($pValue = 0)
	{
		if ($this->_resizeProportional && ($pValue != 0)) {
			$ratio = $this->_width / $this->_height;
			$this->_width = round($ratio * $pValue);
		}

		$this->_height = $pValue;
		return $this;
	}

	public function setWidthAndHeight($width = 0, $height = 0)
	{
		$xratio = $width / $this->_width;
		$yratio = $height / $this->_height;
		if ($this->_resizeProportional && !(($width == 0) || ($height == 0))) {
			if (($xratio * $this->_height) < $height) {
				$this->_height = ceil($xratio * $this->_height);
				$this->_width = $width;
			}
			else {
				$this->_width = ceil($yratio * $this->_width);
				$this->_height = $height;
			}
		}

		return $this;
	}

	public function getResizeProportional()
	{
		return $this->_resizeProportional;
	}

	public function setResizeProportional($pValue = true)
	{
		$this->_resizeProportional = $pValue;
		return $this;
	}

	public function getFilename()
	{
		return basename($this->_path);
	}

	public function getExtension()
	{
		$parts = explode('.', basename($this->_path));
		return end($parts);
	}

	public function getPath()
	{
		return $this->_path;
	}

	public function setPath($pValue = '', $pVerifyFile = true)
	{
		if ($pVerifyFile) {
			if (file_exists($pValue)) {
				$this->_path = $pValue;
				if (($this->_width == 0) && ($this->_height == 0)) {
					list($this->_width, $this->_height) = getimagesize($pValue);
				}
			}
			else {
				throw new PHPExcel_Exception('File ' . $pValue . ' not found!');
			}
		}
		else {
			$this->_path = $pValue;
		}

		return $this;
	}

	public function getHashCode()
	{
		return md5($this->_path . $this->_name . $this->_offsetX . $this->_offsetY . $this->_width . $this->_height . 'PHPExcel_Worksheet_HeaderFooterDrawing');
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

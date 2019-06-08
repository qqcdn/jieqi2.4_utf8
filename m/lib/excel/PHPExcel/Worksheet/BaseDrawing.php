<?php

class PHPExcel_Worksheet_BaseDrawing implements PHPExcel_IComparable
{
	/**
	 * Image counter
	 *
	 * @var int
	 */
	static private $_imageCounter = 0;
	/**
	 * Image index
	 *
	 * @var int
	 */
	private $_imageIndex = 0;
	/**
	 * Name
	 *
	 * @var string
	 */
	protected $_name;
	/**
	 * Description
	 *
	 * @var string
	 */
	protected $_description;
	/**
	 * Worksheet
	 *
	 * @var PHPExcel_Worksheet
	 */
	protected $_worksheet;
	/**
	 * Coordinates
	 *
	 * @var string
	 */
	protected $_coordinates;
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
	/**
	 * Rotation
	 *
	 * @var int
	 */
	protected $_rotation;
	/**
	 * Shadow
	 *
	 * @var PHPExcel_Worksheet_Drawing_Shadow
	 */
	protected $_shadow;

	public function __construct()
	{
		$this->_name = '';
		$this->_description = '';
		$this->_worksheet = NULL;
		$this->_coordinates = 'A1';
		$this->_offsetX = 0;
		$this->_offsetY = 0;
		$this->_width = 0;
		$this->_height = 0;
		$this->_resizeProportional = true;
		$this->_rotation = 0;
		$this->_shadow = new PHPExcel_Worksheet_Drawing_Shadow();
		self::$_imageCounter++;
		$this->_imageIndex = self::$_imageCounter;
	}

	public function getImageIndex()
	{
		return $this->_imageIndex;
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

	public function getDescription()
	{
		return $this->_description;
	}

	public function setDescription($pValue = '')
	{
		$this->_description = $pValue;
		return $this;
	}

	public function getWorksheet()
	{
		return $this->_worksheet;
	}

	public function setWorksheet(PHPExcel_Worksheet $pValue = NULL, $pOverrideOld = false)
	{
		if (is_null($this->_worksheet)) {
			$this->_worksheet = $pValue;
			$this->_worksheet->getCell($this->_coordinates);
			$this->_worksheet->getDrawingCollection()->append($this);
		}
		else if ($pOverrideOld) {
			$iterator = $this->_worksheet->getDrawingCollection()->getIterator();

			while ($iterator->valid()) {
				if ($iterator->current()->getHashCode() == $this->getHashCode()) {
					$this->_worksheet->getDrawingCollection()->offsetUnset($iterator->key());
					$this->_worksheet = NULL;
					break;
				}
			}

			$this->setWorksheet($pValue);
		}
		else {
			throw new PHPExcel_Exception('A PHPExcel_Worksheet has already been assigned. Drawings can only exist on one PHPExcel_Worksheet.');
		}

		return $this;
	}

	public function getCoordinates()
	{
		return $this->_coordinates;
	}

	public function setCoordinates($pValue = 'A1')
	{
		$this->_coordinates = $pValue;
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
			$ratio = $this->_height / $this->_width;
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

	public function getRotation()
	{
		return $this->_rotation;
	}

	public function setRotation($pValue = 0)
	{
		$this->_rotation = $pValue;
		return $this;
	}

	public function getShadow()
	{
		return $this->_shadow;
	}

	public function setShadow(PHPExcel_Worksheet_Drawing_Shadow $pValue = NULL)
	{
		$this->_shadow = $pValue;
		return $this;
	}

	public function getHashCode()
	{
		return md5($this->_name . $this->_description . $this->_worksheet->getHashCode() . $this->_coordinates . $this->_offsetX . $this->_offsetY . $this->_width . $this->_height . $this->_rotation . $this->_shadow->getHashCode() . 'PHPExcel_Worksheet_BaseDrawing');
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

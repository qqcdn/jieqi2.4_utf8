<?php

class PHPExcel_Worksheet_ColumnDimension
{
	/**
	 * Column index
	 *
	 * @var int
	 */
	private $_columnIndex;
	/**
	 * Column width
	 *
	 * When this is set to a negative value, the column width should be ignored by IWriter
	 *
	 * @var double
	 */
	private $_width = -1;
	/**
	 * Auto size?
	 *
	 * @var bool
	 */
	private $_autoSize = false;
	/**
	 * Visible?
	 *
	 * @var bool
	 */
	private $_visible = true;
	/**
	 * Outline level
	 *
	 * @var int
	 */
	private $_outlineLevel = 0;
	/**
	 * Collapsed
	 *
	 * @var bool
	 */
	private $_collapsed = false;
	/**
	 * Index to cellXf
	 *
	 * @var int
	 */
	private $_xfIndex;

	public function __construct($pIndex = 'A')
	{
		$this->_columnIndex = $pIndex;
		$this->_xfIndex = 0;
	}

	public function getColumnIndex()
	{
		return $this->_columnIndex;
	}

	public function setColumnIndex($pValue)
	{
		$this->_columnIndex = $pValue;
		return $this;
	}

	public function getWidth()
	{
		return $this->_width;
	}

	public function setWidth($pValue = -1)
	{
		$this->_width = $pValue;
		return $this;
	}

	public function getAutoSize()
	{
		return $this->_autoSize;
	}

	public function setAutoSize($pValue = false)
	{
		$this->_autoSize = $pValue;
		return $this;
	}

	public function getVisible()
	{
		return $this->_visible;
	}

	public function setVisible($pValue = true)
	{
		$this->_visible = $pValue;
		return $this;
	}

	public function getOutlineLevel()
	{
		return $this->_outlineLevel;
	}

	public function setOutlineLevel($pValue)
	{
		if (($pValue < 0) || (7 < $pValue)) {
			throw new PHPExcel_Exception('Outline level must range between 0 and 7.');
		}

		$this->_outlineLevel = $pValue;
		return $this;
	}

	public function getCollapsed()
	{
		return $this->_collapsed;
	}

	public function setCollapsed($pValue = true)
	{
		$this->_collapsed = $pValue;
		return $this;
	}

	public function getXfIndex()
	{
		return $this->_xfIndex;
	}

	public function setXfIndex($pValue = 0)
	{
		$this->_xfIndex = $pValue;
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

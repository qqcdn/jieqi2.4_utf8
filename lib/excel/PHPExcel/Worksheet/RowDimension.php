<?php

class PHPExcel_Worksheet_RowDimension
{
	/**
	 * Row index
	 *
	 * @var int
	 */
	private $_rowIndex;
	/**
	 * Row height (in pt)
	 *
	 * When this is set to a negative value, the row height should be ignored by IWriter
	 *
	 * @var double
	 */
	private $_rowHeight = -1;
	/**
	 * ZeroHeight for Row?
	 *
	 * @var bool
	 */
	private $_zeroHeight = false;
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
	 * Index to cellXf. Null value means row has no explicit cellXf format.
	 *
	 * @var int|null
	 */
	private $_xfIndex;

	public function __construct($pIndex = 0)
	{
		$this->_rowIndex = $pIndex;
		$this->_xfIndex = NULL;
	}

	public function getRowIndex()
	{
		return $this->_rowIndex;
	}

	public function setRowIndex($pValue)
	{
		$this->_rowIndex = $pValue;
		return $this;
	}

	public function getRowHeight()
	{
		return $this->_rowHeight;
	}

	public function setRowHeight($pValue = -1)
	{
		$this->_rowHeight = $pValue;
		return $this;
	}

	public function getzeroHeight()
	{
		return $this->_zeroHeight;
	}

	public function setzeroHeight($pValue = false)
	{
		$this->_zeroHeight = $pValue;
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

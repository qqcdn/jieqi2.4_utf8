<?php

class PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer
{
	/**
	 * Parent Shape Group Container
	 *
	 * @var PHPExcel_Shared_Escher_DgContainer_SpgrContainer
	 */
	private $_parent;
	/**
	 * Is this a group shape?
	 *
	 * @var boolean
	 */
	private $_spgr = false;
	/**
	 * Shape type
	 *
	 * @var int
	 */
	private $_spType;
	/**
	 * Shape flag
	 *
	 * @var int
	 */
	private $_spFlag;
	/**
	 * Shape index (usually group shape has index 0, and the rest: 1,2,3...)
	 *
	 * @var boolean
	 */
	private $_spId;
	/**
	 * Array of options
	 *
	 * @var array
	 */
	private $_OPT;
	/**
	 * Cell coordinates of upper-left corner of shape, e.g. 'A1'
	 *
	 * @var string
	 */
	private $_startCoordinates;
	/**
	 * Horizontal offset of upper-left corner of shape measured in 1/1024 of column width
	 *
	 * @var int
	 */
	private $_startOffsetX;
	/**
	 * Vertical offset of upper-left corner of shape measured in 1/256 of row height
	 *
	 * @var int
	 */
	private $_startOffsetY;
	/**
	 * Cell coordinates of bottom-right corner of shape, e.g. 'B2'
	 *
	 * @var string
	 */
	private $_endCoordinates;
	/**
	 * Horizontal offset of bottom-right corner of shape measured in 1/1024 of column width
	 *
	 * @var int
	 */
	private $_endOffsetX;
	/**
	 * Vertical offset of bottom-right corner of shape measured in 1/256 of row height
	 *
	 * @var int
	 */
	private $_endOffsetY;

	public function setParent($parent)
	{
		$this->_parent = $parent;
	}

	public function getParent()
	{
		return $this->_parent;
	}

	public function setSpgr($value = false)
	{
		$this->_spgr = $value;
	}

	public function getSpgr()
	{
		return $this->_spgr;
	}

	public function setSpType($value)
	{
		$this->_spType = $value;
	}

	public function getSpType()
	{
		return $this->_spType;
	}

	public function setSpFlag($value)
	{
		$this->_spFlag = $value;
	}

	public function getSpFlag()
	{
		return $this->_spFlag;
	}

	public function setSpId($value)
	{
		$this->_spId = $value;
	}

	public function getSpId()
	{
		return $this->_spId;
	}

	public function setOPT($property, $value)
	{
		$this->_OPT[$property] = $value;
	}

	public function getOPT($property)
	{
		if (isset($this->_OPT[$property])) {
			return $this->_OPT[$property];
		}

		return NULL;
	}

	public function getOPTCollection()
	{
		return $this->_OPT;
	}

	public function setStartCoordinates($value = 'A1')
	{
		$this->_startCoordinates = $value;
	}

	public function getStartCoordinates()
	{
		return $this->_startCoordinates;
	}

	public function setStartOffsetX($startOffsetX = 0)
	{
		$this->_startOffsetX = $startOffsetX;
	}

	public function getStartOffsetX()
	{
		return $this->_startOffsetX;
	}

	public function setStartOffsetY($startOffsetY = 0)
	{
		$this->_startOffsetY = $startOffsetY;
	}

	public function getStartOffsetY()
	{
		return $this->_startOffsetY;
	}

	public function setEndCoordinates($value = 'A1')
	{
		$this->_endCoordinates = $value;
	}

	public function getEndCoordinates()
	{
		return $this->_endCoordinates;
	}

	public function setEndOffsetX($endOffsetX = 0)
	{
		$this->_endOffsetX = $endOffsetX;
	}

	public function getEndOffsetX()
	{
		return $this->_endOffsetX;
	}

	public function setEndOffsetY($endOffsetY = 0)
	{
		$this->_endOffsetY = $endOffsetY;
	}

	public function getEndOffsetY()
	{
		return $this->_endOffsetY;
	}

	public function getNestingLevel()
	{
		$nestingLevel = 0;
		$parent = $this->getParent();

		while ($parent instanceof PHPExcel_Shared_Escher_DgContainer_SpgrContainer) {
			++$nestingLevel;
			$parent = $parent->getParent();
		}

		return $nestingLevel;
	}
}


?>

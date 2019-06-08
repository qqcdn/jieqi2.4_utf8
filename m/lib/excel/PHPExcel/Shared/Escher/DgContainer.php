<?php

class PHPExcel_Shared_Escher_DgContainer
{
	/**
	 * Drawing index, 1-based.
	 *
	 * @var int
	 */
	private $_dgId;
	/**
	 * Last shape index in this drawing
	 *
	 * @var int
	 */
	private $_lastSpId;
	private $_spgrContainer;

	public function getDgId()
	{
		return $this->_dgId;
	}

	public function setDgId($value)
	{
		$this->_dgId = $value;
	}

	public function getLastSpId()
	{
		return $this->_lastSpId;
	}

	public function setLastSpId($value)
	{
		$this->_lastSpId = $value;
	}

	public function getSpgrContainer()
	{
		return $this->_spgrContainer;
	}

	public function setSpgrContainer($spgrContainer)
	{
		return $this->_spgrContainer = $spgrContainer;
	}
}


?>

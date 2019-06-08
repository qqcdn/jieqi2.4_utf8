<?php

abstract class PHPExcel_Style_Supervisor
{
	/**
	 * Supervisor?
	 *
	 * @var boolean
	 */
	protected $_isSupervisor;
	/**
	 * Parent. Only used for supervisor
	 *
	 * @var PHPExcel_Style
	 */
	protected $_parent;

	public function __construct($isSupervisor = false)
	{
		$this->_isSupervisor = $isSupervisor;
	}

	public function bindParent($parent, $parentPropertyName = NULL)
	{
		$this->_parent = $parent;
		return $this;
	}

	public function getIsSupervisor()
	{
		return $this->_isSupervisor;
	}

	public function getActiveSheet()
	{
		return $this->_parent->getActiveSheet();
	}

	public function getSelectedCells()
	{
		return $this->getActiveSheet()->getSelectedCells();
	}

	public function getActiveCell()
	{
		return $this->getActiveSheet()->getActiveCell();
	}

	public function __clone()
	{
		$vars = get_object_vars($this);

		foreach ($vars as $key => $value) {
			if (is_object($value) && ($key != '_parent')) {
				$this->$key = clone $value;
			}
			else {
				$this->$key = $value;
			}
		}
	}
}


?>

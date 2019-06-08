<?php

class PHPExcel_Worksheet_Row
{
	/**
	 * PHPExcel_Worksheet
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_parent;
	/**
	 * Row index
	 *
	 * @var int
	 */
	private $_rowIndex = 0;

	public function __construct(PHPExcel_Worksheet $parent = NULL, $rowIndex = 1)
	{
		$this->_parent = $parent;
		$this->_rowIndex = $rowIndex;
	}

	public function __destruct()
	{
		unset($this->_parent);
	}

	public function getRowIndex()
	{
		return $this->_rowIndex;
	}

	public function getCellIterator()
	{
		return new PHPExcel_Worksheet_CellIterator($this->_parent, $this->_rowIndex);
	}
}


?>

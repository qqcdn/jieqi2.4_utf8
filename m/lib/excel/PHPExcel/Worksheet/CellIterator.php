<?php

class PHPExcel_Worksheet_CellIterator implements Iterator
{
	/**
	 * PHPExcel_Worksheet to iterate
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_subject;
	/**
	 * Row index
	 *
	 * @var int
	 */
	private $_rowIndex;
	/**
	 * Current iterator position
	 *
	 * @var int
	 */
	private $_position = 0;
	/**
	 * Loop only existing cells
	 *
	 * @var boolean
	 */
	private $_onlyExistingCells = true;

	public function __construct(PHPExcel_Worksheet $subject = NULL, $rowIndex = 1)
	{
		$this->_subject = $subject;
		$this->_rowIndex = $rowIndex;
	}

	public function __destruct()
	{
		unset($this->_subject);
	}

	public function rewind()
	{
		$this->_position = 0;
	}

	public function current()
	{
		return $this->_subject->getCellByColumnAndRow($this->_position, $this->_rowIndex);
	}

	public function key()
	{
		return $this->_position;
	}

	public function next()
	{
		++$this->_position;
	}

	public function valid()
	{
		$columnCount = PHPExcel_Cell::columnIndexFromString($this->_subject->getHighestColumn());

		if ($this->_onlyExistingCells) {
			while (!$this->_subject->cellExistsByColumnAndRow($this->_position, $this->_rowIndex)) {
				++$this->_position;
			}
		}

		return $this->_position < $columnCount;
	}

	public function getIterateOnlyExistingCells()
	{
		return $this->_onlyExistingCells;
	}

	public function setIterateOnlyExistingCells($value = true)
	{
		$this->_onlyExistingCells = $value;
	}
}

?>

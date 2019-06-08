<?php

class PHPExcel_Worksheet_RowIterator implements Iterator
{
	/**
	 * PHPExcel_Worksheet to iterate
	 *
	 * @var PHPExcel_Worksheet
	 */
	private $_subject;
	/**
	 * Current iterator position
	 *
	 * @var int
	 */
	private $_position = 1;
	/**
	 * Start position
	 *
	 * @var int
	 */
	private $_startRow = 1;

	public function __construct(PHPExcel_Worksheet $subject = NULL, $startRow = 1)
	{
		$this->_subject = $subject;
		$this->resetStart($startRow);
	}

	public function __destruct()
	{
		unset($this->_subject);
	}

	public function resetStart($startRow = 1)
	{
		$this->_startRow = $startRow;
		$this->seek($startRow);
	}

	public function seek($row = 1)
	{
		$this->_position = $row;
	}

	public function rewind()
	{
		$this->_position = $this->_startRow;
	}

	public function current()
	{
		return new PHPExcel_Worksheet_Row($this->_subject, $this->_position);
	}

	public function key()
	{
		return $this->_position;
	}

	public function next()
	{
		++$this->_position;
	}

	public function prev()
	{
		if (1 < $this->_position) {
			--$this->_position;
		}
	}

	public function valid()
	{
		return $this->_position <= $this->_subject->getHighestRow();
	}
}

?>

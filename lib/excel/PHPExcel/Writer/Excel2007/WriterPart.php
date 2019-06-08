<?php

abstract class PHPExcel_Writer_Excel2007_WriterPart
{
	/**
	 * Parent IWriter object
	 *
	 * @var PHPExcel_Writer_IWriter
	 */
	private $_parentWriter;

	public function setParentWriter(PHPExcel_Writer_IWriter $pWriter = NULL)
	{
		$this->_parentWriter = $pWriter;
	}

	public function getParentWriter()
	{
		if (!is_null($this->_parentWriter)) {
			return $this->_parentWriter;
		}
		else {
			throw new PHPExcel_Writer_Exception('No parent PHPExcel_Writer_IWriter assigned.');
		}
	}

	public function __construct(PHPExcel_Writer_IWriter $pWriter = NULL)
	{
		if (!is_null($pWriter)) {
			$this->_parentWriter = $pWriter;
		}
	}
}


?>

<?php

class PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip
{
	/**
	 * The parent BSE
	 *
	 * @var PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE
	 */
	private $_parent;
	/**
	 * Raw image data
	 *
	 * @var string
	 */
	private $_data;

	public function getData()
	{
		return $this->_data;
	}

	public function setData($data)
	{
		$this->_data = $data;
	}

	public function setParent($parent)
	{
		$this->_parent = $parent;
	}

	public function getParent()
	{
		return $this->_parent;
	}
}


?>

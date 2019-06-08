<?php

class PHPExcel_Shared_Escher_DggContainer_BstoreContainer
{
	/**
	 * BLIP Store Entries. Each of them holds one BLIP (Big Large Image or Picture)
	 *
	 * @var array
	 */
	private $_BSECollection = array();

	public function addBSE($BSE)
	{
		$this->_BSECollection[] = $BSE;
		$BSE->setParent($this);
	}

	public function getBSECollection()
	{
		return $this->_BSECollection;
	}
}


?>

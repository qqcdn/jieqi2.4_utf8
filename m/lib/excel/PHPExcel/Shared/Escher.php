<?php

class PHPExcel_Shared_Escher
{
	/**
	 * Drawing Group Container
	 *
	 * @var PHPExcel_Shared_Escher_DggContainer
	 */
	private $_dggContainer;
	/**
	 * Drawing Container
	 *
	 * @var PHPExcel_Shared_Escher_DgContainer
	 */
	private $_dgContainer;

	public function getDggContainer()
	{
		return $this->_dggContainer;
	}

	public function setDggContainer($dggContainer)
	{
		return $this->_dggContainer = $dggContainer;
	}

	public function getDgContainer()
	{
		return $this->_dgContainer;
	}

	public function setDgContainer($dgContainer)
	{
		return $this->_dgContainer = $dgContainer;
	}
}


?>

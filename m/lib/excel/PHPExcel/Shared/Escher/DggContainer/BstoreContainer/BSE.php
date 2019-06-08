<?php

class PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE
{
	const BLIPTYPE_ERROR = 0;
	const BLIPTYPE_UNKNOWN = 1;
	const BLIPTYPE_EMF = 2;
	const BLIPTYPE_WMF = 3;
	const BLIPTYPE_PICT = 4;
	const BLIPTYPE_JPEG = 5;
	const BLIPTYPE_PNG = 6;
	const BLIPTYPE_DIB = 7;
	const BLIPTYPE_TIFF = 17;
	const BLIPTYPE_CMYKJPEG = 18;

	/**
	 * The parent BLIP Store Entry Container
	 *
	 * @var PHPExcel_Shared_Escher_DggContainer_BstoreContainer
	 */
	private $_parent;
	/**
	 * The BLIP (Big Large Image or Picture)
	 *
	 * @var PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip
	 */
	private $_blip;
	/**
	 * The BLIP type
	 *
	 * @var int
	 */
	private $_blipType;

	public function setParent($parent)
	{
		$this->_parent = $parent;
	}

	public function getBlip()
	{
		return $this->_blip;
	}

	public function setBlip($blip)
	{
		$this->_blip = $blip;
		$blip->setParent($this);
	}

	public function getBlipType()
	{
		return $this->_blipType;
	}

	public function setBlipType($blipType)
	{
		$this->_blipType = $blipType;
	}
}


?>

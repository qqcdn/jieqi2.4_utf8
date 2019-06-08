<?php

class PHPExcel_Writer_Excel5_Escher
{
	/**
	 * The object we are writing
	 */
	private $_object;
	/**
	 * The written binary data
	 */
	private $_data;
	/**
	 * Shape offsets. Positions in binary stream where a new shape record begins
	 *
	 * @var array
	 */
	private $_spOffsets;
	/**
	 * Shape types.
	 *
	 * @var array
	 */
	private $_spTypes;

	public function __construct($object)
	{
		$this->_object = $object;
	}

	public function close()
	{
		$this->_data = '';

		switch (get_class($this->_object)) {
		case 'PHPExcel_Shared_Escher':
			if ($dggContainer = $this->_object->getDggContainer()) {
				$writer = new PHPExcel_Writer_Excel5_Escher($dggContainer);
				$this->_data = $writer->close();
			}
			else if ($dgContainer = $this->_object->getDgContainer()) {
				$writer = new PHPExcel_Writer_Excel5_Escher($dgContainer);
				$this->_data = $writer->close();
				$this->_spOffsets = $writer->getSpOffsets();
				$this->_spTypes = $writer->getSpTypes();
			}

			break;

		case 'PHPExcel_Shared_Escher_DggContainer':
			$innerData = '';
			$recVer = 0;
			$recInstance = 0;
			$recType = 61446;
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$dggData = pack('VVVV', $this->_object->getSpIdMax(), $this->_object->getCDgSaved() + 1, $this->_object->getCSpSaved(), $this->_object->getCDgSaved());
			$IDCLs = $this->_object->getIDCLs();

			foreach ($IDCLs as $dgId => $maxReducedSpId) {
				$dggData .= pack('VV', $dgId, $maxReducedSpId + 1);
			}

			$header = pack('vvV', $recVerInstance, $recType, strlen($dggData));
			$innerData .= $header . $dggData;

			if ($bstoreContainer = $this->_object->getBstoreContainer()) {
				$writer = new PHPExcel_Writer_Excel5_Escher($bstoreContainer);
				$innerData .= $writer->close();
			}

			$recVer = 15;
			$recInstance = 0;
			$recType = 61440;
			$length = strlen($innerData);
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$header = pack('vvV', $recVerInstance, $recType, $length);
			$this->_data = $header . $innerData;
			break;

		case 'PHPExcel_Shared_Escher_DggContainer_BstoreContainer':
			$innerData = '';

			if ($BSECollection = $this->_object->getBSECollection()) {
				foreach ($BSECollection as $BSE) {
					$writer = new PHPExcel_Writer_Excel5_Escher($BSE);
					$innerData .= $writer->close();
				}
			}

			$recVer = 15;
			$recInstance = count($this->_object->getBSECollection());
			$recType = 61441;
			$length = strlen($innerData);
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$header = pack('vvV', $recVerInstance, $recType, $length);
			$this->_data = $header . $innerData;
			break;

		case 'PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE':
			$innerData = '';

			if ($blip = $this->_object->getBlip()) {
				$writer = new PHPExcel_Writer_Excel5_Escher($blip);
				$innerData .= $writer->close();
			}

			$data = '';
			$btWin32 = $this->_object->getBlipType();
			$btMacOS = $this->_object->getBlipType();
			$data .= pack('CC', $btWin32, $btMacOS);
			$rgbUid = pack('VVVV', 0, 0, 0, 0);
			$data .= $rgbUid;
			$tag = 0;
			$size = strlen($innerData);
			$cRef = 1;
			$foDelay = 0;
			$unused1 = 0;
			$cbName = 0;
			$unused2 = 0;
			$unused3 = 0;
			$data .= pack('vVVVCCCC', $tag, $size, $cRef, $foDelay, $unused1, $cbName, $unused2, $unused3);
			$data .= $innerData;
			$recVer = 2;
			$recInstance = $this->_object->getBlipType();
			$recType = 61447;
			$length = strlen($data);
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$header = pack('vvV', $recVerInstance, $recType, $length);
			$this->_data = $header;
			$this->_data .= $data;
			break;

		case 'PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip':
			switch ($this->_object->getParent()->getBlipType()) {
			case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG:
				$innerData = '';
				$rgbUid1 = pack('VVVV', 0, 0, 0, 0);
				$innerData .= $rgbUid1;
				$tag = 255;
				$innerData .= pack('C', $tag);
				$innerData .= $this->_object->getData();
				$recVer = 0;
				$recInstance = 1130;
				$recType = 61469;
				$length = strlen($innerData);
				$recVerInstance = $recVer;
				$recVerInstance |= $recInstance << 4;
				$header = pack('vvV', $recVerInstance, $recType, $length);
				$this->_data = $header;
				$this->_data .= $innerData;
				break;

			case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG:
				$innerData = '';
				$rgbUid1 = pack('VVVV', 0, 0, 0, 0);
				$innerData .= $rgbUid1;
				$tag = 255;
				$innerData .= pack('C', $tag);
				$innerData .= $this->_object->getData();
				$recVer = 0;
				$recInstance = 1760;
				$recType = 61470;
				$length = strlen($innerData);
				$recVerInstance = $recVer;
				$recVerInstance |= $recInstance << 4;
				$header = pack('vvV', $recVerInstance, $recType, $length);
				$this->_data = $header;
				$this->_data .= $innerData;
				break;
			}

			break;

		case 'PHPExcel_Shared_Escher_DgContainer':
			$innerData = '';
			$recVer = 0;
			$recInstance = $this->_object->getDgId();
			$recType = 61448;
			$length = 8;
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$header = pack('vvV', $recVerInstance, $recType, $length);
			$countShapes = count($this->_object->getSpgrContainer()->getChildren());
			$innerData .= $header . pack('VV', $countShapes, $this->_object->getLastSpId());

			if ($spgrContainer = $this->_object->getSpgrContainer()) {
				$writer = new PHPExcel_Writer_Excel5_Escher($spgrContainer);
				$innerData .= $writer->close();
				$spOffsets = $writer->getSpOffsets();
				$spTypes = $writer->getSpTypes();

				foreach ($spOffsets as &$spOffset) {
					$spOffset += 24;
				}

				$this->_spOffsets = $spOffsets;
				$this->_spTypes = $spTypes;
			}

			$recVer = 15;
			$recInstance = 0;
			$recType = 61442;
			$length = strlen($innerData);
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$header = pack('vvV', $recVerInstance, $recType, $length);
			$this->_data = $header . $innerData;
			break;

		case 'PHPExcel_Shared_Escher_DgContainer_SpgrContainer':
			$innerData = '';
			$totalSize = 8;
			$spOffsets = array();
			$spTypes = array();

			foreach ($this->_object->getChildren() as $spContainer) {
				$writer = new PHPExcel_Writer_Excel5_Escher($spContainer);
				$spData = $writer->close();
				$innerData .= $spData;
				$totalSize += strlen($spData);
				$spOffsets[] = $totalSize;
				$spTypes = array_merge($spTypes, $writer->getSpTypes());
			}

			$recVer = 15;
			$recInstance = 0;
			$recType = 61443;
			$length = strlen($innerData);
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$header = pack('vvV', $recVerInstance, $recType, $length);
			$this->_data = $header . $innerData;
			$this->_spOffsets = $spOffsets;
			$this->_spTypes = $spTypes;
			break;

		case 'PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer':
			$data = '';

			if ($this->_object->getSpgr()) {
				$recVer = 1;
				$recInstance = 0;
				$recType = 61449;
				$length = 16;
				$recVerInstance = $recVer;
				$recVerInstance |= $recInstance << 4;
				$header = pack('vvV', $recVerInstance, $recType, $length);
				$data .= $header . pack('VVVV', 0, 0, 0, 0);
			}

			$this->_spTypes[] = $this->_object->getSpType();
			$recVer = 2;
			$recInstance = $this->_object->getSpType();
			$recType = 61450;
			$length = 8;
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$header = pack('vvV', $recVerInstance, $recType, $length);
			$data .= $header . pack('VV', $this->_object->getSpId(), $this->_object->getSpgr() ? 5 : 2560);

			if ($this->_object->getOPTCollection()) {
				$optData = '';
				$recVer = 3;
				$recInstance = count($this->_object->getOPTCollection());
				$recType = 61451;

				foreach ($this->_object->getOPTCollection() as $property => $value) {
					$optData .= pack('vV', $property, $value);
				}

				$length = strlen($optData);
				$recVerInstance = $recVer;
				$recVerInstance |= $recInstance << 4;
				$header = pack('vvV', $recVerInstance, $recType, $length);
				$data .= $header . $optData;
			}

			if ($this->_object->getStartCoordinates()) {
				$clientAnchorData = '';
				$recVer = 0;
				$recInstance = 0;
				$recType = 61456;
				list($column, $row) = PHPExcel_Cell::coordinateFromString($this->_object->getStartCoordinates());
				$c1 = PHPExcel_Cell::columnIndexFromString($column) - 1;
				$r1 = $row - 1;
				$startOffsetX = $this->_object->getStartOffsetX();
				$startOffsetY = $this->_object->getStartOffsetY();
				list($column, $row) = PHPExcel_Cell::coordinateFromString($this->_object->getEndCoordinates());
				$c2 = PHPExcel_Cell::columnIndexFromString($column) - 1;
				$r2 = $row - 1;
				$endOffsetX = $this->_object->getEndOffsetX();
				$endOffsetY = $this->_object->getEndOffsetY();
				$clientAnchorData = pack('vvvvvvvvv', $this->_object->getSpFlag(), $c1, $startOffsetX, $r1, $startOffsetY, $c2, $endOffsetX, $r2, $endOffsetY);
				$length = strlen($clientAnchorData);
				$recVerInstance = $recVer;
				$recVerInstance |= $recInstance << 4;
				$header = pack('vvV', $recVerInstance, $recType, $length);
				$data .= $header . $clientAnchorData;
			}

			if (!$this->_object->getSpgr()) {
				$clientDataData = '';
				$recVer = 0;
				$recInstance = 0;
				$recType = 61457;
				$length = strlen($clientDataData);
				$recVerInstance = $recVer;
				$recVerInstance |= $recInstance << 4;
				$header = pack('vvV', $recVerInstance, $recType, $length);
				$data .= $header . $clientDataData;
			}

			$recVer = 15;
			$recInstance = 0;
			$recType = 61444;
			$length = strlen($data);
			$recVerInstance = $recVer;
			$recVerInstance |= $recInstance << 4;
			$header = pack('vvV', $recVerInstance, $recType, $length);
			$this->_data = $header . $data;
			break;
		}

		return $this->_data;
	}

	public function getSpOffsets()
	{
		return $this->_spOffsets;
	}

	public function getSpTypes()
	{
		return $this->_spTypes;
	}
}


?>

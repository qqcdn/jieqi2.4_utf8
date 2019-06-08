<?php

class PHPExcel_Shared_OLE_PPS
{
	/**
	* The PPS index
	* @var integer
	*/
	public $No;
	/**
	* The PPS name (in Unicode)
	* @var string
	*/
	public $Name;
	/**
	* The PPS type. Dir, Root or File
	* @var integer
	*/
	public $Type;
	/**
	* The index of the previous PPS
	* @var integer
	*/
	public $PrevPps;
	/**
	* The index of the next PPS
	* @var integer
	*/
	public $NextPps;
	/**
	* The index of it's first child if this is a Dir or Root PPS
	* @var integer
	*/
	public $DirPps;
	/**
	* A timestamp
	* @var integer
	*/
	public $Time1st;
	/**
	* A timestamp
	* @var integer
	*/
	public $Time2nd;
	/**
	* Starting block (small or big) for this PPS's data  inside the container
	* @var integer
	*/
	public $_StartBlock;
	/**
	* The size of the PPS's data (in bytes)
	* @var integer
	*/
	public $Size;
	/**
	* The PPS's data (only used if it's not using a temporary file)
	* @var string
	*/
	public $_data;
	/**
	* Array of child PPS's (only used by Root and Dir PPS's)
	* @var array
	*/
	public $children = array();
	/**
	* Pointer to OLE container
	* @var OLE
	*/
	public $ole;

	public function __construct($No, $name, $type, $prev, $next, $dir, $time_1st, $time_2nd, $data, $children)
	{
		$this->No = $No;
		$this->Name = $name;
		$this->Type = $type;
		$this->PrevPps = $prev;
		$this->NextPps = $next;
		$this->DirPps = $dir;
		$this->Time1st = $time_1st;
		$this->Time2nd = $time_2nd;
		$this->_data = $data;
		$this->children = $children;

		if ($data != '') {
			$this->Size = strlen($data);
		}
		else {
			$this->Size = 0;
		}
	}

	public function _DataLen()
	{
		if (!isset($this->_data)) {
			return 0;
		}

		return strlen($this->_data);
	}

	public function _getPpsWk()
	{
		$ret = str_pad($this->Name, 64, "\x00");
		$ret .= pack('v', strlen($this->Name) + 2) . pack('c', $this->Type) . pack('c', 0) . pack('V', $this->PrevPps) . pack('V', $this->NextPps) . pack('V', $this->DirPps) . "\x00\t\x02\x00" . "\x00\x00\x00\x00" . "\xc0\x00\x00\x00" . "\x00\x00\x00F" . "\x00\x00\x00\x00" . PHPExcel_Shared_OLE::LocalDate2OLE($this->Time1st) . PHPExcel_Shared_OLE::LocalDate2OLE($this->Time2nd) . pack('V', isset($this->_StartBlock) ? $this->_StartBlock : 0) . pack('V', $this->Size) . pack('V', 0);
		return $ret;
	}

	static public function _savePpsSetPnt(&$raList, $to_save, $depth = 0)
	{
		if (!is_array($to_save) || empty($to_save)) {
			return 4294967295;
		}
		else if (count($to_save) == 1) {
			$cnt = count($raList);
			$raList[$cnt] = $depth == 0 ? $to_save[0] : clone $to_save[0];
			$raList[$cnt]->No = $cnt;
			$raList[$cnt]->PrevPps = 4294967295;
			$raList[$cnt]->NextPps = 4294967295;
			$raList[$cnt]->DirPps = self::_savePpsSetPnt($raList, @$raList[$cnt]->children, $depth++);
		}
		else {
			$iPos = floor(count($to_save) / 2);
			$aPrev = array_slice($to_save, 0, $iPos);
			$aNext = array_slice($to_save, $iPos + 1);
			$cnt = count($raList);
			$raList[$cnt] = $depth == 0 ? $to_save[$iPos] : clone $to_save[$iPos];
			$raList[$cnt]->No = $cnt;
			$raList[$cnt]->PrevPps = self::_savePpsSetPnt($raList, $aPrev, $depth++);
			$raList[$cnt]->NextPps = self::_savePpsSetPnt($raList, $aNext, $depth++);
			$raList[$cnt]->DirPps = self::_savePpsSetPnt($raList, @$raList[$cnt]->children, $depth++);
		}

		return $cnt;
	}
}


?>

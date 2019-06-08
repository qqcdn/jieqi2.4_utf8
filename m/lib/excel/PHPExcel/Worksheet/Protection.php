<?php

class PHPExcel_Worksheet_Protection
{
	/**
	 * Sheet
	 *
	 * @var boolean
	 */
	private $_sheet = false;
	/**
	 * Objects
	 *
	 * @var boolean
	 */
	private $_objects = false;
	/**
	 * Scenarios
	 *
	 * @var boolean
	 */
	private $_scenarios = false;
	/**
	 * Format cells
	 *
	 * @var boolean
	 */
	private $_formatCells = false;
	/**
	 * Format columns
	 *
	 * @var boolean
	 */
	private $_formatColumns = false;
	/**
	 * Format rows
	 *
	 * @var boolean
	 */
	private $_formatRows = false;
	/**
	 * Insert columns
	 *
	 * @var boolean
	 */
	private $_insertColumns = false;
	/**
	 * Insert rows
	 *
	 * @var boolean
	 */
	private $_insertRows = false;
	/**
	 * Insert hyperlinks
	 *
	 * @var boolean
	 */
	private $_insertHyperlinks = false;
	/**
	 * Delete columns
	 *
	 * @var boolean
	 */
	private $_deleteColumns = false;
	/**
	 * Delete rows
	 *
	 * @var boolean
	 */
	private $_deleteRows = false;
	/**
	 * Select locked cells
	 *
	 * @var boolean
	 */
	private $_selectLockedCells = false;
	/**
	 * Sort
	 *
	 * @var boolean
	 */
	private $_sort = false;
	/**
	 * AutoFilter
	 *
	 * @var boolean
	 */
	private $_autoFilter = false;
	/**
	 * Pivot tables
	 *
	 * @var boolean
	 */
	private $_pivotTables = false;
	/**
	 * Select unlocked cells
	 *
	 * @var boolean
	 */
	private $_selectUnlockedCells = false;
	/**
	 * Password
	 *
	 * @var string
	 */
	private $_password = '';

	public function __construct()
	{
	}

	public function isProtectionEnabled()
	{
		return $this->_sheet || $this->_objects || $this->_scenarios || $this->_formatCells || $this->_formatColumns || $this->_formatRows || $this->_insertColumns || $this->_insertRows || $this->_insertHyperlinks || $this->_deleteColumns || $this->_deleteRows || $this->_selectLockedCells || $this->_sort || $this->_autoFilter || $this->_pivotTables || $this->_selectUnlockedCells;
	}

	public function getSheet()
	{
		return $this->_sheet;
	}

	public function setSheet($pValue = false)
	{
		$this->_sheet = $pValue;
		return $this;
	}

	public function getObjects()
	{
		return $this->_objects;
	}

	public function setObjects($pValue = false)
	{
		$this->_objects = $pValue;
		return $this;
	}

	public function getScenarios()
	{
		return $this->_scenarios;
	}

	public function setScenarios($pValue = false)
	{
		$this->_scenarios = $pValue;
		return $this;
	}

	public function getFormatCells()
	{
		return $this->_formatCells;
	}

	public function setFormatCells($pValue = false)
	{
		$this->_formatCells = $pValue;
		return $this;
	}

	public function getFormatColumns()
	{
		return $this->_formatColumns;
	}

	public function setFormatColumns($pValue = false)
	{
		$this->_formatColumns = $pValue;
		return $this;
	}

	public function getFormatRows()
	{
		return $this->_formatRows;
	}

	public function setFormatRows($pValue = false)
	{
		$this->_formatRows = $pValue;
		return $this;
	}

	public function getInsertColumns()
	{
		return $this->_insertColumns;
	}

	public function setInsertColumns($pValue = false)
	{
		$this->_insertColumns = $pValue;
		return $this;
	}

	public function getInsertRows()
	{
		return $this->_insertRows;
	}

	public function setInsertRows($pValue = false)
	{
		$this->_insertRows = $pValue;
		return $this;
	}

	public function getInsertHyperlinks()
	{
		return $this->_insertHyperlinks;
	}

	public function setInsertHyperlinks($pValue = false)
	{
		$this->_insertHyperlinks = $pValue;
		return $this;
	}

	public function getDeleteColumns()
	{
		return $this->_deleteColumns;
	}

	public function setDeleteColumns($pValue = false)
	{
		$this->_deleteColumns = $pValue;
		return $this;
	}

	public function getDeleteRows()
	{
		return $this->_deleteRows;
	}

	public function setDeleteRows($pValue = false)
	{
		$this->_deleteRows = $pValue;
		return $this;
	}

	public function getSelectLockedCells()
	{
		return $this->_selectLockedCells;
	}

	public function setSelectLockedCells($pValue = false)
	{
		$this->_selectLockedCells = $pValue;
		return $this;
	}

	public function getSort()
	{
		return $this->_sort;
	}

	public function setSort($pValue = false)
	{
		$this->_sort = $pValue;
		return $this;
	}

	public function getAutoFilter()
	{
		return $this->_autoFilter;
	}

	public function setAutoFilter($pValue = false)
	{
		$this->_autoFilter = $pValue;
		return $this;
	}

	public function getPivotTables()
	{
		return $this->_pivotTables;
	}

	public function setPivotTables($pValue = false)
	{
		$this->_pivotTables = $pValue;
		return $this;
	}

	public function getSelectUnlockedCells()
	{
		return $this->_selectUnlockedCells;
	}

	public function setSelectUnlockedCells($pValue = false)
	{
		$this->_selectUnlockedCells = $pValue;
		return $this;
	}

	public function getPassword()
	{
		return $this->_password;
	}

	public function setPassword($pValue = '', $pAlreadyHashed = false)
	{
		if (!$pAlreadyHashed) {
			$pValue = PHPExcel_Shared_PasswordHasher::hashPassword($pValue);
		}

		$this->_password = $pValue;
		return $this;
	}

	public function __clone()
	{
		$vars = get_object_vars($this);

		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			}
			else {
				$this->$key = $value;
			}
		}
	}
}


?>

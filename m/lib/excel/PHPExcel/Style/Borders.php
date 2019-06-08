<?php

class PHPExcel_Style_Borders extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
	const DIAGONAL_NONE = 0;
	const DIAGONAL_UP = 1;
	const DIAGONAL_DOWN = 2;
	const DIAGONAL_BOTH = 3;

	/**
	 * Left
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_left;
	/**
	 * Right
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_right;
	/**
	 * Top
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_top;
	/**
	 * Bottom
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_bottom;
	/**
	 * Diagonal
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_diagonal;
	/**
	 * DiagonalDirection
	 *
	 * @var int
	 */
	protected $_diagonalDirection;
	/**
	 * All borders psedo-border. Only applies to supervisor.
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_allBorders;
	/**
	 * Outline psedo-border. Only applies to supervisor.
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_outline;
	/**
	 * Inside psedo-border. Only applies to supervisor.
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_inside;
	/**
	 * Vertical pseudo-border. Only applies to supervisor.
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_vertical;
	/**
	 * Horizontal pseudo-border. Only applies to supervisor.
	 *
	 * @var PHPExcel_Style_Border
	 */
	protected $_horizontal;

	public function __construct($isSupervisor = false, $isConditional = false)
	{
		parent::__construct($isSupervisor);
		$this->_left = new PHPExcel_Style_Border($isSupervisor, $isConditional);
		$this->_right = new PHPExcel_Style_Border($isSupervisor, $isConditional);
		$this->_top = new PHPExcel_Style_Border($isSupervisor, $isConditional);
		$this->_bottom = new PHPExcel_Style_Border($isSupervisor, $isConditional);
		$this->_diagonal = new PHPExcel_Style_Border($isSupervisor, $isConditional);
		$this->_diagonalDirection = PHPExcel_Style_Borders::DIAGONAL_NONE;

		if ($isSupervisor) {
			$this->_allBorders = new PHPExcel_Style_Border(true);
			$this->_outline = new PHPExcel_Style_Border(true);
			$this->_inside = new PHPExcel_Style_Border(true);
			$this->_vertical = new PHPExcel_Style_Border(true);
			$this->_horizontal = new PHPExcel_Style_Border(true);
			$this->_left->bindParent($this, '_left');
			$this->_right->bindParent($this, '_right');
			$this->_top->bindParent($this, '_top');
			$this->_bottom->bindParent($this, '_bottom');
			$this->_diagonal->bindParent($this, '_diagonal');
			$this->_allBorders->bindParent($this, '_allBorders');
			$this->_outline->bindParent($this, '_outline');
			$this->_inside->bindParent($this, '_inside');
			$this->_vertical->bindParent($this, '_vertical');
			$this->_horizontal->bindParent($this, '_horizontal');
		}
	}

	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getBorders();
	}

	public function getStyleArray($array)
	{
		return array('borders' => $array);
	}

	public function applyFromArray($pStyles = NULL)
	{
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			}
			else {
				if (array_key_exists('left', $pStyles)) {
					$this->getLeft()->applyFromArray($pStyles['left']);
				}

				if (array_key_exists('right', $pStyles)) {
					$this->getRight()->applyFromArray($pStyles['right']);
				}

				if (array_key_exists('top', $pStyles)) {
					$this->getTop()->applyFromArray($pStyles['top']);
				}

				if (array_key_exists('bottom', $pStyles)) {
					$this->getBottom()->applyFromArray($pStyles['bottom']);
				}

				if (array_key_exists('diagonal', $pStyles)) {
					$this->getDiagonal()->applyFromArray($pStyles['diagonal']);
				}

				if (array_key_exists('diagonaldirection', $pStyles)) {
					$this->setDiagonalDirection($pStyles['diagonaldirection']);
				}

				if (array_key_exists('allborders', $pStyles)) {
					$this->getLeft()->applyFromArray($pStyles['allborders']);
					$this->getRight()->applyFromArray($pStyles['allborders']);
					$this->getTop()->applyFromArray($pStyles['allborders']);
					$this->getBottom()->applyFromArray($pStyles['allborders']);
				}
			}
		}
		else {
			throw new PHPExcel_Exception('Invalid style array passed.');
		}

		return $this;
	}

	public function getLeft()
	{
		return $this->_left;
	}

	public function getRight()
	{
		return $this->_right;
	}

	public function getTop()
	{
		return $this->_top;
	}

	public function getBottom()
	{
		return $this->_bottom;
	}

	public function getDiagonal()
	{
		return $this->_diagonal;
	}

	public function getAllBorders()
	{
		if (!$this->_isSupervisor) {
			throw new PHPExcel_Exception('Can only get pseudo-border for supervisor.');
		}

		return $this->_allBorders;
	}

	public function getOutline()
	{
		if (!$this->_isSupervisor) {
			throw new PHPExcel_Exception('Can only get pseudo-border for supervisor.');
		}

		return $this->_outline;
	}

	public function getInside()
	{
		if (!$this->_isSupervisor) {
			throw new PHPExcel_Exception('Can only get pseudo-border for supervisor.');
		}

		return $this->_inside;
	}

	public function getVertical()
	{
		if (!$this->_isSupervisor) {
			throw new PHPExcel_Exception('Can only get pseudo-border for supervisor.');
		}

		return $this->_vertical;
	}

	public function getHorizontal()
	{
		if (!$this->_isSupervisor) {
			throw new PHPExcel_Exception('Can only get pseudo-border for supervisor.');
		}

		return $this->_horizontal;
	}

	public function getDiagonalDirection()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getDiagonalDirection();
		}

		return $this->_diagonalDirection;
	}

	public function setDiagonalDirection($pValue = PHPExcel_Style_Borders::DIAGONAL_NONE)
	{
		if ($pValue == '') {
			$pValue = PHPExcel_Style_Borders::DIAGONAL_NONE;
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('diagonaldirection' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_diagonalDirection = $pValue;
		}

		return $this;
	}

	public function getHashCode()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashcode();
		}

		return md5($this->getLeft()->getHashCode() . $this->getRight()->getHashCode() . $this->getTop()->getHashCode() . $this->getBottom()->getHashCode() . $this->getDiagonal()->getHashCode() . $this->getDiagonalDirection() . 'PHPExcel_Style_Borders');
	}
}

?>

<?php

class PHPExcel_Style_Alignment extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
	const HORIZONTAL_GENERAL = 'general';
	const HORIZONTAL_LEFT = 'left';
	const HORIZONTAL_RIGHT = 'right';
	const HORIZONTAL_CENTER = 'center';
	const HORIZONTAL_CENTER_CONTINUOUS = 'centerContinuous';
	const HORIZONTAL_JUSTIFY = 'justify';
	const HORIZONTAL_FILL = 'fill';
	const HORIZONTAL_DISTRIBUTED = 'distributed';
	const VERTICAL_BOTTOM = 'bottom';
	const VERTICAL_TOP = 'top';
	const VERTICAL_CENTER = 'center';
	const VERTICAL_JUSTIFY = 'justify';
	const VERTICAL_DISTRIBUTED = 'distributed';

	/**
	 * Horizontal
	 *
	 * @var string
	 */
	protected $_horizontal = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
	/**
	 * Vertical
	 *
	 * @var string
	 */
	protected $_vertical = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
	/**
	 * Text rotation
	 *
	 * @var int
	 */
	protected $_textRotation = 0;
	/**
	 * Wrap text
	 *
	 * @var boolean
	 */
	protected $_wrapText = false;
	/**
	 * Shrink to fit
	 *
	 * @var boolean
	 */
	protected $_shrinkToFit = false;
	/**
	 * Indent - only possible with horizontal alignment left and right
	 *
	 * @var int
	 */
	protected $_indent = 0;

	public function __construct($isSupervisor = false, $isConditional = false)
	{
		parent::__construct($isSupervisor);

		if ($isConditional) {
			$this->_horizontal = NULL;
			$this->_vertical = NULL;
			$this->_textRotation = NULL;
		}
	}

	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getAlignment();
	}

	public function getStyleArray($array)
	{
		return array('alignment' => $array);
	}

	public function applyFromArray($pStyles = NULL)
	{
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			}
			else {
				if (isset($pStyles['horizontal'])) {
					$this->setHorizontal($pStyles['horizontal']);
				}

				if (isset($pStyles['vertical'])) {
					$this->setVertical($pStyles['vertical']);
				}

				if (isset($pStyles['rotation'])) {
					$this->setTextRotation($pStyles['rotation']);
				}

				if (isset($pStyles['wrap'])) {
					$this->setWrapText($pStyles['wrap']);
				}

				if (isset($pStyles['shrinkToFit'])) {
					$this->setShrinkToFit($pStyles['shrinkToFit']);
				}

				if (isset($pStyles['indent'])) {
					$this->setIndent($pStyles['indent']);
				}
			}
		}
		else {
			throw new PHPExcel_Exception('Invalid style array passed.');
		}

		return $this;
	}

	public function getHorizontal()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHorizontal();
		}

		return $this->_horizontal;
	}

	public function setHorizontal($pValue = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL)
	{
		if ($pValue == '') {
			$pValue = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('horizontal' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_horizontal = $pValue;
		}

		return $this;
	}

	public function getVertical()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getVertical();
		}

		return $this->_vertical;
	}

	public function setVertical($pValue = PHPExcel_Style_Alignment::VERTICAL_BOTTOM)
	{
		if ($pValue == '') {
			$pValue = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('vertical' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_vertical = $pValue;
		}

		return $this;
	}

	public function getTextRotation()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getTextRotation();
		}

		return $this->_textRotation;
	}

	public function setTextRotation($pValue = 0)
	{
		if ($pValue == 255) {
			$pValue = -165;
		}

		if (((-90 <= $pValue) && ($pValue <= 90)) || ($pValue == -165)) {
			if ($this->_isSupervisor) {
				$styleArray = $this->getStyleArray(array('rotation' => $pValue));
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
			}
			else {
				$this->_textRotation = $pValue;
			}
		}
		else {
			throw new PHPExcel_Exception('Text rotation should be a value between -90 and 90.');
		}

		return $this;
	}

	public function getWrapText()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getWrapText();
		}

		return $this->_wrapText;
	}

	public function setWrapText($pValue = false)
	{
		if ($pValue == '') {
			$pValue = false;
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('wrap' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_wrapText = $pValue;
		}

		return $this;
	}

	public function getShrinkToFit()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getShrinkToFit();
		}

		return $this->_shrinkToFit;
	}

	public function setShrinkToFit($pValue = false)
	{
		if ($pValue == '') {
			$pValue = false;
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('shrinkToFit' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_shrinkToFit = $pValue;
		}

		return $this;
	}

	public function getIndent()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getIndent();
		}

		return $this->_indent;
	}

	public function setIndent($pValue = 0)
	{
		if (0 < $pValue) {
			if (($this->getHorizontal() != self::HORIZONTAL_GENERAL) && ($this->getHorizontal() != self::HORIZONTAL_LEFT) && ($this->getHorizontal() != self::HORIZONTAL_RIGHT)) {
				$pValue = 0;
			}
		}

		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('indent' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_indent = $pValue;
		}

		return $this;
	}

	public function getHashCode()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashCode();
		}

		return md5($this->_horizontal . $this->_vertical . $this->_textRotation . ($this->_wrapText ? 't' : 'f') . ($this->_shrinkToFit ? 't' : 'f') . $this->_indent . 'PHPExcel_Style_Alignment');
	}
}

?>

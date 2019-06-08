<?php

class PHPExcel_Style_Fill extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
	const FILL_NONE = 'none';
	const FILL_SOLID = 'solid';
	const FILL_GRADIENT_LINEAR = 'linear';
	const FILL_GRADIENT_PATH = 'path';
	const FILL_PATTERN_DARKDOWN = 'darkDown';
	const FILL_PATTERN_DARKGRAY = 'darkGray';
	const FILL_PATTERN_DARKGRID = 'darkGrid';
	const FILL_PATTERN_DARKHORIZONTAL = 'darkHorizontal';
	const FILL_PATTERN_DARKTRELLIS = 'darkTrellis';
	const FILL_PATTERN_DARKUP = 'darkUp';
	const FILL_PATTERN_DARKVERTICAL = 'darkVertical';
	const FILL_PATTERN_GRAY0625 = 'gray0625';
	const FILL_PATTERN_GRAY125 = 'gray125';
	const FILL_PATTERN_LIGHTDOWN = 'lightDown';
	const FILL_PATTERN_LIGHTGRAY = 'lightGray';
	const FILL_PATTERN_LIGHTGRID = 'lightGrid';
	const FILL_PATTERN_LIGHTHORIZONTAL = 'lightHorizontal';
	const FILL_PATTERN_LIGHTTRELLIS = 'lightTrellis';
	const FILL_PATTERN_LIGHTUP = 'lightUp';
	const FILL_PATTERN_LIGHTVERTICAL = 'lightVertical';
	const FILL_PATTERN_MEDIUMGRAY = 'mediumGray';

	/**
	 * Fill type
	 *
	 * @var string
	 */
	protected $_fillType = PHPExcel_Style_Fill::FILL_NONE;
	/**
	 * Rotation
	 *
	 * @var double
	 */
	protected $_rotation = 0;
	/**
	 * Start color
	 *
	 * @var PHPExcel_Style_Color
	 */
	protected $_startColor;
	/**
	 * End color
	 *
	 * @var PHPExcel_Style_Color
	 */
	protected $_endColor;

	public function __construct($isSupervisor = false, $isConditional = false)
	{
		parent::__construct($isSupervisor);

		if ($isConditional) {
			$this->_fillType = NULL;
		}

		$this->_startColor = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_WHITE, $isSupervisor, $isConditional);
		$this->_endColor = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK, $isSupervisor, $isConditional);

		if ($isSupervisor) {
			$this->_startColor->bindParent($this, '_startColor');
			$this->_endColor->bindParent($this, '_endColor');
		}
	}

	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getFill();
	}

	public function getStyleArray($array)
	{
		return array('fill' => $array);
	}

	public function applyFromArray($pStyles = NULL)
	{
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			}
			else {
				if (array_key_exists('type', $pStyles)) {
					$this->setFillType($pStyles['type']);
				}

				if (array_key_exists('rotation', $pStyles)) {
					$this->setRotation($pStyles['rotation']);
				}

				if (array_key_exists('startcolor', $pStyles)) {
					$this->getStartColor()->applyFromArray($pStyles['startcolor']);
				}

				if (array_key_exists('endcolor', $pStyles)) {
					$this->getEndColor()->applyFromArray($pStyles['endcolor']);
				}

				if (array_key_exists('color', $pStyles)) {
					$this->getStartColor()->applyFromArray($pStyles['color']);
				}
			}
		}
		else {
			throw new PHPExcel_Exception('Invalid style array passed.');
		}

		return $this;
	}

	public function getFillType()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getFillType();
		}

		return $this->_fillType;
	}

	public function setFillType($pValue = PHPExcel_Style_Fill::FILL_NONE)
	{
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('type' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_fillType = $pValue;
		}

		return $this;
	}

	public function getRotation()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getRotation();
		}

		return $this->_rotation;
	}

	public function setRotation($pValue = 0)
	{
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('rotation' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_rotation = $pValue;
		}

		return $this;
	}

	public function getStartColor()
	{
		return $this->_startColor;
	}

	public function setStartColor(PHPExcel_Style_Color $pValue = NULL)
	{
		$color = ($pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue);

		if ($this->_isSupervisor) {
			$styleArray = $this->getStartColor()->getStyleArray(array('argb' => $color->getARGB()));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_startColor = $color;
		}

		return $this;
	}

	public function getEndColor()
	{
		return $this->_endColor;
	}

	public function setEndColor(PHPExcel_Style_Color $pValue = NULL)
	{
		$color = ($pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue);

		if ($this->_isSupervisor) {
			$styleArray = $this->getEndColor()->getStyleArray(array('argb' => $color->getARGB()));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		}
		else {
			$this->_endColor = $color;
		}

		return $this;
	}

	public function getHashCode()
	{
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashCode();
		}

		return md5($this->getFillType() . $this->getRotation() . $this->getStartColor()->getHashCode() . $this->getEndColor()->getHashCode() . 'PHPExcel_Style_Fill');
	}
}

?>

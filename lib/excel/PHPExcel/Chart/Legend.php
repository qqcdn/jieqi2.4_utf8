<?php

class PHPExcel_Chart_Legend
{
	const xlLegendPositionBottom = -4107;
	const xlLegendPositionCorner = 2;
	const xlLegendPositionCustom = -4161;
	const xlLegendPositionLeft = -4131;
	const xlLegendPositionRight = -4152;
	const xlLegendPositionTop = -4160;
	const POSITION_RIGHT = 'r';
	const POSITION_LEFT = 'l';
	const POSITION_BOTTOM = 'b';
	const POSITION_TOP = 't';
	const POSITION_TOPRIGHT = 'tr';

	static 	private $_positionXLref = array('self::xlLegendPositionBottom' . "\0" . '' => self::POSITION_BOTTOM, 'self::xlLegendPositionCorner' . "\0" . '' => self::POSITION_TOPRIGHT, 'self::xlLegendPositionCustom' . "\0" . '' => ??, 'self::xlLegendPositionLeft' . "\0" . '' => self::POSITION_LEFT, 'self::xlLegendPositionRight' . "\0" . '' => self::POSITION_RIGHT, 'self::xlLegendPositionTop' . "\0" . '' => self::POSITION_TOP);
	/**
	 * Legend position
	 *
	 * @var	string
	 */
	private $_position = self::POSITION_RIGHT;
	/**
	 * Allow overlay of other elements?
	 *
	 * @var	boolean
	 */
	private $_overlay = true;
	/**
	 * Legend Layout
	 *
	 * @var	PHPExcel_Chart_Layout
	 */
	private $_layout;

	public function __construct($position = self::POSITION_RIGHT, PHPExcel_Chart_Layout $layout = NULL, $overlay = false)
	{
		$this->setPosition($position);
		$this->_layout = $layout;
		$this->setOverlay($overlay);
	}

	public function getPosition()
	{
		return $this->_position;
	}

	public function setPosition($position = self::POSITION_RIGHT)
	{
		if (!in_array($position, self::$_positionXLref)) {
			return false;
		}

		$this->_position = $position;
		return true;
	}

	public function getPositionXL()
	{
		return array_search($this->_position, self::$_positionXLref);
	}

	public function setPositionXL($positionXL = self::xlLegendPositionRight)
	{
		if (!array_key_exists($positionXL, self::$_positionXLref)) {
			return false;
		}

		$this->_position = self::$_positionXLref[$positionXL];
		return true;
	}

	public function getOverlay()
	{
		return $this->_overlay;
	}

	public function setOverlay($overlay = false)
	{
		if (!is_bool($overlay)) {
			return false;
		}

		$this->_overlay = $overlay;
		return true;
	}

	public function getLayout()
	{
		return $this->_layout;
	}
}



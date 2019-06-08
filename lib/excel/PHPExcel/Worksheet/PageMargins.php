<?php

class PHPExcel_Worksheet_PageMargins
{
	/**
	 * Left
	 *
	 * @var double
	 */
	private $_left = 0.7;
	/**
	 * Right
	 *
	 * @var double
	 */
	private $_right = 0.7;
	/**
	 * Top
	 *
	 * @var double
	 */
	private $_top = 0.75;
	/**
	 * Bottom
	 *
	 * @var double
	 */
	private $_bottom = 0.75;
	/**
	 * Header
	 *
	 * @var double
	 */
	private $_header = 0.3;
	/**
	 * Footer
	 *
	 * @var double
	 */
	private $_footer = 0.3;

	public function __construct()
	{
	}

	public function getLeft()
	{
		return $this->_left;
	}

	public function setLeft($pValue)
	{
		$this->_left = $pValue;
		return $this;
	}

	public function getRight()
	{
		return $this->_right;
	}

	public function setRight($pValue)
	{
		$this->_right = $pValue;
		return $this;
	}

	public function getTop()
	{
		return $this->_top;
	}

	public function setTop($pValue)
	{
		$this->_top = $pValue;
		return $this;
	}

	public function getBottom()
	{
		return $this->_bottom;
	}

	public function setBottom($pValue)
	{
		$this->_bottom = $pValue;
		return $this;
	}

	public function getHeader()
	{
		return $this->_header;
	}

	public function setHeader($pValue)
	{
		$this->_header = $pValue;
		return $this;
	}

	public function getFooter()
	{
		return $this->_footer;
	}

	public function setFooter($pValue)
	{
		$this->_footer = $pValue;
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

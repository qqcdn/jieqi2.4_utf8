<?php

class PHPExcel_Worksheet_HeaderFooter
{
	const IMAGE_HEADER_LEFT = 'LH';
	const IMAGE_HEADER_CENTER = 'CH';
	const IMAGE_HEADER_RIGHT = 'RH';
	const IMAGE_FOOTER_LEFT = 'LF';
	const IMAGE_FOOTER_CENTER = 'CF';
	const IMAGE_FOOTER_RIGHT = 'RF';

	/**
	 * OddHeader
	 *
	 * @var string
	 */
	private $_oddHeader = '';
	/**
	 * OddFooter
	 *
	 * @var string
	 */
	private $_oddFooter = '';
	/**
	 * EvenHeader
	 *
	 * @var string
	 */
	private $_evenHeader = '';
	/**
	 * EvenFooter
	 *
	 * @var string
	 */
	private $_evenFooter = '';
	/**
	 * FirstHeader
	 *
	 * @var string
	 */
	private $_firstHeader = '';
	/**
	 * FirstFooter
	 *
	 * @var string
	 */
	private $_firstFooter = '';
	/**
	 * Different header for Odd/Even, defaults to false
	 *
	 * @var boolean
	 */
	private $_differentOddEven = false;
	/**
	 * Different header for first page, defaults to false
	 *
	 * @var boolean
	 */
	private $_differentFirst = false;
	/**
	 * Scale with document, defaults to true
	 *
	 * @var boolean
	 */
	private $_scaleWithDocument = true;
	/**
	 * Align with margins, defaults to true
	 *
	 * @var boolean
	 */
	private $_alignWithMargins = true;
	/**
	 * Header/footer images
	 *
	 * @var PHPExcel_Worksheet_HeaderFooterDrawing[]
	 */
	private $_headerFooterImages = array();

	public function __construct()
	{
	}

	public function getOddHeader()
	{
		return $this->_oddHeader;
	}

	public function setOddHeader($pValue)
	{
		$this->_oddHeader = $pValue;
		return $this;
	}

	public function getOddFooter()
	{
		return $this->_oddFooter;
	}

	public function setOddFooter($pValue)
	{
		$this->_oddFooter = $pValue;
		return $this;
	}

	public function getEvenHeader()
	{
		return $this->_evenHeader;
	}

	public function setEvenHeader($pValue)
	{
		$this->_evenHeader = $pValue;
		return $this;
	}

	public function getEvenFooter()
	{
		return $this->_evenFooter;
	}

	public function setEvenFooter($pValue)
	{
		$this->_evenFooter = $pValue;
		return $this;
	}

	public function getFirstHeader()
	{
		return $this->_firstHeader;
	}

	public function setFirstHeader($pValue)
	{
		$this->_firstHeader = $pValue;
		return $this;
	}

	public function getFirstFooter()
	{
		return $this->_firstFooter;
	}

	public function setFirstFooter($pValue)
	{
		$this->_firstFooter = $pValue;
		return $this;
	}

	public function getDifferentOddEven()
	{
		return $this->_differentOddEven;
	}

	public function setDifferentOddEven($pValue = false)
	{
		$this->_differentOddEven = $pValue;
		return $this;
	}

	public function getDifferentFirst()
	{
		return $this->_differentFirst;
	}

	public function setDifferentFirst($pValue = false)
	{
		$this->_differentFirst = $pValue;
		return $this;
	}

	public function getScaleWithDocument()
	{
		return $this->_scaleWithDocument;
	}

	public function setScaleWithDocument($pValue = true)
	{
		$this->_scaleWithDocument = $pValue;
		return $this;
	}

	public function getAlignWithMargins()
	{
		return $this->_alignWithMargins;
	}

	public function setAlignWithMargins($pValue = true)
	{
		$this->_alignWithMargins = $pValue;
		return $this;
	}

	public function addImage(PHPExcel_Worksheet_HeaderFooterDrawing $image = NULL, $location = self::IMAGE_HEADER_LEFT)
	{
		$this->_headerFooterImages[$location] = $image;
		return $this;
	}

	public function removeImage($location = self::IMAGE_HEADER_LEFT)
	{
		if (isset($this->_headerFooterImages[$location])) {
			unset($this->_headerFooterImages[$location]);
		}

		return $this;
	}

	public function setImages($images)
	{
		if (!is_array($images)) {
			throw new PHPExcel_Exception('Invalid parameter!');
		}

		$this->_headerFooterImages = $images;
		return $this;
	}

	public function getImages()
	{
		$images = array();

		if (isset($this->_headerFooterImages[self::IMAGE_HEADER_LEFT])) {
			$images[self::IMAGE_HEADER_LEFT] = $this->_headerFooterImages[self::IMAGE_HEADER_LEFT];
		}

		if (isset($this->_headerFooterImages[self::IMAGE_HEADER_CENTER])) {
			$images[self::IMAGE_HEADER_CENTER] = $this->_headerFooterImages[self::IMAGE_HEADER_CENTER];
		}

		if (isset($this->_headerFooterImages[self::IMAGE_HEADER_RIGHT])) {
			$images[self::IMAGE_HEADER_RIGHT] = $this->_headerFooterImages[self::IMAGE_HEADER_RIGHT];
		}

		if (isset($this->_headerFooterImages[self::IMAGE_FOOTER_LEFT])) {
			$images[self::IMAGE_FOOTER_LEFT] = $this->_headerFooterImages[self::IMAGE_FOOTER_LEFT];
		}

		if (isset($this->_headerFooterImages[self::IMAGE_FOOTER_CENTER])) {
			$images[self::IMAGE_FOOTER_CENTER] = $this->_headerFooterImages[self::IMAGE_FOOTER_CENTER];
		}

		if (isset($this->_headerFooterImages[self::IMAGE_FOOTER_RIGHT])) {
			$images[self::IMAGE_FOOTER_RIGHT] = $this->_headerFooterImages[self::IMAGE_FOOTER_RIGHT];
		}

		$this->_headerFooterImages = $images;
		return $this->_headerFooterImages;
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

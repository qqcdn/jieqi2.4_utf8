<?php

class PHPExcel_Worksheet_MemoryDrawing extends PHPExcel_Worksheet_BaseDrawing implements PHPExcel_IComparable
{
	const RENDERING_DEFAULT = 'imagepng';
	const RENDERING_PNG = 'imagepng';
	const RENDERING_GIF = 'imagegif';
	const RENDERING_JPEG = 'imagejpeg';
	const MIMETYPE_DEFAULT = 'image/png';
	const MIMETYPE_PNG = 'image/png';
	const MIMETYPE_GIF = 'image/gif';
	const MIMETYPE_JPEG = 'image/jpeg';

	/**
	 * Image resource
	 *
	 * @var resource
	 */
	private $_imageResource;
	/**
	 * Rendering function
	 *
	 * @var string
	 */
	private $_renderingFunction;
	/**
	 * Mime type
	 *
	 * @var string
	 */
	private $_mimeType;
	/**
	 * Unique name
	 *
	 * @var string
	 */
	private $_uniqueName;

	public function __construct()
	{
		$this->_imageResource = NULL;
		$this->_renderingFunction = self::RENDERING_DEFAULT;
		$this->_mimeType = self::MIMETYPE_DEFAULT;
		$this->_uniqueName = md5(rand(0, 9999) . time() . rand(0, 9999));
		parent::__construct();
	}

	public function getImageResource()
	{
		return $this->_imageResource;
	}

	public function setImageResource($value = NULL)
	{
		$this->_imageResource = $value;

		if (!is_null($this->_imageResource)) {
			$this->_width = imagesx($this->_imageResource);
			$this->_height = imagesy($this->_imageResource);
		}

		return $this;
	}

	public function getRenderingFunction()
	{
		return $this->_renderingFunction;
	}

	public function setRenderingFunction($value = PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT)
	{
		$this->_renderingFunction = $value;
		return $this;
	}

	public function getMimeType()
	{
		return $this->_mimeType;
	}

	public function setMimeType($value = PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_DEFAULT)
	{
		$this->_mimeType = $value;
		return $this;
	}

	public function getIndexedFilename()
	{
		$extension = strtolower($this->getMimeType());
		$extension = explode('/', $extension);
		$extension = $extension[1];
		return $this->_uniqueName . $this->getImageIndex() . '.' . $extension;
	}

	public function getHashCode()
	{
		return md5($this->_renderingFunction . $this->_mimeType . $this->_uniqueName . parent::getHashCode() . 'PHPExcel_Worksheet_MemoryDrawing');
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

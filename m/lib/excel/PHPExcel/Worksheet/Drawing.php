<?php

class PHPExcel_Worksheet_Drawing extends PHPExcel_Worksheet_BaseDrawing implements PHPExcel_IComparable
{
	/**
	 * Path
	 *
	 * @var string
	 */
	private $_path;

	public function __construct()
	{
		$this->_path = '';
		parent::__construct();
	}

	public function getFilename()
	{
		return basename($this->_path);
	}

	public function getIndexedFilename()
	{
		$fileName = $this->getFilename();
		$fileName = str_replace(' ', '_', $fileName);
		return str_replace('.' . $this->getExtension(), '', $fileName) . $this->getImageIndex() . '.' . $this->getExtension();
	}

	public function getExtension()
	{
		$exploded = explode('.', basename($this->_path));
		return $exploded[count($exploded) - 1];
	}

	public function getPath()
	{
		return $this->_path;
	}

	public function setPath($pValue = '', $pVerifyFile = true)
	{
		if ($pVerifyFile) {
			if (file_exists($pValue)) {
				$this->_path = $pValue;
				if (($this->_width == 0) && ($this->_height == 0)) {
					list($this->_width, $this->_height) = getimagesize($pValue);
				}
			}
			else {
				throw new PHPExcel_Exception('File ' . $pValue . ' not found!');
			}
		}
		else {
			$this->_path = $pValue;
		}

		return $this;
	}

	public function getHashCode()
	{
		return md5($this->_path . parent::getHashCode() . 'PHPExcel_Worksheet_Drawing');
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

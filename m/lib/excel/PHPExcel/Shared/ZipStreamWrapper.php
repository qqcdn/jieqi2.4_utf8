<?php

class PHPExcel_Shared_ZipStreamWrapper
{
	/**
	 * Internal ZipAcrhive
	 *
	 * @var ZipAcrhive
	 */
	private $_archive;
	/**
     * Filename in ZipAcrhive
     *
     * @var string
     */
	private $_fileNameInArchive = '';
	/**
     * Position in file
     *
     * @var int
     */
	private $_position = 0;
	/**
     * Data
     *
     * @var mixed
     */
	private $_data = '';

	static public function register()
	{
		@stream_wrapper_unregister('zip');
		@stream_wrapper_register('zip', 'PHPExcel_Shared_ZipStreamWrapper');
	}

	public function stream_open($path, $mode, $options, &$opened_path)
	{
		if ($mode[0] != 'r') {
			throw new PHPExcel_Reader_Exception('Mode ' . $mode . ' is not supported. Only read mode is supported.');
		}

		$pos = strrpos($path, '#');
		$url['host'] = substr($path, 6, $pos - 6);
		$url['fragment'] = substr($path, $pos + 1);
		$this->_archive = new ZipArchive();
		$this->_archive->open($url['host']);
		$this->_fileNameInArchive = $url['fragment'];
		$this->_position = 0;
		$this->_data = $this->_archive->getFromName($this->_fileNameInArchive);
		return true;
	}

	public function statName()
	{
		return $this->_fileNameInArchive;
	}

	public function url_stat()
	{
		return $this->statName($this->_fileNameInArchive);
	}

	public function stream_stat()
	{
		return $this->_archive->statName($this->_fileNameInArchive);
	}

	public function stream_read($count)
	{
		$ret = substr($this->_data, $this->_position, $count);
		$this->_position += strlen($ret);
		return $ret;
	}

	public function stream_tell()
	{
		return $this->_position;
	}

	public function stream_eof()
	{
		return strlen($this->_data) <= $this->_position;
	}

	public function stream_seek($offset, $whence)
	{
		switch ($whence) {
		case SEEK_SET:
			if (($offset < strlen($this->_data)) && (0 <= $offset)) {
				$this->_position = $offset;
				return true;
			}
			else {
				return false;
			}

			break;

		case SEEK_CUR:
			if (0 <= $offset) {
				$this->_position += $offset;
				return true;
			}
			else {
				return false;
			}

			break;

		case SEEK_END:
			if (0 <= strlen($this->_data) + $offset) {
				$this->_position = strlen($this->_data) + $offset;
				return true;
			}
			else {
				return false;
			}

			break;

		default:
			return false;
		}
	}
}


?>

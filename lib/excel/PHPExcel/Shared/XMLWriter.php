<?php

if (!defined('DATE_W3C')) {
	define('DATE_W3C', 'Y-m-d\\TH:i:sP');
}

if (!defined('DEBUGMODE_ENABLED')) {
	define('DEBUGMODE_ENABLED', false);
}

class PHPExcel_Shared_XMLWriter extends XMLWriter
{
	const STORAGE_MEMORY = 1;
	const STORAGE_DISK = 2;

	/**
	 * Temporary filename
	 *
	 * @var string
	 */
	private $_tempFileName = '';

	public function __construct($pTemporaryStorage = self::STORAGE_MEMORY, $pTemporaryStorageFolder = NULL)
	{
		if ($pTemporaryStorage == self::STORAGE_MEMORY) {
			$this->openMemory();
		}
		else {
			if ($pTemporaryStorageFolder === NULL) {
				$pTemporaryStorageFolder = PHPExcel_Shared_File::sys_get_temp_dir();
			}

			$this->_tempFileName = @tempnam($pTemporaryStorageFolder, 'xml');

			if ($this->openUri($this->_tempFileName) === false) {
				$this->openMemory();
			}
		}

		if (DEBUGMODE_ENABLED) {
			$this->setIndent(true);
		}
	}

	public function __destruct()
	{
		if ($this->_tempFileName != '') {
			@unlink($this->_tempFileName);
		}
	}

	public function getData()
	{
		if ($this->_tempFileName == '') {
			return $this->outputMemory(true);
		}
		else {
			$this->flush();
			return file_get_contents($this->_tempFileName);
		}
	}

	public function writeRawData($text)
	{
		if (is_array($text)) {
			$text = implode("\n", $text);
		}

		if (method_exists($this, 'writeRaw')) {
			return $this->writeRaw(htmlspecialchars($text));
		}

		return $this->text($text);
	}
}

?>

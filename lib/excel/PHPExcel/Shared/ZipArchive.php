<?php

class PHPExcel_Shared_ZipArchive
{
	const OVERWRITE = 'OVERWRITE';
	const CREATE = 'CREATE';

	/**
	 * Temporary storage directory
	 *
	 * @var string
	 */
	private $_tempDir;
	/**
	 * Zip Archive Stream Handle
	 *
	 * @var string
	 */
	private $_zip;

	public function open($fileName)
	{
		$this->_tempDir = PHPExcel_Shared_File::sys_get_temp_dir();
		$this->_zip = new PclZip($fileName);
		return true;
	}

	public function close()
	{
	}

	public function addFromString($localname, $contents)
	{
		$filenameParts = pathinfo($localname);
		$handle = fopen($this->_tempDir . '/' . $filenameParts['basename'], 'wb');
		fwrite($handle, $contents);
		fclose($handle);
		$res = $this->_zip->add($this->_tempDir . '/' . $filenameParts['basename'], PCLZIP_OPT_REMOVE_PATH, $this->_tempDir, PCLZIP_OPT_ADD_PATH, $filenameParts['dirname']);

		if ($res == 0) {
			throw new PHPExcel_Writer_Exception('Error zipping files : ' . $this->_zip->errorInfo(true));
		}

		unlink($this->_tempDir . '/' . $filenameParts['basename']);
	}

	public function locateName($fileName)
	{
		$list = $this->_zip->listContent();
		$listCount = count($list);
		$list_index = -1;

		for ($i = 0; $i < $listCount; ++$i) {
			if ((strtolower($list[$i]['filename']) == strtolower($fileName)) || (strtolower($list[$i]['stored_filename']) == strtolower($fileName))) {
				$list_index = $i;
				break;
			}
		}

		return -1 < $list_index;
	}

	public function getFromName($fileName)
	{
		$list = $this->_zip->listContent();
		$listCount = count($list);
		$list_index = -1;

		for ($i = 0; $i < $listCount; ++$i) {
			if ((strtolower($list[$i]['filename']) == strtolower($fileName)) || (strtolower($list[$i]['stored_filename']) == strtolower($fileName))) {
				$list_index = $i;
				break;
			}
		}

		$extracted = '';

		if ($list_index != -1) {
			$extracted = $this->_zip->extractByIndex($list_index, PCLZIP_OPT_EXTRACT_AS_STRING);
		}
		else {
			$filename = substr($fileName, 1);
			$list_index = -1;

			for ($i = 0; $i < $listCount; ++$i) {
				if ((strtolower($list[$i]['filename']) == strtolower($fileName)) || (strtolower($list[$i]['stored_filename']) == strtolower($fileName))) {
					$list_index = $i;
					break;
				}
			}

			$extracted = $this->_zip->extractByIndex($list_index, PCLZIP_OPT_EXTRACT_AS_STRING);
		}

		if (is_array($extracted) && ($extracted != 0)) {
			$contents = $extracted[0]['content'];
		}

		return $contents;
	}
}

if (!defined('PCLZIP_TEMPORARY_DIR')) {
	define('PCLZIP_TEMPORARY_DIR', PHPExcel_Shared_File::sys_get_temp_dir());
}

require_once PHPEXCEL_ROOT . 'PHPExcel/Shared/PCLZip/pclzip.lib.php';

?>

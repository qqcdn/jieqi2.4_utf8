<?php

class PHPExcel_Shared_File
{
	static protected $_useUploadTempDirectory = false;

	static public function setUseUploadTempDirectory($useUploadTempDir = false)
	{
		self::$_useUploadTempDirectory = (bool) $useUploadTempDir;
	}

	static public function getUseUploadTempDirectory()
	{
		return self::$_useUploadTempDirectory;
	}

	static public function file_exists($pFilename)
	{
		if (strtolower(substr($pFilename, 0, 3)) == 'zip') {
			$zipFile = substr($pFilename, 6, strpos($pFilename, '#') - 6);
			$archiveFile = substr($pFilename, strpos($pFilename, '#') + 1);
			$zip = new ZipArchive();

			if ($zip->open($zipFile) === true) {
				$returnValue = $zip->getFromName($archiveFile) !== false;
				$zip->close();
				return $returnValue;
			}
			else {
				return false;
			}
		}
		else {
			return file_exists($pFilename);
		}
	}

	static public function realpath($pFilename)
	{
		$returnValue = '';

		if (file_exists($pFilename)) {
			$returnValue = realpath($pFilename);
		}

		if (($returnValue == '') || ($returnValue === NULL)) {
			$pathArray = explode('/', $pFilename);

			while ($pathArray[0] != '..') {
				for ($i = 0; $i < count($pathArray); ++$i) {
					if (($pathArray[$i] == '..') && (0 < $i)) {
						unset($pathArray[$i]);
						unset($pathArray[$i - 1]);
						break;
					}
				}
			}

			$returnValue = implode('/', $pathArray);
		}

		return $returnValue;
	}

	static public function sys_get_temp_dir()
	{
		if (self::$_useUploadTempDirectory) {
			if (ini_get('upload_tmp_dir') !== false) {
				if ($temp = ini_get('upload_tmp_dir')) {
					if (file_exists($temp)) {
						return realpath($temp);
					}
				}
			}
		}

		if (!function_exists('sys_get_temp_dir')) {
			if ($temp = getenv('TMP')) {
				if (!empty($temp) && file_exists($temp)) {
					return realpath($temp);
				}
			}

			if ($temp = getenv('TEMP')) {
				if (!empty($temp) && file_exists($temp)) {
					return realpath($temp);
				}
			}

			if ($temp = getenv('TMPDIR')) {
				if (!empty($temp) && file_exists($temp)) {
					return realpath($temp);
				}
			}

			$temp = tempnam(__FILE__, '');

			if (file_exists($temp)) {
				unlink($temp);
				return realpath(dirname($temp));
			}

			return NULL;
		}

		return realpath(sys_get_temp_dir());
	}
}


?>

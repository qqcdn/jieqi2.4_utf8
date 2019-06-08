<?php

class PHPExcel_Shared_CodePage
{
	static public function NumberToName($codePage = 1252)
	{
		switch ($codePage) {
		case 367:
			return 'ASCII';
			break;

		case 437:
			return 'CP437';
			break;

		case 720:
			throw new PHPExcel_Exception('Code page 720 not supported.');
			break;

		case 737:
			return 'CP737';
			break;

		case 775:
			return 'CP775';
			break;

		case 850:
			return 'CP850';
			break;

		case 852:
			return 'CP852';
			break;

		case 855:
			return 'CP855';
			break;

		case 857:
			return 'CP857';
			break;

		case 858:
			return 'CP858';
			break;

		case 860:
			return 'CP860';
			break;

		case 861:
			return 'CP861';
			break;

		case 862:
			return 'CP862';
			break;

		case 863:
			return 'CP863';
			break;

		case 864:
			return 'CP864';
			break;

		case 865:
			return 'CP865';
			break;

		case 866:
			return 'CP866';
			break;

		case 869:
			return 'CP869';
			break;

		case 874:
			return 'CP874';
			break;

		case 932:
			return 'CP932';
			break;

		case 936:
			return 'CP936';
			break;

		case 949:
			return 'CP949';
			break;

		case 950:
			return 'CP950';
			break;

		case 1200:
			return 'UTF-16LE';
			break;

		case 1250:
			return 'CP1250';
			break;

		case 1251:
			return 'CP1251';
			break;

		case 0:
		case 1252:
			return 'CP1252';
			break;

		case 1253:
			return 'CP1253';
			break;

		case 1254:
			return 'CP1254';
			break;

		case 1255:
			return 'CP1255';
			break;

		case 1256:
			return 'CP1256';
			break;

		case 1257:
			return 'CP1257';
			break;

		case 1258:
			return 'CP1258';
			break;

		case 1361:
			return 'CP1361';
			break;

		case 10000:
			return 'MAC';
			break;

		case 10006:
			return 'MACGREEK';
			break;

		case 10007:
			return 'MACCYRILLIC';
			break;

		case 10008:
			return 'CP936';
			break;

		case 10029:
			return 'MACCENTRALEUROPE';
			break;

		case 10079:
			return 'MACICELAND';
			break;

		case 10081:
			return 'MACTURKISH';
			break;

		case 32768:
			return 'MAC';
			break;

		case 32769:
			throw new PHPExcel_Exception('Code page 32769 not supported.');
			break;

		case 65000:
			return 'UTF-7';
			break;

		case 65001:
			return 'UTF-8';
			break;
		}

		throw new PHPExcel_Exception('Unknown codepage: ' . $codePage);
	}
}


?>

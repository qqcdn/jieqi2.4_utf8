<?php

class PHPExcel_Shared_PasswordHasher
{
	static public function hashPassword($pPassword = '')
	{
		$password = 0;
		$charPos = 1;
		$chars = preg_split('//', $pPassword, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($chars as $char) {
			$value = ord($char) << $charPos++;
			$rotated_bits = $value >> 15;
			$value &= 32767;
			$password ^= $value | $rotated_bits;
		}

		$password ^= strlen($pPassword);
		$password ^= 52811;
		return strtoupper(dechex($password));
	}
}


?>

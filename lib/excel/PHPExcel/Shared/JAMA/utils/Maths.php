<?php

function hypo($a, $b)
{
	if (abs($b) < abs($a)) {
		$r = $b / $a;
		$r = abs($a) * sqrt(1 + ($r * $r));
	}
	else if ($b != 0) {
		$r = $a / $b;
		$r = abs($b) * sqrt(1 + ($r * $r));
	}
	else {
		$r = 0;
	}

	return $r;
}


?>

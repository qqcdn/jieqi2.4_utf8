<?php

class EigenvalueDecomposition
{
	/**
	 *	Row and column dimension (square matrix).
	 *	@var int
	 */
	private $n;
	/**
	 *	Internal symmetry flag.
	 *	@var int
	 */
	private $issymmetric;
	/**
	 *	Arrays for internal storage of eigenvalues.
	 *	@var array
	 */
	private $d = array();
	private $e = array();
	/**
	 *	Array for internal storage of eigenvectors.
	 *	@var array
	 */
	private $V = array();
	/**
	*	Array for internal storage of nonsymmetric Hessenberg form.
	*	@var array
	*/
	private $H = array();
	/**
	*	Working storage for nonsymmetric algorithm.
	*	@var array
	*/
	private $ort;
	/**
	*	Used for complex scalar division.
	*	@var float
	*/
	private $cdivr;
	private $cdivi;

	private function tred2()
	{
		$this->d = $this->V[$this->n - 1];

		for ($i = $this->n - 1; 0 < $i; --$i) {
			$i_ = $i - 1;
			$h = $scale = 0;
			$scale += array_sum(array_map(abs, $this->d));

			if ($scale == 0) {
				$this->e[$i] = $this->d[$i_];
				$this->d = array_slice($this->V[$i_], 0, $i_);

				for ($j = 0; $j < $i; ++$j) {
					$this->V[$j][$i] = $this->V[$i][$j] = 0;
				}
			}
			else {
				for ($k = 0; $k < $i; ++$k) {
					$this->d[$k] /= $scale;
					$h += pow($this->d[$k], 2);
				}

				$f = $this->d[$i_];
				$g = sqrt($h);

				if (0 < $f) {
					$g = 0 - $g;
				}

				$this->e[$i] = $scale * $g;
				$h = $h - ($f * $g);
				$this->d[$i_] = $f - $g;

				for ($j = 0; $j < $i; ++$j) {
					$this->e[$j] = 0;
				}

				for ($j = 0; $j < $i; ++$j) {
					$f = $this->d[$j];
					$this->V[$j][$i] = $f;
					$g = $this->e[$j] + ($this->V[$j][$j] * $f);

					for ($k = $j + 1; $k <= $i_; ++$k) {
						$g += $this->V[$k][$j] * $this->d[$k];
						$this->e[$k] += $this->V[$k][$j] * $f;
					}

					$this->e[$j] = $g;
				}

				$f = 0;

				for ($j = 0; $j < $i; ++$j) {
					$this->e[$j] /= $h;
					$f += $this->e[$j] * $this->d[$j];
				}

				$hh = $f / (2 * $h);

				for ($j = 0; $j < $i; ++$j) {
					$this->e[$j] -= $hh * $this->d[$j];
				}

				for ($j = 0; $j < $i; ++$j) {
					$f = $this->d[$j];
					$g = $this->e[$j];

					for ($k = $j; $k <= $i_; ++$k) {
						$this->V[$k][$j] -= ($f * $this->e[$k]) + ($g * $this->d[$k]);
					}

					$this->d[$j] = $this->V[$i - 1][$j];
					$this->V[$i][$j] = 0;
				}
			}

			$this->d[$i] = $h;
		}

		for ($i = 0; $i < ($this->n - 1); ++$i) {
			$this->V[$this->n - 1][$i] = $this->V[$i][$i];
			$this->V[$i][$i] = 1;
			$h = $this->d[$i + 1];

			if ($h != 0) {
				for ($k = 0; $k <= $i; ++$k) {
					$this->d[$k] = $this->V[$k][$i + 1] / $h;
				}

				for ($j = 0; $j <= $i; ++$j) {
					$g = 0;

					for ($k = 0; $k <= $i; ++$k) {
						$g += $this->V[$k][$i + 1] * $this->V[$k][$j];
					}

					for ($k = 0; $k <= $i; ++$k) {
						$this->V[$k][$j] -= $g * $this->d[$k];
					}
				}
			}

			for ($k = 0; $k <= $i; ++$k) {
				$this->V[$k][$i + 1] = 0;
			}
		}

		$this->d = $this->V[$this->n - 1];
		$this->V[$this->n - 1] = array_fill(0, $j, 0);
		$this->V[$this->n - 1][$this->n - 1] = 1;
		$this->e[0] = 0;
	}

	private function tql2()
	{
		for ($i = 1; $i < $this->n; ++$i) {
			$this->e[$i - 1] = $this->e[$i];
		}

		$this->e[$this->n - 1] = 0;
		$f = 0;
		$tst1 = 0;
		$eps = pow(2, -52);

		for ($l = 0; $l < $this->n; ++$l) {
			$tst1 = max($tst1, abs($this->d[$l]) + abs($this->e[$l]));
			$m = $l;

			while ($m < $this->n) {
				if (abs($this->e[$m]) <= $eps * $tst1) {
					break;
				}

				++$m;
			}

			if ($l < $m) {
				$iter = 0;

				do {
					$iter += 1;
					$g = $this->d[$l];
					$p = ($this->d[$l + 1] - $g) / (2 * $this->e[$l]);
					$r = hypo($p, 1);

					if ($p < 0) {
						$r *= -1;
					}

					$this->d[$l] = $this->e[$l] / ($p + $r);
					$this->d[$l + 1] = $this->e[$l] * ($p + $r);
					$dl1 = $this->d[$l + 1];
					$h = $g - $this->d[$l];

					for ($i = $l + 2; $i < $this->n; ++$i) {
						$this->d[$i] -= $h;
					}

					$f += $h;
					$p = $this->d[$m];
					$c = 1;
					$c2 = $c3 = $c;
					$el1 = $this->e[$l + 1];
					$s = $s2 = 0;

					for ($i = $m - 1; $l <= $i; --$i) {
						$c3 = $c2;
						$c2 = $c;
						$s2 = $s;
						$g = $c * $this->e[$i];
						$h = $c * $p;
						$r = hypo($p, $this->e[$i]);
						$this->e[$i + 1] = $s * $r;
						$s = $this->e[$i] / $r;
						$c = $p / $r;
						$p = ($c * $this->d[$i]) - ($s * $g);
						$this->d[$i + 1] = $h + ($s * (($c * $g) + ($s * $this->d[$i])));

						for ($k = 0; $k < $this->n; ++$k) {
							$h = $this->V[$k][$i + 1];
							$this->V[$k][$i + 1] = ($s * $this->V[$k][$i]) + ($c * $h);
							$this->V[$k][$i] = ($c * $this->V[$k][$i]) - ($s * $h);
						}
					}

					$p = ((0 - $s) * $s2 * $c3 * $el1 * $this->e[$l]) / $dl1;
					$this->e[$l] = $s * $p;
					$this->d[$l] = $c * $p;
				} while (($eps * $tst1) < abs($this->e[$l]));
			}

			$this->d[$l] = $this->d[$l] + $f;
			$this->e[$l] = 0;
		}

		for ($i = 0; $i < ($this->n - 1); ++$i) {
			$k = $i;
			$p = $this->d[$i];

			for ($j = $i + 1; $j < $this->n; ++$j) {
				if ($this->d[$j] < $p) {
					$k = $j;
					$p = $this->d[$j];
				}
			}

			if ($k != $i) {
				$this->d[$k] = $this->d[$i];
				$this->d[$i] = $p;

				for ($j = 0; $j < $this->n; ++$j) {
					$p = $this->V[$j][$i];
					$this->V[$j][$i] = $this->V[$j][$k];
					$this->V[$j][$k] = $p;
				}
			}
		}
	}

	private function orthes()
	{
		$low = 0;
		$high = $this->n - 1;

		for ($m = $low + 1; $m <= $high - 1; ++$m) {
			$scale = 0;

			for ($i = $m; $i <= $high; ++$i) {
				$scale = $scale + abs($this->H[$i][$m - 1]);
			}

			if ($scale != 0) {
				$h = 0;

				for ($i = $high; $m <= $i; --$i) {
					$this->ort[$i] = $this->H[$i][$m - 1] / $scale;
					$h += $this->ort[$i] * $this->ort[$i];
				}

				$g = sqrt($h);

				if (0 < $this->ort[$m]) {
					$g *= -1;
				}

				$h -= $this->ort[$m] * $g;
				$this->ort[$m] -= $g;

				for ($j = $m; $j < $this->n; ++$j) {
					$f = 0;

					for ($i = $high; $m <= $i; --$i) {
						$f += $this->ort[$i] * $this->H[$i][$j];
					}

					$f /= $h;

					for ($i = $m; $i <= $high; ++$i) {
						$this->H[$i][$j] -= $f * $this->ort[$i];
					}
				}

				for ($i = 0; $i <= $high; ++$i) {
					$f = 0;

					for ($j = $high; $m <= $j; --$j) {
						$f += $this->ort[$j] * $this->H[$i][$j];
					}

					$f = $f / $h;

					for ($j = $m; $j <= $high; ++$j) {
						$this->H[$i][$j] -= $f * $this->ort[$j];
					}
				}

				$this->ort[$m] = $scale * $this->ort[$m];
				$this->H[$m][$m - 1] = $scale * $g;
			}
		}

		for ($i = 0; $i < $this->n; ++$i) {
			for ($j = 0; $j < $this->n; ++$j) {
				$this->V[$i][$j] = $i == $j ? 1 : 0;
			}
		}

		for ($m = $high - 1; ($low + 1) <= $m; --$m) {
			if ($this->H[$m][$m - 1] != 0) {
				for ($i = $m + 1; $i <= $high; ++$i) {
					$this->ort[$i] = $this->H[$i][$m - 1];
				}

				for ($j = $m; $j <= $high; ++$j) {
					$g = 0;

					for ($i = $m; $i <= $high; ++$i) {
						$g += $this->ort[$i] * $this->V[$i][$j];
					}

					$g = $g / $this->ort[$m] / $this->H[$m][$m - 1];

					for ($i = $m; $i <= $high; ++$i) {
						$this->V[$i][$j] += $g * $this->ort[$i];
					}
				}
			}
		}
	}

	private function cdiv($xr, $xi, $yr, $yi)
	{
		if (abs($yi) < abs($yr)) {
			$r = $yi / $yr;
			$d = $yr + ($r * $yi);
			$this->cdivr = ($xr + ($r * $xi)) / $d;
			$this->cdivi = ($xi - ($r * $xr)) / $d;
		}
		else {
			$r = $yr / $yi;
			$d = $yi + ($r * $yr);
			$this->cdivr = (($r * $xr) + $xi) / $d;
			$this->cdivi = (($r * $xi) - $xr) / $d;
		}
	}

	private function hqr2()
	{
		$nn = $this->n;
		$n = $nn - 1;
		$low = 0;
		$high = $nn - 1;
		$eps = pow(2, -52);
		$exshift = 0;
		$p = $q = $r = $s = $z = 0;
		$norm = 0;

		for ($i = 0; $i < $nn; ++$i) {
			if (($i < $low) || ($high < $i)) {
				$this->d[$i] = $this->H[$i][$i];
				$this->e[$i] = 0;
			}

			for ($j = max($i - 1, 0); $j < $nn; ++$j) {
				$norm = $norm + abs($this->H[$i][$j]);
			}
		}

		$iter = 0;

		while ($low <= $n) {
			$l = $n;

			while ($low < $l) {
				$s = abs($this->H[$l - 1][$l - 1]) + abs($this->H[$l][$l]);

				if ($s == 0) {
					$s = $norm;
				}

				if (abs($this->H[$l][$l - 1]) < ($eps * $s)) {
					break;
				}

				--$l;
			}

			if ($l == $n) {
				$this->H[$n][$n] = $this->H[$n][$n] + $exshift;
				$this->d[$n] = $this->H[$n][$n];
				$this->e[$n] = 0;
				--$n;
				$iter = 0;
			}
			else if ($l == ($n - 1)) {
				$w = $this->H[$n][$n - 1] * $this->H[$n - 1][$n];
				$p = ($this->H[$n - 1][$n - 1] - $this->H[$n][$n]) / 2;
				$q = ($p * $p) + $w;
				$z = sqrt(abs($q));
				$this->H[$n][$n] = $this->H[$n][$n] + $exshift;
				$this->H[$n - 1][$n - 1] = $this->H[$n - 1][$n - 1] + $exshift;
				$x = $this->H[$n][$n];

				if (0 <= $q) {
					if (0 <= $p) {
						$z = $p + $z;
					}
					else {
						$z = $p - $z;
					}

					$this->d[$n - 1] = $x + $z;
					$this->d[$n] = $this->d[$n - 1];

					if ($z != 0) {
						$this->d[$n] = $x - ($w / $z);
					}

					$this->e[$n - 1] = 0;
					$this->e[$n] = 0;
					$x = $this->H[$n][$n - 1];
					$s = abs($x) + abs($z);
					$p = $x / $s;
					$q = $z / $s;
					$r = sqrt(($p * $p) + ($q * $q));
					$p = $p / $r;
					$q = $q / $r;

					for ($j = $n - 1; $j < $nn; ++$j) {
						$z = $this->H[$n - 1][$j];
						$this->H[$n - 1][$j] = ($q * $z) + ($p * $this->H[$n][$j]);
						$this->H[$n][$j] = ($q * $this->H[$n][$j]) - ($p * $z);
					}

					for ($i = 0; $i <= n; ++$i) {
						$z = $this->H[$i][$n - 1];
						$this->H[$i][$n - 1] = ($q * $z) + ($p * $this->H[$i][$n]);
						$this->H[$i][$n] = ($q * $this->H[$i][$n]) - ($p * $z);
					}

					for ($i = $low; $i <= $high; ++$i) {
						$z = $this->V[$i][$n - 1];
						$this->V[$i][$n - 1] = ($q * $z) + ($p * $this->V[$i][$n]);
						$this->V[$i][$n] = ($q * $this->V[$i][$n]) - ($p * $z);
					}
				}
				else {
					$this->d[$n - 1] = $x + $p;
					$this->d[$n] = $x + $p;
					$this->e[$n - 1] = $z;
					$this->e[$n] = 0 - $z;
				}

				$n = $n - 2;
				$iter = 0;
			}
			else {
				$x = $this->H[$n][$n];
				$y = 0;
				$w = 0;

				if ($l < $n) {
					$y = $this->H[$n - 1][$n - 1];
					$w = $this->H[$n][$n - 1] * $this->H[$n - 1][$n];
				}

				if ($iter == 10) {
					$exshift += $x;

					for ($i = $low; $i <= $n; ++$i) {
						$this->H[$i][$i] -= $x;
					}

					$s = abs($this->H[$n][$n - 1]) + abs($this->H[$n - 1][$n - 2]);
					$x = $y = 0.75 * $s;
					$w = -0.4375 * $s * $s;
				}

				if ($iter == 30) {
					$s = ($y - $x) / 2;
					$s = ($s * $s) + $w;

					if (0 < $s) {
						$s = sqrt($s);

						if ($y < $x) {
							$s = 0 - $s;
						}

						$s = $x - ($w / ((($y - $x) / 2) + $s));

						for ($i = $low; $i <= $n; ++$i) {
							$this->H[$i][$i] -= $s;
						}

						$exshift += $s;
						$x = $y = $w = 0.964;
					}
				}

				$iter = $iter + 1;
				$m = $n - 2;

				while ($l <= $m) {
					$z = $this->H[$m][$m];
					$r = $x - $z;
					$s = $y - $z;
					$p = ((($r * $s) - $w) / $this->H[$m + 1][$m]) + $this->H[$m][$m + 1];
					$q = $this->H[$m + 1][$m + 1] - $z - $r - $s;
					$r = $this->H[$m + 2][$m + 1];
					$s = abs($p) + abs($q) + abs($r);
					$p = $p / $s;
					$q = $q / $s;
					$r = $r / $s;

					if ($m == $l) {
						break;
					}

					if ((abs($this->H[$m][$m - 1]) * (abs($q) + abs($r))) < ($eps * abs($p) * (abs($this->H[$m - 1][$m - 1]) + abs($z) + abs($this->H[$m + 1][$m + 1])))) {
						break;
					}

					--$m;
				}

				for ($i = $m + 2; $i <= $n; ++$i) {
					$this->H[$i][$i - 2] = 0;

					if (($m + 2) < $i) {
						$this->H[$i][$i - 3] = 0;
					}
				}

				for ($k = $m; $k <= $n - 1; ++$k) {
					$notlast = $k != ($n - 1);

					if ($k != $m) {
						$p = $this->H[$k][$k - 1];
						$q = $this->H[$k + 1][$k - 1];
						$r = ($notlast ? $this->H[$k + 2][$k - 1] : 0);
						$x = abs($p) + abs($q) + abs($r);

						if ($x != 0) {
							$p = $p / $x;
							$q = $q / $x;
							$r = $r / $x;
						}
					}

					if ($x == 0) {
						break;
					}

					$s = sqrt(($p * $p) + ($q * $q) + ($r * $r));

					if ($p < 0) {
						$s = 0 - $s;
					}

					if ($s != 0) {
						if ($k != $m) {
							$this->H[$k][$k - 1] = (0 - $s) * $x;
						}
						else if ($l != $m) {
							$this->H[$k][$k - 1] = 0 - $this->H[$k][$k - 1];
						}

						$p = $p + $s;
						$x = $p / $s;
						$y = $q / $s;
						$z = $r / $s;
						$q = $q / $p;
						$r = $r / $p;

						for ($j = $k; $j < $nn; ++$j) {
							$p = $this->H[$k][$j] + ($q * $this->H[$k + 1][$j]);

							if ($notlast) {
								$p = $p + ($r * $this->H[$k + 2][$j]);
								$this->H[$k + 2][$j] = $this->H[$k + 2][$j] - ($p * $z);
							}

							$this->H[$k][$j] = $this->H[$k][$j] - ($p * $x);
							$this->H[$k + 1][$j] = $this->H[$k + 1][$j] - ($p * $y);
						}

						for ($i = 0; $i <= min($n, $k + 3); ++$i) {
							$p = ($x * $this->H[$i][$k]) + ($y * $this->H[$i][$k + 1]);

							if ($notlast) {
								$p = $p + ($z * $this->H[$i][$k + 2]);
								$this->H[$i][$k + 2] = $this->H[$i][$k + 2] - ($p * $r);
							}

							$this->H[$i][$k] = $this->H[$i][$k] - $p;
							$this->H[$i][$k + 1] = $this->H[$i][$k + 1] - ($p * $q);
						}

						for ($i = $low; $i <= $high; ++$i) {
							$p = ($x * $this->V[$i][$k]) + ($y * $this->V[$i][$k + 1]);

							if ($notlast) {
								$p = $p + ($z * $this->V[$i][$k + 2]);
								$this->V[$i][$k + 2] = $this->V[$i][$k + 2] - ($p * $r);
							}

							$this->V[$i][$k] = $this->V[$i][$k] - $p;
							$this->V[$i][$k + 1] = $this->V[$i][$k + 1] - ($p * $q);
						}
					}
				}
			}
		}

		if ($norm == 0) {
			return NULL;
		}

		for ($n = $nn - 1; 0 <= $n; --$n) {
			$p = $this->d[$n];
			$q = $this->e[$n];

			if ($q == 0) {
				$l = $n;
				$this->H[$n][$n] = 1;

				for ($i = $n - 1; 0 <= $i; --$i) {
					$w = $this->H[$i][$i] - $p;
					$r = 0;

					for ($j = $l; $j <= $n; ++$j) {
						$r = $r + ($this->H[$i][$j] * $this->H[$j][$n]);
					}

					if ($this->e[$i] < 0) {
						$z = $w;
						$s = $r;
					}
					else {
						$l = $i;

						if ($this->e[$i] == 0) {
							if ($w != 0) {
								$this->H[$i][$n] = (0 - $r) / $w;
							}
							else {
								$this->H[$i][$n] = (0 - $r) / ($eps * $norm);
							}
						}
						else {
							$x = $this->H[$i][$i + 1];
							$y = $this->H[$i + 1][$i];
							$q = (($this->d[$i] - $p) * ($this->d[$i] - $p)) + ($this->e[$i] * $this->e[$i]);
							$t = (($x * $s) - ($z * $r)) / $q;
							$this->H[$i][$n] = $t;

							if (abs($z) < abs($x)) {
								$this->H[$i + 1][$n] = (0 - $r - ($w * $t)) / $x;
							}
							else {
								$this->H[$i + 1][$n] = (0 - $s - ($y * $t)) / $z;
							}
						}

						$t = abs($this->H[$i][$n]);

						if (1 < ($eps * $t * $t)) {
							for ($j = $i; $j <= $n; ++$j) {
								$this->H[$j][$n] = $this->H[$j][$n] / $t;
							}
						}
					}
				}
			}
			else if ($q < 0) {
				$l = $n - 1;

				if (abs($this->H[$n - 1][$n]) < abs($this->H[$n][$n - 1])) {
					$this->H[$n - 1][$n - 1] = $q / $this->H[$n][$n - 1];
					$this->H[$n - 1][$n] = (0 - $this->H[$n][$n] - $p) / $this->H[$n][$n - 1];
				}
				else {
					$this->cdiv(0, 0 - $this->H[$n - 1][$n], $this->H[$n - 1][$n - 1] - $p, $q);
					$this->H[$n - 1][$n - 1] = $this->cdivr;
					$this->H[$n - 1][$n] = $this->cdivi;
				}

				$this->H[$n][$n - 1] = 0;
				$this->H[$n][$n] = 1;

				for ($i = $n - 2; 0 <= $i; --$i) {
					$ra = 0;
					$sa = 0;

					for ($j = $l; $j <= $n; ++$j) {
						$ra = $ra + ($this->H[$i][$j] * $this->H[$j][$n - 1]);
						$sa = $sa + ($this->H[$i][$j] * $this->H[$j][$n]);
					}

					$w = $this->H[$i][$i] - $p;

					if ($this->e[$i] < 0) {
						$z = $w;
						$r = $ra;
						$s = $sa;
					}
					else {
						$l = $i;

						if ($this->e[$i] == 0) {
							$this->cdiv(0 - $ra, 0 - $sa, $w, $q);
							$this->H[$i][$n - 1] = $this->cdivr;
							$this->H[$i][$n] = $this->cdivi;
						}
						else {
							$x = $this->H[$i][$i + 1];
							$y = $this->H[$i + 1][$i];
							$vr = ((($this->d[$i] - $p) * ($this->d[$i] - $p)) + ($this->e[$i] * $this->e[$i])) - ($q * $q);
							$vi = ($this->d[$i] - $p) * 2 * $q;

							if (($vr == 0) & ($vi == 0)) {
								$vr = $eps * $norm * (abs($w) + abs($q) + abs($x) + abs($y) + abs($z));
							}

							$this->cdiv((($x * $r) - ($z * $ra)) + ($q * $sa), ($x * $s) - ($z * $sa) - ($q * $ra), $vr, $vi);
							$this->H[$i][$n - 1] = $this->cdivr;
							$this->H[$i][$n] = $this->cdivi;

							if ((abs($z) + abs($q)) < abs($x)) {
								$this->H[$i + 1][$n - 1] = ((0 - $ra - ($w * $this->H[$i][$n - 1])) + ($q * $this->H[$i][$n])) / $x;
								$this->H[$i + 1][$n] = (0 - $sa - ($w * $this->H[$i][$n]) - ($q * $this->H[$i][$n - 1])) / $x;
							}
							else {
								$this->cdiv(0 - $r - ($y * $this->H[$i][$n - 1]), 0 - $s - ($y * $this->H[$i][$n]), $z, $q);
								$this->H[$i + 1][$n - 1] = $this->cdivr;
								$this->H[$i + 1][$n] = $this->cdivi;
							}
						}

						$t = max(abs($this->H[$i][$n - 1]), abs($this->H[$i][$n]));

						if (1 < ($eps * $t * $t)) {
							for ($j = $i; $j <= $n; ++$j) {
								$this->H[$j][$n - 1] = $this->H[$j][$n - 1] / $t;
								$this->H[$j][$n] = $this->H[$j][$n] / $t;
							}
						}
					}
				}
			}
		}

		for ($i = 0; $i < $nn; ++$i) {
			if (($i < $low) | ($high < $i)) {
				for ($j = $i; $j < $nn; ++$j) {
					$this->V[$i][$j] = $this->H[$i][$j];
				}
			}
		}

		for ($j = $nn - 1; $low <= $j; --$j) {
			for ($i = $low; $i <= $high; ++$i) {
				$z = 0;

				for ($k = $low; $k <= min($j, $high); ++$k) {
					$z = $z + ($this->V[$i][$k] * $this->H[$k][$j]);
				}

				$this->V[$i][$j] = $z;
			}
		}
	}

	public function __construct($Arg)
	{
		$this->A = $Arg->getArray();
		$this->n = $Arg->getColumnDimension();
		$issymmetric = true;

		for ($j = 0; ($j < $this->n) & $issymmetric; ++$j) {
			for ($i = 0; ($i < $this->n) & $issymmetric; ++$i) {
				$issymmetric = $this->A[$i][$j] == $this->A[$j][$i];
			}
		}

		if ($issymmetric) {
			$this->V = $this->A;
			$this->tred2();
			$this->tql2();
		}
		else {
			$this->H = $this->A;
			$this->ort = array();
			$this->orthes();
			$this->hqr2();
		}
	}

	public function getV()
	{
		return new Matrix($this->V, $this->n, $this->n);
	}

	public function getRealEigenvalues()
	{
		return $this->d;
	}

	public function getImagEigenvalues()
	{
		return $this->e;
	}

	public function getD()
	{
		for ($i = 0; $i < $this->n; ++$i) {
			$D[$i] = array_fill(0, $this->n, 0);
			$D[$i][$i] = $this->d[$i];

			if ($this->e[$i] == 0) {
				continue;
			}

			$o = (0 < $this->e[$i] ? $i + 1 : $i - 1);
			$D[$i][$o] = $this->e[$i];
		}

		return new Matrix($D);
	}
}


?>

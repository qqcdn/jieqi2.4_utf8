<?php

class SingularValueDecomposition
{
	/**
	 *	Internal storage of U.
	 *	@var array
	 */
	private $U = array();
	/**
	 *	Internal storage of V.
	 *	@var array
	 */
	private $V = array();
	/**
	 *	Internal storage of singular values.
	 *	@var array
	 */
	private $s = array();
	/**
	 *	Row dimension.
	 *	@var int
	 */
	private $m;
	/**
	 *	Column dimension.
	 *	@var int
	 */
	private $n;

	public function __construct($Arg)
	{
		$A = $Arg->getArrayCopy();
		$this->m = $Arg->getRowDimension();
		$this->n = $Arg->getColumnDimension();
		$nu = min($this->m, $this->n);
		$e = array();
		$work = array();
		$wantu = true;
		$wantv = true;
		$nct = min($this->m - 1, $this->n);
		$nrt = max(0, min($this->n - 2, $this->m));

		for ($k = 0; $k < max($nct, $nrt); ++$k) {
			if ($k < $nct) {
				$this->s[$k] = 0;

				for ($i = $k; $i < $this->m; ++$i) {
					$this->s[$k] = hypo($this->s[$k], $A[$i][$k]);
				}

				if ($this->s[$k] != 0) {
					if ($A[$k][$k] < 0) {
						$this->s[$k] = 0 - $this->s[$k];
					}

					for ($i = $k; $i < $this->m; ++$i) {
						$A[$i][$k] /= $this->s[$k];
					}

					$A[$k][$k] += 1;
				}

				$this->s[$k] = 0 - $this->s[$k];
			}

			for ($j = $k + 1; $j < $this->n; ++$j) {
				if (($k < $nct) & ($this->s[$k] != 0)) {
					$t = 0;

					for ($i = $k; $i < $this->m; ++$i) {
						$t += $A[$i][$k] * $A[$i][$j];
					}

					$t = (0 - $t) / $A[$k][$k];

					for ($i = $k; $i < $this->m; ++$i) {
						$A[$i][$j] += $t * $A[$i][$k];
					}

					$e[$j] = $A[$k][$j];
				}
			}

			if ($wantu && ($k < $nct)) {
				for ($i = $k; $i < $this->m; ++$i) {
					$this->U[$i][$k] = $A[$i][$k];
				}
			}

			if ($k < $nrt) {
				$e[$k] = 0;

				for ($i = $k + 1; $i < $this->n; ++$i) {
					$e[$k] = hypo($e[$k], $e[$i]);
				}

				if ($e[$k] != 0) {
					if ($e[$k + 1] < 0) {
						$e[$k] = 0 - $e[$k];
					}

					for ($i = $k + 1; $i < $this->n; ++$i) {
						$e[$i] /= $e[$k];
					}

					$e[$k + 1] += 1;
				}

				$e[$k] = 0 - $e[$k];
				if ((($k + 1) < $this->m) && ($e[$k] != 0)) {
					for ($i = $k + 1; $i < $this->m; ++$i) {
						$work[$i] = 0;
					}

					for ($j = $k + 1; $j < $this->n; ++$j) {
						for ($i = $k + 1; $i < $this->m; ++$i) {
							$work[$i] += $e[$j] * $A[$i][$j];
						}
					}

					for ($j = $k + 1; $j < $this->n; ++$j) {
						$t = (0 - $e[$j]) / $e[$k + 1];

						for ($i = $k + 1; $i < $this->m; ++$i) {
							$A[$i][$j] += $t * $work[$i];
						}
					}
				}

				if ($wantv) {
					for ($i = $k + 1; $i < $this->n; ++$i) {
						$this->V[$i][$k] = $e[$i];
					}
				}
			}
		}

		$p = min($this->n, $this->m + 1);

		if ($nct < $this->n) {
			$this->s[$nct] = $A[$nct][$nct];
		}

		if ($this->m < $p) {
			$this->s[$p - 1] = 0;
		}

		if (($nrt + 1) < $p) {
			$e[$nrt] = $A[$nrt][$p - 1];
		}

		$e[$p - 1] = 0;

		if ($wantu) {
			for ($j = $nct; $j < $nu; ++$j) {
				for ($i = 0; $i < $this->m; ++$i) {
					$this->U[$i][$j] = 0;
				}

				$this->U[$j][$j] = 1;
			}

			for ($k = $nct - 1; 0 <= $k; --$k) {
				if ($this->s[$k] != 0) {
					for ($j = $k + 1; $j < $nu; ++$j) {
						$t = 0;

						for ($i = $k; $i < $this->m; ++$i) {
							$t += $this->U[$i][$k] * $this->U[$i][$j];
						}

						$t = (0 - $t) / $this->U[$k][$k];

						for ($i = $k; $i < $this->m; ++$i) {
							$this->U[$i][$j] += $t * $this->U[$i][$k];
						}
					}

					for ($i = $k; $i < $this->m; ++$i) {
						$this->U[$i][$k] = 0 - $this->U[$i][$k];
					}

					$this->U[$k][$k] = 1 + $this->U[$k][$k];

					for ($i = 0; $i < ($k - 1); ++$i) {
						$this->U[$i][$k] = 0;
					}
				}
				else {
					for ($i = 0; $i < $this->m; ++$i) {
						$this->U[$i][$k] = 0;
					}

					$this->U[$k][$k] = 1;
				}
			}
		}

		if ($wantv) {
			for ($k = $this->n - 1; 0 <= $k; --$k) {
				if (($k < $nrt) && ($e[$k] != 0)) {
					for ($j = $k + 1; $j < $nu; ++$j) {
						$t = 0;

						for ($i = $k + 1; $i < $this->n; ++$i) {
							$t += $this->V[$i][$k] * $this->V[$i][$j];
						}

						$t = (0 - $t) / $this->V[$k + 1][$k];

						for ($i = $k + 1; $i < $this->n; ++$i) {
							$this->V[$i][$j] += $t * $this->V[$i][$k];
						}
					}
				}

				for ($i = 0; $i < $this->n; ++$i) {
					$this->V[$i][$k] = 0;
				}

				$this->V[$k][$k] = 1;
			}
		}

		$pp = $p - 1;
		$iter = 0;
		$eps = pow(2, -52);

		while (0 < $p) {
			for ($k = $p - 2; -1 <= $k; --$k) {
				if ($k == -1) {
					break;
				}

				if (abs($e[$k]) <= $eps * (abs($this->s[$k]) + abs($this->s[$k + 1]))) {
					$e[$k] = 0;
					break;
				}
			}

			if ($k == ($p - 2)) {
				$kase = 4;
			}
			else {
				for ($ks = $p - 1; $k <= $ks; --$ks) {
					if ($ks == $k) {
						break;
					}

					$t = ($ks != $p ? abs($e[$ks]) : 0) + ($ks != ($k + 1) ? abs($e[$ks - 1]) : 0);

					if (abs($this->s[$ks]) <= $eps * $t) {
						$this->s[$ks] = 0;
						break;
					}
				}

				if ($ks == $k) {
					$kase = 3;
				}
				else if ($ks == ($p - 1)) {
					$kase = 1;
				}
				else {
					$kase = 2;
					$k = $ks;
				}
			}

			++$k;

			switch ($kase) {
			case 1:
				$f = $e[$p - 2];
				$e[$p - 2] = 0;

				for ($j = $p - 2; $k <= $j; --$j) {
					$t = hypo($this->s[$j], $f);
					$cs = $this->s[$j] / $t;
					$sn = $f / $t;
					$this->s[$j] = $t;

					if ($j != $k) {
						$f = (0 - $sn) * $e[$j - 1];
						$e[$j - 1] = $cs * $e[$j - 1];
					}

					if ($wantv) {
						for ($i = 0; $i < $this->n; ++$i) {
							$t = ($cs * $this->V[$i][$j]) + ($sn * $this->V[$i][$p - 1]);
							$this->V[$i][$p - 1] = ((0 - $sn) * $this->V[$i][$j]) + ($cs * $this->V[$i][$p - 1]);
							$this->V[$i][$j] = $t;
						}
					}
				}

				break;

			case 2:
				$f = $e[$k - 1];
				$e[$k - 1] = 0;

				for ($j = $k; $j < $p; ++$j) {
					$t = hypo($this->s[$j], $f);
					$cs = $this->s[$j] / $t;
					$sn = $f / $t;
					$this->s[$j] = $t;
					$f = (0 - $sn) * $e[$j];
					$e[$j] = $cs * $e[$j];

					if ($wantu) {
						for ($i = 0; $i < $this->m; ++$i) {
							$t = ($cs * $this->U[$i][$j]) + ($sn * $this->U[$i][$k - 1]);
							$this->U[$i][$k - 1] = ((0 - $sn) * $this->U[$i][$j]) + ($cs * $this->U[$i][$k - 1]);
							$this->U[$i][$j] = $t;
						}
					}
				}

				break;

			case 3:
				$scale = max(max(max(max(abs($this->s[$p - 1]), abs($this->s[$p - 2])), abs($e[$p - 2])), abs($this->s[$k])), abs($e[$k]));
				$sp = $this->s[$p - 1] / $scale;
				$spm1 = $this->s[$p - 2] / $scale;
				$epm1 = $e[$p - 2] / $scale;
				$sk = $this->s[$k] / $scale;
				$ek = $e[$k] / $scale;
				$b = ((($spm1 + $sp) * ($spm1 - $sp)) + ($epm1 * $epm1)) / 2;
				$c = $sp * $epm1 * $sp * $epm1;
				$shift = 0;
				if (($b != 0) || ($c != 0)) {
					$shift = sqrt(($b * $b) + $c);

					if ($b < 0) {
						$shift = 0 - $shift;
					}

					$shift = $c / ($b + $shift);
				}

				$f = (($sk + $sp) * ($sk - $sp)) + $shift;
				$g = $sk * $ek;

				for ($j = $k; $j < ($p - 1); ++$j) {
					$t = hypo($f, $g);
					$cs = $f / $t;
					$sn = $g / $t;

					if ($j != $k) {
						$e[$j - 1] = $t;
					}

					$f = ($cs * $this->s[$j]) + ($sn * $e[$j]);
					$e[$j] = ($cs * $e[$j]) - ($sn * $this->s[$j]);
					$g = $sn * $this->s[$j + 1];
					$this->s[$j + 1] = $cs * $this->s[$j + 1];

					if ($wantv) {
						for ($i = 0; $i < $this->n; ++$i) {
							$t = ($cs * $this->V[$i][$j]) + ($sn * $this->V[$i][$j + 1]);
							$this->V[$i][$j + 1] = ((0 - $sn) * $this->V[$i][$j]) + ($cs * $this->V[$i][$j + 1]);
							$this->V[$i][$j] = $t;
						}
					}

					$t = hypo($f, $g);
					$cs = $f / $t;
					$sn = $g / $t;
					$this->s[$j] = $t;
					$f = ($cs * $e[$j]) + ($sn * $this->s[$j + 1]);
					$this->s[$j + 1] = ((0 - $sn) * $e[$j]) + ($cs * $this->s[$j + 1]);
					$g = $sn * $e[$j + 1];
					$e[$j + 1] = $cs * $e[$j + 1];
					if ($wantu && ($j < ($this->m - 1))) {
						for ($i = 0; $i < $this->m; ++$i) {
							$t = ($cs * $this->U[$i][$j]) + ($sn * $this->U[$i][$j + 1]);
							$this->U[$i][$j + 1] = ((0 - $sn) * $this->U[$i][$j]) + ($cs * $this->U[$i][$j + 1]);
							$this->U[$i][$j] = $t;
						}
					}
				}

				$e[$p - 2] = $f;
				$iter = $iter + 1;
				break;

			case 4:
				if ($this->s[$k] <= 0) {
					$this->s[$k] = $this->s[$k] < 0 ? 0 - $this->s[$k] : 0;

					if ($wantv) {
						for ($i = 0; $i <= $pp; ++$i) {
							$this->V[$i][$k] = 0 - $this->V[$i][$k];
						}
					}
				}

				while ($k < $pp) {
					if ($this->s[$k + 1] <= $this->s[$k]) {
						break;
					}

					$t = $this->s[$k];
					$this->s[$k] = $this->s[$k + 1];
					$this->s[$k + 1] = $t;
					if ($wantv && ($k < ($this->n - 1))) {
						for ($i = 0; $i < $this->n; ++$i) {
							$t = $this->V[$i][$k + 1];
							$this->V[$i][$k + 1] = $this->V[$i][$k];
							$this->V[$i][$k] = $t;
						}
					}

					if ($wantu && ($k < ($this->m - 1))) {
						for ($i = 0; $i < $this->m; ++$i) {
							$t = $this->U[$i][$k + 1];
							$this->U[$i][$k + 1] = $this->U[$i][$k];
							$this->U[$i][$k] = $t;
						}
					}

					++$k;
				}

				$iter = 0;
				--$p;
				break;
			}
		}
	}

	public function getU()
	{
		return new Matrix($this->U, $this->m, min($this->m + 1, $this->n));
	}

	public function getV()
	{
		return new Matrix($this->V);
	}

	public function getSingularValues()
	{
		return $this->s;
	}

	public function getS()
	{
		for ($i = 0; $i < $this->n; ++$i) {
			for ($j = 0; $j < $this->n; ++$j) {
				$S[$i][$j] = 0;
			}

			$S[$i][$i] = $this->s[$i];
		}

		return new Matrix($S);
	}

	public function norm2()
	{
		return $this->s[0];
	}

	public function cond()
	{
		return $this->s[0] / $this->s[min($this->m, $this->n) - 1];
	}

	public function rank()
	{
		$eps = pow(2, -52);
		$tol = max($this->m, $this->n) * $this->s[0] * $eps;
		$r = 0;

		for ($i = 0; $i < count($this->s); ++$i) {
			if ($tol < $this->s[$i]) {
				++$r;
			}
		}

		return $r;
	}
}


?>

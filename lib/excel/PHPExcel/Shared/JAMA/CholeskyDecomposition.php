<?php

class CholeskyDecomposition
{
	/**
	 *	Decomposition storage
	 *	@var array
	 *	@access private
	 */
	private $L = array();
	/**
	 *	Matrix row and column dimension
	 *	@var int
	 *	@access private
	 */
	private $m;
	/**
	 *	Symmetric positive definite flag
	 *	@var boolean
	 *	@access private
	 */
	private $isspd = true;

	public function __construct($A = NULL)
	{
		if ($A instanceof Matrix) {
			$this->L = $A->getArray();
			$this->m = $A->getRowDimension();

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = $i; $j < $this->m; ++$j) {
					$sum = $this->L[$i][$j];

					for ($k = $i - 1; 0 <= $k; --$k) {
						$sum -= $this->L[$i][$k] * $this->L[$j][$k];
					}

					if ($i == $j) {
						if (0 <= $sum) {
							$this->L[$i][$i] = sqrt($sum);
						}
						else {
							$this->isspd = false;
						}
					}
					else if ($this->L[$i][$i] != 0) {
						$this->L[$j][$i] = $sum / $this->L[$i][$i];
					}
				}

				for ($k = $i + 1; $k < $this->m; ++$k) {
					$this->L[$i][$k] = 0;
				}
			}
		}
		else {
			throw new PHPExcel_Calculation_Exception(jamaerror(ArgumentTypeException));
		}
	}

	public function isSPD()
	{
		return $this->isspd;
	}

	public function getL()
	{
		return new Matrix($this->L);
	}

	public function solve($B = NULL)
	{
		if ($B instanceof Matrix) {
			if ($B->getRowDimension() == $this->m) {
				if ($this->isspd) {
					$X = $B->getArrayCopy();
					$nx = $B->getColumnDimension();

					for ($k = 0; $k < $this->m; ++$k) {
						for ($i = $k + 1; $i < $this->m; ++$i) {
							for ($j = 0; $j < $nx; ++$j) {
								$X[$i][$j] -= $X[$k][$j] * $this->L[$i][$k];
							}
						}

						for ($j = 0; $j < $nx; ++$j) {
							$X[$k][$j] /= $this->L[$k][$k];
						}
					}

					for ($k = $this->m - 1; 0 <= $k; --$k) {
						for ($j = 0; $j < $nx; ++$j) {
							$X[$k][$j] /= $this->L[$k][$k];
						}

						for ($i = 0; $i < $k; ++$i) {
							for ($j = 0; $j < $nx; ++$j) {
								$X[$i][$j] -= $X[$k][$j] * $this->L[$k][$i];
							}
						}
					}

					return new Matrix($X, $this->m, $nx);
				}
				else {
					throw new PHPExcel_Calculation_Exception(jamaerror(MatrixSPDException));
				}
			}
			else {
				throw new PHPExcel_Calculation_Exception(jamaerror(MatrixDimensionException));
			}
		}
		else {
			throw new PHPExcel_Calculation_Exception(jamaerror(ArgumentTypeException));
		}
	}
}


?>

<?php

class PHPExcel_Shared_JAMA_Matrix
{
	const PolymorphicArgumentException = 'Invalid argument pattern for polymorphic function.';
	const ArgumentTypeException = 'Invalid argument type.';
	const ArgumentBoundsException = 'Invalid argument range.';
	const MatrixDimensionException = 'Matrix dimensions are not equal.';
	const ArrayLengthException = 'Array length must be a multiple of m.';

	/**
	 *	Matrix storage
	 *
	 *	@var array
	 *	@access public
	 */
	public $A = array();
	/**
	 *	Matrix row dimension
	 *
	 *	@var int
	 *	@access private
	 */
	private $m;
	/**
	 *	Matrix column dimension
	 *
	 *	@var int
	 *	@access private
	 */
	private $n;

	public function __construct()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'array':
				$this->m = count($args[0]);
				$this->n = count($args[0][0]);
				$this->A = $args[0];
				break;

			case 'integer':
				$this->m = $args[0];
				$this->n = $args[0];
				$this->A = array_fill(0, $this->m, array_fill(0, $this->n, 0));
				break;

			case 'integer,integer':
				$this->m = $args[0];
				$this->n = $args[1];
				$this->A = array_fill(0, $this->m, array_fill(0, $this->n, 0));
				break;

			case 'array,integer':
				$this->m = $args[1];

				if ($this->m != 0) {
					$this->n = count($args[0]) / $this->m;
				}
				else {
					$this->n = 0;
				}

				if (($this->m * $this->n) == count($args[0])) {
					for ($i = 0; $i < $this->m; ++$i) {
						for ($j = 0; $j < $this->n; ++$j) {
							$this->A[$i][$j] = $args[0][$i + ($j * $this->m)];
						}
					}
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArrayLengthException);
				}

				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function getArray()
	{
		return $this->A;
	}

	public function getRowDimension()
	{
		return $this->m;
	}

	public function getColumnDimension()
	{
		return $this->n;
	}

	public function get($i = NULL, $j = NULL)
	{
		return $this->A[$i][$j];
	}

	public function getMatrix()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'integer,integer':
				list($i0, $j0) = $args;

				if (0 <= $i0) {
					$m = $this->m - $i0;
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				if (0 <= $j0) {
					$n = $this->n - $j0;
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				$R = new PHPExcel_Shared_JAMA_Matrix($m, $n);

				for ($i = $i0; $i < $this->m; ++$i) {
					for ($j = $j0; $j < $this->n; ++$j) {
						$R->set($i, $j, $this->A[$i][$j]);
					}
				}

				return $R;
				break;

			case 'integer,integer,integer,integer':
				list($i0, $iF, $j0, $jF) = $args;
				if (($i0 < $iF) && ($iF <= $this->m) && (0 <= $i0)) {
					$m = $iF - $i0;
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				if (($j0 < $jF) && ($jF <= $this->n) && (0 <= $j0)) {
					$n = $jF - $j0;
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				$R = new PHPExcel_Shared_JAMA_Matrix($m + 1, $n + 1);

				for ($i = $i0; $i <= $iF; ++$i) {
					for ($j = $j0; $j <= $jF; ++$j) {
						$R->set($i - $i0, $j - $j0, $this->A[$i][$j]);
					}
				}

				return $R;
				break;

			case 'array,array':
				list($RL, $CL) = $args;

				if (0 < count($RL)) {
					$m = count($RL);
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				if (0 < count($CL)) {
					$n = count($CL);
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				$R = new PHPExcel_Shared_JAMA_Matrix($m, $n);

				for ($i = 0; $i < $m; ++$i) {
					for ($j = 0; $j < $n; ++$j) {
						$R->set($i - $i0, $j - $j0, $this->A[$RL[$i]][$CL[$j]]);
					}
				}

				return $R;
				break;

			case 'array,array':
				list($RL, $CL) = $args;

				if (0 < count($RL)) {
					$m = count($RL);
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				if (0 < count($CL)) {
					$n = count($CL);
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				$R = new PHPExcel_Shared_JAMA_Matrix($m, $n);

				for ($i = 0; $i < $m; ++$i) {
					for ($j = 0; $j < $n; ++$j) {
						$R->set($i, $j, $this->A[$RL[$i]][$CL[$j]]);
					}
				}

				return $R;
				break;

			case 'integer,integer,array':
				list($i0, $iF, $CL) = $args;
				if (($i0 < $iF) && ($iF <= $this->m) && (0 <= $i0)) {
					$m = $iF - $i0;
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				if (0 < count($CL)) {
					$n = count($CL);
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				$R = new PHPExcel_Shared_JAMA_Matrix($m, $n);

				for ($i = $i0; $i < $iF; ++$i) {
					for ($j = 0; $j < $n; ++$j) {
						$R->set($i - $i0, $j, $this->A[$RL[$i]][$j]);
					}
				}

				return $R;
				break;

			case 'array,integer,integer':
				list($RL, $j0, $jF) = $args;

				if (0 < count($RL)) {
					$m = count($RL);
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				if (($j0 <= $jF) && ($jF <= $this->n) && (0 <= $j0)) {
					$n = $jF - $j0;
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentBoundsException);
				}

				$R = new PHPExcel_Shared_JAMA_Matrix($m, $n + 1);

				for ($i = 0; $i < $m; ++$i) {
					for ($j = $j0; $j <= $jF; ++$j) {
						$R->set($i, $j - $j0, $this->A[$RL[$i]][$j]);
					}
				}

				return $R;
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function checkMatrixDimensions($B = NULL)
	{
		if ($B instanceof PHPExcel_Shared_JAMA_Matrix) {
			if (($this->m == $B->getRowDimension()) && ($this->n == $B->getColumnDimension())) {
				return true;
			}
			else {
				throw new PHPExcel_Calculation_Exception(self::MatrixDimensionException);
			}
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
		}
	}

	public function set($i = NULL, $j = NULL, $c = NULL)
	{
		$this->A[$i][$j] = $c;
	}

	public function identity($m = NULL, $n = NULL)
	{
		return $this->diagonal($m, $n, 1);
	}

	public function diagonal($m = NULL, $n = NULL, $c = 1)
	{
		$R = new PHPExcel_Shared_JAMA_Matrix($m, $n);

		for ($i = 0; $i < $m; ++$i) {
			$R->set($i, $i, $c);
		}

		return $R;
	}

	public function getMatrixByRow($i0 = NULL, $iF = NULL)
	{
		if (is_int($i0)) {
			if (is_int($iF)) {
				return $this->getMatrix($i0, 0, $iF + 1, $this->n);
			}
			else {
				return $this->getMatrix($i0, 0, $i0 + 1, $this->n);
			}
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
		}
	}

	public function getMatrixByCol($j0 = NULL, $jF = NULL)
	{
		if (is_int($j0)) {
			if (is_int($jF)) {
				return $this->getMatrix(0, $j0, $this->m, $jF + 1);
			}
			else {
				return $this->getMatrix(0, $j0, $this->m, $j0 + 1);
			}
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
		}
	}

	public function transpose()
	{
		$R = new PHPExcel_Shared_JAMA_Matrix($this->n, $this->m);

		for ($i = 0; $i < $this->m; ++$i) {
			for ($j = 0; $j < $this->n; ++$j) {
				$R->set($j, $i, $this->A[$i][$j]);
			}
		}

		return $R;
	}

	public function trace()
	{
		$s = 0;
		$n = min($this->m, $this->n);

		for ($i = 0; $i < $n; ++$i) {
			$s += $this->A[$i][$i];
		}

		return $s;
	}

	public function uminus()
	{
	}

	public function plus()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$M->set($i, $j, $M->get($i, $j) + $this->A[$i][$j]);
				}
			}

			return $M;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function plusEquals()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$validValues = true;
					$value = $M->get($i, $j);
					if (is_string($this->A[$i][$j]) && (0 < strlen($this->A[$i][$j])) && !is_numeric($this->A[$i][$j])) {
						$this->A[$i][$j] = trim($this->A[$i][$j], '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}

					if (is_string($value) && (0 < strlen($value)) && !is_numeric($value)) {
						$value = trim($value, '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}

					if ($validValues) {
						$this->A[$i][$j] += $value;
					}
					else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}

			return $this;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function minus()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$M->set($i, $j, $M->get($i, $j) - $this->A[$i][$j]);
				}
			}

			return $M;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function minusEquals()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$validValues = true;
					$value = $M->get($i, $j);
					if (is_string($this->A[$i][$j]) && (0 < strlen($this->A[$i][$j])) && !is_numeric($this->A[$i][$j])) {
						$this->A[$i][$j] = trim($this->A[$i][$j], '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}

					if (is_string($value) && (0 < strlen($value)) && !is_numeric($value)) {
						$value = trim($value, '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}

					if ($validValues) {
						$this->A[$i][$j] -= $value;
					}
					else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}

			return $this;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function arrayTimes()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$M->set($i, $j, $M->get($i, $j) * $this->A[$i][$j]);
				}
			}

			return $M;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function arrayTimesEquals()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$validValues = true;
					$value = $M->get($i, $j);
					if (is_string($this->A[$i][$j]) && (0 < strlen($this->A[$i][$j])) && !is_numeric($this->A[$i][$j])) {
						$this->A[$i][$j] = trim($this->A[$i][$j], '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}

					if (is_string($value) && (0 < strlen($value)) && !is_numeric($value)) {
						$value = trim($value, '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}

					if ($validValues) {
						$this->A[$i][$j] *= $value;
					}
					else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}

			return $this;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function arrayRightDivide()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$validValues = true;
					$value = $M->get($i, $j);
					if (is_string($this->A[$i][$j]) && (0 < strlen($this->A[$i][$j])) && !is_numeric($this->A[$i][$j])) {
						$this->A[$i][$j] = trim($this->A[$i][$j], '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}

					if (is_string($value) && (0 < strlen($value)) && !is_numeric($value)) {
						$value = trim($value, '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}

					if ($validValues) {
						if ($value == 0) {
							$M->set($i, $j, '#DIV/0!');
						}
						else {
							$M->set($i, $j, $this->A[$i][$j] / $value);
						}
					}
					else {
						$M->set($i, $j, PHPExcel_Calculation_Functions::NaN());
					}
				}
			}

			return $M;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function arrayRightDivideEquals()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$this->A[$i][$j] = $this->A[$i][$j] / $M->get($i, $j);
				}
			}

			return $M;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function arrayLeftDivide()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$M->set($i, $j, $M->get($i, $j) / $this->A[$i][$j]);
				}
			}

			return $M;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function arrayLeftDivideEquals()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$this->A[$i][$j] = $M->get($i, $j) / $this->A[$i][$j];
				}
			}

			return $M;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function times()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$B = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				if ($this->n == $B->m) {
					$C = new PHPExcel_Shared_JAMA_Matrix($this->m, $B->n);

					for ($j = 0; $j < $B->n; ++$j) {
						for ($k = 0; $k < $this->n; ++$k) {
							$Bcolj[$k] = $B->A[$k][$j];
						}

						for ($i = 0; $i < $this->m; ++$i) {
							$Arowi = $this->A[$i];
							$s = 0;

							for ($k = 0; $k < $this->n; ++$k) {
								$s += $Arowi[$k] * $Bcolj[$k];
							}

							$C->A[$i][$j] = $s;
						}
					}

					return $C;
				}
				else {
					throw new PHPExcel_Calculation_Exception(jamaerror(MatrixDimensionMismatch));
				}

				break;

			case 'array':
				$B = new PHPExcel_Shared_JAMA_Matrix($args[0]);

				if ($this->n == $B->m) {
					$C = new PHPExcel_Shared_JAMA_Matrix($this->m, $B->n);

					for ($i = 0; $i < $C->m; ++$i) {
						for ($j = 0; $j < $C->n; ++$j) {
							$s = '0';

							for ($k = 0; $k < $C->n; ++$k) {
								$s += $this->A[$i][$k] * $B->A[$k][$j];
							}

							$C->A[$i][$j] = $s;
						}
					}

					return $C;
				}
				else {
					throw new PHPExcel_Calculation_Exception(jamaerror(MatrixDimensionMismatch));
				}

				return $M;
				break;

			case 'integer':
				$C = new PHPExcel_Shared_JAMA_Matrix($this->A);

				for ($i = 0; $i < $C->m; ++$i) {
					for ($j = 0; $j < $C->n; ++$j) {
						$C->A[$i][$j] *= $args[0];
					}
				}

				return $C;
				break;

			case 'double':
				$C = new PHPExcel_Shared_JAMA_Matrix($this->m, $this->n);

				for ($i = 0; $i < $C->m; ++$i) {
					for ($j = 0; $j < $C->n; ++$j) {
						$C->A[$i][$j] = $args[0] * $this->A[$i][$j];
					}
				}

				return $C;
				break;

			case 'float':
				$C = new PHPExcel_Shared_JAMA_Matrix($this->A);

				for ($i = 0; $i < $C->m; ++$i) {
					for ($j = 0; $j < $C->n; ++$j) {
						$C->A[$i][$j] *= $args[0];
					}
				}

				return $C;
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function power()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}

				break;

			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$validValues = true;
					$value = $M->get($i, $j);
					if (is_string($this->A[$i][$j]) && (0 < strlen($this->A[$i][$j])) && !is_numeric($this->A[$i][$j])) {
						$this->A[$i][$j] = trim($this->A[$i][$j], '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($this->A[$i][$j]);
					}

					if (is_string($value) && (0 < strlen($value)) && !is_numeric($value)) {
						$value = trim($value, '"');
						$validValues &= PHPExcel_Shared_String::convertToNumberIfFraction($value);
					}

					if ($validValues) {
						$this->A[$i][$j] = pow($this->A[$i][$j], $value);
					}
					else {
						$this->A[$i][$j] = PHPExcel_Calculation_Functions::NaN();
					}
				}
			}

			return $this;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function concat()
	{
		if (0 < func_num_args()) {
			$args = func_get_args();
			$match = implode(',', array_map('gettype', $args));

			switch ($match) {
			case 'object':
				if ($args[0] instanceof PHPExcel_Shared_JAMA_Matrix) {
					$M = $args[0];
				}
				else {
					throw new PHPExcel_Calculation_Exception(self::ArgumentTypeException);
				}
			case 'array':
				$M = new PHPExcel_Shared_JAMA_Matrix($args[0]);
				break;

			default:
				throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
				break;
			}

			$this->checkMatrixDimensions($M);

			for ($i = 0; $i < $this->m; ++$i) {
				for ($j = 0; $j < $this->n; ++$j) {
					$this->A[$i][$j] = trim($this->A[$i][$j], '"') . trim($M->get($i, $j), '"');
				}
			}

			return $this;
		}
		else {
			throw new PHPExcel_Calculation_Exception(self::PolymorphicArgumentException);
		}
	}

	public function solve($B)
	{
		if ($this->m == $this->n) {
			$LU = new PHPExcel_Shared_JAMA_LUDecomposition($this);
			return $LU->solve($B);
		}
		else {
			$QR = new QRDecomposition($this);
			return $QR->solve($B);
		}
	}

	public function inverse()
	{
		return $this->solve($this->identity($this->m, $this->m));
	}

	public function det()
	{
		$L = new PHPExcel_Shared_JAMA_LUDecomposition($this);
		return $L->det();
	}
}

if (!defined('PHPEXCEL_ROOT')) {
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../../');
	require PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php';
}

?>

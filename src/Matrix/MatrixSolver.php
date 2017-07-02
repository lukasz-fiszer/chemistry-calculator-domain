<?php

namespace ChemCalc\Domain\Matrix;

use Chippyash\Math\Matrix\NumericMatrix;
use ChemCalc\Domain\Matrix\Decomposition\MatrixElimination;

/**
 * Matrix solver
 */
class MatrixSolver
{
	/**
	 * Matrix to be solved, matrix a
	 * 
	 * @var NumericMatrix
	 */
	protected $matrixA;

	/**
	 * Matrix to be solved, matrix b
	 * 
	 * @var NumericMatrix
	 */
	protected $matrixB;

	/**
	 * Matrix decomposition elimination used
	 * 
	 * @var MatrixElimination
	 */
	protected $matrixElimination;

	/**
	 * Construct new matrix solver
	 * 
	 * @param NumericMatrix     $matrixA           matrix a to be solved
	 * @param NumericMatrix     $matrixB           matrix b to be solved
	 * @param MatrixElimination $matrixElimination matrix elimination used
	 */
	public function __construct(NumericMatrix $matrixA, NumericMatrix $matrixB, MatrixElimination $matrixElimination){
		$this->$matrixA = $matrixA;
		$this->$matrixB = $matrixB;
		$this->$matrixElimination = $matrixElimination;
	}

	/**
	 * Solve matrix
	 *
	 * @throws Exception exception thrown for inconsistent matrix
	 * @return array array of variables found values
	 */
	public function solve(){
		$decomposed = $this->matrixA->decompose($this->matrixElimination, $this->matrixB);
	}

    /**
     * Gets the Matrix to be solved, matrix a.
     *
     * @return NumericMatrix
     */
    public function getMatrixA()
    {
        return $this->matrixA;
    }

    /**
     * Gets the Matrix to be solved, matrix b.
     *
     * @return NumericMatrix
     */
    public function getMatrixB()
    {
        return $this->matrixB;
    }

    /**
     * Gets the Matrix decomposition elimination used.
     *
     * @return MatrixElimination
     */
    public function getMatrixElimination()
    {
        return $this->matrixElimination;
    }
}
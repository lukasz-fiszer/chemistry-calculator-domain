<?php

namespace ChemCalc\Domain\Matrix;

use Chippyash\Math\Matrix\NumericMatrix;
use ChemCalc\Domain\Matrix\Decomposition\MatrixElimination;
use Chippyash\Type\TypeFactory;
use Chippyash\Math\Type\Calculator;
use MathPHP\Algebra;

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
	 * Calculator for rational typw
	 * 
	 * @var Calculator
	 */
	protected $calc;

	/**
	 * Construct new matrix solver
	 * 
	 * @param NumericMatrix     $matrixA           matrix a to be solved
	 * @param NumericMatrix     $matrixB           matrix b to be solved
	 * @param MatrixElimination $matrixElimination matrix elimination used
	 */
	public function __construct(NumericMatrix $matrixA, NumericMatrix $matrixB, MatrixElimination $matrixElimination, Calculator $calc){
		$this->matrixA = $matrixA;
		$this->matrixB = $matrixB;
		$this->matrixElimination = $matrixElimination;
		$this->calc = $calc;
	}

	/**
	 * Solve matrix
	 *
	 * @throws Exception exception thrown for inconsistent matrix
	 * @return array array of variables found values
	 */
	public function solve(){
		$decomposed = $this->matrixA->decompose($this->matrixElimination, $this->matrixB);
		if(!$decomposed->product('consistent')){
			throw new \Exception('Inconsistent matrix');
		}

		$values = $decomposed->product('values');
		$free = $decomposed->product('free');
		foreach($values as &$value){
			if($value == 'free'){
				$value = TypeFactory::createRational(1);
			}
		}
		foreach($values as $index => &$value){
			if($free[$index]){
				continue;
			}
			//$numberValue = TypeFactory::createRational($value->value);
			$numberValue = TypeFactory::createRational($value->value, null);
			//$numberValue = TypeFactory::createRational(clone $value->value, null);
			foreach($value->addFree as $addFree){
				$numberValue = $this->calc->add($numberValue, $this->calc->mul($addFree->multiplier, $values[$addFree->column]));
			}
			$value = $numberValue;
		}

		$lcm = 1;
		//foreach($values as $value){
		foreach($values as $value2){
			//$lcm = Algebra::lcm($lcm, $value->denominator()->get());
			$lcm = Algebra::lcm($lcm, $value2->denominator()->get());
		}
		$lcm = TypeFactory::createRational($lcm);
		foreach($values as &$value){
			//echo $value->numerator()->get().'/'.$value->denominator()->get()."\n";
			$value = $this->calc->mul($value, $lcm);
			//echo $value->numerator()->get().'/'.$value->denominator()->get()."\n";
		}
		/*$values = array_map(function($value) use($lcm){
			return $this->calc->mul($value, $lcm);
		}, $values);*/

		return array_map(function($value){
			return $value->numerator()->get();
		}, $values);

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
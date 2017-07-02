<?php

namespace ChemCalc\Domain\Tests\Matrix;

use ChemCalc\Domain\Matrix\MatrixSolver;
use Chippyash\Math\Matrix\RationalMatrix;
use ChemCalc\Domain\Matrix\Decomposition\MatrixElimination;
use Chippyash\Math\Type\Calculator;
use ChemCalc\Domain\Tests\Res\MatrixTestsData;

class MatrixSolverTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(){
		if(isset($this->initialized) && $this->initialized == true){
			return;
		}
		$this->initialized = true;
		$this->calc = new Calculator();
		$this->matrixTestsData = new MatrixTestsData();
	}

	public function testConstructorPropertiesInjection(){
		$ma = new RationalMatrix([[1, 2, 3], [1, 2, 3], [1, 2, 3]]);
		$mb = new RationalMatrix([[1], [1], [1]]);
		$el = new MatrixElimination();
		$calc = new Calculator();
		$matrixSolver = new MatrixSolver($ma, $mb, $el, $calc);
		$this->assertAttributeEquals($ma, 'matrixA', $matrixSolver);
		$this->assertAttributeEquals($mb, 'matrixB', $matrixSolver);
		$this->assertAttributeEquals($el, 'matrixElimination', $matrixSolver);
		$this->assertAttributeEquals($calc, 'calc', $matrixSolver);
		$this->assertEquals($ma, $matrixSolver->getMatrixA());
		$this->assertEquals($mb, $matrixSolver->getMatrixB());
		$this->assertEquals($el, $matrixSolver->getMatrixElimination());
	}

	/**
	 * @dataProvider solveMethodDataProvider
	 */
	public function testSolveMethod(array $ma, array $mb, array $values, bool $consistent){
		$matrixSolver = new MatrixSolver(new RationalMatrix($ma), new RationalMatrix($mb), new MatrixElimination(), $this->calc);
		if(!$consistent){
			$this->expectException(\Exception::class);
		}
		$found = $matrixSolver->solve();
		$this->assertEquals($values, $found);
	}

	public function solveMethodDataProvider(){
		$this->setUp();
		return array_map(function($testEntry){
			return [$testEntry['matrix_a'], $testEntry['matrix_b'], $testEntry['found'], $testEntry['consistent']];
		}, $this->matrixTestsData->getMatrixEliminationTestsData());
	}
}
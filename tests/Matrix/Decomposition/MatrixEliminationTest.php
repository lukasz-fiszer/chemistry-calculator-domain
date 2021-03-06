<?php

namespace ChemCalc\Domain\Tests\Matrix\Decomposition;

use ChemCalc\Domain\Matrix\Decomposition\MatrixElimination;
use Chippyash\Math\Matrix\NumericMatrix;
use Chippyash\Math\Matrix\RationalMatrix;
use ChemCalc\Domain\Tests\Res\MatrixTestsData;
use Chippyash\Math\Type\Comparator;
use Chippyash\Type\TypeFactory;

class MatrixEliminationTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(): void {
		if(isset($this->initialized) && $this->initialized == true){
			return;
		}
		$this->initialized = true;
		$this->testNonSingular = [[-12,2,3], [4,5,6], [7,8,9]];
		$this->matrixTestsData = new MatrixTestsData();
		$this->comp = new Comparator();
	}

	/**
	 * @dataProvider matrixEliminationDecomposeDataProvider
	 */
	public function testMatrixEliminationDecomposeMethod(array $a, array $b, array $left, array $right, array $values, array $free, bool $consistent, array $pivoted){
		$elimination = new MatrixElimination();
		$ma = new RationalMatrix($a);
		$mb = new RationalMatrix($b);
		$decomposed = $ma->decompose($elimination, $mb);
		$this->assertEquals(new RationalMatrix($left), $decomposed->product('left'));
		$this->assertEquals(new RationalMatrix($right), $decomposed->product('right'));
		//$this->assertEquals($values, $decomposed->product('values'));
		$this->assertEquals($free, $decomposed->product('free'));
		$this->assertEquals($consistent, $decomposed->product('consistent'));
		$this->assertEquals($pivoted, $decomposed->product('pivoted'));

		$decVal = $decomposed->product('values');
		$this->assertEquals(count($values), count($decVal));
		foreach($values as $index => $value){
			if(is_array($value)){
				$this->assertTrue($this->comp->eq(TypeFactory::createRational($value['value']), $decVal[$index]->value));
				$this->assertEquals(count($value['add_free']), count($decVal[$index]->addFree));
				foreach($value['add_free'] as $j => $addFree){
					$this->assertTrue($this->comp->eq(TypeFactory::createRational($addFree['multiplier']), $decVal[$index]->addFree[$j]->multiplier));
					$this->assertEquals($addFree['column'], $decVal[$index]->addFree[$j]->column);
				}
			}
			else{
				$this->assertEquals($value, $decVal[$index]);
			}
		}
	}

	public function matrixEliminationDecomposeDataProvider(){
		$this->setUp();
		return $this->matrixTestsData->getMatrixEliminationTestsData();
	}

	/**
     * @expectedException Chippyash\Matrix\Exceptions\MatrixException
     * @expectedExceptionMessage Parameter extra is not a matrix
     */
    public function testDecomposeWithOneParameterThrowsException()
    {
    	$this->object = new MatrixElimination();
        $this->object->decompose(new NumericMatrix($this->testNonSingular));
    }

    /**
     * @expectedException Chippyash\Matrix\Exceptions\MatrixException
     * @expectedExceptionMessage Parameter extra is not a matrix
     */
    public function testDecomposeWithNonNumericMatrixExtraParameterThrowsException()
    {
    	$this->object = new MatrixElimination();
        $this->object->decompose(new NumericMatrix($this->testNonSingular), $this->testNonSingular);
    }

    /**
     * @expectedException Chippyash\Matrix\Exceptions\MatrixException
     * @expectedExceptionMessage mA->rows != extra->rows
     */
    public function testDecomposeWithExtraMatrixNotHavingSameNumberOfRowsAsFirstMatrixThrowsException()
    {
    	$this->object = new MatrixElimination();
        $a = $this->testNonSingular;
        array_pop($a);
        $this->object->decompose(new NumericMatrix($this->testNonSingular), new NumericMatrix($a));
    }
}
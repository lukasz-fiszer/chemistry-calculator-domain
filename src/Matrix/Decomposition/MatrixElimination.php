<?php

namespace ChemCalc\Domain\Matrix\Decomposition;

use Chippyash\Math\Matrix\NumericMatrix;
use Chippyash\Math\Matrix\Traits\CreateCorrectMatrixType;
use Chippyash\Math\Matrix\Traits\AssertMatrixIsNumeric;
use Chippyash\Math\Matrix\Exceptions\SingularMatrixException;
use Chippyash\Matrix\Traits\AssertParameterIsMatrix;
use Chippyash\Matrix\Traits\AssertMatrixRowsAreEqual;
use Chippyash\Math\Type\Calculator;
use Chippyash\Math\Type\Comparator;
use Chippyash\Type\Number\Rational\RationalTypeFactory;
use Chippyash\Math\Matrix\Decomposition\GaussJordanElimination;

/**
 * Matrix elimination
 */
class MatrixElimination extends GaussJordanElimination
{
    use AssertParameterIsMatrix;
    use AssertMatrixIsNumeric;
    use AssertMatrixRowsAreEqual;
    use CreateCorrectMatrixType;

    /**
     * Products of the decomposition
     * - left : NumericMatrix - product of left side
     * - right : NumericMatrix - product of right side
     * - values : array of found variables
     * - free : array of indexes in values that are free variables
     * - consistent : bool, true if matrix is consistent
     *
     * @var array [productName => mixed,...]
     */
    protected $products = array(
        'left' => null,
        'right' => null,
        'values' => null,
        'free' => null,
        'consistent' => null
    );

    /**
     * Decompose matrix, eliminate
     *
     * @param NumericMatrix $mA First matrix to act on - required
     * @param NumericMatrix $extra Second matrix to act upon - required
     *
     * @return \Chippyash\Math\Matrix\DecompositionAbstractDecomposition Fluent Interface
     */
    public function decompose(NumericMatrix $mA, $extra = null)
    {
        $this->assertParameterIsMatrix($extra, 'Parameter extra is not a matrix')
                ->assertMatrixIsNumeric($extra, 'Parameter extra is not a numeric matrix')
                ->assertMatrixRowsAreEqual($mA, $extra, 'mA->rows != extra->rows');

        $rows = $mA->rows();
        $cols = $mA->columns();
        $dA = $mA->toArray();
        $dB = $extra->toArray();
        $zero = function(){return RationalTypeFactory::create(0, 1);};
        $one = function(){return RationalTypeFactory::create(1, 1);};
        $calc = new Calculator();
        $comp = new Comparator();

        $ipiv = array_fill(0, $rows, $zero());
        $indxr = array_fill(0, $rows, 0);
        $indxc = array_fill(0, $rows, 0);

        for($i = 0; $i < $cols; $i++){
            $biggestIndex = $this->biggestNonZero($comp, $dA, $i, $i);
        }


        $this->set('left', $this->createCorrectMatrixType($mA, $dA));
        $this->set('right', $this->createCorrectMatrixType($extra, $dB));

        return clone $this;
    }

    /**
     * Find biggest non zero entry in given column
     *
     * @param  Comparator $comp   comparator to use
     * @param  array      $array  array of entries
     * @param  int        $column column to search in
     * @param  int        $from   index from which to search for
     * @return int|bool           index where biggest non zero value was found or false otherwise
     */
    protected function biggestNonZero(Comparator $comp, array $array, int $column, int $from = 0){
        $biggest = false;
        $biggestValue = RationalTypeFactory::create(0);
        foreach($array as $index => $row){
            if($comp->neq($row[$column]), RationalTypeFactory::create(0) && $comp->gt($row[$column]->abs(), $biggestValue)){
                $biggest = $index;
                $biggestValue = $row[$column];
            }
        }
        return $biggest;
    }

    /**
     * Swap columns in an array
     *
     * @param array $a
     * @param int $c1
     * @param int $c2
     */
    protected function swapColumns(array &$a, $c1, $c2)
    {
        foreach($a as &$row){
            $tmp = $row[$c1];
            $row[$c1] = $row[$c2];
            $row[$c2] = $tmp;
        }
    }
}

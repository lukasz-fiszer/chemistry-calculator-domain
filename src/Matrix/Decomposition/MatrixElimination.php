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
     * Construct new matrix elimination
     */
    public function __construct(){
        $this->calc = new Calculator();
        $this->comp = new Comparator();
        $this->zero = function(){return RationalTypeFactory::create(0, 1);};
        $this->one = function(){return RationalTypeFactory::create(1, 1);};
    }

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

        $ipiv = array_fill(0, $rows, ($this->zero)());
        $indxr = array_fill(0, $rows, 0);
        $indxc = array_fill(0, $rows, 0);

        $this->printArray = $printArray = function($a){
            //echo "array print\n";
            foreach($a as $aa){
                foreach($aa as $abcd)
                    echo $abcd.' ';
                echo "\n";
            }
        };

        $currentRow = 0;
        for($i = 0; $i < $cols && $currentRow < $rows; $i++){
            //echo "in loop, current col ".$i.", current row ".$currentRow."\n";
            $biggestIndex = $this->biggestNonZero($dA, $i, $currentRow);
            if($biggestIndex === false){
                continue;
            }
            $this->swapBothRows($dA, $dB, $currentRow, $biggestIndex);
            //echo "after swap\n";
            //$printArray($dA);
            $this->multBothRow($dA, $dB, $currentRow, $this->calc->reciprocal($dA[$currentRow][$i]), $this->calc);
            //echo "after divison\n";
            //$printArray($dA);
            $this->reduceBothOtherRows($dA, $dB, $i, $currentRow);
            //echo "after reduction\n";
            //$printArray($dA);
            //echo "-------------------\n";

            $currentRow++;
        }


        $this->set('left', $this->createCorrectMatrixType($mA, $dA));
        $this->set('right', $this->createCorrectMatrixType($extra, $dB));

        return clone $this;
    }

    /**
     * Find biggest non zero entry in given column
     *
     * @param  array      $array  array of entries
     * @param  int        $column column to search in
     * @param  int        $from   index from which to search for
     * @return int|bool           index where biggest non zero value was found or false otherwise
     */
    protected function biggestNonZero(array $array, int $column, int $from = 0){
        $biggest = false;
        $biggestValue = ($this->zero)();
        for($i = $from; $i < count($array); $i++){
            $row = $array[$i];
            if($this->comp->neq($row[$column], ($this->zero)()) && $this->comp->gt($row[$column]->abs(), $biggestValue)){
                $biggest = $i;
                $biggestValue = $row[$column];
            }
        }
        return $biggest;
    }


    /**
     * Reduce other entries in given column in both arrays
     * 
     * @param  array  &$a1    
     * @param  array  &$a2    
     * @param  int    $column
     * @param  int    $rowNumber
     */
    protected function reduceBothOtherRows(array &$a1, array &$a2, int $column, int $rowNumber){
        //$this->reduceOtherRows($a1, $column, $row);
        //$this->reduceOtherRows($a2, $column, $row);
        //echo "\treducing both other rows: column ".$column." row number ".$rowNumber."\n";
        foreach($a1 as $index => $row){
        //for($index = 0; $index < count($a1); $index++){
            if($index != $rowNumber){
                //echo "\t";
                //echo "current index ".$index;
                $multiplier = clone $a1[$index][$column];
                $multiplier->negate();
                //echo " current multiplier ".$multiplier;
                /*$this->addMultipleOfOtherRowToRow($a1, $multiplier, $column, $index, $this->calc);
                $this->addMultipleOfOtherRowToRow($a2, $multiplier, $column, $index, $this->calc);*/
                //echo "\n\t before adding multiple\n";
                //($this->printArray)($a1);
                $this->addMultipleOfOtherRowToRow($a1, $multiplier, $rowNumber, $index, $this->calc);
                $this->addMultipleOfOtherRowToRow($a2, $multiplier, $rowNumber, $index, $this->calc);
                //echo "\n\t after adding multiple\n";
                //($this->printArray)($a1);
            }
        }
        //echo "\n";
    }

    /**
     * Reduce other entries in given column
     * 
     * @param  array  &$a 
     * @param  int    $column 
     * @param  int    $rowNumber 
     */
    protected function reduceOtherRows(array &$a, int $column, int $rowNumber){
        foreach($a as $index => $row) {
            if ($index != $rowNumber) {
                $multiplier = clone $a[$index][$column];
                $multiplier->negate();
                $this->addMultipleOfOtherRowToRow($a, $multiplier, $column, $index, $this->calc);
            }
        }
    }

    /**
     * Swap both rows in both arrays
     *
     * @param array $a1
     * @param array $a1
     * @param int $r1
     * @param int $r2
     */
    protected function swapBothRows(array &$a1, array &$a2, $r1, $r2)
    {
        $this->swapRows($a1, $r1, $r2);
        $this->swapRows($a2, $r1, $r2);
    }

    /**
     * Multiply each entry in a row by a number in both arrays
     *
     * @param array $a1
     * @param array $a2
     * @param int $row
     * @param NumericTypeInterface $num
     */
    protected function multBothRow(array &$a1, array &$a2, $row, $num)
    {
        $this->multRow($a1, $row, $num, $this->calc);
        $this->multRow($a2, $row, $num, $this->calc);
    }

    /**
     * Inter row multiplication in both arrays
     *
     * @param array $a1
     * @param array $a2
     * @param NumericTypeInterface $multiple
     * @param int $rowToMultiplyWith
     * @param int $rowToAddTo
     */
    protected function addBothMultipleOfOtherRowToRow(array &$a1, array &$a2, $multiple, $rowToMultiplyWith, $rowToAddTo)
    {
        $this->addMultipleOfOtherRowToRow($a1, $multiple, $rowToMultiplyWith, $rowToAddTo, $this->calc);
        $this->addMultipleOfOtherRowToRow($a2, $multiple, $rowToMultiplyWith, $rowToAddTo, $this->calc);
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

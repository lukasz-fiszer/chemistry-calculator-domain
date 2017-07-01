<?php

namespace ChemCalc\Tests;

use Chippyash\Math\Matrix\RationalMatrix;
use Chippyash\Math\Matrix\Decomposition\Lu;
use Chippyash\Type\TypeFactory;
use Chippyash\Math\Type\Calculator;
use ChemCalc\Domain\Matrix\Decomposition\MatrixElimination;

class AbcdTest extends \PHPUnit\Framework\TestCase
{
	public function testAb(){
		$this->assertTrue(true);

		//$a = microtime(true);
		$dlu = new Lu();
		//$ma = new RationalMatrix([[1, 2], [3, 4]]);
		//$ma = new RationalMatrix([[1, 2]]);
		$ma = new RationalMatrix([[2, 0, -2], [0, 2, -1]]);
		//$ma = new RationalMatrix([[1, 2, 3], [3, 4, 5], [2, 5, 5]]);
		$ma2 = new \MathPHP\LinearAlgebra\Matrix([[1, 2], [3, 4]]);
		//$ma2 = new \MathPHP\LinearAlgebra\Matrix([[1, 2, 3], [3, 4, 5], [2, 5, 5]]);
		//$this->printArray($this->rationalToArray($ma));
		//$this->printArray($ma2->getMatrix());
		//$ma = new RationalMatrix([[1, 2], [3, 4], [2, 3]]);
		//$ma = new RationalMatrix([[1, 3], [2, 4]]);
		$lu = $ma->decompose($dlu);
		$lu2 = $ma2->luDecomposition();
		//echo microtime(true) - $a;
		//$this->printArray($this->rationalToArray($lu->product('LU')));
		//$this->printArray($this->rationalToArray($lu->product('L')));
		//$this->printArray($this->rationalToArray($lu->product('U')));
		//$this->printArray($this->rationalToArray($lu->product('PivotVector')));
		//var_dump($lu);

		$l = $lu->product('L');
		$l2 = $lu2['L'];
		$u = $lu->product('U');
		$u2 = $lu2['U'];
		$p = $lu->product('PermutationMatrix');
		$p2 = $lu2['P'];
		//$p = $lu->product('PivotVector');
		$m = $ma->rows();
		$m2 = $ma2->getM();
		//echo $m.$m2;

		//$this->printArray($this->rationalToArray($p));
		//$this->printArray($p2->getMatrix());
		//echo '--------------'."\n";

		$b = [2, 4];
		//$b = [2, 4, 5];
		//$bm = new RationalMatrix($b);
		//$bm = new RationalMatrix([[2], [4]]);
		//$bm = new RationalMatrix([[2]]);
		$bm = new RationalMatrix([[0, 0]]);
		//$bm = new RationalMatrix([[2], [4]]);
		$calc = new Calculator();

		// $pb = $lu->product('PermutationMatrix')->compute('Mul\Matrix', $bm);
		//$pb = $lu->product('PermutationMatrix')('Mul\Matrix', $bm);
		//$pb = $lu->product('PermutationMatrix');
		//$pb = $lu->product('PivotVector');
		//$pb = $bm('Mul\Matrix', $pb);
		//$pb = $lu->product('PivotVector')('Mul\Matrix', $bm);
		$pb = $p('Mul\Matrix', $bm);
		$pb2 = $p2->multiply(new \MathPHP\LinearAlgebra\Vector($b));
		//$pb = $bm('Mul\Matrix', $p);

		//$this->printArray($this->rationalToArray($pb));
		//$this->printArray($pb2->getMatrix());

		$y    = [];
        //$y[0] = $pb->toArray()[0][0] / $l->toArray()[0][0];
        $y[0] = $calc->div($pb->toArray()[0][0], $l->toArray()[0][0]);
        for ($i = 1; $i < $m; $i++) {
            //$sum = 0;
            $sum = TypeFactory::createRational(0);
            for ($j = 0; $j <= $i - 1; $j++) {
                //$sum += $L[$i][$j] * $y[$j];
                $sum = $calc->add($sum, $calc->mul($l->toArray()[$i][$j], $y[$j]));
            }
            //$y[$i] = ($Pb[$i][0] - $sum) / $L[$i][$i];
            $y[$i] = $calc->div($calc->sub($pb->toArray()[$i][0], $sum), $l->toArray()[$i][$i]);
        }
        $x         = [];
        //$x[$m - 1] = $y[$m - 1] / $U[$m - 1][$m - 1];
        $x[$m - 1] = $calc->div($y[$m - 1], $u->toArray()[$m - 1][$m - 1]);
        for ($i = $m - 2; $i >= 0; $i--) {
            //$sum = 0;
            $sum = TypeFactory::createRational(0);
            for ($j = $i + 1; $j < $m; $j++) {
                //$sum += $U[$i][$j] * $x[$j];
                $sum = $calc->add($sum, $calc->mul($u->toArray()[$i][$j], $x[$j]));
            }
            //$x[$i] = ($y[$i] - $sum) / $U[$i][$i];
            $x[$i] = $calc->div($calc->sub($y[$i], $sum), $u->toArray()[$i][$i]);
        }
        //return new Vector(array_reverse($x));
        $x = array_reverse($x);

        /*$ar = new \MathPHP\LinearAlgebra\Matrix([[1, 2], [3, 4]]);
        $ar->luDecomposition();
        var_dump($ar->solve([2, 4]));*/

        //$ab = new \MathPHP\LinearAlgebra\Matrix([[1, 1], [0, 0]]);
        //$ab = new \MathPHP\LinearAlgebra\Matrix([[1, 1, 0], [0, 0, 0], [0, 0, 0]]);
        //$ab = new \MathPHP\LinearAlgebra\Matrix([[2, 3, -2], [5, 2, -4], [1, 1, -2]]);
        //$ab = new \MathPHP\LinearAlgebra\Matrix([[2, 0, -2], [0, 2, -1], [0, 0, 0]]);
        //$ab = new \MathPHP\LinearAlgebra\Matrix([[2, 0, -2], [0, 2, -1]]);
        //$ab->rref();
        //var_dump($ab->solve([1, 0, 0]));
        //var_dump($ab->solve([0, 0, 0]));
        //var_dump($ab->solve([0, 0]));

        //var_dump($x);
        //$this->printArray($this->rationalToArray($x));
        /*$this->printArray([array_map(function($elem){
        	return $elem->numerator().'/'.$elem->denominator();
        }, $x)]);*/


		//echo microtime(true) - $a;
	}

	public function testAb2(){
		$this->assertTrue(true);

		/*$r = TypeFactory::createRational(0, 0);
		echo $r->__toString();*/
		/*$r = TypeFactory::createRational(0);
		echo $r;*/

		//$ma = new RationalMatrix([[2, 0, -2], [0, 2, -1]]);
		$el = new MatrixElimination();
		//$mb = new RationalMatrix([[0], [0]]);
		//$ma = new RationalMatrix([[0, 0], [0, 0]]);
		//$mb = new RationalMatrix([[0], [0]]);
		/*$ma = new RationalMatrix([[1, 2, 3], [1, 2, 3]]);
		$mb = new RationalMatrix([[0], [0]]);*/
		/*$ma = new RationalMatrix([[1, 2], [1, 2]]);
		$mb = new RationalMatrix([[0], [0]]);*/
		/*$ma = new RationalMatrix([[1, 2, 3]]);
		$mb = new RationalMatrix([[0]]);*/
		/*$ma = new RationalMatrix([[1, 0, 0], [0, 2, 0]]);
		$mb = new RationalMatrix([[0], [0]]);*/
		/*$ma = new RationalMatrix([[1, 2, 3], [1, 2, 3]]);
		$mb = new RationalMatrix([[5], [10]]);*/
		/*$ma = new RationalMatrix([[0, 0, 0], [0, 22, 0]]);
		$mb = new RationalMatrix([[5], [2]]);*/
		// $ma = new RationalMatrix([[0, 0, 0], [0, 22, 0]]);
		// $mb = new RationalMatrix([[5], [2]]);
		/*$ma = new RationalMatrix([[1, 2, 3], [1, 2, 3], [1, 2, 3]]);
		$mb = new RationalMatrix([[0], [0], [0]]);*/
		/*$ma = new RationalMatrix([[0, 0, 0], [1, 2, 3], [1, 2, 3]]);
		$mb = new RationalMatrix([[0], [0], [0]]);*/
		/*$ma = new RationalMatrix([[1, 2, 0], [1, 2, 0], [1, 2, 0]]);
		$mb = new RationalMatrix([[0], [0], [0]]);*/
		/*$ma = new RationalMatrix([[1, 2, 3], [2, 2, 3], [1, 2, 5]]);
		$mb = new RationalMatrix([[0], [0], [0]]);*/
		$ma = new RationalMatrix([[1, 2, 3], [1, 2, 3], [1, 2, 3]]);
		$mb = new RationalMatrix([[0], [0], [0]]);
		$dec = $ma->decompose($el, $mb);
		//$dec = $ma->decompose(new \Chippyash\Math\Matrix\Decomposition\GaussJordanElimination(), $mb);

		//$this->printArray($this->rationalToArray($dec->product('left')));
		//$this->printArray($this->rationalToArray($dec->product('right')));
	}

	protected function rationalToArray($m){
		$arr = $m;
		if(is_array($arr) == false){
			$arr = $m->toArray();
		}
		foreach($arr as &$row){
			foreach($row as &$col){
				$col = $col->numerator().'/'.$col->denominator();
				//$col = $col->numerator() / $col->denominator();
			}
		}
		return $arr;
	}

	protected function printArray($arr){
		echo "\n";
		foreach($arr as $row){
			foreach($row as $col){
				echo $col.' ';
			}
			echo "\n";
		}
		echo "\n";
	}
}
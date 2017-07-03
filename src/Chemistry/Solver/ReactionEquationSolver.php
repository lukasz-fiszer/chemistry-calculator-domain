<?php

namespace ChemCalc\Domain\Chemistry\Solver;

use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Matrix\MatrixSolver;
use ChemCalc\Domain\Matrix\Decomposition\MatrixElimination;
use Chippyash\Math\Matrix\RationalMatrix;
use Chippyash\Math\Type\Calculator;

/**
 * Chemistry reaction equation solver
 */
class ReactionEquationSolver
{
	/**
	 * Reaction sides
	 * 
	 * @var array
	 */
	protected $reactionSides;

	/**
	 * Construct new reaction equation solver
	 * 
	 * @param  array $reactionSides reaction equation sides of interpreted molecules
	 * @throws Exception exception throw when number of reaction sides is different from 2
	 */
	public function __construct(array $reactionSides){
		$this->reactionSides = $reactionSides;
		if(count($reactionSides) != 2){
			throw new \Exception('Number of reaction sides is different from 2');
		}
	}

	/**
	 * Find stoichiometric coefficients for given reaction
	 * 
	 * @return array array of coefficients
	 */
	public function findCoefficients(){
		$reactionMatrix = $this->extractReactionMatrix();
		$matrixSolver = new MatrixSolver(new RationalMatrix($reactionMatrix['matrixA']), new RationalMatrix($reactionMatrix['matrixB']), new MatrixElimination(), new Calculator());
		return $matrixSolver->solve();
	}

	/**
	 * Extract reaction matrix
	 * 
	 * @return array of matrices a and b
	 */
	protected function extractReactionMatrix(){
		$elementsSymbols = $this->extractElementsSymbols();
		$reactionMatrix = array_fill(0, count($elementsSymbols), []);
		foreach($this->reactionSides as $i => $side){
			$sideMatrix = $this->buildSideMatrix($side, $elementsSymbols);
			if($i % 2 == 1){
				for($i = 0; $i < count($sideMatrix); $i++){
					for($j = 0; $j < count($sideMatrix[$i]); $j++){
						$sideMatrix[$i][$j] *= -1;
					}
				}
			}
			foreach($sideMatrix as $index => $row){
				$reactionMatrix[$index] = array_merge($reactionMatrix[$index], $row);
			}
		}
		return ['matrixA' => $reactionMatrix, 'matrixB' => array_fill(0, count($elementsSymbols), [0])];
	}

	/**
	 * Build matrix for given side of reaction
	 * 
	 * @param  array  $side side array
	 * @param  array  $elementsSymbol array of elements symbol
	 * @return array array side matrix
	 */
	protected function buildSideMatrix(array $side, array $elementsSymbols){
		$moleculesCount = count($side);
		$sideMatrix = array_fill(0, count($elementsSymbols), array_fill(0, $moleculesCount, 0));
		foreach($side as $i => $molecule){
			foreach($elementsSymbols as $index => $symbol){
				if($elementEntry = $molecule->getElementEntryBySymbol($symbol)){
					$sideMatrix[$index][$i] = $elementEntry['occurences'];
				}
			}
		}
		return $sideMatrix;
	}

	/**
	 * Extract elements symbols
	 * 
	 * @return array array of elements symbols
	 */
	protected function extractElementsSymbols(){
		$symbols = [];
		foreach($this->reactionSides as $side){
			foreach($side as $molecule){
				$elements = $molecule->getElements();
				$symbols = array_merge($symbols, array_map(function($element){
					return $element['element']->getSymbol();
				}, $elements));
			}
		}
		$symbols = array_values(array_unique($symbols));
		return $symbols;
	}
}
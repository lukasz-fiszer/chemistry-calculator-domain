<?php

namespace ChemCalc\Domain\Chemistry\Solver;

use ChemCalc\Domain\Chemistry\Entity\Molecule;

/**
 * Chemistry molecule solver
 */
class MoleculeSolver
{
	/**
	 * Molecule used
	 * 
	 * @var Molecule
	 */
	protected $molecule;

	/**
	 * Construct new molecule solver
	 * 
	 * @param Molecule $molecule molecule used
	 */
	public function __construct(Molecule $molecule){
		$this->molecule = $molecule;
	}

	/**
	 * Get moles for molecule with given grams
	 * 
	 * @param  float  $grams molecule grams
	 * @return float         molecule moles
	 */
	public function getMoles(float $grams){
		return $grams / $this->molecule->getAtomicMass();
	}

	/**
	 * Get grams for molecule with given moles
	 * 
	 * @param  float  $moles molecule moles
	 * @return float         molecule grams
	 */
	public function getGrams(float $moles){
		return $this->molecule->getAtomicMass() * $moles;
	}
}
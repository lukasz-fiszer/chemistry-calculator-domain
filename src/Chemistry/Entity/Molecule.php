<?php

namespace ChemCalc\Domain\Chemistry\Entity;

/**
 * Chemistry molecule
 * Immutable object
 */
class Molecule
{
	/**
	 * Array of molecule elements and their occurences
	 * 
	 * @var array
	 */
	protected $elements;

	/**
	 * Molecule formula
	 * 
	 * @var string
	 */
	protected $formula;

	/**
	 * Molecule atomic mass
	 * 
	 * @var float
	 */
	protected $atomicMass;

	/**
	 * True if molecule is real
	 * 
	 * @var bool
	 */
	protected $isReal;

	/**
	 * Construct new immutable molecule object
	 * 
	 * @param array  $elements array of molecule elements and their occurences
	 * @param string $formula  molecule formula
	 */
	public function __construct(array $elements, string $formula){
		$this->elements = $elements;
		$this->formula = $formula;
		$this->isReal = $this->checkIfIsReal();
		$this->atomicMass = $this->calculateAtomicMass();
	}

	/**
	 * Check if molecule is real, using isReal() on all molecule elements
	 * 
	 * @return bool true if molecule is real
	 */
	protected function checkIfIsReal(){
		//foreach($this->elements as list($element, $occurences)){
		foreach($this->elements as $elementEntry){
			if($elementEntry['element']->isReal() == false){
				return false;
			}
		}
		return true;
	}

	/**
	 * Calculate atomic mass of molecule, using elements atomic masses and their occurences
	 * 
	 * @return float molecule atomic mass
	 */
	protected function calculateAtomicMass(){
		$mass = 0;
		//foreach($this->elements as list($element, $occurences)){
		foreach($this->elements as $elementEntry){
			$mass += $elementEntry['element']->getAtomicMass() * $elementEntry['occurences'];
		}
		return $mass;
	}

	 /**
     * Check if molecule is real
     *
     * @return bool
     */
    public function isReal()
    {
        return $this->isReal;
    }

    /**
     * Gets the Array of molecule elements and their occurences.
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Gets the Molecule formula.
     *
     * @return string
     */
    public function getFormula()
    {
        return $this->formula;
    }

    /**
     * Gets the Molecule atomic mass.
     *
     * @return float
     */
    public function getAtomicMass()
    {
        return $this->atomicMass;
    }
}
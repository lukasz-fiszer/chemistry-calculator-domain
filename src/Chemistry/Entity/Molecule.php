<?php

namespace ChemCalc\Domain\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\MatchesElement;

/**
 * Chemistry molecule
 * Immutable object
 */
class Molecule
{
	use MatchesElement;

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
	 * Function that returns element data out of element entry
	 * 
	 * @var function
	 */
	protected $toElementData;

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
		$this->toElementData  = function($elementEntry){
			return $elementEntry['element']->getData();
		};
	}

	/**
	 * Check if molecule is real, using isReal() on all molecule elements
	 * 
	 * @return bool true if molecule is real
	 */
	protected function checkIfIsReal(){
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
		foreach($this->elements as $elementEntry){
			$mass += $elementEntry['element']->getAtomicMass() * $elementEntry['occurences'];
		}
		return $mass;
	}

	/**
	 * Check if molecule has given element present
	 * 
	 * @param  array   $element key-value pairs describing element data
	 * @return boolean          true if molecule has given element present
	 */
	public function hasElement(array $element){
		return $this->findMatchingElement($element, $this->elements, $this->toElementData) === null ? false : true;
	}

	/**
	 * Check if molecule has given element present by symbol
	 * 
	 * @param  string  $symbol element symbol
	 * @return boolean         true if molecule has given element present
	 */
	public function hasElementBySymbol(string $symbol){
		return $this->hasElement(['symbol' => $symbol]);
	}

	/**
	 * Get element entry containing element object and its occurences count
	 * 
	 * @param  array  $element key-value pairs describing element data
	 * @return array|null      element entry or null otherwise
	 */
	public function getElementEntry(array $element){
		return $this->findMatchingElement($element, $this->elements, $this->toElementData);
	}

	/**
	 * Get element entry containing element object and its occurences count
	 * 
	 * @param  string $symbol element symbol
	 * @return array|null     element entry or null otherwise
	 */
	public function getElementEntryBySymbol(string $symbol){
		return $this->getElementEntry(['symbol' => $symbol]);
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
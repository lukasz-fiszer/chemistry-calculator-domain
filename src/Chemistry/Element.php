<?php

namespace ChemCalc\Domain\Chemistry;

/**
 * Chemistry element
 * Immutable object, except for loading element data
 */
class Element
{
	/**
	 * Data array of element info and properties
	 * 
	 * @var array
	 */
	protected $data;

	/**
	 * Element name
	 * 
	 * @var string
	 */
	protected $name;

	/**
	 * Element symbol
	 * 
	 * @var string
	 */
	protected $symbol;

	/**
	 * Element atomic mass
	 * 
	 * @var float
	 */
	protected $atomicMass;

	/**
	 * True if element is real
	 * 
	 * @var bool
	 */
	protected $isReal;

	/**
	 * Construct new immutable element object
	 * 
	 * @param string $name       element name
	 * @param string $symbol     element symbol
	 * @param float  $atomicMass element atomic mass
	 * @param bool   $isReal     true if element is real
	 * @param array  $data       element data
	 */
	public function __construct(string $name, string $symbol, float $atomicMass, bool $isReal = true, array $data = []){
		$this->name = $name;
		$this->symbol = $symbol;
		$this->atomicMass = $atomicMass;
		$this->isReal = $isReal;
		$this->data = $data;
	}

	/**
	 * Check if element is real
	 * 
	 * @return boolean true if element is real
	 */
	public function isReal(){
		return $this->isReal;
	}

    /**
     * Gets the Data array of element info and properties.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Gets the Element name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets the Element symbol.
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Gets the Element atomic mass.
     *
     * @return float
     */
    public function getAtomicMass()
    {
        return $this->atomicMass;
    }
}
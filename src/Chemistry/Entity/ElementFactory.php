<?php

namespace ChemCalc\Domain\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\DataLoader\Interfaces\ElementDataLoader;

/**
 * Chemistry element factory
 */
class ElementFactory
{
	/**
	 * Element data loader used by factory
	 * 
	 * @var ElementDataLoader
	 */
	protected $elementDataLoader;

	/**
	 * Construct new element factory
	 * 
	 * @param ElementDataLoader $elementDataLoader element data loader used by factory
	 */
	public function __construct(ElementDataLoader $elementDataLoader){
		$this->elementDataLoader = $elementDataLoader;
	}

	/**
	 * Make element instance by symbol
	 * 
	 * @param  string $symbol element symbol
	 * @return Element constructed element
	 */
	public function makeElementBySymbol(string $symbol){
		$elementData = $this->elementDataLoader->getDataForElementBySymbol($symbol);
		if($elementData === null){
			return new Element('unknown', $symbol, 0, false);
		}
		return new Element($elementData['name'], $symbol, $elementData['atomic_mass'], true, $elementData);
	}

    /**
     * Gets the Element data loader used by factory.
     *
     * @return ElementDataLoader
     */
    public function getElementDataLoader()
    {
        return $this->elementDataLoader;
    }
}
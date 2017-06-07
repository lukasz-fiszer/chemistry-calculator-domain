<?php

namespace ChemCalc\Domain\Chemistry\DataLoader\Interfaces;

/**
 * Chemistry element data loader interface
 * Loads array of element data from given resorce
 */
interface ElementDataLoader
{
	/**
	 * Load data array for all elements
	 * 
	 * @return array  data for all elements
	 */
	public function loadData();

	/**
	 * Load data array for given element
	 * 
	 * @param  array  $element array of key-value pairs representing given element
	 * @return array|null  data for given element
	 */
	public function getDataForElement(array $element);

	/**
	 * Load data array for element given by symbol
	 * Its a shortcut method for specifying element symbol key-value pair in getDataForElement() method
	 * 
	 * @param  string $symbol element symbol
	 * @return array|null  data for given element
	 */
	public function getDataForElementBySymbol(string $symbol);
}
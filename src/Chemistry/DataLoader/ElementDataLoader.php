<?php

namespace ChemCalc\Domain\Chemistry\DataLoader;

use ChemCalc\Domain\Chemistry\DataLoader\Interfaces\ElementDataLoader as ElementDataLoaderInterface;

/**
 * Chemistry element data loader interface implementation
 * Loads array of element data from given resorce, here using json from res directory
 */
class ElementDataLoader implements ElementDataLoaderInterface
{
	/**
	 * Data for all elements
	 * 
	 * @var array
	 */
	protected $data;

	/**
	 * Path to json containing elements data
	 * 
	 * @var string
	 */
	protected $dataJsonPath;

	public function __construct(string $dataJsonPath = null){
		//$this->dataJsonPath = $dataJsonPath ?: '../../../res/PeriodicTableJSON.json';
		$this->dataJsonPath = $dataJsonPath ?: 'res/PeriodicTableJSON.json';
	}

	/**
	 * Load data array for all elements
	 * 
	 * @return array  data for all elements
	 */
	public function loadData(){
		if(isset($this->data)){
			return $this->data;
		}

		$this->data = json_decode(file_get_contents($this->dataJsonPath));
		return $this->data;
	}

	/**
	 * Load data array for given element
	 * 
	 * @param  array  $element array of key-value pairs representing given element
	 * @return array|null  data for given element
	 */
	public function getDataForElement(array $element){
		$data = $this->loadData();
		foreach($data as $elementData){
			if($this->checkIfElementMatchesData($element, $elementData)){
				return $elementData;
			}
		}
		return null;
	}

	/**
	 * Check if given element key-value pairs matches given full element data
	 * 
	 * @param  array  $element     element key-value pairs
	 * @param  array  $elementData full element data
	 * @return bool true if they match
	 */
	protected function checkIfElementMatchesData(array $element, array $elementData){
		foreach($element as $key => $value){
			if($elementData[$key] != $value){
				return false;
			}
		}
		return true;
	}

	/**
	 * Load data array for element given by symbol
	 * Its a shortcut method for specifying element symbol key-value pair in getDataForElement() method
	 * 
	 * @param  string $symbol element symbol
	 * @return array|null  data for given element
	 */
	public function getDataForElementBySymbol(string $symbol){
		return $this->getDataForElement(['symbol' => $symbol]);
	}
}
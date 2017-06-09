<?php

namespace ChemCalc\Domain\Chemistry\DataLoader;

use ChemCalc\Domain\Chemistry\DataLoader\Interfaces\ElementDataLoader as ElementDataLoaderInterface;
use ChemCalc\Domain\Chemistry\Entity\MatchesElement;

/**
 * Chemistry element data loader interface implementation
 * Loads array of element data from given resorce, here using json from res directory
 */
class ElementDataLoader implements ElementDataLoaderInterface
{
	use MatchesElement;

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

	/**
	 * Construct new element data loader that uses json
	 * 
	 * @param string|null $dataJsonPath path to json file with elements data, if null, default path for json in res/ directory is used 
	 */
	public function __construct(string $dataJsonPath = null){
		$this->dataJsonPath = $dataJsonPath ?: realpath(dirname(__FILE__)).'/../../../res/PeriodicTableJSON.json';
	}

	/**
	 * Load data array for all elements
	 * 
	 * @return array  data for all elements
	 */
	public function loadData(){
		if(isset($this->data) == false){
			$this->data = json_decode(file_get_contents($this->dataJsonPath), true)['elements'];
		}
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
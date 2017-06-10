<?php

namespace ChemCalc\Domain\Chemistry\Entity;

/**
 * Trait for matching elements
 */
trait MatchesElement
{
	/**
	 * Check if given element key-value pairs matches given full element data
	 * 
	 * @param  array  $element     element key-value pairs
	 * @param  array  $elementData full element data
	 * @return bool true if they match
	 */
	protected function checkIfElementMatchesData(array $element, array $elementData){
		foreach($element as $key => $value){
			if(isset($elementData[$key]) == false || $elementData[$key] != $value){
				return false;
			}
		}
		return true;
		//return array_intersect_assoc($elementData, $element) == $element;
	}

	/**
	 * Find matching element among elements data collection using callback on collection items
	 * 
	 * @param  array    $element                element key-value pairs
	 * @param  array    $elementsDataCollection elements data collection
	 * @param  callable $toElementData          optional callback to use on collection items
	 * @param  bool     $returnElementData      optional, true if element data after callback must be returned, false if original collection entry must be returned
	 * @return array|null  matched element data or null otherwise
	 */
	protected function findMatchingElement(array $element, array $elementsDataCollection, callable $toElementData = null, bool $returnElementData = false){
		$toElementData = $toElementData ?: function($arg){return $arg;};
		foreach($elementsDataCollection as $elementDataEntry){
			$elementData = $toElementData($elementDataEntry);
			if($this->checkIfElementMatchesData($element, $elementData)){
				return $returnElementData ? $elementData : $elementDataEntry;
			}
		}
		return null;
	}
}
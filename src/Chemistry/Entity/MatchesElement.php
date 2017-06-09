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
}
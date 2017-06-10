<?php

namespace ChemCalc\Domain\Tests\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\MatchesElement;
use ChemCalc\Domain\Tests\InvokesInaccessibleMethod;

class MatchesElementTest extends \PHPUnit\Framework\TestCase
{
	use InvokesInaccessibleMethod;

	/**
	 * @dataProvider elementsMatchesProvider
	 */
	public function testCheckIfElementMatchesData(array $element, array $elementData, bool $expectedMatches){
		$elementMatcherMock = $this->getMockForTrait(MatchesElement::class);
		$matches = $this->invokeMethod($elementMatcherMock, 'checkIfElementMatchesData', [$element, $elementData]);
		$this->assertEquals($expectedMatches, $matches);

	}

	public function elementsMatchesProvider(){
		return [
			[['symbol' => 'H'], ['symbol' => 'H'], true],
			[['symbol' => 'H'], ['symbol' => 'H', 'name' => 'Hydrogen'], true],
			[['symbol' => 'O'], ['symbol' => 'H', 'name' => 'Hydrogen'], false],
			[['symbol' => 'O'], ['symbol' => 'O', 'name' => 'Oxygen'], true],
			[['key' => 'val1'], ['key' => 'val1', 'other_key' => 'other_val'], true],
			[['key' => 'val1', 'key2' => 'val2'], ['key' => 'val1', 'key2' => 'val2', 'other_key' => 'other_val'], true],
			[['key' => 'val1', 'key2' => 'val2'], ['key' => 'val1', 'key2' => 'val2_changed', 'other_key' => 'other_val'], false],
			[['key' => 'val1', 'key2_non_existing' => 'val2'], ['key' => 'val1', 'other_key' => 'other_val'], false],
			[[], ['key' => 'val1', 'other_key' => 'other_val'], true],
			[[], [], true],
		];
	}

	public function testFindMatchingElement(){
		$elementMatcherMock = $this->getMockForTrait(MatchesElement::class);
		$toElementData = function($elementEntry){return $elementEntry['element'];};
		$e1 = ['symbol' => 'H'];
		$e2 = ['symbol' => 'H', 'name' => 'Hydrogen_changed_name'];
		$e3 = ['symbol' => 'FicSym'];
		$e4 = ['non_existent_key' => 'value'];
		$dataJsonPath = realpath(dirname(__FILE__)).'/../../../res/PeriodicTableJSON.json';
		$c1 = json_decode(file_get_contents($dataJsonPath), true)['elements'];
		$c2 = [
			['element' => $e1, 'occurences' => 2],
			['element' => $e2, 'occurences' => 4],
		];

		$matched = $this->invokeMethod($elementMatcherMock, 'findMatchingElement', [$e1, $c1]);
		$this->assertEquals($c1[0], $matched);
		$matched2 = $this->invokeMethod($elementMatcherMock, 'findMatchingElement', [$e1, $c1, null, true]);
		$this->assertEquals($c1[0], $matched2);

		/*$matched3 = $this->invokeMethod($elementMatcherMock, 'findMatchingElement', [$e1, $c1, null, true]);
		$this->assertEquals($c1[0], $matched2);*/

		$this->callFindMatchingElement(null, [$e2, $c1]);
		$this->callFindMatchingElement(null, [$e2, $c1, null, true]);
		$this->callFindMatchingElement(null, [$e3, $c1]);
		$this->callFindMatchingElement(null, [$e3, $c1], null, true);
		$this->callFindMatchingElement(null, [$e4, $c1]);
		$this->callFindMatchingElement(null, [$e4, $c1, null, true]);

		$this->callFindMatchingElement($e1, [$e1, $c2, $toElementData, true]);
		$this->callFindMatchingElement(['element' => $e1, 'occurences' => 2], [$e1, $c2, $toElementData]);
		$this->callFindMatchingElement(null, [$e3, $c2, $toElementData]);
		$this->callFindMatchingElement(null, [$e3, $c2, $toElementData, true]);
		$this->callFindMatchingElement(null, [$e4, $c2, $toElementData]);
		$this->callFindMatchingElement(null, [$e4, $c2, $toElementData, true]);

		$this->callFindMatchingElement(null, [$e3, $c2]);
		$this->callFindMatchingElement(null, [$e3, $c2, null, true]);
		$this->callFindMatchingElement(null, [$e4, $c2]);
		$this->callFindMatchingElement(null, [$e4, $c2, null, true]);
	}

	//protected function testFindMatchingElementCall(){
	protected function callFindMatchingElement($expected, $arguments){
		$elementMatcherMock = $this->getMockForTrait(MatchesElement::class);
		$matched = $this->invokeMethod($elementMatcherMock, 'findMatchingElement', $arguments);
		$this->assertEquals($expected, $matched);
	}

	/**
	 * @e/xpectedException Exception
	 * @expectedException \PHPUnit\Framework\Error\Error
	 */
	public function testFindMatchingElementCallableException(){
		$elementMatcherMock = $this->getMockForTrait(MatchesElement::class);
		$e1 = ['symbol' => 'H'];
		$e2 = ['symbol' => 'H', 'name' => 'Hydrogen_changed_name'];
		$c2 = [
			['element' => $e1, 'occurences' => 2],
			['element' => $e2, 'occurences' => 4],
		];
		$this->invokeMethod($elementMatcherMock, 'findMatchingElement', [$e1, $c2, function($entry){return $entry['element_changed_key'];}]);
	}
}
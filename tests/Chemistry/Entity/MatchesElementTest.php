<?php

namespace ChemCalc\Domain\Tests\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\MatchesElement;
use ChemCalc\Domain\Tests\InvokesInaccessibleMethod;

class MatchesElementTest extends \PHPUnit\Framework\TestCase
{
	use InvokesInaccessibleMethod;

	public function setUp(): void {
		if(isset($this->initialized) && $this->initialized == true){
			return;
		}
		$this->initialized = true;
		$this->elementMatcherMock = $this->getMockForTrait(MatchesElement::class);
		$this->toElementData = function($elementEntry){return $elementEntry['element'];};
		$this->e1 = ['symbol' => 'H'];
		$this->e2 = ['symbol' => 'H', 'name' => 'Hydrogen_changed_name'];
		$this->e3 = ['symbol' => 'FicSym'];
		$this->e4 = ['non_existent_key' => 'value'];
		$this->dataJsonPath = realpath(dirname(__FILE__)).'/../../../res/PeriodicTableJSON.json';
		$this->c1 = json_decode(file_get_contents($this->dataJsonPath), true)['elements'];
		$this->c2 = [
			['element' => $this->e1, 'occurences' => 2],
			['element' => $this->e2, 'occurences' => 4],
		];
	}

	/**
	 * @dataProvider elementsMatchesProvider
	 */
	public function testCheckIfElementMatchesData(array $element, array $elementData, bool $expectedMatches){
		$matches = $this->invokeMethod($this->elementMatcherMock, 'checkIfElementMatchesData', [$element, $elementData]);
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
		$this->callFindMatchingElement($this->c1[0], [$this->e1, $this->c1]);
		$this->callFindMatchingElement($this->c1[0], [$this->e1, $this->c1, null, true]);

		$this->callFindMatchingElement(null, [$this->e2, $this->c1]);
		$this->callFindMatchingElement(null, [$this->e2, $this->c1, null, true]);
		$this->callFindMatchingElement(null, [$this->e3, $this->c1]);
		$this->callFindMatchingElement(null, [$this->e3, $this->c1], null, true);
		$this->callFindMatchingElement(null, [$this->e4, $this->c1]);
		$this->callFindMatchingElement(null, [$this->e4, $this->c1, null, true]);

		$this->callFindMatchingElement($this->e1, [$this->e1, $this->c2, $this->toElementData, true]);
		$this->callFindMatchingElement(['element' => $this->e1, 'occurences' => 2], [$this->e1, $this->c2, $this->toElementData]);

		$this->callFindMatchingElement(null, [$this->e3, $this->c2, $this->toElementData]);
		$this->callFindMatchingElement(null, [$this->e3, $this->c2, $this->toElementData, true]);
		$this->callFindMatchingElement(null, [$this->e4, $this->c2, $this->toElementData]);
		$this->callFindMatchingElement(null, [$this->e4, $this->c2, $this->toElementData, true]);

		$this->callFindMatchingElement(null, [$this->e3, $this->c2]);
		$this->callFindMatchingElement(null, [$this->e3, $this->c2, null, true]);
		$this->callFindMatchingElement(null, [$this->e4, $this->c2]);
		$this->callFindMatchingElement(null, [$this->e4, $this->c2, null, true]);
	}

	protected function callFindMatchingElement($expected, $arguments){
		$matched = $this->invokeMethod($this->elementMatcherMock, 'findMatchingElement', $arguments);
		$this->assertEquals($expected, $matched);
	}

	/**
	 * @expectedException PHPUnit\Framework\Error\Error
	 */
	public function testFindMatchingElementCallableException(){
		$this->invokeMethod($this->elementMatcherMock, 'findMatchingElement', [$this->e1, $this->c2, function($entry){return $entry['element_changed_key'];}]);
	}
}
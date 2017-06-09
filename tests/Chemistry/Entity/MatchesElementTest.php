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
}
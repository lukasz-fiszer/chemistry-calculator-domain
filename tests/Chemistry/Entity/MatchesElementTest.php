<?php

namespace ChemCalc\Domain\Tests\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\MatchesElement;
use ChemCalc\Domain\Tests\InvokesInaccessibleMethod;

//class MatchesElementTest extends \PHPUnit_Framework_TestCase
class MatchesElementTest extends \PHPUnit\Framework\TestCase
{
	use InvokesInaccessibleMethod;

	/*public function __construct(){
		parent::__construct();
		$this->elementMatcherMock = $this->getMockForTrait(MatchesElement::class);
	}*/

	/**
	 * @dataProvider elementsMatchesProvider
	 */
	//public function testCheckIfElementMatchesData(array $element, array $elementData, bool $expectedMatches){
	public function testCheckIfElementMatchesData($element, $elementData, bool $expectedMatches){
		//echo 'abcd';
		//$this->elementMatcherMock = $this->getMockForTrait(MatchesElement::class);
		//$this->assertEquals($expectedMatches, $this->elementMatcherMock->checkIfElementMatchesData($element, $elementData));
		$elementMatcherMock = $this->getMockForTrait(MatchesElement::class);
		$matches = $this->invokeMethod($elementMatcherMock, 'checkIfElementMatchesData', [$element, $elementData]);
		$this->assertEquals($expectedMatches, $matches);

	}

	public function testA(){
		$this->assertTrue(true);
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
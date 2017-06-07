<?php

namespace ChemCalc\Domain\Tests\Chemistry;

use ChemCalc\Domain\Chemistry\Element;

class ElementTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructorPropertiesInjection(){
		$element = new Element('Hydrogen', 'H', 1.008);
		$this->assertAttributeEquals('Hydrogen', 'name', $element);
		$this->assertAttributeEquals('H', 'symbol', $element);
		$this->assertAttributeEquals(1.008, 'atomicMass', $element);
	}
}
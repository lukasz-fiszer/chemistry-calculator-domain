<?php

namespace ChemCalc\Domain\Tests\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\Element;

class ElementTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$element = new Element('Hydrogen', 'H', 1.008);
		$this->assertAttributeEquals('Hydrogen', 'name', $element);
		$this->assertAttributeEquals('H', 'symbol', $element);
		$this->assertAttributeEquals(1.008, 'atomicMass', $element);
		$this->assertAttributeEquals(true, 'isReal', $element);
		$this->assertAttributeEquals(['name' => 'Hydrogen', 'symbol' => 'H', 'atomic_mass' => 1.008, 'is_real' => true], 'data', $element);

		$element2 = new Element('Fictious Element', 'FicSym', 20.40, false, ['attribute' => 'value']);
		$this->assertAttributeEquals('Fictious Element', 'name', $element2);
		$this->assertAttributeEquals('FicSym', 'symbol', $element2);
		$this->assertAttributeEquals(20.40, 'atomicMass', $element2);
		$this->assertAttributeEquals(false, 'isReal', $element2);
		$this->assertAttributeEquals(['attribute' => 'value', 'name' => 'Fictious Element', 'symbol' => 'FicSym', 'atomic_mass' => 20.40, 'is_real' => false], 'data', $element2);
	}
}
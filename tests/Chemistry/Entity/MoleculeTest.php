<?php

namespace ChemCalc\Domain\Tests\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\Element;

class MoleculeTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(): void {
		if(isset($this->initialized) && $this->initialized == true){
			return;
		}
		$this->initialized = true;
		$this->h = new Element('Hydrogen', 'H', 1.008);
		$this->o = new Element('Oxygen', 'O', 15.999);
		$this->h2o = new Molecule([
			['element' => $this->h, 'occurences' => 2],
			['element' => $this->o, 'occurences' => 1]
		], 'H2O');
		$this->f = new Element('Fictious Element', 'Fic', 20.40, false);
		$this->molecule2 = new Molecule([
			['element' => $this->h, 'occurences' => 2],
			['element' => $this->f, 'occurences' => 2]
		], 'Fic2H2');
	}

	public function testConstructorPropertiesInjection(){
		$this->assertAttributeEquals([['element' => $this->h, 'occurences' => 2], ['element' => $this->o, 'occurences' => 1]], 'elements', $this->h2o);
		$this->assertAttributeEquals('H2O', 'formula', $this->h2o);
		$this->assertAttributeEquals(1.008*2 + 15.999, 'atomicMass', $this->h2o);
		$this->assertAttributeEquals(true, 'isReal', $this->h2o);
		$this->assertAttributeEquals(0, 'charge', $this->h2o);

		$this->assertAttributeEquals([['element' => $this->h, 'occurences' => 2], ['element' => $this->f, 'occurences' => 2]], 'elements', $this->molecule2);
		$this->assertAttributeEquals('Fic2H2', 'formula', $this->molecule2);
		$this->assertAttributeEquals(20.40*2 + 1.008*2, 'atomicMass', $this->molecule2);
		$this->assertAttributeEquals(false, 'isReal', $this->molecule2);
		$this->assertAttributeEquals(0, 'charge', $this->molecule2);

		$this->molecule3 = new Molecule([['element' => $this->h, 'occurences' => 2]], 'H2{-}2', -2);
		$this->assertAttributeEquals([['element' => $this->h, 'occurences' => 2]], 'elements', $this->molecule3);
		$this->assertAttributeEquals('H2{-}2', 'formula', $this->molecule3);
		$this->assertAttributeEquals(1.008*2 + 2*0.000548579909 , 'atomicMass', $this->molecule3);
		$this->assertAttributeEquals(true, 'isReal', $this->molecule3);
		$this->assertAttributeEquals(-2, 'charge', $this->molecule3);

		$this->molecule4 = new Molecule([['element' => $this->h, 'occurences' => 2]], 'H2{+}2', 2);
		$this->assertAttributeEquals([['element' => $this->h, 'occurences' => 2]], 'elements', $this->molecule4);
		$this->assertAttributeEquals('H2{+}2', 'formula', $this->molecule4);
		$this->assertAttributeEquals(1.008*2 - 2*0.000548579909 , 'atomicMass', $this->molecule4);
		$this->assertAttributeEquals(true, 'isReal', $this->molecule4);
		$this->assertAttributeEquals(2, 'charge', $this->molecule4);
	}

	public function testHasElement(){
		$this->assertTrue($this->h2o->hasElement(['name' => 'Hydrogen', 'symbol' => 'H']));
		$this->assertFalse($this->h2o->hasElement(['name' => 'Fictious Element', 'symbol' => 'FicSym']));
	}

	public function testHasElementBySymbol(){
		$this->assertTrue($this->h2o->hasElementBySymbol('H'));
		$this->assertFalse($this->h2o->hasElementBySymbol('FicSym'));
	}

	public function testGetElementEntry(){
		$this->assertEquals(['element' => $this->h, 'occurences' => 2], $this->h2o->getElementEntry(['name' => 'Hydrogen', 'symbol' => 'H']));
		$this->assertEquals(null, $this->h2o->getElementEntry(['name' => 'Fictious Element', 'symbol' => 'FicSym']));
	}

	public function testGetElementEntryBySymbol(){
		$this->assertEquals(['element' => $this->h, 'occurences' => 2], $this->h2o->getElementEntryBySymbol('H'));
		$this->assertEquals(null, $this->h2o->getElementEntryBySymbol('FicSym'));
	}
}
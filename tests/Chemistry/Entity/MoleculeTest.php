<?php

namespace ChemCalc\Domain\Tests\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\Element;

class MoleculeTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(){
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

		$this->assertAttributeEquals([['element' => $this->h, 'occurences' => 2], ['element' => $this->f, 'occurences' => 2]], 'elements', $this->molecule2);
		$this->assertAttributeEquals('Fic2H2', 'formula', $this->molecule2);
		$this->assertAttributeEquals(20.40*2 + 1.008*2, 'atomicMass', $this->molecule2);
		$this->assertAttributeEquals(false, 'isReal', $this->molecule2);
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
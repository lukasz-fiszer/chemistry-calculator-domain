<?php

namespace ChemCalc\Domain\Tests\Chemistry;

use ChemCalc\Domain\Chemistry\Molecule;
use ChemCalc\Domain\Chemistry\Element;

class MoleculeTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructorPropertiesInjection(){
		$h = new Element('Hydrogen', 'H', 1.008);
		$o = new Element('Oxygen', 'O', 15.999);
		$molecule = new Molecule([
			['element' => $h, 'occurences' => 2],
			['element' => $o, 'occurences' => 1]
		], 'H2O');
		$this->assertAttributeEquals([['element' => $h, 'occurences' => 2], ['element' => $o, 'occurences' => 1]], 'elements', $molecule);
		$this->assertAttributeEquals('H2O', 'formula', $molecule);
		$this->assertAttributeEquals(1.008*2 + 15.999, 'atomicMass', $molecule);
		$this->assertAttributeEquals(true, 'isReal', $molecule);

		$f = new Element('Fictious Element', 'Fic', 20.40, false);
		$molecule2 = new Molecule([
			['element' => $h, 'occurences' => 2],
			['element' => $f, 'occurences' => 2]
		], 'Fic2H2');
		$this->assertAttributeEquals([['element' => $h, 'occurences' => 2], ['element' => $f, 'occurences' => 2]], 'elements', $molecule2);
		$this->assertAttributeEquals('Fic2H2', 'formula', $molecule2);
		$this->assertAttributeEquals(20.40*2 + 1.008*2, 'atomicMass', $molecule2);
		$this->assertAttributeEquals(false, 'isReal', $molecule2);
	}
}
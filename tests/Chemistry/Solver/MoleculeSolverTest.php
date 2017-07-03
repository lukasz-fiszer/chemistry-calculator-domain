<?php

namespace ChemCalc\Domain\Tests\Chemistry\Solver;

use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\Element;
use ChemCalc\Domain\Chemistry\Solver\MoleculeSolver;

class MoleculeSolverTest extends \PHPUnit\Framework\TestCase
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
		$this->emptyMolecule = new Molecule([], '');
	}

	public function testConstructorPropertiesInjection(){
		$molsol = new MoleculeSolver($this->h2o);
		$this->assertAttributeEquals($this->h2o, 'molecule', $molsol);
		$molsol2 = new MoleculeSolver($this->molecule2);
		$this->assertAttributeEquals($this->molecule2, 'molecule', $molsol2);
		$molsol3 = new MoleculeSolver($this->emptyMolecule);
		$this->assertAttributeEquals($this->emptyMolecule, 'molecule', $molsol3);
	}

	public function testGetMoles(){
		$molsol = new MoleculeSolver($this->h2o);
		$this->assertEquals(100 / (1.008*2 + 15.999), $molsol->getMoles(100));
		$this->assertEquals(0 / (1.008*2 + 15.999), $molsol->getMoles(0));
		$molsol2 = new MoleculeSolver($this->molecule2);
		$this->assertEquals(100 / (1.008*2 + 20.40*2), $molsol2->getMoles(100));
		$this->assertEquals(0 / (1.008*2 + 20.40*2), $molsol2->getMoles(0));
		$molsol3 = new MoleculeSolver($this->emptyMolecule);
		$this->expectException(\PHPUnit\Framework\Error\Error::class);
		$this->assertEquals(0, $molsol3->getMoles(100));
		$this->assertEquals(0, $molsol3->getMoles(0));
	}

	public function testGetGrams(){
		$molsol = new MoleculeSolver($this->h2o);
		$this->assertEquals(100 * (1.008*2 + 15.999), $molsol->getGrams(100));
		$this->assertEquals(0 * (1.008*2 + 15.999), $molsol->getGrams(0));
		$molsol2 = new MoleculeSolver($this->molecule2);
		$this->assertEquals(100 * (1.008*2 + 20.40*2), $molsol2->getGrams(100));
		$this->assertEquals(0 * (1.008*2 + 20.40*2), $molsol2->getGrams(0));
		$molsol3 = new MoleculeSolver($this->emptyMolecule);
		$this->assertEquals(0, $molsol3->getGrams(100));
		$this->assertEquals(0, $molsol3->getGrams(0));
	}
}
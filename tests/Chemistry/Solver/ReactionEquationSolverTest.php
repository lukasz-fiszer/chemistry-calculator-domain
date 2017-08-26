<?php

namespace ChemCalc\Domain\Tests\Chemistry\Solver;

use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\Element;
use ChemCalc\Domain\Chemistry\Solver\ReactionEquationSolver;

class ReactionEquationSolverTest extends \PHPUnit\Framework\TestCase
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
		$this->fe = new Element('name', 'Fe', 10);
		$this->cl = new Element('name', 'Cl', 10);
		$this->k = new Element('name', 'K', 5);
		$this->c = new Element('name', 'C', 5);
		$this->n = new Element('name', 'N', 5);
		$this->mn = new Element('name', 'Mn', 5);
		$this->s = new Element('name', 'S', 5);
		$this->cr = new Element('name', 'Cr', 5);
	}

	public function testConstructorPropertiesInjection(){
		$sides = [[$this->h2o], [$this->h2o]];
		$reactionSolver = new ReactionEquationSolver($sides);
		$this->assertAttributeEquals($sides, 'reactionSides', $reactionSolver);
	}

	/**
	 * @expectedException Exception
	 */
	public function testConstructorExceptionForNonTwoSides(){
		$reactionSolver = new ReactionEquationSolver([[$this->h2o]]);
	}

	/**
	 * @dataProvider findCoefficientsDataProvider
	 */
	public function testFindCoefficientsMethod(array $sides, array $coefficients){
		$reactionSolver = new ReactionEquationSolver($sides);
		$this->assertEquals($coefficients, $reactionSolver->findCoefficients());
	}

	public function findCoefficientsDataProvider(){
		$this->setUp();
		return [
			[
				[[$this->h2o],
				[$this->h2o]],
				[1, 1]
			],
			[
				[[$this->molecule2],
				[$this->h2o]],
				[0, 0]
			],
			[
				[[$this->emptyMolecule],
				[$this->emptyMolecule]],
				[1, 1]
			],
			[
				[[new Molecule([['element' => $this->fe, 'occurences' => 1]], 'Fe')],
				[new Molecule([['element' => $this->fe, 'occurences' => 1], ['element' => $this->cl, 'occurences' => 3]], 'FeCl3')]],
				[0, 0]
			],
			[
				[[new Molecule([['element' => $this->fe, 'occurences' => 1]], 'Fe'),
				new Molecule([['element' => $this->cl, 'occurences' => 2]], 'Cl2')],
				[new Molecule([['element' => $this->fe, 'occurences' => 1], ['element' => $this->cl, 'occurences' => 3]], 'FeCl3')]],
				[2, 3, 2]
			],
			[
				[[new Molecule([['element' => $this->k, 'occurences' => 4], ['element' => $this->fe, 'occurences' => 1], ['element' => $this->c, 'occurences' => 6], ['element' => $this->n, 'occurences' => 6]], 'K4Fe(CN)6'),
				new Molecule([['element' => $this->k, 'occurences' => 1], ['element' => $this->mn, 'occurences' => 1], ['element' => $this->o, 'occurences' => 4]], 'KMnO4'),
				new Molecule([['element' => $this->h, 'occurences' => 2], ['element' => $this->s, 'occurences' => 1], ['element' => $this->o, 'occurences' => 4]], 'H2SO4')],
				[new Molecule([['element' => $this->k, 'occurences' => 1], ['element' => $this->h, 'occurences' => 1], ['element' => $this->s, 'occurences' => 1], ['element' => $this->o, 'occurences' => 4]], 'KHSO4'),
				new Molecule([['element' => $this->fe, 'occurences' => 2], ['element' => $this->s, 'occurences' => 3], ['element' => $this->o, 'occurences' => 12]], 'Fe2(SO4)3'),
				new Molecule([['element' => $this->mn, 'occurences' => 1], ['element' => $this->s, 'occurences' => 1], ['element' => $this->o, 'occurences' => 4]], 'MnSO4'),
				new Molecule([['element' => $this->h, 'occurences' => 1], ['element' => $this->n, 'occurences' => 1], ['element' => $this->o, 'occurences' => 3]], 'HNO3'),
				new Molecule([['element' => $this->c, 'occurences' => 1], ['element' => $this->o, 'occurences' => 2]], 'CO2'),
				new Molecule([['element' => $this->h, 'occurences' => 2], ['element' => $this->o, 'occurences' => 1]], 'H2O')]],
				[10, 122, 299, 162, 5, 122, 60, 60, 188]
			],
			[
				[[new Molecule([['element' => $this->cr, 'occurences' => 2], ['element' => $this->o, 'occurences' => 7]], 'Cr2O7{-2}', -2),
				new Molecule([['element' => $this->h, 'occurences' => 1]], 'H{+}', 1),
				new Molecule([], '{-}', -1)],
				[new Molecule([['element' => $this->cr, 'occurences' => 1]], 'Cr{+3}', 3),
				new Molecule([['element' => $this->h, 'occurences' => 2], ['element' => $this->o, 'occurences' => 1]], 'H2O')]],
				[1, 14, 6, 2, 7]
			],
			[
				[[new Molecule([], '{+}', 1),
				new Molecule([], '{-}', -1)],
				[new Molecule([], '{+}', 1),
				new Molecule([], '{-}', -1)]],
				[1, 1, 1, 1]
			],
		];
	}
}
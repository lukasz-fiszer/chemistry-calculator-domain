<?php

namespace ChemCalc\Domain\Tests\Chemistry;

use ChemCalc\Domain\Chemistry\EntryPoint;
use ChemCalc\Domain\Chemistry\Entity\Element;
use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Solver\MoleculeSolver;
use ChemCalc\Domain\Chemistry\DataLoader\ElementDataLoader;
use ChemCalc\Domain\Chemistry\Solver\ReactionEquationSolver;

class EntryPointTest extends \PHPUnit\Framework\TestCase
{
	static $elements;
	static $molecules;
	static $elementsData;

	public function setUp(){
		if(self::$elements === null){
			self::$elementsData = (new ElementDataLoader())->loadData();
			self::$elements = [
				'h' => new Element('Hydrogen', 'H', 1.008, true, self::$elementsData[0]),
				'o' => new Element('Oxygen', 'O', 15.999, true, self::$elementsData[7]),
			];
			self::$molecules = [
				'h' => new Molecule([
					['element' => self::$elements['h'], 'occurences' => 1],
				], 'H'),
				'h2' => new Molecule([
					['element' => self::$elements['h'], 'occurences' => 2],
				], 'H2'),
				'o' => new Molecule([
					['element' => self::$elements['o'], 'occurences' => 1],
				], 'O'),
				'o2' => new Molecule([
					['element' => self::$elements['o'], 'occurences' => 2],
				], 'O2'),
				'h2o' => new Molecule([
					['element' => self::$elements['h'], 'occurences' => 2],
					['element' => self::$elements['o'], 'occurences' => 1],
				], 'H2O'),
			];
		}
	}

	public static function tearDownAfterClass(){
		self::$elements = null;
		self::$molecules = null;
		self::$elementsData = null;
	}

	public function testConstructorPropertiesInjection(){
		$entryPoint = new EntryPoint('test');
		$this->assertAttributeEquals('test', 'input', $entryPoint);
		$this->assertEquals('test', $entryPoint->getInput());

		$this->assertAttributeEquals(null, 'parser', $entryPoint);
		$this->assertEquals(null, $entryPoint->getParser());
		$this->assertAttributeEquals(null, 'parsed', $entryPoint);
		$this->assertEquals(null, $entryPoint->getParsed());
		$this->assertAttributeEquals(null, 'interpreter', $entryPoint);
		$this->assertEquals(null, $entryPoint->getInterpreter());
		$this->assertAttributeEquals(null, 'interpreted', $entryPoint);
		$this->assertEquals(null, $entryPoint->getInterpreted());
		$this->assertAttributeEquals(null, 'solver', $entryPoint);
		$this->assertEquals(null, $entryPoint->getSolver());
		$this->assertAttributeEquals(null, 'solved', $entryPoint);
		$this->assertEquals(null, $entryPoint->getSolved());
	}

	/**
	 * @dataProvider proceedReturnValueDataProvider
	 */
	public function testProceedReturnValue($input, $returned){
		$entryPoint = new EntryPoint($input);
		$this->assertEquals((object) $returned, $entryPoint->proceed());
	}

	public function proceedReturnValueDataProvider(){
		$this->setUp();
		return [
			[
				'H',
				[
					'status' => 'molecule',
					'code' => 1,
					'context' => (object) [
						'molecule' => self::$molecules['h'],
						'solver' => new MoleculeSolver(self::$molecules['h']),
					],
					'message' => null,
					'previous' => null,
				]
			],

			[
				'H2O',
				[
					'status' => 'molecule',
					'code' => 1,
					'context' => (object) [
						'molecule' => self::$molecules['h2o'],
						'solver' => new MoleculeSolver(self::$molecules['h2o']),
					],
					'message' => null,
					'previous' => null,
				]
			],

			[
				'H2 + O2 = H2O',
				[
					'status' => 'reaction_equation',
					'code' => 2,
					'context' => (object) [
						'sides' => [
							[
								self::$molecules['h2'],
								self::$molecules['o2']
							],
							[
								self::$molecules['h2o']
							]
						],
						'solver' => new ReactionEquationSolver([[self::$molecules['h2'], self::$molecules['o2']], [self::$molecules['h2o']]]),
						'solved' => [2, 1, 2],
					],
					'message' => null,
					'previous' => null,
				]
			],

			[
				'H2 = H2',
				[
					'status' => 'reaction_equation',
					'code' => 2,
					'context' => (object) [
						'sides' => [
							[
								self::$molecules['h2'],
							],
							[
								self::$molecules['h2']
							]
						],
						'solver' => new ReactionEquationSolver([[self::$molecules['h2']], [self::$molecules['h2']]]),
						'solved' => [1, 1],
					],
					'message' => null,
					'previous' => null,
				]
			],
		];
	}
}
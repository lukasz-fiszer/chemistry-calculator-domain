<?php

namespace ChemCalc\Domain\Tests\Chemistry;

use ChemCalc\Domain\Chemistry\EntryPoint;
use ChemCalc\Domain\Chemistry\Entity\Element;
use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Solver\MoleculeSolver;
use ChemCalc\Domain\Chemistry\DataLoader\ElementDataLoader;
use ChemCalc\Domain\Chemistry\Solver\ReactionEquationSolver;
use ChemCalc\Domain\Chemistry\Parser\ParserException;

class EntryPointTest extends \PHPUnit\Framework\TestCase
{
	static $elements;
	static $molecules;
	static $elementsData;

	public function setUp(): void {
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

	public static function tearDownAfterClass() : void {
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
	 * @dataProvider proceedErrorReturnValueDataProvider
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
						'sides' => $s = [
							[
								self::$molecules['h2'],
								self::$molecules['o2']
							],
							[
								self::$molecules['h2o']
							]
						],
						'solver' => new ReactionEquationSolver($s),
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
						'sides' => $s = [
							[
								self::$molecules['h2'],
							],
							[
								self::$molecules['h2']
							]
						],
						'solver' => new ReactionEquationSolver($s),
						'solved' => [1, 1],
					],
					'message' => null,
					'previous' => null,
				]
			],
		];
	}

	public function proceedErrorReturnValueDataProvider(){
		$this->setUp();
		return [
			[
				'',
				[
					'status' => 'error',
					'code' => 100,
					'context' => null,
					'message' => 'Empty input',
					'previous' => null,
				]
			],

			[
				' ',
				[
					'status' => 'error',
					'code' => 100,
					'context' => null,
					'message' => 'Empty input',
					'previous' => null,
				]
			],

			[
				'test',
				[
					'status' => 'error',
					'code' => 201,
					'context' => $c = (object) [
						'input' => 'test',
						'position' => 0,
						'line' => 0,
						'column' => 0,
						'character' => 't'
					],
					'message' => $m = 'Character exception: \'t\' (line: 0, column: 0)',
					'previous' => new ParserException($m, $c, 1),
				]
			],

			[
				' test ',
				[
					'status' => 'error',
					'code' => 201,
					'context' => $c = (object) [
						'input' => ' test ',
						'position' => 1,
						'line' => 0,
						'column' => 1,
						'character' => 't'
					],
					'message' => $m = 'Character exception: \'t\' (line: 0, column: 1)',
					'previous' => new ParserException($m, $c, 1),
				]
			],

			[
				'H2O)',
				[
					'status' => 'error',
					'code' => 202,
					'context' => $c = (object) [
						'input' => 'H2O)',
						'position' => 4,
						'line' => 0,
						'column' => 4,
						'token' => $t = (object) [
							'type' => 'punctuation',
							'value' => ')',
							'mode' => 'close',
							'opposite' => '(',
						]
					],
					'message' => $m = 'Unexpected token: '.json_encode($t).' (line: 0, column: 4)',
					'previous' => new ParserException($m, $c, 2),
				]
			],

			[
				'H2O ++++',
				[
					'status' => 'error',
					'code' => 202,
					'context' => $c = (object) [
						'input' => 'H2O ++++',
						'position' => 8,
						'line' => 0,
						'column' => 8,
						'token' => $t = (object) [
							'type' => 'operator',
							'value' => '++++',
						]
					],
					'message' => $m = 'Unexpected token: '.json_encode($t).' (line: 0, column: 8)',
					'previous' => new ParserException($m, $c, 2),
				]
			],

			[
				'H2O2{+)',
				[
					'status' => 'error',
					'code' => 204,
					'context' => $c = (object) [
						'input' => 'H2O2{+)',
						'position' => 7,
						'line' => 0,
						'column' => 7,
						'actualToken' => (object) [
							'type' => 'punctuation',
							'value' => ')',
							'mode' => 'close',
							'opposite' => '(',
						],
						'expectedType' => 'punctuation',
						'expectedValue' => '}',
					],
					'message' => $m = 'Expected token of type: punctuation and value of: } (line: 0, column: 7)',
					'previous' => new ParserException($m, $c, 4),
				]
			],

			[
				'H2O2{->)',
				[
					'status' => 'error',
					'code' => 204,
					'context' => $c = (object) [
						'input' => 'H2O2{->)',
						'position' => 7,
						'line' => 0,
						'column' => 7,
						'actualToken' => (object) [
							'type' => 'operator',
							'value' => '->',
						],
						'expectedType' => 'punctuation',
						'expectedValue' => '}',
					],
					'message' => $m = 'Expected token of type: punctuation and value of: } (line: 0, column: 7)',
					'previous' => new ParserException($m, $c, 4),
				]
			],

			[
				'H2O2(',
				[
					'status' => 'error',
					'code' => 204,
					'context' => $c = (object) [
						'input' => 'H2O2(',
						'position' => 5,
						'line' => 0,
						'column' => 5,
						'actualToken' => null,
						'expectedType' => 'punctuation',
						'expectedValue' => ')',
					],
					'message' => $m = 'Expected token of type: punctuation and value of: ) (line: 0, column: 5)',
					'previous' => new ParserException($m, $c, 4),
				]
			],

			[
				'H2O + +',
				[
					'status' => 'error',
					'code' => 352,
					'context' => $c = (object) [
						'at' => 2,
						'expectedType' => 'molecule',
						'actual' => $a = (object) [
							'type' => 'operator',
							'value' => '+',
							'mode' => 'plus'
						],
						'code' => 2,
					],
					'message' => $m = 'Expected molecule node at 2 node instead of: '.json_encode($a),
					'previous' => (object) [
						'type' => 'unknown',
						'message' => $m,
						'context' => $c
					],
				]
			],

			[
				'+',
				[
					'status' => 'error',
					'code' => 352,
					'context' => $c = (object) [
						'at' => 0,
						'expectedType' => 'molecule',
						'actual' => $a = (object) [
							'type' => 'operator',
							'value' => '+',
							'mode' => 'plus'
						],
						'code' => 2,
					],
					'message' => $m = 'Expected molecule node at 0 node instead of: '.json_encode($a),
					'previous' => (object) [
						'type' => 'unknown',
						'message' => $m,
						'context' => $c
					],
				]
			],

			[
				'H2 + O2',
				[
					'status' => 'error',
					'code' => 354,
					'context' => $c = (object) [
						'sidesCount' => 1,
						'code' => 4,
					],
					'message' => $m = 'Too few sides (1)',
					'previous' => (object) [
						'type' => 'unknown',
						'message' => $m,
						'context' => $c
					],
				]
			],

			[
				'H2 + O2 = H2O2 = H2 + HO = H2O2',
				[
					'status' => 'error',
					'code' => 355,
					'context' => $c = (object) [
						'sidesCount' => 4,
						'code' => 5,
					],
					'message' => $m = 'Too many sides (4)',
					'previous' => (object) [
						'type' => 'unknown',
						'message' => $m,
						'context' => $c
					],
				]
			],

			[
				'H2 +',
				[
					'status' => 'error',
					'code' => 356,
					'context' => $c = (object) [
						'at' => 2,
						'code' => 6,
					],
					'message' => $m = 'Operator should be followed by molecule',
					'previous' => (object) [
						'type' => 'unknown',
						'message' => $m,
						'context' => $c
					],
				]
			],

			[
				'H2+',
				[
					'status' => 'error',
					'code' => 356,
					'context' => $c = (object) [
						'at' => 2,
						'code' => 6,
					],
					'message' => $m = 'Operator should be followed by molecule',
					'previous' => (object) [
						'type' => 'unknown',
						'message' => $m,
						'context' => $c
					],
				]
			],

			[
				'H2+O2=',
				[
					'status' => 'error',
					'code' => 356,
					'context' => $c = (object) [
						'at' => 4,
						'code' => 6,
					],
					'message' => $m = 'Operator should be followed by molecule',
					'previous' => (object) [
						'type' => 'unknown',
						'message' => $m,
						'context' => $c
					],
				]
			],

			[
				'H2+O2=H2O+H2+',
				[
					'status' => 'error',
					'code' => 356,
					'context' => $c = (object) [
						'at' => 8,
						'code' => 6,
					],
					'message' => $m = 'Operator should be followed by molecule',
					'previous' => (object) [
						'type' => 'unknown',
						'message' => $m,
						'context' => $c
					],
				]
			],

		];
	}
}
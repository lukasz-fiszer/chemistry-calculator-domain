<?php

namespace ChemCalc\Domain\Tests\Chemistry\Interpreter;

use ChemCalc\Domain\Chemistry\Interpreter\Interpreter;
use ChemCalc\Domain\Tests\Res\ChemistryTestsData;
use ChemCalc\Domain\Chemistry\Entity\ElementFactory;
use ChemCalc\Domain\Chemistry\DataLoader\ElementDataLoader;
use ChemCalc\Domain\Chemistry\Entity\MoleculeBuilder;


class InterpreterTest extends \PHPUnit\Framework\TestCase
{
	static $testsData;
	static $moleculeBuilder;
	static $h;
	static $o;
	static $hMolecule;
	static $oMolecule;
	static $plus;
	static $sideEquality;
	static $electronMass;

	public function setUp(){
		if(self::$testsData === null){
			self::$testsData = new ChemistryTestsData();
			self::$moleculeBuilder = new MoleculeBuilder(new ElementFactory(new ElementDataLoader()));
			self::$h = (object) ['type' => 'element', 'occurences' => 1, 'entry' => (object) [
				'type' => 'element_identifier', 'value' => 'H'
			]];
			self::$o = (object) ['type' => 'element', 'occurences' => 1, 'entry' => (object) [
				'type' => 'element_identifier', 'value' => 'O'
			]];
			self::$hMolecule = (object) ['type' => 'molecule', 'occurences' => 1, 'entries' => [self::$h]];
			self::$oMolecule = (object) ['type' => 'molecule', 'occurences' => 1, 'entries' => [self::$o]];
			self::$plus = (object) ['type' => 'operator', 'value' => '+', 'mode' => 'plus'];
			self::$sideEquality = (object) ['type' => 'operator', 'value' => '=', 'mode' => 'side_equality'];
			self::$electronMass = 0.000548579909;
		}
	}

	public static function tearDownAfterClass(){
		self::$testsData = null;
		self::$moleculeBuilder = null;
		self::$h = null;
		self::$o = null;
		self::$plus = null;
		self::$sideEquality = null;
		static $hMolecule = null;
		static $oMolecule = null;
		self::$electronMass = null;
	}

	public function testConstructorPropertiesInjection(){
		$parsed = self::$testsData->getInputParseTestsData()[0]['parsed'];
		$parsed = json_decode(json_encode($parsed));
		$interpreter = new Interpreter($parsed, self::$moleculeBuilder);
		$this->assertAttributeEquals($parsed, 'ast', $interpreter);
		$this->assertEquals($parsed, $interpreter->getAst());
		$this->assertAttributeEquals(self::$moleculeBuilder, 'moleculeBuilder', $interpreter);
	}

	/**
	 * @dataProvider interpretMethodDataProvider
	 */
	public function testInterpretMethod($parsed, $interpreted){
		$interpreter = new Interpreter($parsed, self::$moleculeBuilder);
		$this->assertEquals($interpreted, $interpreter->interpret());
	}

	public function testInterpretMethodNoNodes(){
		$interpreter = new Interpreter((object) ['type' => 'top_level', 'nodes' => []], self::$moleculeBuilder);
		$this->assertEquals((object) ['type' => 'unknown', 'message' => 'No nodes', 'context' => (object) ['code' => 1]], $interpreter->interpret());
	}

	public function testInterpretMethodNoExpectedMolecule(){
		$parsed = (object) ['type' => 'top_level', 'nodes' => [
			self::$hMolecule, self::$plus, self::$sideEquality, self::$hMolecule
		]];
		$interpreter = new Interpreter(json_decode(json_encode($parsed)), self::$moleculeBuilder);
		$this->assertEquals((object) ['type' => 'unknown', 'message' => 'Expected molecule node at 2 node instead of: '.json_encode($parsed->nodes[2]), 'context' => (object) ['at' => 2, 'expectedType' => 'molecule', 'actual' => $parsed->nodes[2], 'code' => 2]], $interpreter->interpret());
	}

	public function testInterpretMethodNoExpectedOperator(){
		$parsed = (object) ['type' => 'top_level', 'nodes' => [
			self::$hMolecule, self::$oMolecule, self::$sideEquality, self::$hMolecule, self::$plus, self::$oMolecule
		]];
		$interpreter = new Interpreter(json_decode(json_encode($parsed)), self::$moleculeBuilder);
		$this->assertEquals((object) ['type' => 'unknown', 'message' => 'Expected operator node at 1 node instead of: '.json_encode($parsed->nodes[1]), 'context' => (object) ['at' => 1, 'expectedType' => 'operator', 'actual' => $parsed->nodes[1], 'code' => 3]], $interpreter->interpret());
	}

	public function testInterpretMethodTooFewSides(){
		$parsed = (object) ['type' => 'top_level', 'nodes' => [
			self::$hMolecule, self::$plus, self::$oMolecule
		]];
		$interpreter = new Interpreter(json_decode(json_encode($parsed)), self::$moleculeBuilder);
		$this->assertEquals((object) ['type' => 'unknown', 'message' => 'Too few sides (1)', 'context' => (object) ['sidesCount' => 1, 'code' => 4]], $interpreter->interpret());
	}

	public function testInterpretMethodTooManySides(){
		$parsed = (object) ['type' => 'top_level', 'nodes' => [
			self::$hMolecule, self::$plus, self::$oMolecule, self::$sideEquality, self::$hMolecule, self::$sideEquality, self::$oMolecule, self::$sideEquality, self::$oMolecule
		]];
		$interpreter = new Interpreter(json_decode(json_encode($parsed)), self::$moleculeBuilder);
		$this->assertEquals((object) ['type' => 'unknown', 'message' => 'Too many sides (4)', 'context' => (object) ['sidesCount' => 4, 'code' => 5]], $interpreter->interpret());
	}

	public function testInterpretMethodNoMoleculeAfterOperator(){
		$parsed = (object) ['type' => 'top_level', 'nodes' => [
			self::$hMolecule, self::$sideEquality
		]];
		$parsed2 = (object) ['type' => 'top_level', 'nodes' => [
			self::$hMolecule, self::$sideEquality, self::$hMolecule, self::$plus
		]];
		$interpreter = new Interpreter(json_decode(json_encode($parsed)), self::$moleculeBuilder);
		$interpreter2 = new Interpreter(json_decode(json_encode($parsed2)), self::$moleculeBuilder);
		$response = (object) ['type' => 'unknown', 'message' => 'Operator should be followed by molecule', 'context' => (object) ['at' => 2, 'code' => 6]];
		$this->assertEquals($response, $interpreter->interpret());
		$response->context->at = 4;
		$this->assertEquals($response, $interpreter2->interpret());
	}

	public function interpretMethodDataProvider(){
		$this->setUp();
		return array_map(function($testEntry){
			//return [json_decode(json_encode($testEntry['parsed'])), json_encode(json_decode($testEntry['interpreted']))];
			return [json_decode(json_encode($testEntry['parsed'])), (object) $testEntry['interpreted']];
		}, self::$testsData->getInputParseTestsData());
	}
}
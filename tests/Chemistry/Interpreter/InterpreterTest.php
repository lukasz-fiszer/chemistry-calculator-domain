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

	public function setUp(){
		$this->elementFactory = new ElementFactory(new ElementDataLoader());
		if(self::$testsData === null){
			self::$testsData = new ChemistryTestsData();
			self::$moleculeBuilder = new MoleculeBuilder(new ElementFactory(new ElementDataLoader()));
		}
	}

	public static function tearDownAfterClass(){
		self::$testsData = null;
		self::$moleculeBuilder = null;
	}

	public function testConstructorPropertiesInjection(){
		$parsed = ['type' => 'top_level', 'nodes' => [
			['type' => 'molecule', 'occurences' => 1, 'entries' => [
				['type' => 'element', 'occurences' => 1, 'entry' => ['type' => 'element_identifier', 'value' => 'H']]
			]]
		]];
		//$parsed = $this->testsData[0]['parsed'];
		$parsed = json_decode(json_encode($parsed));
		$interpreter = new Interpreter($parsed, $this->elementFactory);
		$this->assertAttributeEquals($parsed, 'ast', $interpreter);
		$this->assertEquals($parsed, $interpreter->getAst());
	}

	/**
	 * @dataProvider interpretMethodDataProvider
	 */
	public function testInterpretMethod($parsed, $interpreted){
		$interpreter = new Interpreter($parsed, $this->elementFactory);
		$this->assertEquals($interpreted, $interpreter->interpret());
	}

	public function interpretMethodDataProvider(){
		$this->setUp();
		return array_map(function($testEntry){
			//return [json_decode(json_encode($testEntry['parsed'])), json_encode(json_decode($testEntry['interpreted']))];
			return [json_decode(json_encode($testEntry['parsed'])), (object) $testEntry['interpreted']];
		}, self::$testsData->getInputParseTestsData());
	}
}
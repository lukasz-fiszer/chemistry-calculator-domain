<?php

namespace ChemCalc\Domain\Tests\Chemistry\Interpreter;

use ChemCalc\Domain\Chemistry\Interpreter\Interpreter;
use ChemCalc\Domain\Tests\Res\ChemistryTestsData;


class InterpreterTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(){
		if(isset($this->initialized) && $this->initialized == true){
			return;
		}
		$this->initialized = true;
		$this->testsData = new ChemistryTestsData();
	}

	public function testConstructorPropertiesInjection(){
		$parsed = ['type' => 'top_level', 'nodes' => [
			['type' => 'molecule', 'occurences' => 1, 'entries' => [
				['type' => 'element', 'occurences' => 1, 'entry' => ['type' => 'element_identifier', 'value' => 'H']]
			]]
		]];
		//$parsed = $this->testsData[0]['parsed'];
		$parsed = json_decode(json_encode($parsed));
		$interpreter = new Interpreter($parsed);
		$this->assertAttributeEquals($parsed, 'ast', $interpreter);
		$this->assertEquals($parsed, $interpreter->getAst());
	}

	/**
	 * @dataProvider interpretMethodDataProvider
	 */
	public function testInterpretMethod($parsed, $interpreted){
		$interpreter = new Interpreter($parsed);
		$this->assertEquals($interpreted, $interpreter->interpret());
	}

	public function interpretMethodDataProvider(){
		$this->setUp();
		return [array_map(function($testEntry){
			//return [json_decode(json_encode($testEntry['parsed'])), json_encode(json_decode($testEntry['interpreted']))];
			return [json_decode(json_encode($testEntry['parsed'])), (object) $testEntry['interpreted']];
		}, $this->testsData->getInputParseTestsData())[0]];
	}
}
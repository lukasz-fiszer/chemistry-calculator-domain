<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use Exception;
use ChemCalc\Domain\Chemistry\Parser\TokenStream;
use ChemCalc\Domain\Tests\InvokesInaccessibleMethod;
use ChemCalc\Domain\Chemistry\Parser\Parser;
use ChemCalc\Domain\Chemistry\Parser\ParserException;
use ChemCalc\Domain\Tests\Res\ChemistryTestsData;
use ChemCalc\Domain\Chemistry\Parser\ParserExceptionBuilder;

class ParserTest extends \PHPUnit\Framework\TestCase
{
	use InvokesInaccessibleMethod;

	static $exceptionBuilderMock;

	public function setUp(){
		if(self::$exceptionBuilderMock === null){
			self::$exceptionBuilderMock = $this->createMock(ParserExceptionBuilder::class);
		}
	}

	public static function tearDownAfterClass(){
		self::$exceptionBuilderMock = null;
	}

	public function testConstructorPropertiesInjection(){
		$tokenStream = new TokenStream(new InputStream('test', self::$exceptionBuilderMock));
		$parser = new Parser($tokenStream);
		$this->assertAttributeEquals($tokenStream, 'tokenStream', $parser);
		$this->assertEquals($tokenStream, $parser->getTokenStream());
	}

	/**
	 * @dataProvider parseMethodDataProvider
	 */
	public function testParseMethod($input, $parsed){
		$parser = new Parser(new TokenStream(new InputStream($input, self::$exceptionBuilderMock)));
		$this->assertEquals(json_decode(json_encode($parsed)), $parser->parse());
	}

	/**
	 * @dataProvider parserExceptionMethodDataProvider
	 */
	public function testParserException($input, $message){
		$parser = new Parser(new TokenStream(new InputStream($input, new ParserExceptionBuilder())));
		$this->expectException(ParserException::class);
		$this->expectExceptionMessage($message);
		$parser->parse();
	}

	/**
	 * @expectedException ChemCalc\Domain\Chemistry\Parser\ParserException
	 */
	public function testExceptionThrowing(){
		$parser = new Parser(new TokenStream(new InputStream('test', new ParserExceptionBuilder())));
		$parser->parse();
	}

	public function parserExceptionMethodDataProvider(){
		return [
			['test', 'Character exception: \'t\' (line: 0, column: 0)'],
			['test2', 'Character exception: \'t\' (line: 0, column: 0)'],
			['Ab + test2', 'Character exception: \'t\' (line: 0, column: 5)'],
			['Ab12  +   test2', 'Character exception: \'t\' (line: 0, column: 10)'],
			['H2O++++', 'Unexpected token: {"type":"operator","value":"++++"} (line: 0, column: 7)'],
			['H2O++++Ab', 'Unexpected token: {"type":"operator","value":"++++"} (line: 0, column: 7)'],
			//['(Ab]2', 'Expected token: {"type":"punctuation","value":"]"} (line: 0, column: 4)'],
			['(Ab]2', 'Expected token of type: punctuation and value of: ) (line: 0, column: 4)'],
			['H2O)', 'Unexpected token: {"type":"punctuation","value":")","mode":"close","opposite":"("} (line: 0, column: 4)'],
			//['H2O(Ab=)5', 'Unexpected token: {"type":"operator","value"=")"} (line: 0, column: 7)'],
			//['H2O(Ab->)5', 'Unexpected token: {"type":"operator","value"->")"} (line: 0, column: 8)'],
			['H2O(Ab=)5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 7)'],
			['H2O(Ab->)5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 8)'],
			['H2O(Ab->]5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 8)'],
			['H2O(AbAb]5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 9)'],
		];
	}

	public function parseMethodDataProvider(){
		$testsData = new ChemistryTestsData();
		$testsData = $testsData->getInputParseTestsData();
		/*$testsData = array_map(function($testEntry){
			return [$testEntry['input'], $testEntry['parsed']];
		}, $testsData);*/
		return $testsData;
	}
}
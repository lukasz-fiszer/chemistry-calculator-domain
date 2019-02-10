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

	public function setUp(): void {
		if(self::$exceptionBuilderMock === null){
			self::$exceptionBuilderMock = $this->createMock(ParserExceptionBuilder::class);
		}
	}

	public static function tearDownAfterClass(): void {
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
	public function testParserException($input, $message, $code, $parserContext){
		$parser = new Parser(new TokenStream(new InputStream($input, new ParserExceptionBuilder())));
		$this->expectException(ParserException::class);
		$this->expectExceptionMessage($message);
		try{
			$parser->parse();
		}
		catch(Exception $e){
			$this->assertAttributeEquals($code, 'code', $e);
			$this->assertAttributeEquals((object) $parserContext, 'parserContext', $e);
			throw $e;
		}
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
			['test', 'Character exception: \'t\' (line: 0, column: 0)', 1, ['input' => 'test', 'position' => 0, 'line' => 0, 'column' => 0, 'character' => 't']],
			['test2', 'Character exception: \'t\' (line: 0, column: 0)', 1, ['input' => 'test2', 'position' => 0, 'line' => 0, 'column' => 0, 'character' => 't']],
			['Ab + test2', 'Character exception: \'t\' (line: 0, column: 5)', 1, ['input' => 'Ab + test2', 'position' => 5, 'line' => 0, 'column' => 5, 'character' => 't']],
			['Ab12  +   test2', 'Character exception: \'t\' (line: 0, column: 10)', 1, ['input' => 'Ab12  +   test2', 'position' => 10, 'line' => 0, 'column' => 10, 'character' => 't']],
			['H2O++++', 'Unexpected token: {"type":"operator","value":"++++"} (line: 0, column: 7)', 2, ['input' => 'H2O++++', 'position' => 7, 'line' => 0, 'column' => 7, 'token' => (object) ['type' => 'operator', 'value' => '++++']]],
			['H2O++++Ab', 'Unexpected token: {"type":"operator","value":"++++"} (line: 0, column: 7)', 2, ['input' => 'H2O++++Ab', 'position' => 7, 'line' => 0, 'column' => 7, 'token' => (object) ['type' => 'operator', 'value' => '++++']]],
			['(Ab]2', 'Expected token of type: punctuation and value of: ) (line: 0, column: 4)', 4, ['input' => '(Ab]2', 'position' => 4, 'line' => 0, 'column' => 4, 'actualToken' => (object) ['type' => 'punctuation', 'value' => ']', 'mode' => 'close', 'opposite' => '['], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H2O)', 'Unexpected token: {"type":"punctuation","value":")","mode":"close","opposite":"("} (line: 0, column: 4)', 2, ['input' => 'H2O)', 'position' => 4, 'line' => 0, 'column' => 4, 'token' => (object) ['type' => 'punctuation', 'value' => ')', 'mode' => 'close', 'opposite' => '(']]],
			//['H2O(Ab=)5', 'Unexpected token: {"type":"operator","value"=")"} (line: 0, column: 7)'],
			//['H2O(Ab->)5', 'Unexpected token: {"type":"operator","value"->")"} (line: 0, column: 8)'],
			['H2O(Ab=)5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 7)', 4, ['input' => 'H2O(Ab=)5', 'position' => 7, 'line' => 0, 'column' => 7, 'actualToken' => (object) ['type' => 'operator', 'value' => '='], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H2O(Ab->)5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 8)', 4, ['input' => 'H2O(Ab->)5', 'position' => 8, 'line' => 0, 'column' => 8, 'actualToken' => (object) ['type' => 'operator', 'value' => '->'], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H2O(Ab->]5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 8)', 4, ['input' => 'H2O(Ab->]5', 'position' => 8, 'line' => 0, 'column' => 8, 'actualToken' => (object) ['type' => 'operator', 'value' => '->'], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H2O(AbAb]5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 9)', 4, ['input' => 'H2O(AbAb]5', 'position' => 9, 'line' => 0, 'column' => 9, 'actualToken' => (object) ['type' => 'punctuation', 'value' => ']', 'mode' => 'close', 'opposite' => '['], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H(()', 'Expected token of type: punctuation and value of: ) (line: 0, column: 4)', 4, ['input' => 'H(()', 'position' => 4, 'line' => 0, 'column' => 4, 'actualToken' => null, 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H({}', 'Expected token of type: punctuation and value of: ) (line: 0, column: 4)', 4, ['input' => 'H({}', 'position' => 4, 'line' => 0, 'column' => 4, 'actualToken' => null, 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H({}}', 'Expected token of type: punctuation and value of: ) (line: 0, column: 5)', 4, ['input' => 'H({}}', 'position' => 5, 'line' => 0, 'column' => 5, 'actualToken' => (object) ['type' => 'punctuation', 'value' => '}', 'mode' => 'close', 'opposite' => '{'], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H(12)', 'Expected token of type: punctuation and value of: ) (line: 0, column: 4)', 4, ['input' => 'H(12)', 'position' => 4, 'line' => 0, 'column' => 4, 'actualToken' => (object) ['type' => 'number', 'value' => '12'], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['H(++)', 'Expected token of type: punctuation and value of: ) (line: 0, column: 4)', 4, ['input' => 'H(++)', 'position' => 4, 'line' => 0, 'column' => 4, 'actualToken' => (object) ['type' => 'operator', 'value' => '++'], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['{+-}', 'Expected token of type: punctuation and value of: } (line: 0, column: 3)', 4, ['input' => '{+-}', 'position' => 3, 'line' => 0, 'column' => 3, 'actualToken' => (object) ['type' => 'operator', 'value' => '+-'], 'expectedType' => 'punctuation', 'expectedValue' => '}']],
			['{-+}', 'Expected token of type: punctuation and value of: } (line: 0, column: 3)', 4, ['input' => '{-+}', 'position' => 3, 'line' => 0, 'column' => 3, 'actualToken' => (object) ['type' => 'operator', 'value' => '-+'], 'expectedType' => 'punctuation', 'expectedValue' => '}']],
			['H(+-)', 'Expected token of type: punctuation and value of: ) (line: 0, column: 4)', 4, ['input' => 'H(+-)', 'position' => 4, 'line' => 0, 'column' => 4, 'actualToken' => (object) ['type' => 'operator', 'value' => '+-'], 'expectedType' => 'punctuation', 'expectedValue' => ')']],
			['+-', 'Unexpected token: {"type":"operator","value":"+-"} (line: 0, column: 2)', 2, ['input' => '+-', 'position' => 2, 'line' => 0, 'column' => 2, 'token' => (object) ['type' => 'operator', 'value' => '+-']]],
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
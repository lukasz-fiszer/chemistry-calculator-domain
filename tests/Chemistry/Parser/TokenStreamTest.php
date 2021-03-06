<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use Exception;
use ChemCalc\Domain\Chemistry\Parser\TokenStream;
use ChemCalc\Domain\Tests\InvokesInaccessibleMethod;
use ChemCalc\Domain\Chemistry\Parser\ParserException;
use ChemCalc\Domain\Chemistry\Parser\ParserExceptionBuilder;

class TokenStreamTest extends \PHPUnit\Framework\TestCase
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
		$this->assertAttributeEquals(new InputStream('test', self::$exceptionBuilderMock), 'inputStream', $tokenStream);
		$this->assertAttributeEquals(null, 'current', $tokenStream);
		$this->assertAttributeEquals('()[]{}', 'punctuationCharacters', $tokenStream);
	}

	public function testStreamMethods(){
		//$tokenStream = new TokenStream(new InputStream('H2+4O + He2(H2Oa5)10'));
		$tokenStream = new TokenStream(new InputStream('H2+4O -> He2(H2Oa5)10', self::$exceptionBuilderMock));

		$token = (object) ['type' => 'element_identifier', 'value' => 'H'];
		$this->assertEquals($token, $tokenStream->peek());
		$this->assertEquals($token, $tokenStream->next());
		$token->type = 'number';
		$token->value = '2';
		$this->assertEquals($token, $tokenStream->next());
		$this->assertFalse($tokenStream->eof());

		$this->assertNextToken($tokenStream, 'operator', '+');
		$this->assertNextToken($tokenStream, 'number', 4);
		$this->assertNextToken($tokenStream, 'element_identifier', 'O');
		$this->assertNextToken($tokenStream, 'operator', '->');
		$this->assertNextToken($tokenStream, 'element_identifier', 'He');
		$this->assertNextToken($tokenStream, 'number', 2);
		$this->assertNextToken($tokenStream, 'punctuation', '(', ['mode' => 'open', 'opposite' => ')']);
		$this->assertNextToken($tokenStream, 'element_identifier', 'H');
		$this->assertNextToken($tokenStream, 'number', 2);
		$this->assertNextToken($tokenStream, 'element_identifier', 'Oa');
		$this->assertNextToken($tokenStream, 'number', 5);
		$this->assertNextToken($tokenStream, 'punctuation', ')', ['mode' => 'close', 'opposite' => '(']);
		$this->assertNextToken($tokenStream, 'number', 10);
		$this->assertTrue($tokenStream->eof());
		$this->assertEquals(null, $tokenStream->next());
		$this->assertEquals(null, $tokenStream->peek());
		$this->assertTrue($tokenStream->eof());
		$this->assertEquals(null, $tokenStream->next());
		$this->assertEquals(null, $tokenStream->peek());
	}

	protected function assertNextToken(TokenStream $tokenStream, $type, $value, $merge = []){
		$this->assertEquals((object) array_merge(['type' => $type, 'value' => $value], $merge), $tokenStream->next());
	}

	public function testPredicates(){
		$tokenStream = new TokenStream(new InputStream('test', self::$exceptionBuilderMock));

		$this->assertTrue($this->invokeMethod($tokenStream, 'is_digit', ['12345']));
		$this->assertTrue($this->invokeMethod($tokenStream, 'is_digit', ['1234567890789056786']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_digit', ['1a']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_digit', ['a']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_digit', ['']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_digit', [' ']));

		$this->assertTrue($this->invokeMethod($tokenStream, 'is_whitespace', [' ']));
		$this->assertTrue($this->invokeMethod($tokenStream, 'is_whitespace', [" \t\n "]));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_whitespace', ['1a']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_whitespace', ['a']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_whitespace', ['']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_whitespace', ['abcd1234']));

		$this->assertTrue($this->invokeMethod($tokenStream, 'is_punctuation', ['(']));
		$this->assertTrue($this->invokeMethod($tokenStream, 'is_punctuation', [')']));
		$this->assertTrue($this->invokeMethod($tokenStream, 'is_punctuation', ['[']));
		$this->assertTrue($this->invokeMethod($tokenStream, 'is_punctuation', [']']));
		$this->assertTrue($this->invokeMethod($tokenStream, 'is_punctuation', ['{']));
		$this->assertTrue($this->invokeMethod($tokenStream, 'is_punctuation', ['}']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_punctuation', ['1a']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_punctuation', [' a']));
		$this->assertFalse($this->invokeMethod($tokenStream, 'is_punctuation', ['abcd1234']));
	}

	/**
	 * @dataProvider throwExceptionDataProvider
	 */
	public function testThrowException($message, $codeKey, $mergeContext){
		$inputStreamMock = $this->createMock(InputStream::class);
		$inputStreamMock->expects($this->once())->method('throwException')->with($message, $codeKey, $mergeContext);
		$tokenStream = new TokenStream($inputStreamMock);
		$tokenStream->throwException($message, $codeKey, $mergeContext);
	}

	public function throwExceptionDataProvider(){
		return [
			['message', null, null],
			['message', 'code key', null],
			['test message', 'tokenizer_unrecognized_character', null],
			['test message', 'tokenizer_unrecognized_character', (object) ['character' => 'b']],
		];
	}

	public function testExceptionThrown(){
		$tokenStream = new TokenStream(new InputStream('test', new ParserExceptionBuilder()));
		$catched = false;
		try{
			$tokenStream->throwException('message');
		}
		catch(Exception $e){
			$catched = true;
			$this->assertEquals('ChemCalc\Domain\Chemistry\Parser\ParserException', get_class($e));
			$this->assertAttributeEquals('message (line: 0, column: 0)', 'message', $e);
			$c = (object) ['input' => 'test', 'position' => 0, 'line' => 0, 'column' => 0];
			$this->assertAttributeEquals($c, 'parserContext', $e);
		}
		if(!$catched){
			$this->fail('Token stream had to throw exception');
		}
	}

	/**
	 * @expectedException ChemCalc\Domain\Chemistry\Parser\ParserException
	 */
	public function testExceptionThrowing(){
		$tokenStream = new TokenStream(new InputStream('test', new ParserExceptionBuilder()));
		$tokenStream->throwException();
	}

	/**
	 * @dataProvider exceptionThrowingMessageDataProvider
	 */
	public function testExceptionThrowingMessage($input, $message, $code, $context){
		$tokenStream = new TokenStream(new InputStream($input, new ParserExceptionBuilder()));
		$this->expectException(ParserException::class);
		$this->expectExceptionMessage($message);
		try{
			while(!$tokenStream->eof()){
				$tokenStream->next();
			}
		}
		catch(Exception $e){
			$this->assertAttributeEquals($code, 'code', $e);
			$this->assertAttributeEquals((object) $context, 'parserContext', $e);
			throw $e;
		}
	}

	public function exceptionThrowingMessageDataProvider(){
		return [
			['test', 'Character exception: \'t\' (line: 0, column: 0)', 1, ['input' => 'test', 'position' => 0, 'line' => 0, 'column' => 0, 'character' => 't']],
			['test2', 'Character exception: \'t\' (line: 0, column: 0)', 1, ['input' => 'test2', 'position' => 0, 'line' => 0, 'column' => 0, 'character' => 't']],
			['A + b', 'Character exception: \'b\' (line: 0, column: 4)', 1, ['input' => 'A + b', 'position' => 4, 'line' => 0, 'column' => 4, 'character' => 'b']],
			['A + Ab2b', 'Character exception: \'b\' (line: 0, column: 7)', 1, ['input' => 'A + Ab2b', 'position' => 7, 'line' => 0, 'column' => 7, 'character' => 'b']],
			['A + Abe2b20', 'Character exception: \'b\' (line: 0, column: 8)', 1, ['input' => 'A + Abe2b20', 'position' => 8, 'line' => 0, 'column' => 8, 'character' => 'b']],
		];
	}
}
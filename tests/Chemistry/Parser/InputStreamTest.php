<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use Exception;
use ChemCalc\Domain\Chemistry\Parser\ParserExceptionBuilder;

class InputStreamTest extends \PHPUnit\Framework\TestCase
{
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
		$inputStream = new InputStream('test', self::$exceptionBuilderMock);
		$this->assertAttributeEquals('test', 'input', $inputStream);
		$this->assertAttributeEquals(0, 'position', $inputStream);
		$this->assertAttributeEquals(0, 'line', $inputStream);
		$this->assertAttributeEquals(0, 'column', $inputStream);
		$this->assertEquals($this->buildInputContext('test', 0, 0, 0), $inputStream->getContext());
		$this->assertAttributeEquals(self::$exceptionBuilderMock, 'parserExceptionBuilder', $inputStream);
	}

	public function testStreamMethods(){
		$inputStream = new InputStream("abcd\nabcd\ntest\n\t ab", self::$exceptionBuilderMock);
		$this->streamValuesTest("abcd\nabcd\ntest\n\t ab", 0, 0, 0, false, $inputStream);

		$this->assertEquals('a', $inputStream->peek());
		$this->assertEquals('a', $inputStream->next());
		$this->streamValuesTest("abcd\nabcd\ntest\n\t ab", 1, 0, 1, false, $inputStream);

		$this->assertEquals('bcd', $inputStream->next().$inputStream->next().$inputStream->next());
		$this->assertEquals("\n", $inputStream->peek());
		$this->assertEquals("\n", $inputStream->next());
		$this->streamValuesTest("abcd\nabcd\ntest\n\t ab", 5, 1, 0, false, $inputStream);

		for($i = 0; $i < 10; $i++){
			$inputStream->next();
		}
		$this->assertEquals("\t", $inputStream->next());
		$this->assertEquals(' ', $inputStream->next());
		$this->assertEquals('a', $inputStream->next());
		$this->assertEquals('b', $inputStream->next());
		
		$this->assertEquals(null, $inputStream->peek());
		$this->assertEquals(null, $inputStream->next());
		$this->assertEquals(null, $inputStream->next());
		$this->assertEquals(true, $inputStream->eof());
		$this->streamValuesTest("abcd\nabcd\ntest\n\t ab", 19, 3, 4, true, $inputStream);
	}

	/**
	 * @dataProvider exceptionThrownDataProvider
	 */
	public function testExceptionThrown($input, $message, $codeKey, $code){
		$inputStream = new InputStream($input, new ParserExceptionBuilder());
		$catched = false;
		try{
			$inputStream->throwException($message, $codeKey);
		}
		catch(Exception $e){
			$catched = true;
			$this->assertAttributeEquals($message.' (line: 0, column: 0)', 'message', $e);
			$this->assertAttributeEquals($this->buildInputContext($input, 0, 0, 0), 'parserContext', $e);
			$this->assertAttributeEquals($code, 'code', $e);
			$this->assertAttributeEquals(null, 'previous', $e);
		}
		if(!$catched){
			$this->fail('Input stream had to throw exception');
		}
	}

	public function exceptionThrownDataProvider(){
		return [
			['test', 'message', null, null],
			['test', 'message', 'tokenizer_unrecognized_character', 1],
		];
	}

	protected function streamValuesTest($input, $pos, $line, $col, $eof, $stream){
		$this->assertAttributeEquals($input, 'input', $stream);
		$this->assertAttributeEquals($pos, 'position', $stream);
		$this->assertAttributeEquals($line, 'line', $stream);
		$this->assertAttributeEquals($col, 'column', $stream);
		$this->assertEquals($eof, $stream->eof());
		$this->assertEquals($this->buildInputContext($input, $pos, $line, $col), $stream->getContext());
	}

	protected function buildInputContext($input, $pos, $line, $col){
		return (object) ['input' => $input, 'position' => $pos, 'line' => $line, 'column' => $col];
	}

	/**
	 * @expectedException ChemCalc\Domain\Chemistry\Parser\ParserException
	 */
	public function testExceptionThrowing(){
		$inputStream = new InputStream('test', new ParserExceptionBuilder());
		$inputStream->throwException();
	}
}
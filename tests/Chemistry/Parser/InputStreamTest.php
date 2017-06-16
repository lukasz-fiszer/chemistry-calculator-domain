<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use Exception;

class InputStreamTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$inputStream = new InputStream('test');
		$this->assertAttributeEquals('test', 'input', $inputStream);
		$this->assertAttributeEquals(0, 'position', $inputStream);
		$this->assertAttributeEquals(0, 'line', $inputStream);
		$this->assertAttributeEquals(0, 'column', $inputStream);
	}

	public function testStreamMethods(){
		$inputStream = new InputStream("abcd\nabcd\ntest\n\t ab");
		$this->assertAttributeEquals("abcd\nabcd\ntest\n\t ab", 'input', $inputStream);

		$this->assertEquals('a', $inputStream->peek());
		$this->assertEquals('a', $inputStream->next());
		$this->assertAttributeEquals("abcd\nabcd\ntest\n\t ab", 'input', $inputStream);
		$this->assertAttributeEquals(1, 'position', $inputStream);
		$this->assertAttributeEquals(0, 'line', $inputStream);
		$this->assertAttributeEquals(1, 'column', $inputStream);
		$this->assertEquals(false, $inputStream->eof());

		$this->assertEquals('bcd', $inputStream->next().$inputStream->next().$inputStream->next());
		$this->assertEquals("\n", $inputStream->peek());
		$this->assertEquals("\n", $inputStream->next());
		$this->assertAttributeEquals(5, 'position', $inputStream);
		$this->assertAttributeEquals(1, 'line', $inputStream);
		$this->assertAttributeEquals(0, 'column', $inputStream);

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
		$this->assertAttributeEquals(19, 'position', $inputStream);
		$this->assertAttributeEquals(3, 'line', $inputStream);
		$this->assertAttributeEquals(4, 'column', $inputStream);
	}

	public function testExceptionThrown(){
		$inputStream = new InputStream('test');
		$catched = false;
		try{
			$inputStream->throwException('message');
		}
		catch(Exception $e){
			$catched = true;
			$this->assertAttributeEquals('message (line: 0, column: 0)', 'message', $e);
			$this->assertAttributeEquals('test', 'parserInput', $e);
			$this->assertAttributeEquals(0, 'parserPosition', $e);
			$this->assertAttributeEquals(0, 'parserLine', $e);
			$this->assertAttributeEquals(0, 'parserColumn', $e);
		}
		if(!$catched){
			$this->fail('Input stream had to throw exception');
		}
	}

	/**
	 * @expectedException ChemCalc\Domain\Chemistry\Parser\ParserException
	 */
	public function testExceptionThrowing(){
		$inputStream = new InputStream('test');
		$inputStream->throwException();
	}
}
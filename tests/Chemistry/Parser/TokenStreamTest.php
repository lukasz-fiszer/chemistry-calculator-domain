<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use Exception;
use ChemCalc\Domain\Chemistry\Parser\TokenStream;

class TokenStreamTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$tokenStream = new TokenStream(new InputStream('test'));
		$this->assertAttributeEquals(new InputStream('test'), 'inputStream', $tokenStream);
		$this->assertAttributeEquals(null, 'current', $tokenStream);
	}

	public function testStreamMethods(){
		/*$inputStream = new InputStream("abcd\nabcd\ntest\n\t ab");
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
		$this->assertAttributeEquals(4, 'column', $inputStream);*/
		//$tokenStream = new TokenStream(new InputStream('H2+4O + He2(H2Oa5)10'));
		$tokenStream = new TokenStream(new InputStream('H2+4O -> He2(H2Oa5)10'));
		echo "\n";
		while($tokenStream->peek()){
			//echo "\n";
			//var_dump($tokenStream->next());
			print_r($tokenStream->next());
			echo "\n";
		}
	}

	public function testExceptionThrown(){
		$tokenStream = new TokenStream(new InputStream('test'));
		$catched = false;
		try{
			$tokenStream->throwException('message');
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
			$this->fail('Token stream had to throw exception');
		}
	}

	/**
	 * @expectedException ChemCalc\Domain\Chemistry\Parser\ParserException
	 */
	public function testExceptionThrowing(){
		$tokenStream = new TokenStream(new InputStream('test'));
		$tokenStream->throwException();
	}
}
<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use Exception;
use ChemCalc\Domain\Chemistry\Parser\TokenStream;
use ChemCalc\Domain\Tests\InvokesInaccessibleMethod;

class TokenStreamTest extends \PHPUnit\Framework\TestCase
{
	use InvokesInaccessibleMethod;

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
		/*$tokenStream = new TokenStream(new InputStream('H2+4O -> He2(H2Oa5)10'));
		echo "\n";
		while($tokenStream->peek()){
			print_r($tokenStream->next());
			echo "\n";
		}*/
		$tokenStream = new TokenStream(new InputStream('H2+4O -> He2(H2Oa5)10'));

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
		$this->assertNextToken($tokenStream, 'punctuation', '(');
		$this->assertNextToken($tokenStream, 'element_identifier', 'H');
		$this->assertNextToken($tokenStream, 'number', 2);
		$this->assertNextToken($tokenStream, 'element_identifier', 'Oa');
		$this->assertNextToken($tokenStream, 'number', 5);
		$this->assertNextToken($tokenStream, 'punctuation', ')');
		$this->assertNextToken($tokenStream, 'number', 10);
		$this->assertTrue($tokenStream->eof());
		$this->assertEquals(null, $tokenStream->next());
		$this->assertEquals(null, $tokenStream->peek());
		$this->assertTrue($tokenStream->eof());
		$this->assertEquals(null, $tokenStream->next());
		$this->assertEquals(null, $tokenStream->peek());
	}

	protected function assertNextToken(TokenStream $tokenStream, $type, $value){
		$this->assertEquals((object) ['type' => $type, 'value' => $value], $tokenStream->next());
	}

	public function testPredicates(){
		$tokenStream = new TokenStream(new InputStream('test'));

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
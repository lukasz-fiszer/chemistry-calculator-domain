<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\ParserException;

class ParserExceptionTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$exception = new ParserException();
		$this->assertAttributeEquals(null, 'message', $exception);
		$this->assertAttributeEquals('', 'parserInput', $exception);
		$this->assertAttributeSame(null, 'parserInput', $exception);
		$this->assertAttributeEquals(0, 'parserPosition', $exception);
		$this->assertAttributeSame(null, 'parserPosition', $exception);
		$this->assertAttributeEquals(0, 'parserLine', $exception);
		$this->assertAttributeSame(null, 'parserLine', $exception);
		$this->assertAttributeEquals(0, 'parserColumn', $exception);
		$this->assertAttributeSame(null, 'parserColumn', $exception);
		$this->assertAttributeEquals(0, 'code', $exception);
		$this->assertAttributeEquals(null, 'previous', $exception);
		$this->assertAttributeEquals((object) ['input' => null, 'position' => null, 'line' => null, 'column' => null], 'parserContext', $exception);

		$exception2 = new ParserException('Parser Exception', 'test input', 5, 1, 2);
		$this->assertAttributeEquals('Parser Exception (line: 1, column: 2)', 'message', $exception2);
		$this->assertAttributeEquals('test input', 'parserInput', $exception2);
		$this->assertAttributeEquals(5, 'parserPosition', $exception2);
		$this->assertAttributeEquals(1, 'parserLine', $exception2);
		$this->assertAttributeEquals(2, 'parserColumn', $exception2);
		$this->assertAttributeEquals(0, 'code', $exception2);
		$this->assertAttributeEquals(null, 'previous', $exception2);
		$this->assertAttributeEquals((object) ['input' => 'test input', 'position' => 5, 'line' => 1, 'column' => 2], 'parserContext', $exception2);
	}
}
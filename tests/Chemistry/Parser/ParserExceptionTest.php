<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\ParserException;
use Exception;

class ParserExceptionTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$exception = new ParserException();
		$this->assertAttributeEquals(null, 'message', $exception);
		$this->assertAttributeEquals(null, 'parserContext', $exception);
		$this->assertAttributeEquals(0, 'code', $exception);
		$this->assertAttributeEquals(null, 'previous', $exception);

		$prev = new Exception('previous');
		$parserContext = (object) ['input' => 'test', 'position' => 40, 'line' => 4, 'column' => 20];
		$exception2 = new ParserException('Parser Exception', $parserContext, 2, $prev);
		$this->assertAttributeEquals('Parser Exception', 'message', $exception2);
		$this->assertAttributeEquals($parserContext, 'parserContext', $exception2);
		$this->assertAttributeEquals(2, 'code', $exception2);
		$this->assertAttributeEquals($prev, 'previous', $exception2);
	}
}
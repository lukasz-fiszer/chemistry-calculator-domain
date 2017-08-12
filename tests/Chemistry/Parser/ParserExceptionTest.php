<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\ParserException;
use Exception;
use stdClass;

class ParserExceptionTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$exception = new ParserException();
		$this->assertAttributeEquals(null, 'message', $exception);
		$this->assertAttributeEquals(new stdClass(), 'parserContext', $exception);
		$this->assertAttributeEquals(0, 'code', $exception);
		$this->assertAttributeEquals(null, 'previous', $exception);

		$prev = new Exception('previous');
		$parserContext = (object) ['input' => 'test', 'position' => 40, 'line' => 4, 'column' => 20];
		$exception2 = new ParserException('Parser Exception', $parserContext, 2, $prev);
		$this->assertAttributeEquals('Parser Exception', 'message', $exception2);
		$this->assertAttributeEquals($parserContext, 'parserContext', $exception2);
		$this->assertAttributeEquals(2, 'code', $exception2);
		$this->assertAttributeEquals($prev, 'previous', $exception2);

		$parserContext2 = (object) ['input' => 'test', 'position' => 40, 'column' => 20, 'character' => 'b'];
		$exception3 = new ParserException('message', $parserContext2);
		$this->assertAttributeEquals('message', 'message', $exception3);
		$this->assertAttributeEquals($parserContext2, 'parserContext', $exception3);
		$this->assertAttributeEquals(0, 'code', $exception3);
		$this->assertAttributeEquals(null, 'previous', $exception3);
	}
}
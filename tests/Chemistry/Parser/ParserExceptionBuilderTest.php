<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\ParserException;
use ChemCalc\Domain\Chemistry\Parser\ParserExceptionBuilder;
use Exception;

class ParserExceptionBuilderTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$builder = new ParserExceptionBuilder();
		$this->assertAttributeEquals(null, 'message', $builder);
		$this->assertAttributeEquals(0, 'code', $builder);
		$this->assertAttributeEquals(null, 'previous', $builder);

		$prev = new Exception();
		$builder = new ParserExceptionBuilder('exception message', 10, $prev);
		$this->assertAttributeEquals('exception message', 'message', $builder);
		$this->assertAttributeEquals(10, 'code', $builder);
		$this->assertAttributeEquals($prev, 'previous', $builder);
	}

	public function testBuildMethod(){
		$builder = new ParserExceptionBuilder();
		$exBuild = $builder->build();
		$ex = new ParserException();
		$this->assertEquals($ex, $exBuild);

		$prev = new Exception('previous msg');
		$builder2 = new ParserExceptionBuilder('message', 0, $prev);
		$builder2 = $builder2->withMessage('new msg')->withCode(2)->withParserInput('parser input')->withParserLine(1)->withParserColumn(2);
		$exBuild2 = $builder2->build();
		$ex2 = new ParserException('new msg (line: 1, column: 2)', 'parser input', null, 1, 2, 2, $prev);
		$this->assertEquals($ex2, $exBuild2);

		$builder2 = new ParserExceptionBuilder();
		$builder2 = $builder2->withMessage('new msg')->withParserLine(1);
		$exBuild2 = $builder2->build();
		$ex2 = new ParserException('new msg (line: 1)', null, null, 1);
		$this->assertEquals($ex2, $exBuild2);

		$builder2 = new ParserExceptionBuilder();
		$builder2 = $builder2->withMessage('new msg')->withParserColumn(2);
		$exBuild2 = $builder2->build();
		$ex2 = new ParserException('new msg (column: 2)', null, null, null, 2);
		$this->assertEquals($ex2, $exBuild2);
	}

	public function testWithMessage(){
		$builder = new ParserExceptionBuilder();
		$builder = $builder->withMessage('msg');
		$this->assertAttributeEquals('msg', 'message', $builder);
		$builder2 = $builder->withMessage('msg 2');
		$this->assertAttributeEquals('msg 2', 'message', $builder2);
	}

	public function testWithCode(){
		$builder = new ParserExceptionBuilder();
		$builder = $builder->withCode(2);
		$this->assertAttributeEquals(2, 'code', $builder);
		$builder2 = $builder->withCode(4);
		$this->assertAttributeEquals(4, 'code', $builder2);
	}

	public function testWithCodeByKey(){
		$builder = new ParserExceptionBuilder();
		$builder = $builder->withCodeByKey('tokenizer_unrecognized_character');
		$this->assertAttributeEquals(1, 'code', $builder);
		$builder2 = $builder->withCodeByKey('parser_unexpected_token');
		$this->assertAttributeEquals(2, 'code', $builder2);

		$this->assertAttributeEquals(['tokenizer_unrecognized_character' => 1, 'parser_unexpected_token' => 2, 'parser_expected_other_token' => 4], 'codes', $builder);
	}

	/**
	 * @expectedException Exception
	 * @expectedExceptionMessage Unknown code key: 'unknown code key'
	 */
	public function testWithCodeByKeyException(){
		$builder = new ParserExceptionBuilder();
		$builder = $builder->withCodeByKey('unknown code key');
	}

	public function testWithPreviousException(){
		$builder = new ParserExceptionBuilder();
		$ex1 = new Exception();
		$ex2 = new Exception('previous');
		$builder = $builder->withPreviousException($ex1);
		$this->assertAttributeEquals($ex1, 'previous', $builder);
		$builder2 = $builder->withPreviousException($ex2);
		$this->assertAttributeEquals($ex2, 'previous', $builder2);
	}

	public function testWithParserInput(){
		$builder = new ParserExceptionBuilder();
		$builder = $builder->withParserInput('parser input 1');
		$this->assertAttributeEquals('parser input 1', 'parserInput', $builder);
		$builder2 = $builder->withParserInput('parser input 2');
		$this->assertAttributeEquals('parser input 2', 'parserInput', $builder2);
	}

	public function testWithParserPosition(){
		$builder = new ParserExceptionBuilder();
		$builder = $builder->withParserPosition(10);
		$this->assertAttributeEquals(10, 'parserPosition', $builder);
		$builder2 = $builder->withParserPosition(20);
		$this->assertAttributeEquals(20, 'parserPosition', $builder2);
	}

	public function testWithParserLine(){
		$builder = new ParserExceptionBuilder();
		$builder = $builder->withParserLine(100);
		$this->assertAttributeEquals(100, 'parserLine', $builder);
		$builder2 = $builder->withParserLine(200);
		$this->assertAttributeEquals(200, 'parserLine', $builder2);
	}

	public function testWithParserColumn(){
		$builder = new ParserExceptionBuilder();
		$builder = $builder->withParserColumn(5);
		$this->assertAttributeEquals(5, 'parserColumn', $builder);
		$builder2 = $builder->withParserColumn(40);
		$this->assertAttributeEquals(40, 'parserColumn', $builder2);
	}

	public function testWithParserContext(){
		$builder = new ParserExceptionBuilder();
		$c1 = (object) ['input' => null, 'position' => null, 'line' => null, 'column' => null];
		$c2 = (object) ['input' => 'test input', 'position' => 5, 'line' => 1, 'column' => 4];
		$builder = $builder->withParserContext($c1);
		$this->assertAttributeEquals($c1, 'parserContext', $builder);
		$builder2 = $builder->withParserContext($c2);
		$this->assertAttributeEquals($c2, 'parserContext', $builder2);
	}
}
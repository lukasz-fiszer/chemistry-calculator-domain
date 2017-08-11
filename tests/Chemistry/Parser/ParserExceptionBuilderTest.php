<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\ParserException;
use ChemCalc\Domain\Chemistry\Parser\ParserExceptionBuilder;
use Exception;
use stdClass;

class ParserExceptionBuilderTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$builder = new ParserExceptionBuilder();
		$this->assertAttributeEquals(null, 'message', $builder);
		$this->assertAttributeEquals(0, 'code', $builder);
		$this->assertAttributeEquals(null, 'previous', $builder);
		$this->assertAttributeEquals(new stdClass(), 'parserContext', $builder);

		$prev = new Exception();
		$builder = new ParserExceptionBuilder('exception message', 10, $prev);
		$this->assertAttributeEquals('exception message', 'message', $builder);
		$this->assertAttributeEquals(10, 'code', $builder);
		$this->assertAttributeEquals($prev, 'previous', $builder);
		$this->assertAttributeEquals(new stdClass(), 'parserContext', $builder);
	}

	public function testBuildMethod(){
		$builder = new ParserExceptionBuilder();
		$exBuild = $builder->build();
		$ex = new ParserException();
		$this->assertEquals($ex, $exBuild);

		$prev = new Exception('previous msg');
		$builder = new ParserExceptionBuilder('message', 0, $prev);
		$builder = $builder->withMessage('new msg')->withCode(2)->withParserInput('parser input')->withParserLine(1)->withParserColumn(2);
		$exBuild = $builder->build();
		//$c = (object) ['input' => 'parser input', 'position' => null, 'line' => 1, 'column' => 2];
		$c = (object) ['input' => 'parser input', 'line' => 1, 'column' => 2];
		$ex = new ParserException('new msg (line: 1, column: 2)', $c, 2, $prev);
		$this->assertEquals($ex, $exBuild);

		$builder = new ParserExceptionBuilder();
		$builder = $builder->withMessage('new msg')->withParserLine(1);
		$exBuild = $builder->build();
		//$c = (object) ['input' => null, 'position' => null, 'line' => 1, 'column' => null];
		$c = (object) ['line' => 1];
		$ex = new ParserException('new msg (line: 1)', $c);
		$this->assertEquals($ex, $exBuild);

		$builder = new ParserExceptionBuilder();
		$builder = $builder->withMessage('new msg')->withParserColumn(2);
		$exBuild = $builder->build();
		//$c = (object) ['input' => null, 'position' => null, 'line' => null, 'column' => 2];
		$c = (object) ['column' => 2];
		$ex = new ParserException('new msg (column: 2)', $c);
		$this->assertEquals($ex, $exBuild);
	}

	public function testWithMessage(){
		$this->withMethodTest('withMessage', 'message', 'msg', 'msg 2');
	}

	public function testWithCode(){
		$this->withMethodTest('withCode', 'code', 2, 4);
	}

	public function testWithCodeByKey(){
		$tmp1 = $builder1 = new ParserExceptionBuilder();
		$tmp2 = $builder2 = $builder1->withCodeByKey('tokenizer_unrecognized_character');
		$builder3 = $builder2->withCodeByKey('parser_unexpected_token');

		$this->assertEquals($tmp1, $builder1);
		$this->assertEquals($tmp2, $builder2);

		$this->assertAttributeEquals(1, 'code', $builder2);
		$this->assertAttributeEquals(2, 'code', $builder3);

		$this->assertAttributeEquals(['tokenizer_unrecognized_character' => 1, 'parser_unexpected_token' => 2, 'parser_expected_other_token' => 4], 'codes', $builder1);
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
		$ex1 = new Exception();
		$ex2 = new Exception('previous');
		$this->withMethodTest('withPreviousException', 'previous', $ex1, $ex2);
	}

	public function testWithParserContextHelpers(){
		$tmp1 = $builder1 = new ParserExceptionBuilder();
		$tmp2 = $builder2 = $builder1->withParserInput('')->withParserPosition(0)->withParserLine(0)->withParserColumn(0);
		$builder3 = $builder2->withParserInput('parser input')->withParserPosition(10)->withParserLine(2)->withParserColumn(4);

		$this->assertEquals($tmp1, $builder1);
		$this->assertEquals($tmp2, $builder2);

		$this->assertAttributeEquals((object) ['input' => '', 'position' => 0, 'line' => 0, 'column' => 0], 'parserContext', $builder2);
		$this->assertAttributeEquals((object) ['input' => 'parser input', 'position' => 10, 'line' => 2, 'column' => 4], 'parserContext', $builder3);
	}

	public function testWithParserContext(){
		$c1 = (object) ['input' => null, 'position' => null, 'line' => null, 'column' => null];
		$c2 = (object) ['input' => 'test input', 'position' => 5, 'line' => 1, 'column' => 4];
		$this->withMethodTest('withParserContext', 'parserContext', $c1, $c2);
	}

	protected function withMethodTest($method, $attribute, $value1, $value2){
		$tmp1 = $builder1 = new ParserExceptionBuilder();
		$tmp2 = $builder2 = $builder1->{$method}($value1);
		$builder3 = $builder2->{$method}($value2);

		$this->assertEquals($tmp1, $builder1);
		$this->assertEquals($tmp2, $builder2);

		$this->assertAttributeEquals($value1, $attribute, $builder2);
		$this->assertAttributeEquals($value2, $attribute, $builder3);
	}
}
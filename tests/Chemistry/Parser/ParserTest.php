<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use Exception;
use ChemCalc\Domain\Chemistry\Parser\TokenStream;
use ChemCalc\Domain\Tests\InvokesInaccessibleMethod;
use ChemCalc\Domain\Chemistry\Parser\Parser;

class ParserTest extends \PHPUnit\Framework\TestCase
{
	use InvokesInaccessibleMethod;

	public function testConstructorPropertiesInjection(){
		$tokenStream = new TokenStream(new InputStream('test'));
		$parser = new Parser($tokenStream);
		$this->assertAttributeEquals($tokenStream, 'tokenStream', $parser);
		$this->assertEquals($tokenStream, $parser->getTokenStream());
	}

	public function testAbcd(){
		//$tokenStream = new TokenStream(new InputStream('test'));
		$tokenStream = new TokenStream(new InputStream('H2O'));
		//$tokenStream = new TokenStream(new InputStream('H2O + Ab'));
		$parser = new Parser($tokenStream);
		//var_dump($parser->parse());
		print_r($parser->parse());
	}
}
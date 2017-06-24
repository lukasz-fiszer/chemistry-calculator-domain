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

	/**
	 * @dataProvider parseMethodDataProvider
	 */
	public function testParseMethod($input, $parsed){
		$parser = new Parser(new TokenStream(new InputStream($input)));
		$this->assertEquals(json_decode(json_encode($parsed)), $parser->parse());
	}

	public function parseMethodDataProvider(){
		return [
			['H', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]]
			]]],

			['H2O', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]],
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]],
				]]
			]]],

			['H3O{+}', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 3, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]],
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]],
					['type' => 'molecule', 'occurences' => 1, 'entries' => [
						['type' => 'charge', 'occurences' => 1, 'value' => '+'],
					]],
				]]
			]]],

			['{H3O+}', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
						['type' => 'molecule', 'occurences' => 1, 'entries' => [
							['type' => 'element', 'occurences' => 3, 'entry' => [
								'type' => 'element_identifier', 'value' => 'H'
							]],
							['type' => 'element', 'occurences' => 1, 'entry' => [
								'type' => 'element_identifier', 'value' => 'O'
							]],
							['type' => 'charge', 'occurences' => 1, 'value' => '+'],
						]]
					]
				]
			]]],

			['(Ab1Ab2)20', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
						['type' => 'molecule', 'occurences' => 20, 'entries' => [
							['type' => 'element', 'occurences' => 1, 'entry' => [
								'type' => 'element_identifier', 'value' => 'Ab'
							]],
							['type' => 'element', 'occurences' => 2, 'entry' => [
								'type' => 'element_identifier', 'value' => 'Ab'
							]],
						]]
					]
				]
			]]],

			['H2+O2=H2O', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '='],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]],
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
			]]],

			['H2+O2<->H2O', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '<->'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]],
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
			]]],

			['H2 + O2 <-> H2O', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '<->'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]],
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
			]]],

			['H2 + O2 <- H2O', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '<-'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]],
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
			]]],

			['H2 + O2 -> (H2O)', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '->'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 1, 'entries' => [
						['type' => 'element', 'occurences' => 2, 'entry' => [
							'type' => 'element_identifier', 'value' => 'H'
						]],
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'O'
						]]
					]]
				]],
			]]],

			['H2 + O2 + Ab(Ab[Ab{+}2]3)4 + {-}2 = (H2O) + {+2}5 + {Ab-2}5 + Ab10', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'Ab'
					]],
					['type' => 'molecule', 'occurences' => 4, 'entries' => [
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'Ab'
						]],
						['type' => 'molecule', 'occurences' => 3, 'entries' => [
							['type' => 'element', 'occurences' => 1, 'entry' => [
								'type' => 'element_identifier', 'value' => 'Ab'
							]],
							['type' => 'molecule', 'occurences' => 2, 'entries' => [
								['type' => 'charge', 'occurences' => 1, 'value' => '+'],
							]],
						]],
					]],
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 2, 'entries' => [
						['type' => 'charge', 'occurences' => 1, 'value' => '-']
					]]
				]],
				['type' => 'operator', 'value' => '='],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 1, 'entries' => [
						['type' => 'element', 'occurences' => 2, 'entry' => [
							'type' => 'element_identifier', 'value' => 'H'
						]],
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'O'
						]]
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 5, 'entries' => [
						['type' => 'charge', 'occurences' => 2, 'value' => '+']
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 5, 'entries' => [
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'Ab'
						]],
						['type' => 'charge', 'occurences' => 2, 'value' => '-']
					]]
				]],
				['type' => 'operator', 'value' => '+'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 10, 'entry' => [
						'type' => 'element_identifier', 'value' => 'Ab'
					]]
				]],
			]]],
		];
	}

	/**
	 * @expectedException ChemCalc\Domain\Chemistry\Parser\ParserException
	 */
	public function testExceptionThrowing(){
		$parser = new Parser(new TokenStream(new InputStream('test')));
		$parser->parse();
	}
}
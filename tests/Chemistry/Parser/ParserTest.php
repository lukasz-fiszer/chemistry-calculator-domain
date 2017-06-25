<?php

namespace ChemCalc\Domain\Tests\Chemistry\Parser;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use Exception;
use ChemCalc\Domain\Chemistry\Parser\TokenStream;
use ChemCalc\Domain\Tests\InvokesInaccessibleMethod;
use ChemCalc\Domain\Chemistry\Parser\Parser;
use ChemCalc\Domain\Chemistry\Parser\ParserException;

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

	/**
	 * @dataProvider parserExceptionMethodDataProvider
	 */
	public function testParserException($input, $message){
		$parser = new Parser(new TokenStream(new InputStream($input)));
		$this->expectException(ParserException::class);
		$this->expectExceptionMessage($message);
		$parser->parse();
	}

	/**
	 * @expectedException ChemCalc\Domain\Chemistry\Parser\ParserException
	 */
	public function testExceptionThrowing(){
		$parser = new Parser(new TokenStream(new InputStream('test')));
		$parser->parse();
	}

	public function parserExceptionMethodDataProvider(){
		return [
			['test', 'Character exception t (line: 0, column: 0)'],
			['test2', 'Character exception t (line: 0, column: 0)'],
			['H2O++++', 'Unexpected token: {"type":"operator","value":"++++"} (line: 0, column: 7)'],
			['H2O++++Ab', 'Unexpected token: {"type":"operator","value":"++++"} (line: 0, column: 7)'],
			//['(Ab]2', 'Expected token: {"type":"punctuation","value":"]"} (line: 0, column: 4)'],
			['(Ab]2', 'Expected token of type: punctuation and value of: ) (line: 0, column: 4)'],
			['H2O)', 'Unexpected token: {"type":"punctuation","value":")","mode":"close","opposite":"("} (line: 0, column: 4)'],
			//['H2O(Ab=)5', 'Unexpected token: {"type":"operator","value"=")"} (line: 0, column: 7)'],
			//['H2O(Ab->)5', 'Unexpected token: {"type":"operator","value"->")"} (line: 0, column: 8)'],
			['H2O(Ab=)5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 7)'],
			['H2O(Ab->)5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 8)'],
			['H2O(Ab->]5', 'Expected token of type: punctuation and value of: ) (line: 0, column: 8)'],
		];
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
					['type' => 'molecule', 'occurences' => 1, 'delimited' => [
						'type' => 'punctuation', 'value' => '{', 'mode' => 'open', 'opposite' => '}'
					], 'entries' => [
						['type' => 'charge', 'occurences' => 1, 'value' => '+'],
					]],
				]]
			]]],

			['{H3O+}', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
						['type' => 'molecule', 'occurences' => 1, 'delimited' => [
							'type' => 'punctuation', 'value' => '{', 'mode' => 'open', 'opposite' => '}'
						], 'entries' => [
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
						['type' => 'molecule', 'occurences' => 20, 'delimited' => [
							'type' => 'punctuation', 'value' => '(', 'mode' => 'open', 'opposite' => ')'
						], 'entries' => [
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
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '=', 'mode' => 'side_equality'],
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
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '<->', 'mode' => 'side_equality'],
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
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '<->', 'mode' => 'side_equality'],
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
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '<-', 'mode' => 'side_equality'],
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
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '->', 'mode' => 'side_equality'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 1, 'delimited' => [
							'type' => 'punctuation', 'value' => '(', 'mode' => 'open', 'opposite' => ')'
						], 'entries' => [
						['type' => 'element', 'occurences' => 2, 'entry' => [
							'type' => 'element_identifier', 'value' => 'H'
						]],
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'O'
						]]
					]]
				]],
			]]],

			['H2 + O2 <=> (H2O)', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '<=>', 'mode' => 'side_equality'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 1, 'delimited' => [
							'type' => 'punctuation', 'value' => '(', 'mode' => 'open', 'opposite' => ')'
						], 'entries' => [
						['type' => 'element', 'occurences' => 2, 'entry' => [
							'type' => 'element_identifier', 'value' => 'H'
						]],
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'O'
						]]
					]]
				]],
			]]],

			['H2 + O2 <= (H2O)', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '<=', 'mode' => 'side_equality'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 1, 'delimited' => [
							'type' => 'punctuation', 'value' => '(', 'mode' => 'open', 'opposite' => ')'
						], 'entries' => [
						['type' => 'element', 'occurences' => 2, 'entry' => [
							'type' => 'element_identifier', 'value' => 'H'
						]],
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'O'
						]]
					]]
				]],
			]]],

			['H2 + O2 => (H2O)', ['type' => 'top_level', 'nodes' => [
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'H'
					]]
				]],
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '=>', 'mode' => 'side_equality'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 1, 'delimited' => [
							'type' => 'punctuation', 'value' => '(', 'mode' => 'open', 'opposite' => ')'
						], 'entries' => [
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
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 2, 'entry' => [
						'type' => 'element_identifier', 'value' => 'O'
					]]
				]],
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 1, 'entry' => [
						'type' => 'element_identifier', 'value' => 'Ab'
					]],
					['type' => 'molecule', 'occurences' => 4, 'delimited' => [
							'type' => 'punctuation', 'value' => '(', 'mode' => 'open', 'opposite' => ')'
						], 'entries' => [
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'Ab'
						]],
						['type' => 'molecule', 'occurences' => 3, 'delimited' => [
							'type' => 'punctuation', 'value' => '[', 'mode' => 'open', 'opposite' => ']'
						], 'entries' => [
							['type' => 'element', 'occurences' => 1, 'entry' => [
								'type' => 'element_identifier', 'value' => 'Ab'
							]],
							['type' => 'molecule', 'occurences' => 2, 'delimited' => [
								'type' => 'punctuation', 'value' => '{', 'mode' => 'open', 'opposite' => '}'
							], 'entries' => [
								['type' => 'charge', 'occurences' => 1, 'value' => '+'],
							]],
						]],
					]],
				]],
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 2, 'delimited' => [
						'type' => 'punctuation', 'value' => '{', 'mode' => 'open', 'opposite' => '}'
					], 'entries' => [
						['type' => 'charge', 'occurences' => 1, 'value' => '-']
					]]
				]],
				['type' => 'operator', 'value' => '=', 'mode' => 'side_equality'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 1, 'delimited' => [
						'type' => 'punctuation', 'value' => '(', 'mode' => 'open', 'opposite' => ')'
					], 'entries' => [
						['type' => 'element', 'occurences' => 2, 'entry' => [
							'type' => 'element_identifier', 'value' => 'H'
						]],
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'O'
						]]
					]]
				]],
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 5, 'delimited' => [
						'type' => 'punctuation', 'value' => '{', 'mode' => 'open', 'opposite' => '}'
					], 'entries' => [
						['type' => 'charge', 'occurences' => 2, 'value' => '+']
					]]
				]],
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'molecule', 'occurences' => 5, 'delimited' => [
						'type' => 'punctuation', 'value' => '{', 'mode' => 'open', 'opposite' => '}'
					], 'entries' => [
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'Ab'
						]],
						['type' => 'charge', 'occurences' => 2, 'value' => '-']
					]]
				]],
				['type' => 'operator', 'value' => '+', 'mode' => 'plus'],
				['type' => 'molecule', 'occurences' => 1, 'entries' => [
					['type' => 'element', 'occurences' => 10, 'entry' => [
						'type' => 'element_identifier', 'value' => 'Ab'
					]]
				]],
			]]],
		];
	}
}
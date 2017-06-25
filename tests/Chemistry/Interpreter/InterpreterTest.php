<?php

namespace ChemCalc\Domain\Tests\Chemistry\Interpreter;

use ChemCalc\Domain\Chemistry\Interpreter\Interpreter;


class InterpreterTest extends \PHPUnit\Framework\TestCase
{
	public function testConstructorPropertiesInjection(){
		$parsed = ['type' => 'top_level', 'nodes' => [
			['type' => 'molecule', 'occurences' => 1, 'entries' => [
				['type' => 'element', 'occurences' => 1, 'entry' => ['type' => 'element_identifier', 'value' => 'H']]
			]]
		]];
		$parsed = json_decode(json_encode($parsed));
		$interpreter = new Interpreter($parsed);
		$this->assertAttributeEquals($parsed, 'ast', $interpreter);
		$this->assertEquals($parsed, $interpreter->getAst());
	}

	public function testAbcd(){
		$a = ['H2 + O2 + Ab(Ab[Ab{+}2]3)4 + {-}2 = (H2O) + {+2}5 + {Ab-2}5 + Ab10', ['type' => 'top_level', 'nodes' => [
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
			]]];

		$parsed = new \ChemCalc\Domain\Chemistry\Parser\Parser(new \ChemCalc\Domain\Chemistry\Parser\TokenStream(new \ChemCalc\Domain\Chemistry\Parser\InputStream($a[0])));
		$parsed = $parsed->parse();
		//var_dump($parsed);
		//echo "\n\n\n\n";
		$interpreter = new Interpreter($parsed);
		//var_dump($interpreter->interpret());
	}
}
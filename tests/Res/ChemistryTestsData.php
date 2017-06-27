<?php

namespace ChemCalc\Domain\Tests\Res;

use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\Element;

class ChemistryTestsData
{
	public function getInputParseTestsData(){
		return [
			['input' => 'H',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
					['type' => 'molecule', 'occurences' => 1, 'entries' => [
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'H'
						]]
					]]
				]],
			'interpreted' =>
				['type' => 'molecule', 'interpreted' => [
					new Molecule([['element' => new Element('Hydrogen', 'H', 1.008, true, json_decode(file_get_contents(realpath(dirname(__FILE__)).'/../../res/PeriodicTableJSON.json'), true)['elements'][0]), 'occurences' => 1]], 'H')
				]],
			],

			['input' => 'H2O', 
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
					['type' => 'molecule', 'occurences' => 1, 'entries' => [
						['type' => 'element', 'occurences' => 2, 'entry' => [
							'type' => 'element_identifier', 'value' => 'H'
						]],
						['type' => 'element', 'occurences' => 1, 'entry' => [
							'type' => 'element_identifier', 'value' => 'O'
						]],
					]]
				]],
			],

			['input' => 'H3O{+}',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => '{H3O+}',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => '(Ab1Ab2)20',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => 'H2+O2=H2O',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => 'H2+O2<->H2O',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => 'H2 + O2 <-> H2O',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => 'H2 + O2 <- H2O',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => 'H2 + O2 -> (H2O)',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => 'H2 + O2 <=> (H2O)',
			'parsed' => 
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => 'H2 + O2 <= (H2O)',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],

			['input' => 'H2 + O2 => (H2O)',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],


			['input' => 'H2 + O2 + Ab(Ab[Ab{+}2]3)4 + {-}2 = (H2O) + {+2}5 + {Ab-2}5 + Ab10',
			'parsed' =>
				['type' => 'top_level', 'nodes' => [
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
				]],
			],
		];
	}
}
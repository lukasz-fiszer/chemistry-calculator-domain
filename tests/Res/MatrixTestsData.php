<?php

namespace ChemCalc\Domain\Tests\Res;

class MatrixTestsData
{
	public function getMatrixEliminationTestsData(){
		return [
			['matrix_a' => [
				[]
			],
			'matrix_b' => [
				[]
			],
			'matrix_a_eliminated' => [],
			'matrix_b_eliminated' => [],
			'values' => [],
			'free' => [],
			'consistent' => true,
			'pivoted' => [null],
			'found' => [],
			],

			['matrix_a' => [
				[0]
			],
			'matrix_b' => [
				[0]
			],
			'matrix_a_eliminated' => [0],
			'matrix_b_eliminated' => [0],
			'values' => ['free'],
			'free' => [true],
			'consistent' => true,
			'pivoted' => [null],
			'found' => [1],
			],

			['matrix_a' => [
				[0]
			],
			'matrix_b' => [
				[0]
			],
			'matrix_a_eliminated' => [[0]],
			'matrix_b_eliminated' => [[0]],
			'values' => ['free'],
			'free' => [true],
			'consistent' => true,
			'pivoted' => [null],
			'found' => [1],
			],

			['matrix_a' => [
				[1, 2, 3],
				[1, 2, 3]
			],
			'matrix_b' => [
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 2, 3],
				[0, 0, 0]
			],
			'matrix_b_eliminated' => [
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 1, 'multiplier' => -2],
					['column' => 2, 'multiplier' => -3],
				]],
				'free', 'free'
			],
			'free' => [false, true, true],
			'consistent' => true,
			'pivoted' => [0, null],
			'found' => [-5, 1, 1],
			],

			['matrix_a' => [
				[1, 2, 3],
				[0, 0, 0]
			],
			'matrix_b' => [
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 2, 3],
				[0, 0, 0]
			],
			'matrix_b_eliminated' => [
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 1, 'multiplier' => -2],
					['column' => 2, 'multiplier' => -3],
				]],
				'free', 'free'
			],
			'free' => [false, true, true],
			'consistent' => true,
			'pivoted' => [0, null],
			'found' => [-5, 1, 1],
			],

			['matrix_a' => [
				[2, 0, -2],
				[0, 2, -1]
			],
			'matrix_b' => [
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 0, -1],
				[0, 1, -1/2]
			],
			'matrix_b_eliminated' => [
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => 1],
				]],
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => 1/2],
				]], 'free'
			],
			'free' => [false, false, true],
			'consistent' => true,
			'pivoted' => [0, 1],
			'found' => [2, 1, 2],
			],

			['matrix_a' => [
				[0, 2, -1],
				[2, 0, -2]
			],
			'matrix_b' => [
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 0, -1],
				[0, 1, -1/2]
			],
			'matrix_b_eliminated' => [
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => 1],
				]],
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => 1/2],
				]], 'free'
			],
			'free' => [false, false, true],
			'consistent' => true,
			'pivoted' => [0, 1],
			'found' => [2, 1, 2],
			],

			['matrix_a' => [
				[1, 2, 0],
				[1, 2, 0]
			],
			'matrix_b' => [
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 2, 0],
				[0, 0, 0]
			],
			'matrix_b_eliminated' => [
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 1, 'multiplier' => -2],
				]],
				'free', 'free'
			],
			'free' => [false, true, true],
			'consistent' => true,
			'pivoted' => [0, null],
			'found' => [-2, 1, 1],
			],

			['matrix_a' => [
				[1, 2, 0],
				[1, 2, 0]
			],
			'matrix_b' => [
				[5],
				[10]
			],
			'matrix_a_eliminated' => [
				[1, 2, 0],
				[0, 0, 0]
			],
			'matrix_b_eliminated' => [
				[5],
				[5]
			],
			'values' => [
				['value' => 5, 'add_free' => [
					['column' => 1, 'multiplier' => -2],
				]],
				'free', 'free'
			],
			'free' => [false, true, true],
			'consistent' => false,
			'pivoted' => [0, null],
			'found' => [],
			],

			['matrix_a' => [
				[1, 2, 3],
				[4, 5, 6],
				[7, 8, 9]
			],
			'matrix_b' => [
				[0],
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 0, -1],
				[0, 1, 2],
				[0, 0, 0]
			],
			'matrix_b_eliminated' => [
				[0],
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => 1],
				]],
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => -2],
				]],
				'free'
			],
			'free' => [false, false, true],
			'consistent' => true,
			'pivoted' => [0, 1, null],
			'found' => [1, -2, 1],
			],

			['matrix_a' => [
				[1, 2, -3],
				[4, 5, -6],
				[7, 8, -9]
			],
			'matrix_b' => [
				[0],
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 0, 1],
				[0, 1, -2],
				[0, 0, 0]
			],
			'matrix_b_eliminated' => [
				[0],
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => -1],
				]],
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => 2],
				]],
				'free'
			],
			'free' => [false, false, true],
			'consistent' => true,
			'pivoted' => [0, 1, null],
			'found' => [-1, 2, 1],
			],

			['matrix_a' => [
				[-1, 2, 3],
				[-4, 5, 6],
				[-7, 8, 9]
			],
			'matrix_b' => [
				[0],
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 0, 1],
				[0, 1, 2],
				[0, 0, 0]
			],
			'matrix_b_eliminated' => [
				[0],
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => -1],
				]],
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => -2],
				]],
				'free'
			],
			'free' => [false, false, true],
			'consistent' => true,
			'pivoted' => [0, 1, null],
			'found' => [-1, -2, 1],
			],

			['matrix_a' => [
				[2, 0, -1, 0],
				[0, 2, 0, -1]
			],
			'matrix_b' => [
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 0, -1/2, 0],
				[0, 1, 0, -1/2]
			],
			'matrix_b_eliminated' => [
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => 1/2],
				]],
				['value' => 0, 'add_free' => [
					['column' => 3, 'multiplier' => 1/2],
				]],
				'free', 'free'
			],
			'free' => [false, false, true, true],
			'consistent' => true,
			'pivoted' => [0, 1],
			'found' => [1, 1, 2, 2],
			],

			['matrix_a' => [
				[2, 0, -1, 0],
				[0, 2, 0, -1]
			],
			'matrix_b' => [
				[5],
				[10]
			],
			'matrix_a_eliminated' => [
				[1, 0, -1/2, 0],
				[0, 1, 0, -1/2]
			],
			'matrix_b_eliminated' => [
				[5/2],
				[5]
			],
			'values' => [
				['value' => 5/2, 'add_free' => [
					['column' => 2, 'multiplier' => 1/2],
				]],
				['value' => 5, 'add_free' => [
					['column' => 3, 'multiplier' => 1/2],
				]],
				'free', 'free'
			],
			'free' => [false, false, true, true],
			'consistent' => true,
			'pivoted' => [0, 1],
			'found' => [6, 11, 2, 2],
			],

			['matrix_a' => [
				[1, 2, 3],
				[1, 2, 3],
				[5, 5, 5],
				[1, 1, 1],
				[2, 4, 6]
			],
			'matrix_b' => [
				[0],
				[0],
				[0],
				[0],
				[0]
			],
			'matrix_a_eliminated' => [
				[1, 0, -1],
				[0, 1, 2],
				[0, 0, 0],
				[0, 0, 0],
				[0, 0, 0]
			],
			'matrix_b_eliminated' => [
				[0],
				[0],
				[0],
				[0],
				[0]
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => 1],
				]],
				['value' => 0, 'add_free' => [
					['column' => 2, 'multiplier' => -2],
				]],
				'free'
			],
			'free' => [false, false, true],
			'consistent' => true,
			'pivoted' => [0, 1, null, null, null],
			'found' => [1, -2, 1],
			],

			['matrix_a' => [
				[1, 2, 3],
				[5, 10, 20],
				[4, 8, 20],
			],
			'matrix_b' => [
				[0],
				[0],
				[0],
			],
			'matrix_a_eliminated' => [
				[1, 2, 0],
				[0, 0, 1],
				[0, 0, 0],
			],
			'matrix_b_eliminated' => [
				[0],
				[0],
				[0],
			],
			'values' => [
				['value' => 0, 'add_free' => [
					['column' => 1, 'multiplier' => -2],
				]],
				'free',
				['value' => 0, 'add_free' => []]
			],
			'free' => [false, true, false],
			'consistent' => true,
			'pivoted' => [0, 2, null],
			'found' => [-2, 1, 0],
			],
		];
	}
}
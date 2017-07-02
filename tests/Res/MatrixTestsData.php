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
			'pivoted' => [null],
			'consistent' => true
			],

			['matrix_a' => [
				[0]
			],
			'matrix_b' => [
				[0]
			],
			'matrix_a_eliminated' => [0],
			'matrix_b_eliminated' => [0],
			'pivoted' => [null],
			'consistent' => true
			],

			['matrix_a' => [
				[0]
			],
			'matrix_b' => [
				[0]
			],
			'matrix_a_eliminated' => [[0]],
			'matrix_b_eliminated' => [[0]],
			'pivoted' => [null],
			'consistent' => true
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
			'pivoted' => [0, null],
			'consistent' => true
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
			'pivoted' => [0, null],
			'consistent' => true
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
			'pivoted' => [0, 1],
			'consistent' => true
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
			'pivoted' => [0, 1],
			'consistent' => true
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
			'pivoted' => [0, null],
			'consistent' => true
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
			'pivoted' => [0, null],
			'consistent' => false
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
			'pivoted' => [0, 1, null],
			'consistent' => true
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
			'pivoted' => [0, 1, null],
			'consistent' => true
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
			'pivoted' => [0, 1, null],
			'consistent' => true
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
			'pivoted' => [0, 1],
			'consistent' => true
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
			'pivoted' => [0, 1],
			'consistent' => true
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
			'pivoted' => [0, 1, null, null, null],
			'consistent' => true
			],
		];
	}
}
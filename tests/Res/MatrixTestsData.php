<?php

namespace ChemCalc\Domain\Tests\Res;

class MatrixTestsData
{
	public function getMatrixEliminationTestsData(){
		return [
			[	
				[[]],
				[[]],
				[],
				[]
			],

			[	
				[[0]],
				[[0]],
				[0],
				[0]
			],

			[	
				[[0]],
				[[0]],
				[[0]],
				[[0]]
			],

			[	
				[[1, 2, 3],
				[1, 2, 3]],
				[[0],
				[0]],
				[[1, 2, 3],
				[0, 0, 0]],
				[[0],
				[0]]
			],

			[	
				[[1, 2, 3],
				[0, 0, 0]],
				[[0],
				[0]],
				[[1, 2, 3],
				[0, 0, 0]],
				[[0],
				[0]]
			],

			[	
				[[2, 0, -2],
				[0, 2, -1]],
				[[0],
				[0]],
				[[1, 0, -1],
				[0, 1, -1/2]],
				[[0],
				[0]]
			],

			[	
				[[0, 2, -1],
				[2, 0, -2]],
				[[0],
				[0]],
				[[1, 0, -1],
				[0, 1, -1/2]],
				[[0],
				[0]]
			],

			[	
				[[1, 2, 0],
				[1, 2, 0]],
				[[0],
				[0]],
				[[1, 2, 0],
				[0, 0, 0]],
				[[0],
				[0]]
			],

			[	
				[[1, 2, 0],
				[1, 2, 0]],
				[[5],
				[10]],
				[[1, 2, 0],
				[0, 0, 0]],
				[[5],
				[5]]
			],

			[	
				[[1, 2, 3],
				[4, 5, 6],
				[7, 8, 9]],
				[[0],
				[0],
				[0]],
				[[1, 0, -1],
				[0, 1, 2],
				[0, 0, 0]],
				[[0],
				[0],
				[0]]
			],

			[	
				[[1, 2, -3],
				[4, 5, -6],
				[7, 8, -9]],
				[[0],
				[0],
				[0]],
				[[1, 0, 1],
				[0, 1, -2],
				[0, 0, 0]],
				[[0],
				[0],
				[0]]
			],

			[	
				[[-1, 2, 3],
				[-4, 5, 6],
				[-7, 8, 9]],
				[[0],
				[0],
				[0]],
				[[1, 0, 1],
				[0, 1, 2],
				[0, 0, 0]],
				[[0],
				[0],
				[0]]
			],

			[	
				[[2, 0, -1, 0],
				[0, 2, 0, -1]],
				[[0],
				[0]],
				[[1, 0, -1/2, 0],
				[0, 1, 0, -1/2]],
				[[0],
				[0]]
			],

			[	
				[[2, 0, -1, 0],
				[0, 2, 0, -1]],
				[[5],
				[10]],
				[[1, 0, -1/2, 0],
				[0, 1, 0, -1/2]],
				[[5/2],
				[5]]
			],
		];
	}
}
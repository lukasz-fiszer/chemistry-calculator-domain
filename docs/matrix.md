## Matrix

Matrix consists of decompositions and solver.

**MatrixElimination**
`Decomposition\MatrixElimination` is a class that performs elimination on given matrix and returns the decomposition object with elimination products.
Products are:

* left
`NumericMatrix` instance, eliminated matrix product of the variables side
* right
`NumericMatrix` instance, eliminated matrix product of the free values side
* values
`array` of `objects` or `strings` indexed by column index, if given column represents free variable then string `'free'` is placed, if given column represents dependent variable then `object` is placed
`object` has key of `value` where numeric value is placed taken from free values and additional `add_free` key with `array` of variables needed to by added, every entry has key `multiplier` with numeric value of how much multiplied the variable must be before addition and key `column` with int representing the column index of variable to be added
* free
`array` indexed by column with `bool` saying if given variable is free
* consistent
`bool` saying if matrix is consistent
* pivoted
`array` of integers or null indexed by rows, if there was pivoting in given row then the value is the column index where pivot was, otherwise the values is null

**MatrixSolver**
`MatrixSolver` is a class that solves matrix and brings the solved variables to ordered array of coprime integer values.
In constructor it takes `NumericMatrix $matrixA` and `NumericMatrix $matrixB` that are matrices to be solved and `MatrixElimination` and `Calculator` instances.
In `solve()` method matrices are decomposed at first. If they are inconsistent then exception is thrown, otherwise array of integer values is returned.
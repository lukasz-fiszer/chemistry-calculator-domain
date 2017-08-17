## Chemistry

Chemistry namespace consists of DataLoader, Entity, Parser, Interpreter and Solver.

### DataLoader
Data loaders are objects that load elements data. In `Interfaces\ElementDataLoader` it is an interface for elements data loaders.
`ElementDataLoader` is a data loader that uses json file from `res/` directory or in constructor it takes optional filepath to other json file with elements data.

### Entity
Entity is a directory with basic chemical entites, it contains Element and Molecule as most basic entites.

`Element` is an immutable class representing element. It has basic properties of name, symbol and atomic mass plus optional is real that is default to true and elements data array that is filled with basic properties if not given in array.

`ElementFactory` is elements factory that uses `ElementDataLoader` to load elements data and then create it or an 'unknown' one if no data is present for it.

`MatchesElement` is a trait that checks and searches for elements among elements data collections.

`Molecule` is an immutable class representing molecule. It takes in constructor array of elements entries, formula and charge. Then it calculates its own atomic mass and check if it is real molecule (based on is real property of elements contained).
Array of elements entries consists of single entries of form `['element' => {ElementInstance}, 'occurences' => {int}]`.

`MoleculeBuilder` is an immutable builder of molecules. It takes in constructor `ElementFactory` and manages molecule elements, formula and charge. It also allows for merging other builder instance inside, used for example when submolecule needs to be build into molecule instance.

### Parser
Parser namespace contains input and token streams, parser itself and parser exception with its builder.

`InputStream` is constructed with string representing input to be streamed (and then parsed) and `ParserExceptionBuilder` used when `ParserException` is thrown. Maintains internal state of line, column and position. Allows for peeking one character ahead. Has helper method `throwException()` where `ParserException` is being thrown with input stream context data of input, line, column and character pointer position with additional exception key code and merge context.

`ParserExceptionBuilder` is an immutable builder of parser exceptions. It manages exception message, code, previous exception and parser context. In addition it manages parser exception codes by their code keys and builds exception messages with additional context data append `(line: {line}, column: {column})` if given.

`ParserException` is exception thrown within parser namespace. It contains additional parser context object that if null is given is set to empty object.

`TokenStream` is constructed with `InputStream` that is given to be tokenized. Has helper method `throwException()` that delegates to the `InputStream`. Allows for peeking one token ahead. Throws `tokenizer_unrecognized_character` parser exception when it meets character exception of unrecognized character and passes additional key-value pair `character: {string}` to parser context representing character that the exception was raised on. Tokens returned are objects of scheme: `[type: {string}, value: {string}]` with possible additional key-value pairs. Whitespace is ignored (when in between of tokens).
The tokens are:

* Number token (all digits)
  `[type: 'number', value: '123']`
* Element identifier (uppercase letter and possible lowercase letters)
  `[type: 'element_identifier', value: 'H']`
  `[type: 'element_identifier', value: 'He']`
  `[type: 'element_identifier', value: 'Abc']`
* Punctuation token (one of `()[]{}` brackets in pairs)
  `[type: 'punctuation', value: '(', 'mode' => 'open', 'opposite' => ')']`
  `[type: 'punctuation', value: ')', 'mode' => 'close', 'opposite' => '(']`
* Operator token (string of `+=<->` characters)
  `[type: 'operator', value: '+']`
  `[type: 'operator', value: '<->']`

The tokenizer peeks only one character ahead and because of its simple structure it allows for uncregonized operators (that are still a string of `+=<->` characters) and plus character indicating positive charge (allowed for such purpose inside brackets) is treated as plus operator. Both issues are addressed by parser later on.

`Parser` is constructed with `TokenStream` that is given to be parsed. In its `parse()` method it returns parsed AST of the tokenized stream. Object returned is of scheme: `[type: 'top_level', nodes: {nodes}]`. Nodes are molecules or operators. The nodes are:

* Molecule node
  `[type: 'molecule', entries: {entries}, 'occurences': {int}]`
  Where entries (array of entries) are nodes of types:
    * Molecule nodes type itself (molecule groups in punctuation brackets pairs)
    * Element identifiers nodes
      `[type: 'element', entry: {element_identifier_token}, 'occurences': {int}]`
    * Charge nodes
      `[type: 'charge', value: {string}, 'occurences': {int}]`
      `[type: 'charge', value: '+', 'occurences': {int}]`
      `[type: 'charge', value: '-', 'occurences': {int}]`
* Operator node
  `[type: 'operator', value: {string}, 'mode': {'plus' or 'side_equality'}]`
    * Plus operator
      `[type: 'operator', value: '+', 'mode': 'plus']`
    * Side equality operator
      `[type: 'operator', value: {'=', '<=', '=>', '<=>', '<-', '->' or '<->'}, 'mode': 'side_equality']`

Parser throws two types of exceptions. First, when unexpected token appears, then `parser_unexpected_token` code key is used and merge context contains `token: {token}` field with token that exception was thrown on. Second, when other token was expected than the one that appeared, then `parser_expected_other_token` code key is used and merge context contains: `[actualToken: {token}, expectedType: {string}, expectedValue: {string or null}]`with actual token met, expected token type and expected token value or null if any token value would match.

#### Tokens grammar
```
whitespace: \w or \s (whitespace)
number: \d+ (number, digits)
element_identifier: [A-Z][a-z]+ (uppercase and possible lowercase)
punctuation: ()[]{} (brackets in pairs)
operator: (+=<->)+ (operator characters)
```
#### Parsing grammar
```
molecule: molecule_entries (array of molecule entries)
molecule_entry: element_identifier, number or molecule (inner_molecule), (number) or charge_identifier, number
inner_molecule: punctuation(mode: open), molecule, punctuation(matching), number
charge_identifier: operator with value in [+-] when in between of punctuation brackets pairs
operator(mode: plus): operator with value + (when not charge_identifier)
operator(mode: side_equality): operator with value in {'=', '<=', '=>', '<=>', '<-', '->' or '<->'}
```

### Interpereter

Interpreter namespace contains interpreter and interpreter exception.

`Interpreter` is constructed with `MoleculeBuilder`. It interprets parsed AST with its nodes that is given in constructor. In its `interpret()` method it returns interpreted object of type molecule, reaction equation or unknown, they are of scheme `[type: {string}, interpreted: {interpreted}]` or `[type: 'unknown', message: {string}, context: [code: {int}, ...]]`. Following interpreted objects are returned:

* Molecule (single node of type molecule)
  `[type: 'molecule', interpreted: {MoleculeInstance}]`
* Reaction equation (molecule nodes with plus and side equality operators)
  `[type: 'reaction_equation', interpreted: [[MoleculeInstances], [MoleculeInstances], ]]`
* Unknown (no nodes (1), expected molecule (2) or operator (3) where other nodes was, too few (4) or too many (5) sides, operator should be followed by molecule (6))
  `[type: 'unknown', message: {string}, context: [code: {int}, ...]]`

When interpreter meets nodes of unknown type it throws `InterpreterException`, when checking top level nodes or molecule inner nodes.

`InterpreterException` is exception used by interpreter.

### Solver

Solver namespace contains molecule and reaction equation solvers.

`MoleculeSolver` takes in constructor `Molecule` and finds grams and moles for the molecule.

`ReactionEquationSolver` takes in constructor array with sides of reaction equation with instances of `Molecule`. In its `findCoefficients()` method it finds ordered list of coprime integers that are stoichiometric coefficients for the reaction given.
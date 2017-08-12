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

`InputStream` is constructed with string representing input to be streamed (and then parsed) and `ParserExceptionBuilder` used when `ParserException` is thrown. Maintains internal state of line, column and position. Allows for peeking one character ahead. Has helper method `throwException()` where `ParserException` is being thrown with input stream context data of input, line, column and character pointer position.

`ParserExceptionBuilder` is an immutable builder of parser exceptions. It manages exception message, code, previous exception and parser context. In addition it manages parser exception codes by their code keys and builds exception messages with additional context data append `(line: {line}, column: {column})` if given.

`ParserException` is exception thrown within parser namespace. It contains additional parser context object that if null is given is set to empty object.
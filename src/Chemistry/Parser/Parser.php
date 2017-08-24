<?php

namespace ChemCalc\Domain\Chemistry\Parser;


/**
 * Chemistry reaction equation
 * Parser
 */
class Parser
{
	/**
	 * Token stream used by parser
	 * 
	 * @var TokenStream
	 */
	protected $tokenStream;

	/**
	 * Construct new parser
	 * 
	 * @param TokenStream $tokenStream token stream used for parsing
	 */
	public function __construct(TokenStream $tokenStream){
		$this->tokenStream = $tokenStream;
	}

	/**
	 * Parse the input and return AST from the token stream
	 * 
	 * @return object the AST parsed
	 */
	public function parse(){
		return $this->parseTopLevel();
	}

	/**
	 * Parse the token stream top level
	 * 
	 * @return object the AST parsed on top level of token stream
	 */
	protected function parseTopLevel(){
		$nodes = [];
		while(!$this->tokenStream->eof()){
			$nodes[] = $this->parseAtom();
		}
		return (object) ['type' => 'top_level', 'nodes' => $nodes];
	}

	/**
	 * Parse next atomic node
	 * 
	 * @return object atomic AST node parsed
	 */
	protected function parseAtom(){
		if($this->isMoleculeStart()){
			return $this->parseMolecule();
		}
		if($this->isTokenType('operator')){
			$token = $this->tokenStream->peek();
			if(in_array($token->value, ['=', '<-', '->', '<->', '<=>', '<=', '=>'], true)){
				$token = $this->tokenStream->next();
				$token->mode = 'side_equality';
				return $token;
			}
			if(in_array($token->value, ['+'], true)){
				$token = $this->tokenStream->next();
				$token->mode = 'plus';
				return $token;
			}
		}
		$this->unexpectedToken();
	}

	/**
	 * Parse molecule
	 *
	 * @param bool $chargeAllowed true if charge identifiers are allowed
	 * @return object parsed AST of the molecule
	 */
	protected function parseMolecule(bool $chargeAllowed = false){
		$entries = [];
		// $isChargeOperator = function(){
		// $isChargeOperator = function() use($this){
		// $isChargeOperator = (function(){
		$isChargeOperator = function(){
			/*$token = $this->tokenStream->peek();
			if($token->type == 'operator' && strpos('+-', $token->value) !== false){
				return true;
			}
			return false;*/
			if(!$this->isTokenType('operator')){
				return false;
			}
			if(strpos('+-', $this->tokenStream->peek()->value) !== false){
				return true;
			}
			return false;
		};
			// return false;});
		// })->bindTo($this);
		// })->bindTo($this, $this);
		while($this->isMoleculeStart() || ($chargeAllowed && $isChargeOperator())){
			$entries[] = $this->parseMoleculeEntry($chargeAllowed);
		}
		//return (object) ['type' => 'molecule', 'entries' => $entries];
		return (object) ['type' => 'molecule', 'entries' => $entries, 'occurences' => 1];
	}

	/**
	 * Parse molecule entry
	 *
	 * @param bool $chargeAllowed true if charge identifiers are allowed
	 * @return array parsed molecule entry
	 */
	protected function parseMoleculeEntry(bool $chargeAllowed = false){
		$token = $this->tokenStream->peek();
		if($token->type == 'punctuation' && $token->mode == 'open'){
			$this->tokenStream->next();
			$molecule = $this->parseMolecule(true);
			$this->skipTokenType('punctuation', $token->opposite);
			$molecule->occurences = $this->findOccurences();
			$molecule->delimited = $token;
			return $molecule;
		}
		if($token->type == 'element_identifier'){
			$element = $this->tokenStream->next();
			return (object) ['type' => 'element', 'entry' => $element, 'occurences' => $this->findOccurences()];
		}
		if($chargeAllowed && $token->type == 'operator' && in_array($token->value, ['+', '-'], true)){
			$chargeToken = $this->tokenStream->next();
			$charge = (object) ['type' => 'charge', 'value' => $chargeToken->value, 'occurences' => $this->findOccurences()];
			return $charge;
		}
	}

	/**
	 * Find occurences in next number token or return 1 as default occurence number
	 * 
	 * @return int number of occurences found
	 */
	protected function findOccurences(){
		$occurences = 1;
		if($this->isTokenType('number')){
			$occurences = (int) $this->tokenStream->next()->value;
		}
		return $occurences;
	}

	/**
	 * Check if next token is molecule start token
	 * 
	 * @return boolean true if token is molecule start token
	 */
	protected function isMoleculeStart(){
		if($this->isTokenType('punctuation', null, ['mode' => 'open']) || $this->isTokenType('element_identifier')){
			return true;
		}
		return false;
	}

	/**
	 * Check if next token is of given type and contains specified value
	 * 
	 * @param  string      $tokenType  token type to check for
	 * @param  string|null $value      optional value to check for
	 * @param  array       $additional additional key-value pairs to check on token
	 * @return boolean                 true if token matches type and value
	 */
	protected function isTokenType(string $tokenType, string $value = null, array $additional = []){
		$token = $this->tokenStream->peek();
		$matchesTypeAndValue = $token && $token->type == $tokenType && ($value === null || $token->value === $value);
		return $matchesTypeAndValue && array_intersect_assoc((array) $token, $additional) == $additional;
	}

	/**
	 * Skip next token of given type that contains specified value
	 * 
	 * @param  string      $tokenType  token type to skip
	 * @param  string|null $tokenValue optional token value
	 * @throws ParserException         exception thrown if next token does not match
	 * @return void
	 */
	protected function skipTokenType(string $tokenType, string $tokenValue = null){
		if($this->isTokenType($tokenType, $tokenValue)){
			$this->tokenStream->next();
		}
		else{
			$this->tokenStream->throwException('Expected token of type: '.$tokenType.' and value of: '.$tokenValue, 'parser_expected_other_token', (object) ['actualToken' => $this->tokenStream->peek(), 'expectedType' => $tokenType, 'expectedValue' => $tokenValue]);
		}
	}

	/**
	 * Throw exception for unexpected token
	 *
	 * @throws ParserException exception thrown for unexpected token
	 * @return void
	 */
	protected function unexpectedToken(){
		$this->tokenStream->throwException('Unexpected token: '.json_encode($this->tokenStream->peek()), 'parser_unexpected_token', (object) ['token' => $this->tokenStream->peek()]);
	}

    /**
     * Gets the Token stream used by parser.
     *
     * @return TokenStream
     */
    public function getTokenStream()
    {
        return $this->tokenStream;
    }
}
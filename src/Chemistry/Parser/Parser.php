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
		//if($this->isTokenType(''))
		if($this->isMoleculeStart()){
			$token = $this->tokenStream->peek();
			if($token->type == 'punctuation' && $token->mode == 'open'){
				$this->tokenStream->next();
				$molecule = $this->parseMolecule();
				//$this->tokenStream
				$this->skipTokenType('punctuation', $token->opposite);
				if($this->isTokenType('number')){
					//$occurences = (int) $this->tokenStream->next()->value();
					$occurences = (int) $this->tokenStream->next()->value;
					return (object) ['type' => 'molecule', 'entries' => [['entry' => $molecule, 'occurences' => $occurences]]];
				}
			}
			else{
				$molecule = $this->parseMolecule();
			}
			return $molecule;
		}
		if($this->isTokenType('operator')){
			//return $this->tokenStream->next();
			$token = $this->tokenStream->peek();
			if(in_array($token->value, ['+', '=', '<-', '->', '<->'], true)){
				return $this->tokenStream->next();
			}
			else{
				$this->unexpectedToken();
			}
		}
		$this->unexpectedToken();
	}

	/**
	 * Parse molecule
	 * 
	 * @return object parsed AST of the molecule
	 */
	protected function parseMolecule(){
		$token = $this->tokenStream->peek();
		if($token->type == 'punctuation' && $token->mode == 'open'){
			$this->tokenStream->next();
			$molecule = $this->parseMolecule();
			$this->skipTokenType('punctuation', $token->opposite);
			if($this->isTokenType('number')){
				//$occurences = (int) $this->tokenStream->next()->value();
				$occurences = (int) $this->tokenStream->next()->value;
				return (object) ['type' => 'molecule', 'entries' => [['entry' => $molecule, 'occurences' => $occurences]]];
			}
			return $molecule;
			/*else{
				return (object) ['type' => 'molecule', 'entries' => [['entry' => $molecule, 'occurences' => 1]]];
			}*/
		}
		$entries = [];
		while(!$this->tokenStream->eof()){
			$entries[] = $this->parseMoleculeEntry();
		}
		return (object) ['type' => 'molecule', 'entries' => $entries];
	}

	/**
	 * Parse molecule entry
	 * 
	 * @return array parsed molecule entry
	 */
	protected function parseMoleculeEntry(){
		/*$token = $this->tokenStream->next();
		if()*/
		$token = $this->tokenStream->peek();
		if($token->type == 'punctuation' && $token->mode == 'open'){
			$this->tokenStream->next();
			$molecule = $this->parseMolecule();
			$this->skipTokenType('punctuation', $token->opposite);
			if($this->isTokenType('number')){
				//$occurences = (int) $this->tokenStream->next()->value();
				$occurences = (int) $this->tokenStream->next()->value;
				return (object) ['type' => 'molecule', 'entries' => [['entry' => $molecule, 'occurences' => $occurences]]];
			}
			return $molecule;
			/*else{
				return (object) ['type' => 'molecule', 'entries' => [['entry' => $molecule, 'occurences' => 1]]];
			}*/
		}

		if(!$this->isTokenType('element_identifier')){
			$this->unexpectedToken();
		}
		$element = $this->tokenStream->next();
		$occurences = 1;
		if($this->isTokenType('number')){
			//$occurences = (int) $this->tokenStream->next()->value();
			$occurences = (int) $this->tokenStream->next()->value;
		}
		return (object) ['entry' => $element, 'occurences' => $occurences];
	}

	/**
	 * Check if next token is molecule start token
	 * 
	 * @return boolean true if token is molecule start token
	 */
	protected function isMoleculeStart(){
		if($this->isTokenType('punctuation') && $this->tokenStream->peek()->mode == 'open'){
			return true;
		}
		if($this->isTokenType('element_identifier')){
			return true;
		}
		return false;
	}

	/**
	 * Check if next token is of given type and contains specified value
	 * 
	 * @param  string      $tokenType token type to check for
	 * @param  string|null $value     optional value to check for
	 * @return boolean                true if token matches type and value
	 */
	protected function isTokenType(string $tokenType, string $value = null){
		$token = $this->tokenStream->peek();
		//return $token && $token->type == $tokenType && (!$value || $token->value === $value);
		return $token && $token->type == $tokenType && ($value === null || $token->value === $value);
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
			$this->tokenStream->throwException('Expected token of type: '.$tokenType.' and value of: '.$tokenValue);
		}
	}

	/**
	 * Throw exception for unexpected token
	 *
	 * @throws ParserException exception thrown for unexpected token
	 * @return void
	 */
	protected function unexpectedToken(){
		$this->tokenStream->throwException('Unexpected token: '.json_encode($this->tokenStream->peek()));
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
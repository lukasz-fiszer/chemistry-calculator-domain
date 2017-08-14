<?php

namespace ChemCalc\Domain\Chemistry\Parser;

use stdClass;

/**
 * Chemistry reaction equation
 * Parser token stream
 */
class TokenStream
{
	/**
	 * Input stream used for tokenizing
	 * 
	 * @var InputStream
	 */
	protected $inputStream;

	/**
	 * Current input stream token object
	 * 
	 * @var object
	 */
	protected $current = null;

	/**
	 * List of punctuation characters, brackets in pairs
	 * 
	 * @var string
	 */
	protected $punctuationCharacters = '()[]{}';

	/**
	 * Construct new token stream
	 * 
	 * @param InputStream  $inputStream  input stream used by tokenizer
	 */
	public function __construct(InputStream $inputStream){
		$this->inputStream = $inputStream;
	}

	/**
	 * Get the next token and increment the position pointer
	 * 
	 * @return object next token
	 */
	public function next(){
		$token = $this->current;
		$this->current = null;
		return $token ?: $this->readNext();
	}

	/**
	 * Check the next token
	 * 
	 * @return object the next token
	 */
	public function peek(){
		return $this->current ?: ($this->current = $this->readNext());
	}

	/**
	 * Check if tokenized input stream ended
	 * 
	 * @return bool true if stream ended
	 */
	public function eof(){
		return $this->peek() === null;
	}

	/**
	 * Throw new parser exception on token stream
	 * 
	 * @param  string $message exception message
	 * @param  string $codeKey exception code key
	 * @param  object $mergeContext exception context to be merged in
	 * @throws ParserException parser exception
	 * @return void
	 */
	public function throwException(string $message = '', string $codeKey = null, stdClass $mergeContext = null){
		return $this->inputStream->throwException($message, $codeKey, $mergeContext);
	}

	/**
	 * Read next token
	 * 
	 * @return object next token object
	 */
	protected function readNext(){
		$whitespace = $this->readWhile([$this, 'is_whitespace']);
		if($this->inputStream->eof()){
			return null;
		}
		$character = $this->inputStream->peek();
		if($this->is_digit($character)){
			return $this->readNumber();
		}
		if($this->is_element_identifier_start($character)){
			return $this->readElementIdentifier();
		}
		/*if($this->is_charge_identifier($character)){
			return (object) ['type' => 'charge_identifier', 'value' => $this->inputStream->next()];
		}*/
		if($this->is_punctuation($character)){
			$value = $this->inputStream->next();
			return (object) array_merge(['type' => 'punctuation', 'value' => $value], $this->punctuationData($value));
		}
		if($this->is_operator_character($character)){
			return (object) ['type' => 'operator', 'value' => $this->readWhile([$this, 'is_operator_character'])];
		}
		$this->throwException('Character exception: \''.$character.'\'', 'tokenizer_unrecognized_character', (object) ['character' => $character]);
	}

	/**
	 * Read input stream for element identifier
	 * 
	 * @return object element identifier token object
	 */
	protected function readElementIdentifier(){
		$start = $this->inputStream->next();
		$middle = $this->readWhile([$this, 'is_element_identifier']);
		return (object) ['type' => 'element_identifier', 'value' => $start.$middle];
	}

	/**
	 * Read input stream for a number
	 * 
	 * @return object number token object
	 */
	protected function readNumber(){
		return (object) ['type' => 'number', 'value' => $this->readWhile([$this, 'is_digit'])];
	}

	/**
	 * Read input stream while predicate is true for streamed characters
	 * 
	 * @param  callable $predicate predicate to test on stream characters
	 * @return string              streamed characters accepted by predicate
	 */
	protected function readWhile(callable $predicate){
		$string = '';
		while(!$this->inputStream->eof() && $predicate($this->inputStream->peek())){
			$string .= $this->inputStream->next();
		}
		return $string;
	}

	/**
	 * Get mode and opposite character for punctuation bracket
	 * 
	 * @param  string $punctuation punctuation bracket character
	 * @return array  array with mode and opposite character
	 */
	protected function punctuationData(string $punctuation){
		$mode = strpos($this->punctuationCharacters, $punctuation) % 2 == 0 ? 'open' : 'close';
		$opposite = $this->punctuationCharacters[($pos = strpos($this->punctuationCharacters, $punctuation)) % 2 == 0 ? $pos+1 : $pos-1];
		return ['mode' => $mode, 'opposite' => $opposite];
	}

	/**
	 * Check if given string is element identifier character
	 * 
	 * @param  string  $character character to test
	 * @return bool               true if given character is element identifier middle character
	 */
	protected function is_element_identifier(string $character){
		return ctype_lower($character);
	}

	/**
	 * Check if given string is element identifier start character
	 * 
	 * @param  string  $character character to test
	 * @return bool               true if given character is element start character identifier
	 */
	protected function is_element_identifier_start(string $character){
		return ctype_upper($character) && strlen($character) === 1;
	}

	/**
	 * Check if given string is charge identifier character
	 * 
	 * @param  string  $character character to test
	 * @return bool               true if given character is charge identifier
	 */
	protected function is_charge_identifier(string $character){
		return $character === '+' || $character === '-';
	}

	/**
	 * Check if given string is operator character
	 * 
	 * @param  string  $character character to test
	 * @param  string  $previous  previous characters used in operator, defaults to empty string
	 * @return bool               true if given character is operator character
	 */
	protected function is_operator_character(string $character, string $previous = ''){
		if($previous === ''){
			return strpos('+=<->', $character) !== false;
		}
		return in_array($previous.$character, ['<-', '->', '<->'], true);
	}

	/**
	 * Check if given string is punctuation character
	 * 
	 * @param  string  $character character to test
	 * @return bool               true if given character is punctuation
	 */
	protected function is_punctuation(string $character){
		return strpos($this->punctuationCharacters, $character) !== false;
	}

	/**
	 * Check if given string is whitespace
	 * 
	 * @param  string  $test string to test
	 * @return bool          true if given string is made of whitespace only
	 */
	protected function is_whitespace(string $test){
		return ctype_space($test);
	}

	/**
	 * Check if given string is made of only digits
	 * 
	 * @param  string  $test string to test
	 * @return bool          true if string is made of digits only
	 */
	protected function is_digit(string $test){
		return ctype_digit($test);
	}

    /**
     * Gets the Input stream used for tokenizing.
     *
     * @return InputStream
     */
    public function getInputStream()
    {
        return $this->inputStream;
    }
}
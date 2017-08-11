<?php

namespace ChemCalc\Domain\Chemistry\Parser;

/**
 * Chemistry reaction equation
 * Parser input stream
 */
class InputStream
{
	/**
	 * Input to the stream
	 * 
	 * @var string
	 */
	protected $input;

	/**
	 * Current position in the stream
	 * 
	 * @var integer
	 */
	protected $position = 0;

	/**
	 * Current line in the stream
	 * 
	 * @var integer
	 */
	protected $line = 0;

	/**
	 * Current column in the stream
	 * 
	 * @var integer
	 */
	protected $column = 0;

	/**
	 * Parser exception builder, used when exception is thrown from input stream
	 * 
	 * @var ParserExceptionBuilder
	 */
	protected $parserExceptionBuilder;

	/**
	 * Construct new input stream
	 * 
	 * @param string $input input to the stream
	 * @param ParserExceptionBuilder $parserExceptionBuilder parser exception builder used to build thrown exceptions
	 */
	public function __construct(string $input, ParserExceptionBuilder $parserExceptionBuilder){
		$this->input = $input;
		$this->parserExceptionBuilder = $parserExceptionBuilder;
	}

	/**
	 * Get the next character and increment the position pointer
	 * 
	 * @return string next character
	 */
	public function next(){
		if(!isset($this->input[$this->position])){
			return null;
		}
		$character = $this->input[$this->position++];
		if($character == "\n"){
			$this->line++;
			$this->column = 0;
		}
		else{
			$this->column++;
		}
		return $character;
	}

	/**
	 * Check the next character
	 * 
	 * @return string the next character
	 */
	public function peek(){
		return isset($this->input[$this->position]) ? $this->input[$this->position] : null;
	}

	/**
	 * Check if input stream ended
	 * 
	 * @return bool true if stream ended
	 */
	public function eof(){
		return $this->peek() === null;
	}

	/**
	 * Throw new parser exception
	 * 
	 * @param  string $message exception message
	 * @param  string $codeKey exception code key
	 * @throws ParserException parser exception
	 * @return void
	 */
	public function throwException(string $message = '', string $codeKey = null){
		$exceptionBuilder = $this->parserExceptionBuilder->withMessage($message)->withParserContext($this->getContext());
		if($codeKey !== null){
			$exceptionBuilder = $exceptionBuilder->withCodeByKey($codeKey);
		}
		throw $exceptionBuilder->build();
	}

	/**
	 * Get input context, input and current position, line and column
	 * 
	 * @return object input stream context
	 */
	public function getContext(){
		return (object) ['input' => $this->input, 'position' => $this->position, 'line' => $this->line, 'column' => $this->column];
	}

    /**
     * Gets the Input to the stream.
     *
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Gets the Current position in the stream.
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Gets the Current line in the stream.
     *
     * @return integer
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * Gets the Current column in the stream.
     *
     * @return integer
     */
    public function getColumn()
    {
        return $this->column;
    }
}
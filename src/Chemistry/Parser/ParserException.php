<?php

namespace ChemCalc\Domain\Chemistry\Parser;

use Exception;

/**
 * Chemistry reaction equation
 * Parser exception
 */
class ParserException extends Exception
{
	/**
	 * Input to the parser stream
	 * 
	 * @var string
	 */
	protected $parserInput;

	/**
	 * Current position in the parser stream where exception was thrown
	 * 
	 * @var integer
	 */
	protected $parserPosition;

	/**
	 * Current line in the parser stream where exception was thrown
	 * 
	 * @var integer
	 */
	protected $parserLine;

	/**
	 * Current column in the parser stream where exception was thrown
	 * 
	 * @var integer
	 */
	protected $parserColumn;

	/**
	 * Parser context, has input, line, column and position
	 * 
	 * @var object
	 */
	protected $parserContext;


	/**
	 * Construct new parser exception
	 * 
	 * @param string|null    $message  exception message
	 * @param string|null    $input    parser input
	 * @param integer|null   $position parser position
	 * @param integer|null   $line     parser line
	 * @param integer|null   $column   parser column
	 * @param integer|null   $code     exception code
	 * @param Exception|null $previous previous exception
	 */
	public function __construct(string $message = null, string $input = null, int $position = null, int $line = null, int $column = null, int $code = 0, Exception $previous = null){
		$message = $message !== null ? $message.sprintf(' (line: %d, column: %d)', $line, $column) : null;
		parent::__construct($message, $code, $previous);
		$this->parserInput = $input;
		$this->parserPosition = $position;
		$this->parserLine = $line;
		$this->parserColumn = $column;
		$this->parserContext = (object) ['input' => $input, 'position' => $position, 'line' => $line, 'column' => $column];
	}

    /**
     * Gets the Input to the parser stream.
     *
     * @return string
     */
    public function getParserInput()
    {
        return $this->parserInput;
    }

    /**
     * Gets the Current position in the parser stream where exception was thrown.
     *
     * @return integer
     */
    public function getParserPosition()
    {
        return $this->parserPosition;
    }

    /**
     * Gets the Current line in the parser stream where exception was thrown.
     *
     * @return integer
     */
    public function getParserLine()
    {
        return $this->parserLine;
    }

    /**
     * Gets the Current column in the parser stream where exception was thrown.
     *
     * @return integer
     */
    public function getParserColumn()
    {
        return $this->parserColumn;
    }

    /**
     * Gets the Parser context, has input, line, column and position
     * 
     * @return object
     */
    public function getParserContext()
    {
        return $this->parserContext;
    }
}
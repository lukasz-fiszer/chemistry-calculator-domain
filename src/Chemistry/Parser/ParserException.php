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
	protected $parserPosition = 0;

	/**
	 * Current line in the parser stream where exception was thrown
	 * 
	 * @var integer
	 */
	protected $parserLine = 0;

	/**
	 * Current column in the parser stream where exception was thrown
	 * 
	 * @var integer
	 */
	protected $parserColumn = 0;


	/**
	 * Construct new parser exception
	 * 
	 * @param string|null    $message  exception message
	 * @param string         $input    parser input
	 * @param integer        $position parser position
	 * @param integer        $line     parser line
	 * @param integer        $column   parser column
	 * @param integer        $code     exception code
	 * @param Exception|null $previous previous exception
	 */
	public function __construct(string $message = null, string $input = '', int $position = 0, int $line = 0, int $column = 0, int $code = 0, Exception $previous = null){
		//$message = $message === null ? $message.sprintf(' (line: %d, column: %d)', $line, $column) : null;
		$message = $message !== null ? $message.sprintf(' (line: %d, column: %d)', $line, $column) : null;
		parent::__construct($message, $code, $previous);
		$this->parserInput = $input;
		$this->parserPosition = $position;
		$this->parserLine = $line;
		$this->parserColumn = $column;
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
}
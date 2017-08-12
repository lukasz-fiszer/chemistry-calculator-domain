<?php

namespace ChemCalc\Domain\Chemistry\Parser;

use Exception;
use stdClass;

/**
 * Chemistry reaction equation
 * Parser exception
 */
class ParserException extends Exception
{
	/**
	 * Parser context, has input, line, column and position, with possible additional merged key-value pairs
	 * 
	 * @var object
	 */
	protected $parserContext;

	/**
	 * Construct new parser exception
	 * 
	 * @param string|null    $message       exception message
	 * @param object|null    $parserContext exception parser context
	 * @param integer|null   $code          exception code
	 * @param Exception|null $previous      previous exception
	 */
	public function __construct(string $message = null, stdClass $parserContext = null, int $code = 0, Exception $previous = null){
		parent::__construct($message, $code, $previous);
		$this->parserContext = $parserContext ?? new stdClass();
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
<?php

namespace ChemCalc\Domain\Chemistry\Parser;

use Exception;
use stdClass;

/**
 * Chemistry reaction equation
 * Parser exception builder, immutable object
 */
class ParserExceptionBuilder
{
	/**
	 * Parser context, has input, line, column and position, on when exception was thrown
	 * 
	 * @var object
	 */
	protected $parserContext;

	/**
	 * Exception message
	 * 
	 * @var string
	 */
	protected $message;

	/**
	 * Exception code
	 * 
	 * @var int
	 */
	protected $code;

	/**
	 * Previous exception
	 * 
	 * @var Exception
	 */
	protected $previous;

	/**
	 * Exception codes array
	 * 
	 * @var array
	 */
	protected $codes = [
		'tokenizer_unrecognized_character' => 1,
		'parser_unexpected_token' => 2,
		'parser_expected_other_token' => 4,
	];

	/**
	 * Construct new parser exception builder
	 * 
	 * @param string|null    $message  exception message
	 * @param integer|null   $code     exception code
	 * @param Exception|null $previous previous exception
	 */
	public function __construct(string $message = null, int $code = 0, Exception $previous = null){
		$this->message = $message;
		$this->code = $code;
		$this->previous = $previous;
		$this->parserContext = new stdClass();
	}

	/**
	 * Clone parser exception builder with its parser context, make deep copy
	 */
	public function __clone(){
		$this->parserContext = clone $this->parserContext;
	}

	/**
	 * Build parser exception
	 * 
	 * @return ParserException built parser exception
	 */
	public function build(){
		$message = $this->buildMessage();
		return new ParserException($message, $this->parserContext, $this->code, $this->previous);
	}

	/**
	 * Build exception message
	 * 
	 * @return string|null exception message
	 */
	protected function buildMessage(){
		if($this->message === null){
			return $this->message;
		}
		//$line = $this->parserContext->line !== null ? 'line: '.$this->parserContext->line : null;
		//$column = $this->parserContext->column !== null ? 'column: '.$this->parserContext->column : null;
		$line = null;
		if(isset($this->parserContext->line)){
			$line = 'line: '.$this->parserContext->line;
		}
		$column = null;
		if(isset($this->parserContext->column)){
			$column = 'column: '.$this->parserContext->column;
		}
		$implode = [$line, $column];
		$implode = array_filter($implode, function($entry){
			return $entry !== null;
		});
		$append = '';
		if(count($implode) > 0){
			$append = ' ('.implode(', ', $implode).')';
		}
		return $this->message.$append;
	}

	/**
	 * Return new builder instance with given message
	 * 
	 * @param  string $message exception message
	 * @return self new exception builder instance
	 */
	public function withMessage(string $message){
		$new = clone $this;
		$new->message = $message;
		return $new;
	}

	/**
	 * Return new builder instance with given code
	 * 
	 * @param  int $code exception code
	 * @return self new exception builder instance
	 */
	public function withCode(int $code){
		$new = clone $this;
		$new->code = $code;
		return $new;
	}

	/**
	 * Return new builder instance with given code, specified by its key
	 * 
	 * @param  string $key exception code key
	 * @throws Exception exception thrown when given key does not exist
	 * @return self new exception builder instance
	 */
	public function withCodeByKey(string $key){
		$new = clone $this;
		if(!isset($this->codes[$key])){
			throw new Exception('Unknown code key: \''.$key.'\'');
		}
		$new->code = $this->codes[$key];
		return $new;
	}


	/**
	 * Return new builder instance with previous exception
	 * 
	 * @param  Exception $previous previous exception
	 * @return self new exception builder instance
	 */
	public function withPreviousException(Exception $previous){
		$new = clone $this;
		$new->previous = $previous;
		return $new;
	}

	/**
	 * Return new builder instance with parser input in parser context
	 * 
	 * @param  string $input parser input
	 * @return self new exception builder instance
	 */
	public function withParserInput(string $input){
		$new = clone $this;
		$new->parserContext->input = $input;
		return $new;
	}

	/**
	 * Return new builder instance with parser position in parser context
	 * 
	 * @param  int $position parser position
	 * @return self new exception builder instance
	 */
	public function withParserPosition(int $position){
		$new = clone $this;
		$new->parserContext->position = $position;
		return $new;
	}

	/**
	 * Return new builder instance with parser line in parser context
	 * 
	 * @param  int $line parser line
	 * @return self new exception builder instance
	 */
	public function withParserLine(int $line){
		$new = clone $this;
		$new->parserContext->line = $line;
		return $new;
	}

	/**
	 * Return new builder instance with parser column in parser context
	 * 
	 * @param  int $column parser column
	 * @return self new exception builder instance
	 */
	public function withParserColumn(int $column){
		$new = clone $this;
		$new->parserContext->column = $column;
		return $new;
	}

	/**
	 * Return new builder instance with parser context
	 * 
	 * @param  object $parserContext parser context
	 * @return self new exception builder instance
	 */
	public function withParserContext(stdClass $parserContext){
		$new = clone $this;
		$new->parserContext = $parserContext;
		return $new;
	}
}
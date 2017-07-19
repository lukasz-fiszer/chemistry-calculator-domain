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
	}

	/**
	 * Build parser exception
	 * 
	 * @return ParserException built parser exception
	 */
	public function build(){
		$message = $this->buildMessage();
		$parserContext = (object) array_merge((array) $this->parserContext, ['input' => $this->parserInput, 'position' => $this->parserPosition, 'line' => $this->parserLine, 'column' => $this->parserColumn]);
		//$parserContext = $parserContext == new stdClass() ? null : $parserContext;
		$parserContext = $this->buildParserContext();
		return new ParserException($message, $parserContext, $this->code, $this->previous);
	}

	/**
	 * Build parser context, merge parser input, position, line and column with parser context object
	 * 
	 * @return object parser context
	 */
	protected function buildParserContext(){
		$mergeContext = ['input' => $this->parserInput, 'position' => $this->parserPosition, 'line' => $this->parserLine, 'column' => $this->parserColumn];
		$mergeContext = array_filter($mergeContext, function($entry){
			return $entry !== null;
		});
		return (object) array_merge((array) $this->parserContext, $mergeContext);
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
		$line = $this->parserLine !== null ? 'line: '.$this->parserLine : null;
		$column = $this->parserColumn !== null ? 'column: '.$this->parserColumn : null;
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
	 * Return new builder instance with parser input
	 * 
	 * @param  string $input parser input
	 * @return self new exception builder instance
	 */
	public function withParserInput(string $input){
		$new = clone $this;
		$new->parserInput = $input;
		return $new;
	}

	/**
	 * Return new builder instance with parser position
	 * 
	 * @param  int $position parser position
	 * @return self new exception builder instance
	 */
	public function withParserPosition(int $position){
		$new = clone $this;
		$new->parserPosition = $position;
		return $new;
	}

	/**
	 * Return new builder instance with parser line
	 * 
	 * @param  int $line parser line
	 * @return self new exception builder instance
	 */
	public function withParserLine(int $line){
		$new = clone $this;
		$new->parserLine = $line;
		return $new;
	}

	/**
	 * Return new builder instance with parser column
	 * 
	 * @param  int $column parser column
	 * @return self new exception builder instance
	 */
	public function withParserColumn(int $column){
		$new = clone $this;
		$new->parserColumn = $column;
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
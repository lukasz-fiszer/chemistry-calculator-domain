<?php

namespace ChemCalc\Domain;

use ChemCalc\Domain\Chemistry\Parser\InputStream;
use ChemCalc\Domain\Chemistry\Parser\TokenStream;
use ChemCalc\Domain\Chemistry\Parser\Parser;
use ChemCalc\Domain\Chemistry\Parser\ParserExceptionBuilder;
use stdClass;
use ChemCalc\Domain\Chemistry\Parser\ParserException;
use ChemCalc\Domain\Chemistry\Interpreter\Interpreter;
use ChemCalc\Domain\Chemistry\Entity\MoleculeBuilder;
use ChemCalc\Domain\Chemistry\Entity\ElementFactory;
use ChemCalc\Domain\Chemistry\DataLoader\ElementDataLoader;
use ChemCalc\Domain\Chemistry\Interpreter\InterpreterException;
use ChemCalc\Domain\Chemistry\Solver\MoleculeSolver;
use ChemCalc\Domain\Chemistry\Solver\ReactionEquationSolver;

/**
 * Chemistry entry point for molecule and reaction equation queries
 */
class EntryPoint
{
	/**
	 * Entry point query input
	 * 
	 * @var string
	 */
	protected $input;

	/**
	 * Parser with the query input
	 * 
	 * @var Parser
	 */
	protected $parser;

	/**
	 * Parsed AST object
	 * 
	 * @var object
	 */
	protected $parsed;

	/**
	 * Interpreter with the AST from query input
	 * 
	 * @var Interpreter
	 */
	protected $interpreter;

	/**
	 * Interpreted object
	 * 
	 * @var object
	 */
	protected $interpreted;

	/**
	 * Solver for the interpreted object
	 * 
	 * @var mixed
	 */
	protected $solver;

	/**
	 * Solved object
	 * 
	 * @var mixed
	 */
	protected $solved;

	/**
	 * Construct new entry point for chemistry queries for molecules and reaction equations
	 * 
	 * @param string $input query input
	 */
	public function __construct(string $input){
		$this->input = $input;
	}

	/**
	 * Proceed with query input
	 * 
	 * @return object response object for query input
	 */
	public function proceed(){
		if(count(trim($this->input)) == 0){
			return $this->makeResponse('error', 1, null, 'Empty input');
		}

		$this->parser = $this->makeParser($this->input);
		try{
			$this->parsed = $this->parser->parse();
		}
		catch(ParserException $e){
			return $this->makeResponse('error', $e->getCode() + 100, $e->getParserContext(), $e->getMessage(), $e);
		}

		$this->interpreter = $this->makeInterpreter($this->parsed);
		try{
			$this->interpreted = $this->interpreter->interpret();
		}
		catch(InterpreterException $e){
			return $this->makeResponse('error', $e->getCode() + 200, null, $e->getMessage(), $e);
		}
		if($this->interpreted->type == 'unknown'){
			return $this->makeResponse('error', $this->interpreted->context->code + 250, $this->interpreted->context, $this->interpreted->message, $this->interpreted);
		}

		if($this->interpreted->type == 'molecule'){
			$molecule = $this->interpreted->interpreted[0];
			$this->solver = new MoleculeSolver($molecule);
			return $this->makeResponse('molecule', 2, (object) ['molecule' => $molecule, 'solver' => $this->solver]);
		}

		if($this->interpreted->type == 'reaction_equation'){
			$sides = $this->interpreted->interpreted;
			$this->solver = new ReactionEquationSolver($sides);
			$this->solved = $this->solver->findCoefficients();
			return $this->makeResponse('reaction_equation', 4, (object) ['sides' => $sides, 'solver' => $this->solver, 'solved' => $this->solved]);
		}

		return $this->makeResponse('unknown', 0);
	}

	/**
	 * Get parser for given input
	 * 
	 * @param  string $input input for the parser
	 * @return Parser        parser with given input to be parsed
	 */
	protected function makeParser(string $input){
		return new Parser(new TokenStream(new InputStream($input, new ParserExceptionBuilder())));
	}

	/**
	 * Get interpreter for given parsed AST object
	 * 
	 * @param  object $parsed parsed AST object for interpreter
	 * @return Interpreter    interpreter with given parsed AST object
	 */
	protected function makeInterpreter(object $parsed){
		return new Interpreter($parsed, new MoleculeBuilder(new ElementFactory(new ElementDataLoader())));
	}

	/**
	 * Make response object
	 * 
	 * @param  string           $status   response status
	 * @param  int              $code     response code
	 * @param  stdClass|null    $context  response context
	 * @param  string|null      $message  response message
	 * @param  mixed|null       $previous previous exception or object
	 * @return object                     response object
	 */
	protected function makeResponse(string $status, int $code, stdClass $context = null, string $message = null, $previous = null){
		return (object) ['status' => $status, 'code' => $code, 'context' => $context, 'message' => $message, 'previous' => $previous];
	}

    /**
     * Gets the Entry point query input
     * 
     * @return string
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Gets the Parser with the query input
     * 
     * @return Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Gets the Interpreter with the AST from query input
     * 
     * @return Interpreter
     */
    public function getInterpreter()
    {
        return $this->interpreter;
    }

    /**
     * Gets the Solver for the interpreted object
     * 
     * @return mixed
     */
    public function getSolver()
    {
        return $this->solver;
    }

    /**
     * Gets the Parsed AST object
     * 
     * @return object
     */
    public function getParsed()
    {
        return $this->parsed;
    }

    /**
     * Gets the Interpreted object
     * 
     * @return object
     */
    public function getInterpreted()
    {
        return $this->interpreted;
    }

    /**
     * Gets the Solved object
     * 
     * @return mixed
     */
    public function getSolved()
    {
        return $this->solved;
    }
}
<?php

namespace ChemCalc\Domain\Chemistry\Interpreter;

use stdClass;
use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\MoleculeBuilder;

/**
 * Chemistry reaction equation
 * Interpreter
 */
class Interpreter
{
	/**
	 * Parsed AST for interpreter
	 * 
	 * @var object
	 */
	protected $ast;

	/**
	 * Molecule builder used to make molecules
	 * 
	 * @var MoleculeBuilder
	 */
	protected $moleculeBuilder;

	/**
	 * Construct new interpreter
	 * 
	 * @param stdClass        $ast             parsed AST for interpreter
	 * @param MoleculeBuilder $moleculeBuilder molecule builder used to make molecules
	 */
	public function __construct(stdClass $ast, MoleculeBuilder $moleculeBuilder){
		$this->ast = $ast;
		$this->moleculeBuilder = $moleculeBuilder;
	}

	/**
	 * Interpret given AST of the object
	 * 
	 * @return object interpreted results of type equation, molecule or unknown
	 */
	public function interpret(){
		$nodes = $this->ast->nodes;
		if(count($nodes) <= 0){
			return $this->getUnknownResult('No nodes', (object) ['code' => 1]);
		}

		if(count($nodes) == 1 && $nodes[0]->type == 'molecule'){
			return (object) ['type' => 'molecule', 'interpreted' => $this->interpretNodes($nodes)];
		}

		$sidesCount = 1;
		$end = false;
		for($i = 0; $i < count($nodes); $i++){
			if($nodes[$i]->type != 'molecule'){
				return $this->getUnknownResult('Expected molecule node at '.$i.' node instead of: '.json_encode($nodes[$i]), (object) ['at' => $i, 'expectedType' => 'molecule', 'actual' => $nodes[$i], 'code' => 2]);
			}
			$end = true;
			if(++$i >= count($nodes)){
				break;
			}

			if($nodes[$i]->type != 'operator'){
				return $this->getUnknownResult('Expected operator node at '.$i.' node instead of: '.json_encode($nodes[$i]), (object) ['at' => $i, 'expectedType' => 'operator', 'actual' => $nodes[$i], 'code' => 3]);
			}
			$end = false;
			if($nodes[$i]->mode == 'side_equality'){
				$sidesCount++;
			}
		}

		if($sidesCount < 2){
			return $this->getUnknownResult('Too few sides ('.$sidesCount.')', (object) ['sidesCount' => $sidesCount, 'code' => 4]);
		}
		if($sidesCount > 2){
			return $this->getUnknownResult('Too many sides ('.$sidesCount.')', (object) ['sidesCount' => $sidesCount, 'code' => 5]);
		}
		if($end === false){
			return $this->getUnknownResult('Operator should be followed by molecule',  (object) ['at' => $i, 'code' => 6]);
		}

		$interpreted = $this->interpretNodes($nodes);

		$sides = [[]];
		$i = 0;
		foreach($interpreted as $node){
			if($node instanceof stdClass && $node->type == 'operator' && $node->mode == 'side_equality'){
				$sides[++$i] = [];
			}
			if($node instanceof Molecule){
				$sides[$i][] = $node;
			}
		}
		if(count($sides) == 2){
			if(count($sides[0]) > 0 && count($sides[1]) > 0){
				return (object) ['type' => 'reaction_equation', 'interpreted' => $sides];
			}
			else{
				return $this->getUnknownResult('Missing molecules on either side of reaction equation');
			}
		}
		
		return $this->getUnknownResult('Nodes do not represent molecule or reaction equation');
	}

	/**
	 * Interpret nodes
	 * 
	 * @param  array  $nodes array of nodes
	 * @return array  array of interpreted nodes, plus count and sides count
	 */
	protected function interpretNodes(array $nodes){
		return array_map([$this, 'interpretNode'], $nodes);
	}

	/**
	 * Interpret given node
	 * 
	 * @param  stdClass $node node to interpret
	 * @throws InterpreterException exception thrown for unknown node
	 * @return object interpreted results for given node
	 */
	protected function interpretNode(stdClass $node){
		if($node->type == 'operator'){
			return $node;
		}
		if($node->type == 'molecule'){
			return $this->makeMolecule($node);
		}
		throw new InterpreterException('Unknown node: '.json_encode($node));
	}

	/**
	 * Make molecule object out of molecule node
	 * 
	 * @param  stdClass $moleculeNode molecule node
	 * @return Molecule molecule object
	 */
	protected function makeMolecule(stdClass $moleculeNode){
		return $this->extractMoleculeNodeToBuilder($moleculeNode)->build();
	}

	/**
	 * Extract molecule node to builder, return molecule builder prepared with inner part of molecule node
	 * 
	 * @param  stdClass $moleculeNode molecule node
	 * @throws InterpreterException exception thrown for unknown molecule node entry
	 * @return MoleculeBuilder extracted molecule builder
	 */
	protected function extractMoleculeNodeToBuilder(stdClass $moleculeNode){
		$moleculeBuilder = $this->moleculeBuilder;
		foreach($moleculeNode->entries as $entry){
			if($entry->type == 'element'){
				$moleculeBuilder = $moleculeBuilder->withElement($entry->entry->value, $entry->occurences);
			}
			else if($entry->type == 'charge'){
				$moleculeBuilder = $moleculeBuilder->withElement($entry->value, $entry->occurences);
			}
			else if($entry->type == 'molecule'){
				$molecule = $this->extractMoleculeNodeToBuilder($entry);
				$moleculeBuilder = $moleculeBuilder->withBuilder($molecule, $entry->delimited ?? null, $entry->occurences);
			}
			else{
				throw new InterpreterException('Unknown molecule node entry: '.json_encode($entry));
			}
		}
		return $moleculeBuilder;
	}

	/**
	 * Get result object of type unknown, given message and given context
	 * 
	 * @param  string        $message object message
	 * @param  stdClass|null $context object context
	 * @return object interpretation result object
	 */
	protected function getUnknownResult(string $message, stdClass $context = null){
		return (object) ['type' => 'unknown', 'message' => $message, 'context' => $context];
	}

    /**
     * Gets the Parsed AST for interpreter.
     *
     * @return object
     */
    public function getAst()
    {
        return $this->ast;
    }
}
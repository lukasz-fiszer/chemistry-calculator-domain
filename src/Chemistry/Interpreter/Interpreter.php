<?php

namespace ChemCalc\Domain\Chemistry\Interpreter;

use stdClass;
use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\ElementFactory;
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
			return $this->getUnknownResult('No nodes');
		}

		$interpreted = $this->interpretNodes($nodes);

		if(count($interpreted) == 1 && $interpreted[0] instanceof Molecule){
			return (object) ['type' => 'molecule', 'interpreted' => $interpreted];
		}

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
		return $this->extractMoleculeNode($moleculeNode)->build();

		$extractedMolecule = $this->extractMoleculeNode($moleculeNode);
		$moleculeBuilder = $this->moleculeBuilder;
		//$elements = [];
		foreach($extractedMolecule['elements'] as $symbol => $occurences){
			//$elements[] = ['element' => $this->makeElement($symbol), 'occurences' => $occurences];
			$moleculeBuilder = $moleculeBuilder->withElement($symbol, $occurences);
		}
		//$molecule = new Molecule($elements, $extractedMolecule['formula']);
		//return $molecule;
		return $moleculeBuilder->withFormula($extractedMolecule['formula'])->build();
	}

	/**
	 * Make element object given its symbol
	 * 
	 * @param  string $symbol element symbol
	 * @return Element element object
	 */
	protected function makeElement(string $symbol){
		return $this->elementFactory->makeElementBySymbol($symbol);
	}

	/**
	 * Extract molecule node, get elements and their occurences for given molecule node and its string formula
	 * 
	 * @param  stdClass $moleculeNode molecule node
	 * @throws InterpreterException exception thrown for unknown molecule node entry
	 * @return array array of elements and their occurences and molecule string formula
	 */
	protected function extractMoleculeNode(stdClass $moleculeNode){
		$moleculeBuilder = $this->moleculeBuilder;
		foreach($moleculeNode->entries as $entry){
			if($entry->type == 'element'){
				$moleculeBuilder = $moleculeBuilder->withElement($entry->entry->value, $entry->occurences);
			}
			else if($entry->type == 'charge'){
				$moleculeBuilder = $moleculeBuilder->withElement($entry->value, $entry->occurences);
			}
			else if($entry->type == 'molecule'){
				$molecule = $this->extractMoleculeNode($entry);
				$moleculeBuilder = $moleculeBuilder->withBuilder($molecule, $entry->delimited ?? null, $entry->occurences);
			}
			else{
				throw new InterpreterException('Unknown molecule node entry: '.json_encode($entry));
			}
		}
		return $moleculeBuilder;
		//return $moleculeBuilder->build();

		$elements = [];
		$molecules = [];
		$formula = '';
		foreach($moleculeNode->entries as $entry){
			if($entry->type == 'element'){
				isset($elements[$entry->entry->value]) ? null : $elements[$entry->entry->value] = 0;
				$elements[$entry->entry->value] += $entry->occurences * $moleculeNode->occurences;
				$formula .= $entry->occurences != 1 ? $entry->entry->value.$entry->occurences : $entry->entry->value;
			}
			else if($entry->type == 'charge'){
				isset($elements[$entry->value]) ? null : $elements[$entry->value] = 0;
				$elements[$entry->value] += $entry->occurences * $moleculeNode->occurences;
				$formula .= $entry->occurences != 1 ? $entry->value.$entry->occurences : $entry->value;
			}
			else if($entry->type == 'molecule'){
				$molecule = $this->extractMoleculeNode($entry);
				$molecules[] = $molecule['elements'];
				$formula .= $molecule['formula'];
			}
			else{
				throw new InterpreterException('Unknown molecule node entry: '.json_encode($entry));
			}
		}
		foreach($molecules as $molecule){
			foreach($molecule as $element => $occurences){
				isset($elements[$element]) ? null : $elements[$element] = 0;
				$elements[$element] += $occurences * $moleculeNode->occurences;
			}
		}
		if(isset($moleculeNode->delimited)){
			$formula = $moleculeNode->delimited->value.$formula.$moleculeNode->delimited->opposite.($moleculeNode->occurences != 1 ? $moleculeNode->occurences : null);
		}
		return ['elements' => $elements, 'formula' => $formula];
	}

	/**
	 * Get result object of type unknown and given message
	 * 
	 * @param  string $message object message
	 * @return object interpretation result object
	 */
	protected function getUnknownResult(string $message){
		return (object) ['type' => 'unknown', 'message' => $message];
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
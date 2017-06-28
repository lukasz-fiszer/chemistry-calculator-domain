<?php

namespace ChemCalc\Domain\Chemistry\Interpreter;

use stdClass;
use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\Element;
use ChemCalc\Domain\Chemistry\DataLoader\ElementDataLoader;

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
	 * Construct new interpreter
	 * 
	 * @param stdClass $ast parsed AST for interpreter
	 */
	public function __construct(stdClass $ast){
		$this->ast = $ast;
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

		$interpretedNodes = $this->interpretNodes($nodes);
		list($interpreted, $plusCount, $sidesCount, $moleculesCount) = array_values($interpretedNodes);

		if(count($interpreted) == 1 && $moleculesCount == 1 && $plusCount == 0 && $sidesCount == 1){
			return (object) ['type' => 'molecule', 'interpreted' => $interpreted];
		}
		if($sidesCount == 2 && $moleculesCount > 1){
			$sides = [];
			$i = 0;
			foreach($interpreted as $interpretedNode){
				if($interpretedNode instanceof stdClass && $interpretedNode->type == 'operator' && $interpretedNode->mode == 'side_equality'){
					$i++;
				}
				if($interpretedNode instanceof Molecule){
					isset($sides[$i]) ? null : $sides[$i] = [];
					$sides[$i][] = $interpretedNode;
				}
			}
			if(count($sides) == 2 && count($sides[0]) > 0 && count($sides[1]) > 0){
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
		$interpreted = [];
		$plusCount = 0;
		$sidesCount = 1;
		$moleculesCount = 0;
		foreach($nodes as $node){
			$interpretedNode = $this->interpretNode($node);
			if($interpretedNode instanceof stdClass && $interpretedNode->type == 'operator'){
				$interpretedNode->mode == 'plus' ? $plusCount++ : null;
				$interpretedNode->mode == 'side_equality' ? $sidesCount++ : null;
			}
			if($interpretedNode instanceof Molecule){
				$moleculesCount++;
			}
			$interpreted[] = $interpretedNode;
		}
		return ['interpreted' => $interpreted, 'plusCount' => $plusCount, 'sidesCount' => $sidesCount, 'moleculesCount' => $moleculesCount];
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
		$extractedMolecule = $this->extractMoleculeNode($moleculeNode);
		$elements = [];
		foreach($extractedMolecule['elements'] as $symbol => $occurences){
			$elements[] = ['element' => $this->makeElement($symbol), 'occurences' => $occurences];
			//$elements[] = ['element' => $this->makeElement($symbol), 'occurences' => $occurences * $moleculeNode->occurences];
		}
		$molecule = new Molecule($elements, $extractedMolecule['formula']);
		return $molecule;
	}

	/**
	 * Make element object given its symbol
	 * 
	 * @param  string $symbol element symbol
	 * @return Element element object
	 */
	protected function makeElement(string $symbol){
		$dataLoader = new ElementDataLoader();
		$elementData = $dataLoader->getDataForElementBySymbol($symbol);
		return $elementData === null ? new Element('unknown', $symbol, 0, false) : new Element($elementData['name'], $symbol, $elementData['atomic_mass'], true, $elementData);
	}

	/**
	 * Extract molecule node, get elements and their occurences for given molecule node and its string formula
	 * 
	 * @param  stdClass $moleculeNode molecule node
	 * @throws InterpreterException exception thrown for unknown molecule node entry
	 * @return array array of elements and their occurences and molecule string formula
	 */
	//protected function getElementsOccurencesForMolecule(stdClass $moleculeNode){
	protected function extractMoleculeNode(stdClass $moleculeNode){
		$elements = [];
		$molecules = [];
		$formula = '';
		foreach($moleculeNode->entries as $entry){
			if($entry->type == 'element'){
				isset($elements[$entry->entry->value]) ? null : $elements[$entry->entry->value] = 0;
				//$elements[$entry->entry->value] += $entry->occurences;
				$elements[$entry->entry->value] += $entry->occurences * $moleculeNode->occurences;
				$formula .= $entry->occurences != 1 ? $entry->entry->value.$entry->occurences : $entry->entry->value;
			}
			else if($entry->type == 'charge'){
				isset($elements[$entry->value]) ? null : $elements[$entry->value] = 0;
				$elements[$entry->value] += $entry->occurences * $moleculeNode->occurences;
				//$formula .= $entry->value.$entry->occurences;
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
				//$elements[$element] += $occurences;
				$elements[$element] += $occurences * $moleculeNode->occurences;
			}
		}
		if(isset($moleculeNode->delimited)){
		//if(isset($moleculeNode->delimited) && $moleculeNode->occurences != 1){
			//$formula = $moleculeNode->delimited->value.$formula.$moleculeNode->delimited->opposite.$moleculeNode->occurences;
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
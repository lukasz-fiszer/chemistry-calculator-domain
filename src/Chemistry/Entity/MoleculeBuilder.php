<?php

namespace ChemCalc\Domain\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\ElementFactory;

/**
 * Chemistry molecule builder
 * Immutable object
 */
class MoleculeBuilder
{
	/**
	 * Array of molecule elements and their occurences for molecule being built
	 * 
	 * @var array
	 */
	protected $elements;

	/**
	 * Molecule formula
	 * 
	 * @var string
	 */
	protected $formula;

	/**
	 * Molecule charge
	 * 
	 * @var int
	 */
	protected $charge;

	/**
	 * Element factory used for making elements when building molecule
	 * 
	 * @var ElementFactory
	 */
	protected $elementFactory;

	/**
	 * Construct new immutable molecule builder object
	 *
	 * @param ElementFactory $elementFactory element factory used to make elements
	 * @param array          $elements       array of molecule elements and their occurences
	 * @param string         $formula        molecule formula
	 * @param int            $charge         molecule charge
	 */
	public function __construct(ElementFactory $elementFactory, array $elements = [], string $formula = '', int $charge = 0){
		$this->elementFactory = $elementFactory;
		$this->elements = $elements;
		$this->formula = $formula;
		$this->charge = $charge;
	}

	/**
	 * Build molecule instance
	 * 
	 * @return Molecule built molecule
	 */
	public function build(){
		$elementEntries = [];
		foreach($this->elements as $symbol => $occurences){
			$elementEntries[] = ['element' => $this->elementFactory->makeElementBySymbol($symbol), 'occurences' => $occurences];
		}
		return new Molecule($elementEntries, $this->formula, $this->charge);
	}

	/**
	 * Add element and its occurences to the builder
	 * 
	 * @param  string $symbol     element symbol
	 * @param  int    $occurences element occurences
	 * @return self  cloned builder
	 */
	public function withElement(string $symbol, int $occurences){
		$new = clone $this;

		$new = $this->addElement($new, $symbol, $occurences);
		$new->formula .= $this->buildFormulaFragment($symbol, $occurences);

		return $new;
	}

	/**
	 * Add charge and its occurences to the builder
	 * 
	 * @param  string $symbol charge symbol
	 * @param  int    $charge charge occurences
	 * @return self  cloned builder
	 */
	public function withCharge(string $symbol, int $charge){
		$new = clone $this;

		$new->charge += $charge;
		$new->formula .= $this->buildFormulaFragment($symbol, abs($charge));

		return $new;
	}

	/**
	 * Add builder, merge it into current immutable builder clone
	 * 
	 * @param  self   $moleculeBuilder molecule builder
	 * @return self                    new cloned builder merged
	 */
	public function withBuilder(self $moleculeBuilder){
		$new = clone $this;

		foreach($moleculeBuilder->elements as $element => $occurences){
			$new = $this->addElement($new, $element, $occurences);
		}
		$new->charge += $moleculeBuilder->charge;
		$new->formula .= $this->buildFormulaFragment($moleculeBuilder->formula);

		return $new;
	}

	/**
	 * Add element and its occurences to the builder instance
	 * 
	 * @param  self   $moleculeBuilder molecule builder instance
	 * @param  string $symbol          element symbol
	 * @param  int    $occurences      element occurences
	 * @return self                    builder instance with element added
	 */
	protected function addElement(self $moleculeBuilder, string $symbol, int $occurences){
		if(!isset($moleculeBuilder->elements[$symbol])){
			$moleculeBuilder->elements[$symbol] = 0;
		}
		$moleculeBuilder->elements[$symbol] += $occurences;
		return $moleculeBuilder;
	}

	/**
	 * Build formula fragment
	 * 
	 * @param  string $formulaFragment formula fragment
	 * @param  int    $occurences      formula fragment occurences
	 * @return string                  built formula fragment
	 */
	protected function buildFormulaFragment(string $formulaFragment, int $occurences = 1){
		return $formulaFragment.($occurences != 1 ? $occurences : '');
	}
}
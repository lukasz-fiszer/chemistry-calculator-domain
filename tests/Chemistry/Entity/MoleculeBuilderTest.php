<?php

namespace ChemCalc\Domain\Tests\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\Molecule;
use ChemCalc\Domain\Chemistry\Entity\Element;
use ChemCalc\Domain\Chemistry\Entity\MoleculeBuilder;
use ChemCalc\Domain\Chemistry\Entity\ElementFactory;

class MoleculeBuilderTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(){
		if(isset($this->initialized) && $this->initialized == true){
			return;
		}
		$this->initialized = true;
		$this->h = new Element('Hydrogen', 'H', 1.008);
		$this->o = new Element('Oxygen', 'O', 15.999);
		$this->h2o = new Molecule([
			['element' => $this->h, 'occurences' => 2],
			['element' => $this->o, 'occurences' => 1]
		], 'H2O');
		$this->f = new Element('Fictious Element', 'Fic', 20.40, false);
		$this->molecule2 = new Molecule([
			['element' => $this->h, 'occurences' => 2],
			['element' => $this->f, 'occurences' => 2]
		], 'Fic2H2');
	}

	public function testConstructorPropertiesInjection(){
		$elementFactoryMock = $this->createMock(ElementFactory::class);
		$moleculeBuilder = new MoleculeBuilder($elementFactoryMock);
		$this->assertAttributeEquals($elementFactoryMock, 'elementFactory', $moleculeBuilder);
		$this->assertAttributeEquals([], 'elements', $moleculeBuilder);
		$this->assertAttributeEquals('', 'formula', $moleculeBuilder);
		$this->assertAttributeEquals(0, 'charge', $moleculeBuilder);
		$moleculeBuilder2 = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O', 10);
		$this->assertAttributeEquals($elementFactoryMock, 'elementFactory', $moleculeBuilder2);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1], 'elements', $moleculeBuilder2);
		$this->assertAttributeEquals('H2O', 'formula', $moleculeBuilder2);
		$this->assertAttributeEquals(10, 'charge', $moleculeBuilder2);
	}

	public function testWithElementMethod(){
		$elementFactoryMock = $this->createMock(ElementFactory::class);
		$moleculeBuilder = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O');

		$builder2 = $moleculeBuilder->withElement('FicSym', 10);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1], 'elements', $moleculeBuilder);
		$this->assertAttributeEquals('H2O', 'formula', $moleculeBuilder);
		$this->assertAttributeEquals(0, 'charge', $moleculeBuilder);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1, 'FicSym' => 10], 'elements', $builder2);
		$this->assertAttributeEquals('H2OFicSym10', 'formula', $builder2);
		$this->assertAttributeEquals(0, 'charge', $builder2);

		$builder3 = $builder2->withElement('H', 2);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1, 'FicSym' => 10], 'elements', $builder2);
		$this->assertAttributeEquals('H2OFicSym10', 'formula', $builder2);
		$this->assertAttributeEquals(0, 'charge', $builder2);
		$this->assertAttributeEquals(['H' => 4, 'O' => 1, 'FicSym' => 10], 'elements', $builder3);
		$this->assertAttributeEquals('H2OFicSym10H2', 'formula', $builder3);
		$this->assertAttributeEquals(0, 'charge', $builder3);

		$builder4 = $moleculeBuilder->withElement('FicSym', 1);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1, 'FicSym' => 1], 'elements', $builder4);
		$this->assertAttributeEquals('H2OFicSym', 'formula', $builder4);
		$this->assertAttributeEquals(0, 'charge', $builder4);
	}

	public function testWithChargeMethod(){
		$elementFactoryMock = $this->createMock(ElementFactory::class);
		$moleculeBuilder = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O');

		$builder2 = $moleculeBuilder->withCharge('+', 10);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1], 'elements', $moleculeBuilder);
		$this->assertAttributeEquals('H2O', 'formula', $moleculeBuilder);
		$this->assertAttributeEquals(0, 'charge', $moleculeBuilder);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1], 'elements', $builder2);
		$this->assertAttributeEquals('H2O+10', 'formula', $builder2);
		$this->assertAttributeEquals(10, 'charge', $builder2);

		$builder3 = $builder2->withCharge('-', -20);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1], 'elements', $builder2);
		$this->assertAttributeEquals('H2O+10', 'formula', $builder2);
		$this->assertAttributeEquals(10, 'charge', $builder2);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1], 'elements', $builder3);
		$this->assertAttributeEquals('H2O+10-20', 'formula', $builder3);
		$this->assertAttributeEquals(-10, 'charge', $builder3);

		$builder4 = $moleculeBuilder->withCharge('+', 1);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1], 'elements', $builder4);
		$this->assertAttributeEquals('H2O+', 'formula', $builder4);
		$this->assertAttributeEquals(1, 'charge', $builder4);
	}

	public function testWithBuilderMethod(){
		$elementFactoryMock = $this->createMock(ElementFactory::class);
		$moleculeBuilder = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O');

		$testBuilder = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O', 10);
		$builder2 = $moleculeBuilder->withBuilder($testBuilder);
		$this->assertAttributeEquals(['H' => 2, 'O' => 1], 'elements', $moleculeBuilder);
		$this->assertAttributeEquals('H2O', 'formula', $moleculeBuilder);
		$this->assertAttributeEquals(0, 'charge', $moleculeBuilder);
		$this->assertAttributeEquals(['H' => 4, 'O' => 2], 'elements', $builder2);
		$this->assertAttributeEquals('H2OH2O', 'formula', $builder2);
		$this->assertAttributeEquals(10, 'charge', $builder2);

		$testBuilder2 = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 2, 'FicSym' => 2], 'H2O2FicSym2', -1);
		$builder3 = $builder2->withBuilder($testBuilder2);
		$this->assertAttributeEquals(['H' => 4, 'O' => 2], 'elements', $builder2);
		$this->assertAttributeEquals('H2OH2O', 'formula', $builder2);
		$this->assertAttributeEquals(10, 'charge', $builder2);
		$this->assertAttributeEquals(['H' => 6, 'O' => 4, 'FicSym' => 2], 'elements', $builder3);
		$this->assertAttributeEquals('H2OH2OH2O2FicSym2', 'formula', $builder3);
		$this->assertAttributeEquals(9, 'charge', $builder3);
	}

	public function testBuildMethod(){
		$elementFactoryMock = $this->createMock(ElementFactory::class);
		$elementFactoryMock->expects($this->at(0))->method('makeElementBySymbol')->willReturn($this->h);
		$elementFactoryMock->expects($this->at(1))->method('makeElementBySymbol')->willReturn($this->o);
		$moleculeBuilder = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O');
		$builtMolecule = $moleculeBuilder->build();
		$this->assertEquals($this->h2o, $builtMolecule);

		$elementFactoryMock = $this->createMock(ElementFactory::class);
		$elementFactoryMock->expects($this->at(0))->method('makeElementBySymbol')->willReturn($this->h);
		$elementFactoryMock->expects($this->at(1))->method('makeElementBySymbol')->willReturn($this->o);
		$nEl = new Element('Nitrogen', 'N', 14.007);
		$elementFactoryMock->expects($this->at(2))->method('makeElementBySymbol')->willReturn($nEl);
		$moleculeBuilder = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O');
		$moleculeBuilder = $moleculeBuilder->withElement('N', 2);
		$builtMolecule = $moleculeBuilder->build();
		$expected = new Molecule([['element' => $this->h, 'occurences' => 2], ['element' => $this->o, 'occurences' => 1], ['element' => $nEl, 'occurences' => 2]], 'H2ON2');
		$this->assertEquals($expected, $builtMolecule);

		$elementFactoryMock = $this->createMock(ElementFactory::class);
		$elementFactoryMock->expects($this->at(0))->method('makeElementBySymbol')->willReturn($this->h);
		$elementFactoryMock->expects($this->at(1))->method('makeElementBySymbol')->willReturn($this->o);
		$moleculeBuilder = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O{-2}', -2);
		$builtMolecule = $moleculeBuilder->build();
		$expected = new Molecule([['element' => $this->h, 'occurences' => 2], ['element' => $this->o, 'occurences' => 1]], 'H2O{-2}', -2);
		$this->assertEquals($expected, $builtMolecule);

		$elementFactoryMock = $this->createMock(ElementFactory::class);
		$elementFactoryMock->expects($this->at(0))->method('makeElementBySymbol')->willReturn($this->h);
		$elementFactoryMock->expects($this->at(1))->method('makeElementBySymbol')->willReturn($this->o);
		$moleculeBuilder = new MoleculeBuilder($elementFactoryMock, ['H' => 2, 'O' => 1], 'H2O{+2}', 2);
		$builtMolecule = $moleculeBuilder->build();
		$expected = new Molecule([['element' => $this->h, 'occurences' => 2], ['element' => $this->o, 'occurences' => 1]], 'H2O{+2}', 2);
		$this->assertEquals($expected, $builtMolecule);
	}
}
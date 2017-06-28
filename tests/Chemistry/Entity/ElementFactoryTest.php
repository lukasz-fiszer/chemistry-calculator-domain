<?php

namespace ChemCalc\Domain\Tests\Chemistry\Entity;

use ChemCalc\Domain\Chemistry\Entity\Element;
use ChemCalc\Domain\Chemistry\Entity\ElementFactory;
use ChemCalc\Domain\Chemistry\DataLoader\Interfaces\ElementDataLoader;

class ElementFactoryTest extends \PHPUnit\Framework\TestCase
{
	public function setUp(){
		if(isset($this->initialized) && $this->initialized == true){
			return;
		}
		$this->initialized = true;
		$this->dataJsonPath = realpath(dirname(__FILE__)).'/../../../res/PeriodicTableJSON.json';
		$this->elementsData = json_decode(file_get_contents($this->dataJsonPath), true)['elements'];
	}

	public function testConstructorPropertiesInjection(){
		$elementDataLoader = $this->createMock(ElementDataLoader::class);
		$elementFactory = new ElementFactory($elementDataLoader);
		$this->assertAttributeEquals($elementDataLoader, 'elementDataLoader', $elementFactory);
		$this->assertEquals($elementDataLoader, $elementFactory->getElementDataLoader());
	}

	public function testMakeElementBySymbol(){
		$loaderMock = $this->createMock(ElementDataLoader::class);
		$hData = $this->elementsData[0];
		$oData = $this->elementsData[7];
		$loaderMock->expects($this->at(0))->method('getDataForElementBySymbol')->willReturn($hData);
		$loaderMock->expects($this->at(1))->method('getDataForElementBySymbol')->willReturn($oData);
		$loaderMock->expects($this->at(2))->method('getDataForElementBySymbol')->willReturn(null);
		$loaderMock->expects($this->at(3))->method('getDataForElementBySymbol')->willReturn(null);
		$loaderMock->expects($this->at(4))->method('getDataForElementBySymbol')->willReturn(null);
		$loaderMock->expects($this->at(5))->method('getDataForElementBySymbol')->willReturn(null);

		$factory = new ElementFactory($loaderMock);

		$h = $factory->makeElementBySymbol('H');
		$this->assertEquals(new Element('Hydrogen', 'H', 1.008, true, $hData), $h);
		$o = $factory->makeElementBySymbol('O');
		$this->assertEquals(new Element('Oxygen', 'O', 15.999, true, $oData), $o);

		$ficSym = $factory->makeElementBySymbol('FicSym');
		$this->assertEquals(new Element('unknown', 'FicSym', 0, false), $ficSym);
		$fic = $factory->makeElementBySymbol('Fic');
		$this->assertEquals(new Element('unknown', 'Fic', 0, false), $fic);
		$empty = $factory->makeElementBySymbol('');
		$this->assertEquals(new Element('unknown', '', 0, false), $empty);
		$space = $factory->makeElementBySymbol(' ');
		$this->assertEquals(new Element('unknown', ' ', 0, false), $space);
	}
}
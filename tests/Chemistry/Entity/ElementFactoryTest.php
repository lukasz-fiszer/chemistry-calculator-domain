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
		//$loaderMock->expects($this->any())->method('getDataForElementBySymbol');
		//$loaderMock->expects($this->any())->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'));
		$hData = $this->elementsData[0];
		$oData = $this->elementsData[7];

		$factory = new ElementFactory($loaderMock);

		//$loaderMock->expects($this->at(0))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->will($this->returnValue('a'));
		// $loaderMock->expects($this->at(0))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn('a');
		//$loaderMock->expects($this->at(0))->method('getDataForElementBySymbol')->willReturn('a');
		//$loaderMock->expects($this->at(0))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn($hData);
		//$loaderMock->expects($this->at(0))->method('getDataForElementBySymbol', 'getDataForElement')->willReturn($hData);
		$loaderMock->expects($this->at(0))->method('getDataForElementBySymbol')->willReturn($hData);
		$loaderMock->expects($this->at(1))->method('getDataForElementBySymbol')->willReturn($oData);
		//$loaderMock->expects($this->at(1))->method($this->logicalAnd($this->logicalOr('getDataForElementBySymbol'), $this->logicalOr('getDataForElement')))->willReturn($hData);
		$h = $factory->makeElementBySymbol('H');
		$this->assertEquals(new Element('Hydrogen', 'H', 1.008, true, $hData), $h);
		//$loaderMock->expects($this->at(1))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn($oData);
		//$loaderMock->expects($this->at(1))->method($this->logicalAnd('getDataForElementBySymbol', 'getDataForElement'))->willReturn($oData);
		//$loaderMock->expects($this->at(1))->method($this->logicalAnd($this->logicalOr('getDataForElementBySymbol'), $this->logicalOr('getDataForElement')))->willReturn($oData);
		//$loaderMock->expects($this->at(1))->method('getDataForElementBySymbol', 'getDataForElement')->willReturn($oData);
		//$loaderMock->expects($this->at(1))->method('getDataForElementBySymbol')->willReturn($oData);
		//var_dump($loaderMock->getDataForElementBySymbol('O'));
		$o = $factory->makeElementBySymbol('O');
		$this->assertEquals(new Element('Oxygen', 'O', 15.999, true, $oData), $o);

		//$loaderMock->expects($this->at(2))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn([]);
		//$loaderMock->expects($this->at(2))->method('getDataForElementBySymbol', 'getDataForElement')->willReturn(null);
		//$loaderMock->expects($this->at(2))->method('getDataForElementBySymbol')->willReturn(null);
		$ficSym = $factory->makeElementBySymbol('FicSym');
		$this->assertEquals(new Element('unknown', 'FicSym', 0, false), $ficSym);
		// $loaderMock->expects($this->at(3))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn([]);
		//$loaderMock->expects($this->at(3))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn(null);
		//$loaderMock->expects($this->at(3))->method('getDataForElementBySymbol', 'getDataForElement')->willReturn(null);
		//$loaderMock->expects($this->at(3))->method('getDataForElementBySymbol')->willReturn(null);
		$fic = $factory->makeElementBySymbol('Fic');
		$this->assertEquals(new Element('unknown', 'Fic', 0, false), $fic);
		// $loaderMock->expects($this->at(4))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn([]);
		//$loaderMock->expects($this->at(4))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn(null);
		//$loaderMock->expects($this->at(4))->method('getDataForElementBySymbol')->willReturn(null);
		$empty = $factory->makeElementBySymbol('');
		$this->assertEquals(new Element('unknown', '', 0, false), $empty);
		//$loaderMock->expects($this->at(5))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn([]);
		//$loaderMock->expects($this->at(5))->method($this->logicalOr('getDataForElementBySymbol', 'getDataForElement'))->willReturn(null);
		//$loaderMock->expects($this->at(5))->method('getDataForElementBySymbol')->willReturn(null);
		$space = $factory->makeElementBySymbol(' ');
		$this->assertEquals(new Element('unknown', ' ', 0, false), $space);
	}
}
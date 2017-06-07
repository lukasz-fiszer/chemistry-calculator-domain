<?php

namespace ChemCalc\Domain\Tests\Chemistry\DataLoader;

use ChemCalc\Domain\Chemistry\DataLoader\ElementDataLoader;

class ElementDataLoaderTest extends \PHPUnit_Framework_TestCase
{
	public function __construct(){
		$this->dataJsonPath = realpath(dirname(__FILE__)).'/../../../res/PeriodicTableJSON.json';
		$this->data = json_decode(file_get_contents($this->dataJsonPath), true)['elements'];
	}

	public function testConstructorPropertiesInjection(){
		$elementDataLoader = new ElementDataLoader('some/directory/path');
		$this->assertAttributeEquals('some/directory/path', 'dataJsonPath', $elementDataLoader);
	}

	public function testLoadData(){
		$elementDataLoader = new ElementDataLoader();
		$this->assertEquals($this->data, $elementDataLoader->loadData());
	}

	public function testGetDataForElement(){
		$elementDataLoader = new ElementDataLoader();
		$this->assertEquals($this->data[0], $elementDataLoader->getDataForElement(['name' => 'Hydrogen']));
		$this->assertEquals(null, $elementDataLoader->getDataForElement(['name' => 'Fictious element']));
	}

	public function testGetDataForElementBySymbol(){
		$elementDataLoader = new ElementDataLoader();
		$this->assertEquals($this->data[0], $elementDataLoader->getDataForElementBySymbol('H'));
		$this->assertEquals(null, $elementDataLoader->getDataForElementBySymbol('FicSym'));
	}
}
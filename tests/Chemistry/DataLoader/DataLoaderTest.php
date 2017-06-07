<?php

namespace ChemCalc\Domain\Tests\Chemistry\DataLoader;

use ChemCalc\Domain\Chemistry\DataLoader\ElementDataLoader;

class DataLoaderTest extends \PHPUnit_Framework_TestCase
{
	public function testConstructorPropertiesInjection(){
		$elementDataLoader = new ElementDataLoader('some/directory/path');
		$this->assertAttributeEquals('some/directory/path', 'dataJsonPath', $elementDataLoader);
	}

	public function testLoadData(){
		$elementDataLoader = new ElementDataLoader();
		$data = json_decode(file_get_contents('res/PeriodicTableJSON.json'));
		$this->assertEquals($data, $elementDataLoader->loadData());
	}

	public function testGetDataForElement(){
		$elementDataLoader = new ElementDataLoader();
		$data = json_decode(file_get_contents('res/PeriodicTableJSON.json'));
		//$this->
	}
}
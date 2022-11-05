<?php

use Classes\TestClass;
use Classes\CDate;

class ExampleTest extends \Codeception\Test\Unit
{
	protected UnitTester $tester;

	protected function _before()
	{
	}

	// tests
	public function testExample()
	{
		$testDate = new TestClass();



		$this->assertFalse(false);
	}

	public function testDate()
	{
		new CDate();
	}
}

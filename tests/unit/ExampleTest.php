<?php 
namespace tests\unit;

use ReflectionClass;


// get some setup stuff, otherwise the die will prevent us from getting into the files
require './base.php';

// satisfy class calls in db.class.php
// require './classes/ui.class.php';
// $AppUI = new \CAppUI;

// call file to test
// require './classes/dp.class.php';

// require './includes/main_functions.php';
require './classes/csscolor.class.php';

class ExampleTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    
    protected function _before()
    {
        
    }

    protected function _after()
    {
    }

    // tests
    // public function testSomeCode()
    // {   
    //     $class1 = $this->getMockBuilder(CAppUI::class)
    //     ->disableOriginalConstructor()
    //     ->setMethods(['dPgetConfig'])
    //     ->getMock();
        
    //     $class2 = $this->getMockBuilder(CDpObject::class)
    //     ->disableOriginalConstructor()
    //     ->setMethods(null)
    //     ->getMock();

    //     // $reflection = new ReflectionClass(CDpObject::class);
    //     // $reflection_property = $reflection->getProperty('_error');
    //     // $reflection_property->setAccessible(true);

    //     // $reflection_property->setValue($class, 'A test error');

    //     // $class->method('dPgetConfig')->willReturn(1);

    // $this->assertSame('a test name', $class->dPgetConfig('locale_warn'));

    // }

    public function testEEEEEEAndFFFFFFAndZeroShouldEqualZero()
    {   
        $actual = bestColor('#eeeeee', '#ffffff', '#000000');

        $this->assertSame('#000000', $actual);

    }
}
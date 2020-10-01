<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('DP_BASE_DIR', '.'); // need to set this, or files can't be referenced.
define('UNIT_TEST', true); // need to set this, or files can't be referenced.

// require_once('./tests/autoload.php');
require_once('./includes/main_functions.php');
require_once('./classes/dp.class.php');

// $AppUI = new CAppUI;

class DotProjectBaseClassTest extends \Codeception\Test\Unit
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
    public function testGetErrorMethodReturnsMessage()
    {
        
        $CDpObject = $this->makeEmptyExcept('CDpObject',  'getError', ['_error' => 'Error Message']); //return $this->_error;
        // $DBQuery = $this->makeEmptyExcept('DBQuery', 'dPgetConfig'); //return $this->_error;

        $actual = $CDpObject->getError();

        
        $this->assertSame('Error Message',$actual);

    }
}
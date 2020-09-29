<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('DP_BASE_DIR', '.'); // need to set this, or files can't be referenced.

require_once('./tests/autoload.php');
require_once('./includes/main_functions.php');

$AppUI = new CAppUI;

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
    public function testSomeFeature()
    {
        $this->makeEmpty('CAppUI');
        $CDpObject = $this->makeEmptyExcept('CDpObject', '_error', 'getError'); //return $this->_error;
        $CDpObject = $this->makeEmptyExcept('DBQuery', 'dPgetConfig'); //return $this->_error;

        $actual = $CDpObject->getError();

        
        $this->assertTrue($actual);

    }
}
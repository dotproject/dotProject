<?php 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('DP_BASE_DIR', '.'); // need to set this, or files can't be referenced.
define('UNIT_TEST', true); // need to set this, or files can't be referenced.

require_once('./includes/main_functions.php');
require_once('./classes/dp.class.php');

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

    /** @test */
    public function testCanGetModuleDirectoryIfPassedIn()
    {

        $CDpObject = $this->makeEmptyExcept('CDpObject',  'getModuleName', ['_module_directory' => './modules']); //return $this->_error;
        // $DBQuery = $this->makeEmptyExcept('DBQuery', 'dPgetConfig'); //return $this->_error;

        $actual = $CDpObject->getModuleName();

        
        $this->assertSame('./modules',$actual);
    }
    
        /** @test */
        public function testCanGetModuleDirectoryIfPassedIn2()
        {
    
            $CDpObject = $this->makeEmptyExcept('CDpObject',  'getModuleName', ['_module_directory' => './modules']); //return $this->_error;
            // $DBQuery = $this->makeEmptyExcept('DBQuery', 'dPgetConfig'); //return $this->_error;
    
            $actual = $CDpObject->getModuleName();
    
            
            $this->assertSame('./modules',$actual);
        }
}
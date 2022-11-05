<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define('DP_BASE_DIR', '.'); // need to set this, or files can't be referenced.
define('UNIT_TEST', true); // need to set this, or files can't be referenced.

require_once './includes/main_functions.php';
require_once './classes/dp.class.php';
require_once './classes/query.class.php';
require_once './modules/system/system.class.php';

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

        $CDpObject = $this->makeEmptyExcept('CDpObject', 'getError', ['_error' => 'Error Message']); //return $this->_error;
        // $DBQuery = $this->makeEmptyExcept('DBQuery', 'dPgetConfig'); //return $this->_error;

        $actual = $CDpObject->getError();

        $this->assertSame('Error Message', $actual);

    }

    /** @test */
    public function testCanGetModuleDirectoryIfPassedIn()
    {

        $CDpObject = $this->makeEmptyExcept('CDpObject', 'getModuleName', ['_module_directory' => './modules']); 

        $actual = $CDpObject->getModuleName();

        $this->assertSame('./modules', $actual);
    }

    /** @test */
    public function testCanGetModuleByName()
    {
        // TODO: Get this test working
        // $CModule = $this->makeEmpty('CModule');
        // $CModule = $this->makeEmpty('DBQuery');

        // $CDpObject = $this->makeEmptyExcept('CDpObject', 'getModuleName', ['_module_directory' => '', '_permission_name', 'admin']);
        
        // $actual = $CDpObject->getModuleName();

        // $this->assertSame('./modules', $actual);
    }
}

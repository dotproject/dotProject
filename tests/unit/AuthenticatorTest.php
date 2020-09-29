<?php 
define('DP_BASE_DIR', true); // need to set this, or files can't be referenced.


require_once('./tests/autoload.php');


class AuthenticatorTest extends \Codeception\Test\Unit
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
    public function testIfThePasswordsAreTheSameTrueIsReturned()
    {

        $test = $this->makeEmptyExcept('SQLAuthenticator', 'comparePasswords');

        $actual = $test->comparePasswords('whatacoolpassword', MD5('whatacoolpassword'));

         $this->assertTrue($actual);

    }

    public function testIfPasswordsAreNotTheSameFalseIsReturned()
    {

        $test = $this->makeEmptyExcept('SQLAuthenticator', 'comparePasswords');

        $actual = $test->comparePasswords('whatacoolpassword', MD5('notthesamepassword'));

         $this->assertFalse($actual);

    }
}
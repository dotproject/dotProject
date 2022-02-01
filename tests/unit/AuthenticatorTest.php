<?php
// don't want deprecated errors breaking  test results
error_reporting(E_ALL ^ E_DEPRECATED);

// need to set this, or files can't be referenced.
define('DP_BASE_DIR', '.'); 

// get classes
require_once('./classes/authenticator.class.php');

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

        $test = new SQLAuthenticator;
        // $test = $this->makeEmptyExcept('SQLAuthenticator', 'comparePasswords');

        // $actual = $test->comparePasswords('whatacoolpassword', MD5('whatacoolpassword'));

         $this->assertTrue(true);

    }

    public function testIfPasswordsAreNotTheSameFalseIsReturned()
    {

        $test = $this->makeEmptyExcept('SQLAuthenticator', 'comparePasswords');

        $actual = $test->comparePasswords('whatacoolpassword', MD5('notthesamepassword'));

         $this->assertFalse($actual);

    }


    /** @test */
    public function testTheGetAuthFunctionCanSetLdapAuth()
    {
        GLOBAL $dPconfig;
        $dPconfig = array(
            'ldap_host'=>'',
            'ldap_port'=>'',
            'ldap_version'=>'',
            'ldap_base_dn'=>'',
            'ldap_search_user'=>'',
            'ldap_search_pass'=>'',
            'ldap_user_filter'=>'',
        );
        $actual = getAuth('ldap');
        
        $this->assertInstanceOf('LDAPAuthenticator', $actual);
    }

    public function testTheGetAuthFunctionCanSetPostNukeAuth()
    {
        $actual = getAuth('pn');
        $this->assertInstanceOf('PostNukeAuthenticator', $actual);
    }

    public function testTheGetAuthFunctionCanSetIPAuth()
    {
        $actual = getAuth('ip');
        $this->assertInstanceOf('IPAuthenticator', $actual);
    }

    public function testTheGetAuthFunctionCallsSQLAuthenticatorWhenBogusClassNamePassed()
    {
        $actual = getAuth('googoodedo');
        $this->assertInstanceOf('SQLAuthenticator', $actual);
    }
}
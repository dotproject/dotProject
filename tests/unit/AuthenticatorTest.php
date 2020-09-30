<?php 
define('DP_BASE_DIR', '.'); // need to set this, or files can't be referenced.


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
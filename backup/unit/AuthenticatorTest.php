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

	/**
	 * @return array
	 * ldap provider
	 */
	public function ldapAttributeProvider()
	{
		return [
			['attribute'=>"ldap_host"],
			['attribute'=>"ldap_port"],
			['attribute'=>"ldap_version"],
			['attribute'=>"base_dn"],
			['attribute'=>"ldap_search_user"],
			['attribute'=>"ldap_search_pass"],
			['attribute'=>"filter"],
			['attribute'=>"user_id"],
			['attribute'=>"username"],
			['attribute'=>"fallback"]
		];
	}



    // tests

	/**
	 * @dataProvider ldapAttributeProvider
	 */
	public function testIfLdapHasAllAttributes($a)
	{
		$actual = getAuth('ldap');

		$this->assertIsObject($actual);
		$this->assertObjectHasAttribute($a,$actual);
    }


	/**
	 * @return array
	 * postnuke provider
	 */
	public function postNukerAttributeProvider()
	{
		return [
			['attribute'=>"user_id"],
			['attribute'=>"username"],
			['attribute'=>"fallback"]
		];
	}

	/**
	 * @dataProvider postNukerAttributeProvider
	 */
	public function testIfPostNukeHasAllAttributes($a)
	{
		$actual = getAuth('pn');

		$this->assertIsObject($actual);
//		$this->assertEquals(new \StdClass, $actual);
		$this->assertObjectHasAttribute($a,$actual);
	}


	/**
	 * @return array
	 * ip provider
	 */
	public function ipAttributeProvider()
	{
		return [
			['attribute'=>"user_id"],
			['attribute'=>"username"],
		];
	}

	/**
	 * @dataProvider ipAttributeProvider
	 */
	public function testIfIPHasAllAttributes($a)
	{
		$actual = getAuth('ip');

		$this->assertIsObject($actual);
//		$this->assertEquals(new \StdClass, $actual);
		$this->assertObjectHasAttribute($a,$actual);
	}
}

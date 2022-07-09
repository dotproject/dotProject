<?php


class CompaniesModuleCest
{

	private $faker;
	private $company_name;
	private $company_name2;

    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', $I->grabFromConfig('username'));
        $I->fillField('password', $I->grabFromConfig('password'));
        $I->click(['class' => 'button']);

		$this->faker = $I->getFaker();
		$this->company_name = $this->faker->company();
		$this->company_name2 = $this->faker->company();
		$this->company_phone1 = '7777777777';
    }

    // tests
     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfCompaniesPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=companies');
        $I->see('Companies');// if text not found, test fails
    }

     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfCompaniesPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=companies');
        $I->dontSee('ERROR: ');// if text not found, test passes
    }

    /**
     * @depends SigninCest:canLoginIn
     */
    public function canAddNewCompany(AcceptanceTester $I)
    {

        $I->amOnPage('/index.php?m=companies');
        $I->click(['class' => 'button']);
        $I->see('Add Company');

		$description = $this->faker->sentence(20);


        $I->fillField('company_name', $this->company_name);
        $I->fillField('company_email', 'nothing@nowhere.com');
        $I->fillField('company_phone1', $this->company_phone1);
        $I->fillField('company_phone2', '2222222222');
        $I->fillField('company_fax', '0000000000');
        $I->fillField('company_address1', '1120 S. Westway st.');
        $I->fillField('company_address2', 'nothing really');
        $I->fillField('company_city', 'Jacksonville');
        $I->fillField('company_state', 'Florida');
        $I->fillField('company_zip', '49302');
        $I->fillField('company_primary_url', 'http://wheretheheckami.com');

        $I->selectOption('company_owner','Person, Admin');
        $I->selectOption('company_type','Internal');

		$I->fillField('company_description', $description);

        $I->click('submit'); //TODO: uncomment this if you want this test to save to the database

		$I->seeInDatabase('dotp_companies', ['company_name' => $this->company_name]);
		$I->seeInDatabase('dotp_companies', ['company_description' => $description]);

		$I->amOnPage('/index.php?m=companies');

		$I->see($this->company_name);
    }

	/**
	 * @depends SigninCest:canLoginIn
	 */
	public function canUpdateCompany(AcceptanceTester $I) {

		$I->updateInDatabase('dotp_companies', ['company_name' => $this->company_name2]);

		// can see change in the database
		$I->seeInDatabase('dotp_companies', ['company_name' => $this->company_name2]);

		// can see the change on the page
		$I->amOnPage('/index.php?m=companies');
		$I->see($this->company_name2);
	}

	/**
	 * @depends SigninCest:canLoginIn
	 */
	public function canDeleteCompany(AcceptanceTester $I)
	{
		$company_id = $I->grabFromDatabase('dotp_companies', 'company_id', ['company_phone1' => $this->company_phone1]);

		$I->amOnPage('http://dotproject.test/index.php?m=companies&a=view&company_id='.$company_id);
		$I->see('Admin Person');
//		$I->see($this->company_name2);
		// TODO: must see company
		// TODO: must delete company
		// TODO: must not see company
	}
}

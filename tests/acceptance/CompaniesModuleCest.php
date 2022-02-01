<?php 

class CompaniesModuleCest
{

    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', $I->grabFromConfig('username'));
        $I->fillField('password', $I->grabFromConfig('password'));
        $I->click(['class' => 'button']);
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
        $I->dontSee('ERROR: ');// if text not found, test fails
    }

    /**
     * @depends SigninCest:canLoginIn
     */
    public function canAddNewCompany(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=companies');
        $I->click(['class' => 'button']);
        $I->see('Add Company');

        $I->fillField('company_name', 'Test Company');
        $I->fillField('company_email', 'nothing@nowhere.com');
        $I->fillField('company_phone1', '7777777777');
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

        // $I->click('submit'); //TODO: uncomment this if you want this test to save to the database
    }
}

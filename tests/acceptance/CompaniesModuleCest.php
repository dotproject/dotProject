<?php 

class CompaniesModuleCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
    }

    // tests
    public function seeIfCompaniesPageLoads(AcceptanceTester $I)
    { 
        $I->amOnPage('/index.php?m=companies');
        $I->see('Companies');// if text not found, test fails
    }

    public function seeIfCompaniesPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=companies');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

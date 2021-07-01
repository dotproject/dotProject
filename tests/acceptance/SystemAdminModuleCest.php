<?php 

class SystemAdminModuleCest
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
    public function seeIfSystemAdminPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=system');
        $I->see('System Administration');// if text not found, test fails
    }
     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfSystemAdminPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=system');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

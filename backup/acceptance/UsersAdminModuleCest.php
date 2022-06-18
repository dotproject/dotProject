<?php 

class UsersAdminModuleCest
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
    public function seeIfUsersAdminPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=admin');
        $I->see('User Management');// if text not found, test fails
    }
     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfUsersAdminPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=admin');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

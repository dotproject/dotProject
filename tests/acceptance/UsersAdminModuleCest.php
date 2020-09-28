<?php 

class UsersAdminModuleCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
    }

    // tests
    public function seeIfUsersAdminPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=admin');
        $I->see('User Management');// if text not found, test fails
    }

    public function seeIfUsersAdminPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=admin');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

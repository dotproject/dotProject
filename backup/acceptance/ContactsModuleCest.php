<?php 

class ContactsModuleCest
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
    public function seeIfContactsPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=contacts');
        $I->see('Contacts');// if text not found, test fails
    }

     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfContactsPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=contacts');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

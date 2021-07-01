<?php 

class ForumsModuleCest
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
    public function seeIfForumsPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=forums');
        $I->see('Forums');// if text not found, test fails
    }

     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfForumsPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=forums');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

<?php 

class ForumsModuleCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
    }

    // tests
    public function seeIfForumsPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=forums');
        $I->see('Forums');// if text not found, test fails
    }

    public function seeIfForumsPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=forums');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

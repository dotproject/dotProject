<?php 

class CalendarModuleCest
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
    public function seeIfCalendarPageLoads(AcceptanceTester $I)
    { 
        $I->amOnPage('/index.php?m=calendar');
        $I->see('calendar');// if text not found, test fails
    }

    /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfCalendarPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=calendar');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

<?php 

class CalendarModuleCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
    }

    // tests
    public function seeIfCalendarPageLoads(AcceptanceTester $I)
    { 
        $I->amOnPage('/index.php?m=calendar');
        $I->see('calendar');// if text not found, test fails
    }

    public function seeIfCalendarPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=calendar');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}
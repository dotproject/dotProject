<?php 

class TicketsModuleCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
    }

    // tests
    public function seeIfTicketsPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=ticketsmith');
        $I->see('Trouble Ticket Management');// if text not found, test fails
    }

    public function seeIfTicketsPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=ticketsmith');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

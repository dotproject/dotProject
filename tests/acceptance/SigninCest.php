<?php

// use AcceptanceTester;

class SigninCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function weAreAbleToLoginIn(AcceptanceTester $I)
    {
        // TODO: Add to this test the ability to auto-create a test user
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
        $I->see('My Project');// if text not found, test fails
    }


    public function weDontSeeErrorsOnPage(AcceptanceTester $I)
    {
        // TODO: Add to this test the ability to auto-create a test user
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
        $I->dontSee('ERROR: '); //if text is found, test fails.
    }
}

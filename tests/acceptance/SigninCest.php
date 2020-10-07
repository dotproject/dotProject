<?php

// use AcceptanceTester;

class SigninCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function canSeeLoginForm(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');

        $I->see('Username');
        $I->see('Password');
        $I->see('login');
    }

     /**
     * @depends SigninCest:canSeeLoginForm
     */
    public function canLoginIn(AcceptanceTester $I)
    {
        // TODO: Add to this test the ability to auto-create a test user
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
        $I->see('My Project');// if text not found, test fails
    }

     /**
     * @depends SigninCest:canLoginIn
     */
    public function canSeeErrorIfUserNotInSystem(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', 'ogooakkkablahblah');
        $I->fillField('password', 'ogooakkkablahblah');
        $I->click(['class' => 'button']);
        $I->see('Login Failed');// if text not found, test fails
    }

     /**
     * @depends SigninCest:canLoginIn
     */
    public function shouldntSeeErrorsOnPage(AcceptanceTester $I)
    {
        // TODO: Add to this test the ability to auto-create a test user
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
        $I->dontSee('ERROR: '); //if text is found, test fails.
    }
}

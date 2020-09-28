<?php 

class ProjectModuleCest
{
    public function _before(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php');
        $I->fillField('username', 'admin');
        $I->fillField('password', 'pass');
        $I->click(['class' => 'button']);
    }

    // tests
     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfProjectsPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=projects');
        $I->see('Projects');// if text not found, test fails
    }

     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfProjectsPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=projects');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

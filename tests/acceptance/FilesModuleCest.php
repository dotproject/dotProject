<?php 

class FilesModuleCest
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
    public function seeIfFilesPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=files');
        $I->see('Files');// if text not found, test fails
    }
    
     /**
     * @depends SigninCest:canLoginIn
     */
    public function seeIfFilesPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=files');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

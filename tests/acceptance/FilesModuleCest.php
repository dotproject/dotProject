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
    public function seeIfFilesPageLoads(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=files');
        $I->see('Files');// if text not found, test fails
    }

    public function seeIfFilesPageHasNoErrors(AcceptanceTester $I)
    {
        $I->amOnPage('/index.php?m=files');
        $I->dontSee('ERROR: ');// if text not found, test fails
    }
}

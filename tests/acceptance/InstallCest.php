<?php 

class InstallCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function statusMessageShowsThatInstallAbortedIfDotProjectIsAlreadyInstalled(AcceptanceTester $I)
    {
        if (is_file('../includes/config.php')) {
            $I->amOnPage('/install/db.php');
            $I->See('Security Check: dotProject seems to be already configured. Install aborted!'); //if text is found, test fails.
        }
    }
}

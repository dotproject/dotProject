<?php
/**
 * Check user against the database
 */
class SQLAuthenticator
{
    public $user_id;
    public $username;

    public function SQLAuthenticator($username, $password)
    {
        $this->checkUser($username, $password);
    }

    /**
     * Verify credentials sent, match those in the database
     *
     * @param [string] $username
     * @param [string] $password
     * @return boolean
     */
    private function checkUser($username, $password)
    {
        global $db, $AppUI;

        $this->username = $username;

        $q = new DBQuery;
        $q->addTable('users');
        $q->addQuery('user_id, user_password');
        $q->addWhere("user_username = '$username'");
        if (!$rs = $q->exec()) {
            $q->clear();
            return false;
        }
        if (!$row = $q->fetchRow()) {
            $q->clear();
            return false;
        }

        $this->user_id = $row["user_id"];
        $q->clear();
        if (MD5($password) == $row["user_password"]) {
            return true;
        }

        return false;
    }


    public function userId($username)
    {
        // We ignore the username provided
        return $this->user_id;
    }
}

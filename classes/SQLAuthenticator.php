<?php
class SQLAuthenticator
{
    var $user_id;
    var $username;

    function __construct($username, $password)
    {
        GLOBAL $db, $AppUI;

        $this->username = $username;

        $q  = new DBQuery;
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
        if (MD5($password) == $row["user_password"]) return true;
        return false;
    }

    function userId($username)
    {
         // We ignore the username provided
        return $this->user_id;
    }
}
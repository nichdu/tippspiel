<?php
/**
 * Diese Klasse ist fuer Datenbankverbindung zustaendig
 */
class Database
{

    /**
     * @return mysqli
     * @static
     */
    public static function getDbObject()
    {
        $db = new mysqli(TIPPSPIEL_CONF_HOST, TIPPSPIEL_CONF_USER,
            TIPPSPIEL_CONF_PAWD, TIPPSPIEL_CONF_DABA);
        if ($db->connect_error)
        {
            throw new ErrorException($db->connect_error, $db->connect_errno);
        }
        $db->query("SET NAMES utf8");

        return $db;
    }

    public static function createUser($username, $email, $password)
    {
        $db = self::getDbObject();
        if (!($stmt = $db->prepare("INSERT INTO `users`(`name`, `email`, `password`)
            VALUES(?, ?, ?);")))
        {
            return TIPPSPIEL_USER_UNKNOWN_ERROR;
        }
        $stmt->bind_param('sss', $username, $email, $password);
        if ($stmt->execute())
        {
            return TIPPSPIEL_USER_SUCCESS;
        }
        else
        {
            if ($db->errno === ERR_DUP_ENTRY)
            {
                return TIPPSPIEL_USER_EXIST;
            }
            else
            {
                return TIPPSPIEL_USER_UNKNOWN_ERROR;
            }
        }
    }
}

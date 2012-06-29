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
    $db->query("SET NAMES utf8");

    return $db;
    }
}

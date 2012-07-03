<?php
/**
 * Startet die Anwendung
 */
class StartUp
{
    public function __construct()
    {
        include './Session.class.php';
        session_start();
        $this->sendHeader();
        include './Loader.class.php';
        new Loader();
        include '../lib/rain.tpl.class.php';
        include '../lib/PasswordHash.php';
        $this->createSession();
        $this->defineConstants();
    }

    private function createSession()
    {
        if (!isset($_SESSION['session']))
        {
            $_SESSION['session'] = new Session();
        }
    }

    private function sendHeader()
    {
        header("Content-type: text/html; charset=UTF-8");
    }

    private function defineConstants()
    {
        define('TIPPSPIEL_AUSWAERTSSIEG', 31999);
        define('TIPPSPIEL_UNENTSCHIEDEN', 32000);
        define('TIPPSPIEL_HEIMSIEG', 32001);
    }
}

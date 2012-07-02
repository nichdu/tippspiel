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
}

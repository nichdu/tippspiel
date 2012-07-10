<?php
/**
 * Bearbeitet Aufrufe, wenn das Profil bearbeitet wird
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class EditProfileProcessor
{
    private $errno = 1;
    private $error = 'Keine Daten angegeben.';

    /**
     * @var Checker
     */
    private $checker;

    public function __construct()
    {
        $this->checker = new Checker();
        if (!$this->checkCurrentPassword())
        {
            $this->respond();
        }
        if (isset($_POST['profilEmailAddress']))
        {
            $this->changeEmailAddress();
        }
        if (isset($_POST['profilPassword']))
        {
            $this->changePassword();
        }
        $this->respond();
    }

    private function checkCurrentPassword()
    {
        if (!isset($_POST['curPwd']))
        {
            $this->errno = 5;
            $this->error = 'Bitte geben Sie Ihr aktuelles Passwort zur Bestätigung ein.';
            return false;
        }
        $username = $_SESSION['session']->getUserName();
        $user = new User($username);
        if (!$user->checkPassword($_POST['curPwd']))
        {
            $this->errno = 6;
            $this->error = 'Das eingegebene aktuelle Passwort ist nicht korrekt.';
            return false;
        }
        return true;
    }

    private function changeEmailAddress()
    {
        $uid = $_SESSION['session']->getUserId();
        if (!$this->checker->checkEmailAddress($_POST['profilEmailAddress']))
        {
            $this->errno = 2;
            $this->error = 'Die angegebene E-Mail-Adresse ist nicht gültig.';
            return;
        }
        $this->errno = 0;
        $this->error = '';
        $db = Database::getDbObject();
        $stmt = $db->prepare("UPDATE `users` SET `email` = ? WHERE `id` = ?;");
        $stmt->bind_param('si', $_POST['profilEmailAddress'], $uid);
        if (!$stmt->execute())
        {
            $this->errno = $stmt->errno;
            $this->error = 'Es ist ein Datenbankfehler aufgetreten. Bitte versuchen Sie es später noch einmal.';
        }
    }

    private function changePassword()
    {
        $uid = $_SESSION['session']->getUserId();
        if ($this->errno !== 0 && $this->errno !== 1)
        {
            return;
        }
        if (!$this->checker->checkPassword($_POST['profilPassword']))
        {
            $this->errno = 3;
            $this->error = 'Das angegebene Passwort ist nicht gültig.';
            return;
        }
        if ($_POST['profilPassword'] !== $_POST['profilPwdWdh'])
        {
            $this->errno = 4;
            $this->error = 'Die angegebenen Passwörter stimmen nicht überein.';
            return;
        }
        $this->errno = 0;
        $this->error = '';
        $hasher = new PasswordHash(8, false);
        $pwd = $hasher->HashPassword($_POST['profilPassword']);
        $db = Database::getDbObject();
        $stmt = $db->stmt_init();
        $stmt->prepare("UPDATE `users` SET `password` = ? WHERE `id` = ?;");
        $stmt->bind_param('si', $pwd, $uid);
        $success = $stmt->execute();
        if (!$success || $stmt->errno)
        {
            $this->errno = $stmt->errno;
            $this->error = 'Es ist ein Datenbankfehler aufgetreten. Bitte versuchen Sie es später noch einmal.';
        }
    }

    private function respond()
    {
        $msg = null;
        if ($this->errno === 0)
        {
            $msg = 'Erfolgreich gespeichert.';
        }
        else
        {
            $msg = $this->error;
        }
        $arr = array(
            'error' => $this->errno,
            'message' => $msg,
        );
        header("Content-type: application/json");
        print json_encode($arr);
        exit;
    }
}

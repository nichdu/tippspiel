<?php
/**
 * Diese Seite bearbeitet Registrierungs-Requests
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 * @method checkEmail()
 * @method checkPassword()
 * @method checkUserName()
*/
class RegisterProcessor
{
    const userRegEx = '/^[a-zA-Z0-9_]{5,60}$/';
    const pwdRegEx = '/^\S.{6,70}\S$/';
    const emailRegEx = '/^([A-Za-z0-9_\-\.\+]+)@([A-Za-z0-9_\-\.]+)\.([A-Za-z]{1,5})/';
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string
     */
    private $pwd_wdh;
    /**
     * @var string
     */
    private $email;

    private $errno;
    private $error;

    public function __construct()
    {
        $this->user = trim($_POST['username']);
        $this->email = trim($_POST['email']);
        $this->password = trim($_POST['pwd']);
        $this->pwd_wdh = trim($_POST['wdh']);
        if (!$this->checkEmail())
        {
            $this->fail();
        }
        if (!$this->checkUserName())
        {
            $this->fail();
        }
        if (!$this->checkPassword())
        {
            $this->fail();
        }
        $this->create();
    }

    private function checkEmail()
    {
        $check = preg_match(self::emailRegEx, $this->email) === 1;
        if (!$check)
        {
            $this->errno = 1;
            $this->error = 'Es wurde eine ungültige E-Mail-Adresse eingegeben.';
        }
        return $check;
    }

    private function checkUserName()
    {
        $check = preg_match(self::userRegEx, $this->user) === 1;
        if (!$check)
        {
            $this->errno = 2;
            $this->error = 'Es wurde ein ungültiger Benutzername eingegeben.';
        }
        return $check;
    }

    private function checkPassword()
    {
        $check = preg_match(self::pwdRegEx, $this->password) === 1;
        if (!$check)
        {
            $this->errno = 3;
            $this->error = 'Es wurde ein ungültiges Passwort eingegeben.';
            return false;
        }
        if ($this->password !== $this->pwd_wdh)
        {
            $this->errno = 4;
            $this->error = 'Die eingegebenen Passwörter stimmen nicht überein.';
            $check = false;
        }
        return $check;
    }

    private function create()
    {
        $hasher = new PasswordHash(8, false);
        $hashedPwd = $hasher->HashPassword($this->password);
        if (strlen($hashedPwd) < 20)
        {
            $this->errno = 5;
            $this->error = 'Beim Speichern des Passwortes ist ein unbekannter Fehler aufgetreten.';
            $this->fail();
        }
        $success = Database::createUser($this->user, $this->email, $hashedPwd);
        if ($success === TIPPSPIEL_USER_SUCCESS)
        {
            $this->success();
        }
        else if ($success === TIPPSPIEL_USER_EXIST)
        {
            $this->errno = 6;
            $this->error = 'Ein Benutzer mit diesem Namen existiert bereits.';
            $this->fail();
        }
        else if ($success === TIPPSPIEL_USER_UNKNOWN_ERROR)
        {
            $this->errno = 7;
            $this->error = 'Beim Erstellen des Benutzers ist ein unbekannter Fehler aufgetreten. '
                . 'Sollte dieser Fehler weiterhin auftreten, wenden Sie sich bitte an einen Administrator.';
            $this->fail();
        }
    }

    private function fail()
    {
        $ret = array();
        $ret['error'] = $this->errno;
        $ret['message'] = $this->error;
        header("Content-type: application/json");
        print json_encode($ret);
        exit;
    }

    private function success()
    {
        $ret = array();
        $ret['error'] = 0;
        $ret['message'] = 'Der Benutzer wurde erfolgreich angelegt. Er wird in Kürze von einem Administrator bestätigt.';
        header("Content-type: application/json");
        print json_encode($ret);
        exit;
    }
}

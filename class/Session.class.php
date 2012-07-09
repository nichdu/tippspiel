<?php
/**
 * Diese Klasse repraesentiert eine Session
 */
class Session
{
    /**
     * @var boolean
     */
    private $login = false;

    /**
     * @var string|null
     */
    private $userName = null;

    /**
     * @var string
     */
    private $loginError = '';

    /**
     * @var int
     */
    private $loginErrno = 0;

    private $errno = 0;
    private $error = '';

    /**
     * @param boolean $login
     */
    public function setLogin($login)
    {
        $this->login = $login == true;
    }

    /**
     * @return boolean
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string|null Der UserName des gerade eingelogten Users oder null, wenn nicht eingelogt
     */
    public function getUserName()
    {
        return $this->userName;
    }

    public function getUserId()
    {
        return User::getIdByName($this->userName);
    }

    /**
     * Logt einen Benutzer mit gegebenen Daten ein
     * @param $username string Der Benutzername des zu einloggenden Users
     * @param $password string Das dazugehoerige Passwort
     * @param $permanent bool Soll der User eingeloggt bleiben, bis er sich ausloggt (NYI)
     * @return bool War das einloggen erfolgreich? Wenn nicht, liefert {@link getLoginError()} den dazugehoerigen Fehler
     */
    public function login($username, $password, $permanent = false)
    {
        $success = false;
        $user = null;
        try
        {
            $user = new User($username);
        }
        catch (UserExistiertNichtException $e)
        {
            $this->loginErrno = 1;
            $this->loginError = 'Der Benutzername existiert nicht.';
            $success = $this->login = false;
        }
        if ($user !== null)
        {
            if (!$user->isActive())
            {
                $this->loginErrno = 2;
                $this->loginError = 'Der Benutzer ist nicht aktiviert. Bitte wenden Sie sich an einen Administrator.';
                $success = $this->login = false;
            }
            else
            {
                if (!$user->checkPassword($password))
                {
                    $this->loginErrno = 3;
                    $this->loginError = 'Das angegebene Passwort ist falsch.';
                    $success = $this->login = false;
                }
                else
                {
                    $success = $this->login = true;
                    $this->userName = $user->getName();
                    $this->loginErrno = 0;
                    $this->loginError = '';
                    if ($permanent)
                    {
                        $this->createPermanentLogin($user->getId());
                    }
                }
            }
        }
        return $success;
    }

    private function createPermanentLogin($uid)
    {
        $series = $this->token();
        $token = $this->token();
        $exp = time() + TIPPSPIEL_CONF_VALIDITY;
        $expires_string = date('Y-m-d H:i:s', $exp);
        $db = Database::getDbObject();
        $stmt = $db->prepare("INSERT INTO `login_token` VALUES(?, ?, ?, ?);");
        $stmt->bind_param('isss', $uid, $series, $token, $expires_string);
        $stmt->execute();
        setcookie('login_token', $series . $token . $uid, $exp, TIPPSPIEL_CONF_PATH, TIPPSPIEL_CONF_DOMAIN, false, true);
    }

    public function checkPermanentLogin()
    {
        if (!isset($_COOKIE['login_token'])) { return; }
        $regex = '/^[0-9a-f]{64}[0-9]{1,5}$/';
        if (!preg_match($regex, $_COOKIE['login_token'])) { return; }
        $series = substr($_COOKIE['login_token'], 0, 32);
        $token = substr($_COOKIE['login_token'], 32, 32);
        $uid = (int)substr($_COOKIE['login_token'], 64);
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `token`, `valid` FROM `login_token` WHERE `uid` = ? AND `series` = ?;");
        $stmt->bind_param('is', $uid, $series);
        if (!$stmt->execute()) { return; }
        $stmt->store_result();
        if ($stmt->num_rows < 1) { return; }
        $dbtoken = null;
        $valid = null;
        $stmt->bind_result($dbtoken, $valid);
        $stmt->fetch();
        if ($token === $dbtoken)
        { // valid: Login user
            $valid = new DateTime($valid);
            $now = new DateTime();
            if ($valid < $now)
            {
                $stmt = $db->prepare("DELETE FROM `login_token` WHERE `uid` = ? AND `series` = ?;");
                $stmt->bind_param('is', $uid, $series);
                $stmt->execute();
            }
            else
            {
                $username = User::getNameById($uid);
                $user = new User($username);
                $this->login = true;
                $this->userName = $user->getName();
                $newtoken = $this->token();
                $stmt = $db->prepare("UPDATE `login_token` SET `token` = ? WHERE `uid` = ? AND `series` = ?;");
                $stmt->bind_param('sis', $newtoken, $uid, $series);
                if ($stmt->execute())
                {
                    $exp = $valid->format('U');
                    setcookie('login_token', $series . $token . $newtoken, $exp, TIPPSPIEL_CONF_PATH, TIPPSPIEL_CONF_DOMAIN, false, true);
                }
            }
        }
        else
        { // invalid: inform the user
            $this->errno = 1;
            $this->error = 'Anscheinend wurde Ihr Login-Cookie gestohlen. Aus Sicherheitsgründen wurden Sie ausgelogt'
                . '. Sollte dieser Fehler weiterhin auftreten, überprüfen Sie bitte Ihren Rechner auf Schadprogramme.';
            $stmt = $db->prepare("DELETE FROM `login_token` WHERE `uid` = ?;");
            $stmt->bind_param('i', $uid);
            $stmt->execute();
        }
    }

    /**
     * Logt den eingeloggten Benutzer wieder aus
     */
    public function logout()
    {
        $this->login = false;
        $this->userName = null;
        setcookie('login_token', '', time()-1024, TIPPSPIEL_CONF_PATH, TIPPSPIEL_CONF_DOMAIN, false, true);
        session_destroy();
    }

    /**
     * Sofern {@link login()} fehlschlug, gibt diese Funktion den Fehler zurueck
     * @return string
     */
    public function getLoginError()
    {
        return $this->loginError;
    }

    public function getErrno()
    {
        $a = $this->errno;
        $this->errno = 0;
        return $a;
    }

    public function getError()
    {
        return $this->error;
    }

    /**
     * Sofern {@link login()} fehlschlug, gibt diese Funktion die Fehlernummer zurueck
     * @return int
     */
    public function getLoginErrno()
    {
        return $this->loginErrno;
    }

    /**
     * @return string randomized 256 bit token
     */
    function token()
    {
        return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}

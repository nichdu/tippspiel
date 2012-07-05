<?php
/**
 * Repraesentiert einen Benutzer und stellt Methoden dafuer zur Verfuegung
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class User
{
    /**
     * Ueberprueft, ob ein gegebener Username existiert
     * @static
     * @param $username string der zu pruefende Username
     * @return bool true, wenn der Benutzer existiert, ansonsten false
     */
    public static function exists($username)
    {
        $ret = false;
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `id` FROM `users` WHERE `name` = ?;");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0)
        {
            $ret = true;
        }
        return $ret;
    }

    /**
     * Gibt den Namen zu einer gegebenen User-ID zurueck
     * @static
     * @param $uid int die gesuchte uid
     * @return string|null Der Username oder null, wenn der User nicht existiert
     */
    public static function getNameById($uid)
    {
        $ret = null;
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `name` FROM `users` WHERE `id` = ?;");
        $stmt->bind_param('i', $uid);
        $stmt->execute();
        $stmt->bind_result($ret);
        $stmt->fetch();
        return $ret;
    }

    public static function getIdByName($name)
    {
        $ret = null;
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `id` FROM `users` WHERE `name` = ?;");
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $stmt->bind_result($ret);
        $stmt->fetch();
        return $ret;
    }

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $email;

    /**
     * Ueberprueft, ob ein gegebener Username aktiv ist
     * @return bool true, wenn der Benutzer aktiv ist, ansonsten false
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * Ueberprueft, ob ein gegebenes Paswort zu einem gegebenen Usernamen korrekt ist
     * @param $password
     * @return bool
     */
    public function checkPassword($password)
    {
        $hasher = new PasswordHash(8, false);
        $check = $hasher->CheckPassword(trim($password), $this->password);
        return $check;
    }

    public function __construct($username)
    {
        $this->username = $username;
        $this->loadFromDatabase();
    }

    private function loadFromDatabase()
    {
        $active = 0;
        if (!self::exists($this->username))
        {
            throw new UserExistiertNichtException();
        }
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `id`, `email`, `password`, `active` FROM `users` WHERE `name` = ?;");
        $stmt->bind_param('s', $this->username);
        $stmt->execute();
        $stmt->bind_result($this->id, $this->email, $this->password, $active);
        $stmt->fetch();
        $this->active = $active == 1;
    }

    /**
     * @return string Der Name des Benutzers
     */
    public function getName()
    {
        return $this->username;
    }

    /**
     * @return string Die E-Mail-Adresse des Benutzers
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return int Die ID des Benutzers
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gibt die Punkte als mehrdimensionales Array zurueck TODO: Struktur des Arrays beschreiben
     * @return array Die Punkte
     */
    public function getPunkteDetail()
    {
        $gesamtPkt = 0;
        $gesamtAnzahl = 0;
        $ergebnisPunkte = 0;
        $differenzPunkte = 0;
        $siegerPunkte = 0;
        $falschAnzahl = 0;
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `spiel_id` FROM `tipps` WHERE `uid` = ?;");
        $stmt->bind_param('i', $this->id);
        if ($stmt->execute())
        {
            if ($stmt->store_result())
            {
                $spiel_id = 0;
                $stmt->bind_result($spiel_id);
                while ($stmt->fetch())
                {
                    $tipp = new DbTipp($this->getId(), $spiel_id);
                    if ($tipp->getPunkte() === TIPPSPIEL_CONF_ERGEBNIS_KORREKT)
                    {
                        $ergebnisPunkte += TIPPSPIEL_CONF_ERGEBNIS_KORREKT;
                    }
                    else if ($tipp->getPunkte() === TIPPSPIEL_CONF_DIFFERENZ_KORREKT)
                    {
                        $differenzPunkte += TIPPSPIEL_CONF_DIFFERENZ_KORREKT;
                    }
                    else if ($tipp->getPunkte() === TIPPSPIEL_CONF_SIEGER_KORREKT)
                    {
                        $siegerPunkte += TIPPSPIEL_CONF_SIEGER_KORREKT;
                    }
                    else if ($tipp->getPunkte() === 0)
                    {
                        $falschAnzahl++;
                    }
                    $gesamtAnzahl++;
                    $gesamtPkt += $tipp->getPunkte();
                }
            }
        }
        return array
        (
            'gesamt' => array
            (
                'anzahl' => $gesamtAnzahl,
                'punkte' => $gesamtPkt,
            ),
            'ergebnis' => array
            (
                'anzahl' => ($ergebnisPunkte/TIPPSPIEL_CONF_ERGEBNIS_KORREKT),
                'punkte' => $ergebnisPunkte,
            ),
            'differenz' => array
            (
                'anzahl' => ($differenzPunkte/TIPPSPIEL_CONF_DIFFERENZ_KORREKT),
                'punkte' => $differenzPunkte,
            ),
            'sieger' => array
            (
                'anzahl' => ($siegerPunkte/TIPPSPIEL_CONF_SIEGER_KORREKT),
                'punkte' => $siegerPunkte,
            ),
            'falsch' => array
            (
                'anzahl' => $falschAnzahl,
            )
        );
    }

    /**
     * @return int
     */
    public function getGesamtPunkte()
    {
        $arr = $this->getPunkteDetail();
        return $arr['gesamt']['punkte'];
    }
}

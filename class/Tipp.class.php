<?php
/**
 * Diese Klasse repraesentiert einen Tipp zu einem Ergebnis eines Spiels
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class Tipp
{
    /**
     * @var Spiel
     */
    protected $spiel;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var int
     */
    protected $heim;

    /**
     * @var int
     */
    protected $auswaerts;

    /**
     * @var int|null
     */
    protected $punkte = null;

    /**
     * @param $user User Der Benutzer, der den Tipp abgegeben hat
     * @param $spiel Spiel Das Spiel, fuer das der Tiopp gilt
     * @param $heim int Die fuer die Heimmannschaft getippten Tore
     * @param $auswaerts int Die fuer die Auswaertsmannschaft getippten Tore
     */
    public function __construct($user, $spiel, $heim, $auswaerts)
    {
        $this->user = $user;
        $this->spiel = $spiel;
        $this->heim = $heim;
        $this->auswaerts = $auswaerts;
    }

    /**
     * Speichert den aktuellen Tipp in der Datenbank
     * @return bool true, wenn das Speichern erfolgreich war, false sonst
     */
    public function save()
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("INSERT INTO `tipps`(`uid`, `spiel_id`, `heim`, `auswaerts`)
            VALUES(?, ?, ?, ?);");
        $stmt->bind_param('iiii', $this->user->getId(), $this->spiel->getId(), $this->heim, $this->auswaerts);
        if (!$stmt->execute())
        {
            return false;
        }
        return true;
    }

    /**
     * @return Spiel Das Spiel, zu dem der Tipp gehoert
     */
    public function getSpiel()
    {
        return $this->spiel;
    }

    /**
     * @return User Der Benutzer, zu dem der Tipp gehoert
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return int Die fuer die Heimmannschaft getippten Tore
     */
    public function getHeimTipp()
    {
        return $this->heim;
    }

    /**
     * @return int Die fuer die Auswaertsmannschaft getippten Tore
     */
    public function getAuswaertsTipp()
    {
        return $this->auswaerts;
    }

    /**
     * @return int|null Die Anzahl an Punkten fuer das Spiel oder null, wenn die nicht kalkuliert werden konnten
     */
    public function getPunkte()
    {
        if ($this->punkte === null)
        {
            $this->generatePunkte();
        }
        return $this->punkte;
    }

    protected function generatePunkte()
    {
        // TODO: Punkte aus Datei lesen und schreiben
        if (!is_int($this->spiel->getHeimTore()) || !is_int($this->spiel->getAuswaertsTore()))
        {
            return;
        }
        if ($this->heim === $this->spiel->getHeimTore() && $this->auswaerts === $this->spiel->getAuswaertsTore())
        { // Das getippte Ergebnis entspricht genau dem realen Ergebnis
            $this->punkte = TIPPSPIEL_CONF_ERGEBNIS_KORREKT;
        }
        else if ((($this->spiel->getHeimTore() - $this->spiel->getAuswaertsTore()) === ($this->heim - $this->auswaerts)))
        { // Die getippte Tordifferenz ist korrekt (schliesst Unentschieden mit ein)
            $this->punkte = TIPPSPIEL_CONF_DIFFERENZ_KORREKT;
        }
        else if ($this->getGetippterSieger() === $this->spiel->getSieger())
        { // Der getippte Sieger ist korrekt
            $this->punkte = TIPPSPIEL_CONF_SIEGER_KORREKT;
        }
        else
        {
            $this->punkte = 0;
        }
    }

    /**
     * Gibt den Sieger des Spiels zurueck
     * @return int|null TIPPSPIEL_HEIMSIEG, TIPPSPIEL_UNENTSCHIEDEN, TIPPSPIEL_AUSWAERTSSIEG oder null, wenn kein Ergebnis bekannt
     */
    public function getGetippterSieger()
    {
        $ret = null;
        if ($this->heim === $this->auswaerts)
        {
            $ret =  TIPPSPIEL_UNENTSCHIEDEN;
        }
        else if ($this->heim > $this->auswaerts)
        {
            $ret =  TIPPSPIEL_HEIMSIEG;
        }
        else if ($this->auswaerts > $this->heim)
        {
            $ret = TIPPSPIEL_AUSWAERTSSIEG;
        }
        return $ret;
    }
}

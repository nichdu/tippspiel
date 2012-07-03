<?php
/**
 * Diese Klasse repraesentiert ein Spiel zwischen zwei Vereinen
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class Spiel
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var Verein
     */
    private $heim;
    /**
     * @var Verein
     */
    private $auswaerts;
    /**
     * @var int
     */
    private $heimTore;
    /**
     * @var int
     */
    private $auswaertsTore;

    public function __construct($id)
    {
        $this->id = $id;
        $this->loadFromDatabase();
    }

    private function loadFromDatabase()
    {
        $db = Database::getDbObject();
        $stmt = $db->stmt_init();
        $stmt->prepare("SELECT `id`, `heim`, `auswaerts` FROM `spiele` WHERE `id` = ?;");
        $stmt->bind_param('i', $this->id);
        if (!$stmt->execute())
        {
            throw new mysqli_sql_exception($stmt->error, $stmt->errno);
        }
        $hId = 0;
        $aId = 0;
        $stmt->bind_result($this->id, $hId, $aId);
        if (!$stmt->fetch())
        {
            throw new VereinExistiertNichtException();
        }
        $this->heim = new Verein($hId);
        $this->auswaerts = new Verein($aId);

        $stmt = $db->stmt_init();
        $stmt->prepare("SELECT `heim`, `auswaerts` FROM `ergebnisse` WHERE `spiel_id` = ?;");
        $stmt->bind_param('i', $this->id);
        if (!$stmt->execute())
        {
            throw new mysqli_sql_exception($stmt->error, $stmt->errno);
        }
        $hTor = null;
        $aTor = null;
        $stmt->bind_result($hTor, $aTor);
        if ($stmt->fetch())
        {
            $this->heimTore = $hTor;
            $this->auswaertsTore = $aTor;
        }
        $stmt->close();
        $stmt = null;
    }

    /**
     * Gibt die Heimmannschaft des Spiels zurueck
     * @return Verein
     */
    public function getHeim()
    {
        return $this->heim;
    }

    /**
     * Gibt die Auswaertsmannschaft des Spiels zurueck
     * @return Verein
     */
    public function getAuswaerts()
    {
        return $this->auswaerts;
    }

    /**
     * Gibt die Anzahl der Tore der Heimmannschaft zurueck.
     * Ist kein Ergebnis vorhanden, wird '-' zurueckgegeben.
     * @return int|string Tore der Heimmannschaft oder '-'
     */
    public function getHeimTore()
    {
        $ret = '-';
        if (isset($this->heimTore))
        {
            $ret = $this->heimTore;
        }
        return $ret;
    }

    /**
     * Gibt die Anzahl der Tore der Auswaertsmannschaft zurueck.
     * Ist kein Ergebnis vorhanden, wird '-' zurueckgegeben.
     * @return int|string Tore der Auswaertsmannschaft oder '-'
     */
    public function getAuswaertsTore()
    {
        $ret = '-';
        if (isset($this->auswaertsTore))
        {
            $ret = $this->auswaertsTore;
        }
        return $ret;
    }

    public  function getId()
    {
        return $this->id;
    }

    /**
     * Gibt den Sieger des Spiels zurueck
     * @return int|null TIPPSPIEL_HEIMSIEG, TIPPSPIEL_UNENTSCHIEDEN, TIPPSPIEL_AUSWAERTSSIEG oder null, wenn kein Ergebnis bekannt
     */
    public function getSieger()
    {
        $ret = null;
        if (is_int($this->heimTore) && is_int($this->auswaertsTore))
        {
            if ($this->heimTore === $this->auswaertsTore)
            {
                $ret =  TIPPSPIEL_UNENTSCHIEDEN;
            }
            else if ($this->heimTore > $this->auswaertsTore)
            {
                $ret =  TIPPSPIEL_HEIMSIEG;
            }
            else if ($this->auswaertsTore > $this->heimTore)
            {
                $ret = TIPPSPIEL_AUSWAERTSSIEG;
            }
        }
        return $ret;
    }
}

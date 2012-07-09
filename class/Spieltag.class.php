<?php
/**
 * Diese Klasse repraesentiert einen Spieltag mit einer beliebigen Anzahl an Spielen.
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class Spieltag implements Iterator
{
    /**
     * @var int
     */
    private $position = 0;
    /**
     * @var int
     */
    private $spieltag;
    /**
     * @var array
     */
    private $spiele;

    /**
     * @var DateTime
     */
    private $datum;
    /**
     * @var DateTime
     */
    private $tipp_ende;

    /**
     * Erstellt eine neue Instanz der Klasse
     * @param $spieltag int
     */
    public function __construct($spieltag)
    {
        $this->spieltag = $spieltag;
        $this->loadDatum();
        $this->loadFromDatabase();
    }

    private function loadDatum()
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `datum`, `tipp_ende` FROM `spieltage` WHERE `spieltag` = ?;");
        $stmt->bind_param('i', $this->spieltag);
        if (!$stmt->execute())
        {
            throw new mysqli_sql_exception($stmt->error, $stmt->errno);
        }
        $stmt->store_result();
        if ($stmt->num_rows === 0)
        {
            throw new SpieltagExistiertNichtException();
        }
        $datum = null;
        $tipp_ende = null;
        $stmt->bind_result($datum, $tipp_ende);
        $stmt->fetch();
        $this->datum = new DateTime($datum);
        $this->tipp_ende = new DateTime($tipp_ende);
    }

    private function loadFromDatabase()
    {
        $this->spiele = array();
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `id` FROM `spiele` WHERE `spieltag` = ?;");
        $stmt->bind_param('i', $this->spieltag);
        if (!$stmt->execute())
        {
            throw new mysqli_sql_exception($stmt->error, $stmt->errno);
        }
        $stmt->store_result();
        if ($stmt->num_rows === 0)
        {
            throw new SpieltagExistiertNichtException();
        } /* */
        $id = 0;
        $stmt->bind_result($id);
        while ($stmt->fetch())
        {
            $this->spiele[] = new Spiel($id);
        }
        $stmt->close();
    }

    function rewind()
    {
        $this->position = 0;
    }

    function current()
    {
        return $this->spiele[$this->position];
    }

    function key()
    {
        return $this->position;
    }

    function next()
    {
        ++$this->position;
    }

    function valid()
    {
        return isset($this->spiele[$this->position]);
    }

    public function getDate()
    {
        return $this->datum;
    }

    public function getTippFrist()
    {
        return $this->tipp_ende;
    }
}

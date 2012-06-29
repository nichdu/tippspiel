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
     * Erstellt eine neue Instanz der Klasse
     * @param $spieltag int
     */
    public function __construct($spieltag)
    {
        $this->spieltag = $spieltag;
        $this->loadFromDatabase();
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
        /*
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
}

<?php
/**
 * Diese Klasse repraesentiert einen Verein
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class Verein
{
    /**
     * @var int
     */
    private $id;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $kuerzel;
    /**
     * @var string
     */
    private $logo;

    /**
     * Erstellt ein neues Objekt dieser Klasse
     * @param $id int
     */
    public function __construct($id)
    {
        assert('is_int($id); // $id muss ein Integer sein.');
        $this->id = $id;
        $this->loadFromDatabase();
    }

    private function loadFromDatabase()
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT * FROM `vereine` WHERE `id` = ?;");
        $stmt->bind_param('i', $this->id);
        if (!$stmt->execute())
        {
            throw new mysqli_sql_exception($stmt->error, $stmt->errno);
        }
        $logo = '';
        $stmt->bind_result($this->id, $this->name, $this->kuerzel, $logo);
        if (!$stmt->fetch())
        {
            throw new VereinExistiertNichtException();
        }
        $this->logo = base64_encode($logo);
        $stmt->close();
    }

    /**
     * Gibt den Namen des Vereins zurueck
     * @return string der Name des Vereins
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gibt die ID des Vereins zurueck
     * @return int die ID des Vereins
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Gibt das drei-Buchstaben-Kuerzel des Vereins zurueck
     * @return string das Kuerzel des Vereins
     */
    public function getKuerzel()
    {
        return $this->kuerzel;
    }

    /**
     * Gibt das Logo des Vereins als Binärdokument zurück
     * @return string das Logo des Vereins
     */
    public function getLogo()
    {
        return $this->logo;
    }
}

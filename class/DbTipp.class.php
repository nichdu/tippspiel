<?php
/**
 * Repraesentiert einen bereits existierenden Tipp aus der Datenbank
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class DbTipp extends Tipp
{
    /**
     * @var int
     */
    private $uid;

    /**
     * @var int
     */
    private $spiel_id;

    /**
     * @param int $uid
     * @param int $spiel_id
     */
    public function __construct($uid, $spiel_id)
    {
        $this->uid = $uid;
        $this->spiel_id = $spiel_id;
        $this->spiel = new Spiel($this->spiel_id);
        $this->user = new User(User::getNameById($this->uid));
        $this->loadFromDatabase();
    }

    private function loadFromDatabase()
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `heim`, `auswaerts` FROM `tipps` WHERE `uid` = ? AND `spiel_id` = ?");
        $stmt->bind_param('ii', $this->uid, $this->spiel_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 0)
        {
            throw new TippExistiertNichtException();
        }
        $stmt->bind_result($this->heim, $this->auswaerts);
        $stmt->fetch();
    }
}

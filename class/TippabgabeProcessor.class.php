<?php
/**
 * Bearbeitet abgegebene Tipps und speichert sie in der Datenbank
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class TippabgabeProcessor
{
    /**
     * @var int
     */
    private $spieltag;
    /**
     * @var Spieltag
     */
    private $st_obj;
    /**
     * @var int[][]
     */
    private $spiele = array();

    /**
     * @var int
     */
    private $errno = 0;
    /**
     * @var string
     */
    private $error = '';

    public function __construct()
    {
        $this->spieltag = (int)$_POST['spieltag'];
        $this->loadSpiele();
        if (!$this->possible())
        {
            $this->fail();
            exit;
        }
        $this->createTipps();
        if ($this->writeIntoDatabase())
        {
            $this->success();
        }
        else
        {
            $this->fail();
            exit;
        }
    }

    private function loadSpiele()
    {
        $this->st_obj = new Spieltag($this->spieltag);
    }

    private function createTipps()
    {
        foreach ($this->st_obj as $spiel)
        {
            /* @var $spiel Spiel */
            if (is_numeric($_POST['spiel_' . $spiel->getId() . '_heim']) && is_numeric($_POST['spiel_' . $spiel->getId() . '_auswaerts']))
            {
                $this->spiele[$spiel->getId()] = array();
                $this->spiele[$spiel->getId()]['heim'] = $spiel->getHeim()->getId();
                $this->spiele[$spiel->getId()]['ausw'] = $spiel->getAuswaerts()->getId();
                $this->spiele[$spiel->getId()]['heimTore'] = (int)$_POST['spiel_' . $spiel->getId() . '_heim'];
                $this->spiele[$spiel->getId()]['auswTore'] = (int)$_POST['spiel_' . $spiel->getId() . '_auswaerts'];
            }
        }
    }

    private function possible()
    {
        $now = new DateTime('now');
        if (!$this->st_obj->getTippFrist() >= $now)
        {
            $this->errno = 1;
            $this->error = 'Die Tippfrist für diesen Spieltag ist bereits abgelaufen.';
        }
        return $this->st_obj->getTippFrist() >= $now;
    }

    private function writeIntoDatabase()
    {
        $uid = $_SESSION['session']->getUserId();
        $db = Database::getDbObject();
        foreach ($this->spiele as $id => $v)
        {
            $stmt = $db->prepare("INSERT INTO `tipps` VALUES(?,?,?,?)
              ON DUPLICATE KEY UPDATE `heim` = VALUES(`heim`), `auswaerts` = VALUES(`auswaerts`);");
            $stmt->bind_param('iiii', $uid, $id, $v['heimTore'], $v['auswTore']);
            if (!$stmt->execute())
            {
                $this->errno = $stmt->errno;
                $this->error = 'Es ist ein Datenbankfehler aufgetreten. Bitte versuchen Sie es später noch einmal. '
                    . 'Sollte dieser Fehler weiterhin auftreten, wenden Sie sich bitte an einen Administrator.';
                return false;
            }
            $stmt->close();
        }
        return true;
    }

    private function fail()
    {
        $arr = array();
        $arr['error'] = $this->errno;
        $arr['message'] = $this->error;
        $this->sendJson($arr);
    }

    private function success()
    {
        $arr = array(
            'error' => 0,
            'message' => 'Erfolgreich gespeichert.',
        );
        $this->sendJson($arr);
    }

    private function sendJson($arrayOrObject)
    {
        header("Content-type: application:json");
        print json_encode($arrayOrObject);
    }
}

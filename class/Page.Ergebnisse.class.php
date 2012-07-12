<?php
/**
 * Gibt aktuelle Spielergebnisse aus
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class ErgebnissePage
{
    /**
     * @var RainTPL
     */
    private $tpl;

    /**
     * @var int
     */
    private $spieltag;

    public function __construct()
    {
        if ($_SESSION['session']->getLogin())
        {
            $this->tpl = new RainTPL();
            try
            {
                $this->assignVariables();
                $this->draw();
            }
            catch (SpieltagExistiertNichtException $e)
            {
                throw new Error404Exception();
            }
        }
        else
        {
            throw new Error403Exception();
        }
    }

    private function assignVariables()
    {
        $sel = PageSelector::getSelector();
        if (!is_null($sel->getQuery(1)) && is_numeric($sel->getQuery(1)))
        {
            $this->spieltag = (int)$sel->getQuery(1);
        }
        else if (!is_null($sel->getQuery(1)))
        {
            throw new SpieltagExistiertNichtException();
        }
        else
        {
            $this->loadNaechstenSpieltagIdFromDatabase();
        }
        $this->tpl->assign('spieltage', $this->loadSpieltageFromDatabase());
        $this->tpl->assign('spieltag', $this->spieltag);
        $this->tpl->assign('spiele', $this->loadSpiele());
    }

    private function loadNaechstenSpieltagIdFromDatabase()
    {
        $db = Database::getDbObject();
        $query = "SELECT `spieltag` FROM `spieltage` WHERE `datum` > CURDATE() LIMIT 1;";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($this->spieltag);
        if (!($stmt->num_rows > 0) || !$stmt->fetch())
        {
            $this->spieltag = 1;
        }
    }

    private function loadSpieltageFromDatabase()
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `spieltag` FROM `spieltage`;");
        $arr = array();
        $stmt->execute();
        $stmt->store_result();
        $id = 0;
        $stmt->bind_result($id);
        while ($stmt->fetch())
        {
            $arr[] = $id;
        }
        return $arr;
    }

    private function loadSpiele()
    {
        $spieltag = new Spieltag($this->spieltag);
        $uid = $_SESSION['session']->getUserId();
        $now = new DateTime('now');
        $this->tpl->assign('ende', $spieltag->getTippFrist());
        if ($spieltag->getTippFrist() <= $now)
        {
            $this->tpl->assign('abgelaufen', true);
        }
        else
        {
            $this->tpl->assign('abgelaufen', false);
        }
        $arr = array();
        foreach ($spieltag as $spiel)
        {
            /* @var $spiel Spiel */
            $arr[$spiel->getId()] = array();
            $arr[$spiel->getId()]['spiel'] = $spiel;
            try {
                $tipp = new DbTipp($uid, $spiel->getId());
                $arr[$spiel->getId()]['tipp'] = $tipp;
            }
            catch (TippExistiertNichtException $e)
            {
                $arr[$spiel->getId()]['tipp'] = false;
            }
        }
        return $arr;
    }

    private function draw()
    {
        StartUp::AssignVars($this->tpl, 'Ergebnisse');
        $this->tpl->draw('ergebnisse');
    }
}

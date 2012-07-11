<?php
/**
 * Diese Klasse generiert eine Homepage zu dieser Applikation
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class HomePage
{
    /**
     * @var RainTPL
     */
    private $raintpl;

    public function __construct()
    {
        $this->raintpl = new RainTPL();
        $this->determineIfLoggedInAndCallAccordingMethods();
    }

    private function determineIfLoggedInAndCallAccordingMethods()
    {
        StartUp::AssignVars($this->raintpl);
        if ($_SESSION['session']->getLogin())
        {
            $this->beginLoggedIn();
        }
        else
        {
            $this->beginNotLoggedIn();
        }
    }

    private function beginNotLoggedIn()
    {
        $_SESSION['login_access_token'] = $_SESSION['session']->token();
        $this->raintpl->assign('login_access_token', $_SESSION['login_access_token']);
        $this->raintpl->draw('home');
    }

    private function beginLoggedIn()
    {
        $this->generateAktuelleErgebnisse();
        $this->generateNaechstenSpieltag();
        $this->raintpl->draw('home');
    }

    private function generateAktuelleErgebnisse()
    {
        $db = Database::getDbObject();
        $query = "SELECT `spieltag` FROM `spieltage` WHERE `datum` < CURDATE() LIMIT 1;";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0)
        {
            $id = 0;
            $stmt->bind_result($id);
            $stmt->fetch();
            $spieltag = new Spieltag($id);
            $st = array();
            $i = 0;
            foreach ($spieltag as $v)
            {
                $st[$i] = array();
                $st[$i]['spiel'] = $v;
                try
                {
                    $tipp = new DbTipp(User::getIdByName($_SESSION['session']->getUserName()), $v->getId());
                    $st[$i]['tipp'] = $tipp;
                }
                catch (TippExistiertNichtException $e)
                {
                    $st[$i]['tipp'] = null;
                }
                ++$i;
            }
            $this->raintpl->assign('aktuelleErgebnisse', true);
            $this->raintpl->assign('aktuelleSpiele', $st);
            $this->raintpl->assign('spieltagAktuell', $id);
        }
        else
        {
            $this->raintpl->assign('aktuelleErgebnisse', false);
        }
    }

    private function generateNaechstenSpieltag()
    {
        $db = Database::getDbObject();
        $query = "SELECT `spieltag` FROM `spieltage` WHERE `datum` >= CURDATE() LIMIT 1;";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0)
        {
            $id = 0;
            $stmt->bind_result($id);
            $stmt->fetch();
            $spieltag = new Spieltag($id);
            $st = array();
            foreach ($spieltag as $v)
            {
                $st[] = $v;
            }
            $this->raintpl->assign('naechsterSpieltag', true);
            $this->raintpl->assign('naechsteSpiele', $st);
            $this->raintpl->assign('spieltagNaechst', $id);
        }
        else
        {
            $this->raintpl->assign('naechsterSpieltag', false);
        }
    }
}

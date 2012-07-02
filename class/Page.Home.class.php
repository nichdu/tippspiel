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
        $this->raintpl->assign('title', 'Tippspiel');
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
        $this->raintpl->draw('home');
    }

    private function beginLoggedIn()
    {
        $this->raintpl->assign('login', true);
        $this->generateAktuelleErgebnisse();
        $this->generateNaechstenSpieltag();
        $this->raintpl->draw('home');
    }

    private function generateAktuelleErgebnisse()
    {
        $query = "SELECT `spieltag` FROM `spieltage` WHERE `datum` < CURDATE() LIMIT 1;";
        $this->generate($query, 'aktuelleErgebnisse', 'aktuelleSpiele', 'spieltagAktuell');
    }

    private function generateNaechstenSpieltag()
    {
        $query = "SELECT `spieltag` FROM `spieltage` WHERE `datum` >= CURDATE() LIMIT 1;";
        $this->generate($query, 'naechsterSpieltag', 'naechsteSpiele', 'spieltagNaechst');
    }

    private function generate($query, $nameBooleanField, $nameSpieltagField, $nameIdField)
    {
        $db = Database::getDbObject();
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
            $this->raintpl->assign($nameBooleanField, true);
            $this->raintpl->assign($nameSpieltagField, $st);
            $this->raintpl->assign($nameIdField, $id);
        }
    }
}

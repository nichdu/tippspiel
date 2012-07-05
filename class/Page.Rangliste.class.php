<?php
/**
 * Gibt eine Rangliste an Benutzern aus
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class RanglistePage
{
    /**
     * @var RainTPL
     */
    private $tpl;

    public function __construct()
    {
        $this->tpl = new RainTPL();
        $this->generateListe();
        $this->draw();
    }

    private function generateListe()
    {
        $vorlaeufigesArray = array();
        $punkteArray = array();
        $uidArray = User::getUIDArray();
        foreach ($uidArray as $uid)
        {
            $username = User::getNameById($uid);
            $u = new User($username);
            $vorlaeufigesArray[$uid] = array();
            $vorlaeufigesArray[$uid]['username'] = $username;
            $punkteDetail = $u->getPunkteDetail();
            $vorlaeufigesArray[$uid]['exakt'] = $punkteDetail['ergebnis']['anzahl'];
            $vorlaeufigesArray[$uid]['verhaeltnis'] = $punkteDetail['differenz']['anzahl'];
            $vorlaeufigesArray[$uid]['sieger'] = $punkteDetail['sieger']['anzahl'];
            $vorlaeufigesArray[$uid]['punkte'] = $punkteDetail['gesamt']['punkte'];

            $punkteArray[$uid] = $punkteDetail['gesamt']['punkte'];
        }
        unset ($uid, $punkteDetail, $u);
        asort($punkteArray, SORT_NUMERIC);
        $punkteArray = array_reverse($punkteArray, true);
        $finalArray = array();
        $i = 1;
        foreach ($punkteArray as $uid => $punkte)
        {
            $finalArray[$i] = $vorlaeufigesArray[$uid];
            $i++;
        }
        unset($punkteArray, $uid, $punkte, $vorlaeufigesArray, $i);
        $this->tpl->assign('points', $finalArray);
    }

    private function draw()
    {
        StartUp::AssignVars($this->tpl, 'Rangliste');
        $this->tpl->assign('username', $_SESSION['session']->getUserName());
        $this->tpl->draw('rangliste');
    }
}

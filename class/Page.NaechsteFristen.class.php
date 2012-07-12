<?php
/**
 * Gibt die naechsten n Tippabgabefristen an
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class NaechsteFristenPage
{
    /**
     * @var RainTPL
     */
    private $tpl;

    /**
     * @var int
     */
    private $n;

    /**
     * @var bool
     */
    private $display;

    /**
     * @param bool $display Soll die Ausgabe angezeigt werden oder als String
     * zurueckgegeben werden (default: true, wird angezeigt)
     * @param int $n Anzahl der anzuzeigenden Tippfristen (default: 5)
     */
    public function __construct($display = true, $n = 5)
    {
        $this->tpl = new RainTPL();
        $this->display = $display == true;
        $this->n = (int)$n;
    }

    /**
     * Generiert die Ausgabe nach der im Konstruktor vorgegebenen Regeln
     * @return string|null String, wenn $display im Konstruktor auf false gesetzt wurde, ansonsten null
     * und das Template wird direkt ausgegeben
     */
    public function generate()
    {
        $spieltagArray = $this->loadFromDatabase();
        $this->tpl->assign('spieltage', $spieltagArray);
        if ($this->display)
        {
            StartUp::AssignVars($this->tpl, 'Nächste Tippfristen');
            $this->tpl->draw('naechste_fristen', false);
        }
        else
        {
            return $this->tpl->draw('naechste_fristen', true);
        }
        return null;
    }

    /**
     * Laed die naechsten $this->n Spieltage aus der Datenbank und gibt sie als Array (int)=>(DateTime) zurueck
     * ( (spieltag)=>(Tippfrist) )
     * @return DateTime[]
     * @throws SpieltagExistiertNichtException
     */
    private function loadFromDatabase()
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `spieltag`, `tipp_ende` FROM `spieltage` WHERE `tipp_ende` > NOW()
            ORDER BY `tipp_ende` ASC LIMIT ?;");
        $stmt->bind_param('i', $this->n);
        $arr = array();
        if ($stmt->execute())
        {
            $st = null;
            $tippEnde = null;
            $stmt->bind_result($st, $tippEnde);
            while ($stmt->fetch())
            {
                $arr[$st] = new DateTime($tippEnde);
            }
        }
        else
        {
            throw new SpieltagExistiertNichtException();
        }
        return $arr;
    }
}

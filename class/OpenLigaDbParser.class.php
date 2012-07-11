<?php
/**
 * Laedt Daten aus der OpenLigaDB
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class OpenLigaDbParser
{
    const openLigaUrl = 'http://openligadb-json.heroku.com/api/';

    public static function writeNewSpieltagIntoDatabase($spieltag, $liga = 'bl1', $saison = TIPPSPIEL_CONF_CURRENT_SAISON)
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("INSERT INTO `spiele`(`spieltag`, `heim`, `auswaerts`)
            VALUES(?, ?, ?);");
        $arr = self::getSpieltag($spieltag, $liga, $saison);
        foreach ($arr['matchdata'] as $spiel)
        {
            $heim = (int)$spiel['id_team1'];
            $auswaerts = (int)$spiel['id_team2'];
            $stmt->bind_param('iii', $spieltag, $heim, $auswaerts);
            $stmt->execute();
        }
    }

    public static function writeVergangeneSpieltageIntoDatabase()
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `spieltag` FROM `spieltage` WHERE `datum` < CURDATE();");
        $stmt->execute();
        $spieltag = 0;
        $stmt->bind_result($spieltag);
        while ($stmt->fetch())
        {
            self::writeToreIntoDatabase($spieltag);
        }
    }

    private static function writeToreIntoDatabase($spieltag)
    {
        $db = Database::getDbObject();
        $arr = self::getSpieltag($spieltag);
        foreach ($arr['matchdata'] as $spiel)
        {
            if (is_null($spiel['match_results'])) { continue; }
            $stmt = $db->prepare("SELECT `id` FROM `spiele` WHERE `heim` = ? AND `auswaerts` = ?;");
            $stmt->bind_param('ii', $heim, $auswaerts);
            $heim = (int)$spiel['id_team1'];
            $auswaerts = (int)$spiel['id_team2'];
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0)
            {
                $stmt->bind_result($spiel_id);
                $stmt->fetch();
                $stmt->prepare("SELECT `spiel_id` FROM `ergebnisse` WHERE `spiel_id` = ?;");
                $stmt->bind_param('i', $spiel_id);
                $stmt->execute();
                $stmt->store_result();
                if ($stmt->num_rows === 0)
                {
                    $heimTore = 0;
                    $auswTore = 0;
                    foreach ($spiel['match_results']['match_result'] as $res)
                    {
                        if ($res['result_name'] === 'Endergebnis')
                        {
                            $heimTore = $res['points_team1'];
                            $auswTore = $res['points_team2'];
                            break;
                        }
                    }
                    $stmt->prepare("INSERT INTO `ergebnisse` VALUES(?,?,?);");
                    $stmt->bind_param('iii', $spiel_id, $heimTore, $auswTore);
                    $stmt->execute();
                }
            }
        }
    }

    /**
     * @param int $spieltag Der abzurufende Spieltag
     * @param string $liga optional. Der abzurufenden Liga-Identifier
     * @param int $saison optional. Die abzurufende Saison
     * @return array
     */
    public static function getSpieltag($spieltag, $liga = 'bl1', $saison = TIPPSPIEL_CONF_CURRENT_SAISON)
    {
        $fn = 'matchdata_by_group_league_saison';
        $args = array(
            'group_order_id' => $spieltag,
            'league_saison' => $saison,
            'league_shortcut' => $liga,
        );

        return self::get($fn, $args);
    }

    /**
     * @param $function string Die aufzurufende API-Funktion
     * @param $arguments string[] Die Argumente zu der Funktion
     * @return array
     */
    private static function get($function, $arguments)
    {
        $url = self::openLigaUrl . $function . '?';
        foreach ($arguments as $k => $v)
        {
            $url .= $k . '=' . urlencode($v) . '&';
        }
        $url = substr($url, 0, -1);

        $dt = file_get_contents($url);
        $arr = json_decode($dt, true);
        unset ($url, $dt);
        return $arr;
    }
}

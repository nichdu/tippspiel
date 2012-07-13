<?php
/**
 * Konfigurationsdatei fuer Tippspiel-Software
 * Diese Konfigurationsdatei sollte an die Konfiguration angepast werden und in config.inc umbenannt
 * werden. Ausserdem sollten jedem Nutzer die Schreibrechte an dieser Datei genommen werden.
 * Leserechte sollte nur der Webserver haben.
 */

/**
 * @const TIPPSPIEL_CONF_HOST Der Host, auf dem die Datenbank laeuft
 * @const TIPPSPIEL_CONF_USER Der Datenbankbenutzer
 * @const TIPPSPIEL_CONF_PAWD Das Datenbankpasswort
 * @const TIPPSPIEL_CONF_DABA Die zu nutzende Datenbank
 */
define('TIPPSPIEL_CONF_HOST', 'localhost');
define('TIPPSPIEL_CONF_USER', 'tippspiel');
define('TIPPSPIEL_CONF_PAWD', 'passwort');
define('TIPPSPIEL_CONF_DABA', 'tippspiel');

/**
 * @const TIPPSPIEL_CONF_ERGEBNIS_KORREKT Die Anzahl zu vergebenen Punkte, wenn das Ergebnis korrekt getippt wurde
 * @const TIPPSPIEL_CONF_DIFFERENZ_KORREKT Die Anzahl zu vergebenen Punkte, wenn die Tordifferenz korrekt getippt wurde
 * @const TIPPSPIEL_CONF_SIEGER_KORREKT Die Anzahl zu vergebener Punkte, wenn der Sieger korrekt getippt wurde
 */
define('TIPPSPIEL_CONF_ERGEBNIS_KORREKT', 4);
define('TIPPSPIEL_CONF_DIFFERENZ_KORREKT', 3);
define('TIPPSPIEL_CONF_SIEGER_KORREKT', 2);

/**
 * @const TIPPSPIEL_CONF_PROTO Das Protokoll, unter dem das Tippspiel standardmaessig zu erreichen ist ('http' || 'https')
 * @const TIPPSPIEL_CONF_DOMAIN Die Domain, unter der das Tippspiel zu erreichen ist
 * @const TIPPSPIEL_CONF_PATH Der Pfad, unter dem das Tippspiel zu erreichen ist
 * @const TIPPSPIEL_CONF_TITLE Der <title> fuer das Tippspiel
 */
define('TIPPSPIEL_CONF_PROTO', 'http');
define('TIPPSPIEL_CONF_DOMAIN', 'tippspiel.example.com');
define('TIPPSPIEL_CONF_PATH', '/');
define('TIPPSPIEL_CONF_TITLE', 'Tippspiel');

/**
 * @const TIPPSPIEL_CONF_CURRENT_SAISON Die aktuelle Saison des Tippspiels (int: Die Jahreszahl des ersten Saisonjahres)
 */
define('TIPPSPIEL_CONF_CURRENT_SAISON', 2012);

/**
 * @conf TIPPSPIEL_CONF_VALIDITY Die Zeit in Sekunden, die ein permanenter Login gueltig ist
 */
define('TIPPSPIEL_CONF_VALIDITY', 365 * 24 * 60 * 60);
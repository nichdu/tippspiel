<?php
/**
 * Konfigurationsdatei fuer Tippspiel-Software
 * Diese Konfigurationsdatei sollte an die Konfiguration angepast werden und in config.inc umbenannt
 * werden. Ausserdem sollten jedem Nutzer die Schreibrechte an dieser Datei genommen werden.
 * Leserechte sollte nur der Webserver haben.
 */

define('TIPPSPIEL_CONF_HOST', 'localhost');
define('TIPPSPIEL_CONF_USER', 'tippspiel');
define('TIPPSPIEL_CONF_PAWD', 'passwort');
define('TIPPSPIEL_CONF_DABA', 'tippspiel');

define('TIPPSPIEL_CONF_ERGEBNIS_KORREKT', 4);
define('TIPPSPIEL_CONF_DIFFERENZ_KORREKT', 3);
define('TIPPSPIEL_CONF_SIEGER_KORREKT', 2);

define('TIPPSPIEL_CONF_PROTO', 'http');
define('TIPPSPIEL_CONF_DOMAIN', 'tippspiel.example.com');
define('TIPPSPIEL_CONF_PATH', '/');
define('TIPPSPIEL_CONF_TITLE', 'Tippspiel');

define('TIPPSPIEL_CONF_CURRENT_SAISON', 2012);

define('TIPPSPIEL_CONF_VALIDITY', 365 * 24 * 60 * 60);
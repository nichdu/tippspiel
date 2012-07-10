<?php
/**
 * Startseite der Applikation, die alles weitere aufruft
 */
    // Log all errors but don't show them
    error_reporting(E_ALL | E_STRICT);// & ~E_NOTICE);
    ini_set('display_errors', '0');
    include './class/StartUp.class.php';
    new StartUp();

    $selector = PageSelector::getSelector();
    $selector->selectPage();
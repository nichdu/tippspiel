<?php
/**
 * Diese Klasse laed alles benoetigte
 */
class Loader
{
    public function __construct()
    {
        include '../config.php';
        spl_autoload_register(array($this, 'autoLoad'));
    }

    private function autoLoad($class)
    {
        $ret = include './' . $class . '.class.php';
        if (!$ret)
        {
            include './Exception/' . $class . '.class.php';
        }
    }
}

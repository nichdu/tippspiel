<?php
/**
 * Diese Klasse laed alles benoetigte
 */
class Loader
{
    public function __construct()
    {
        include __DIR__ . '/../config.php';
        spl_autoload_register(array($this, 'autoLoad'));
    }

    private function autoLoad($class)
    {
        if (substr($class, -4) === 'Page')
        {
            $cn = substr($class, 0, -4);
            $ret = include __DIR__ . '/Page.' . $cn . '.class.php';
        }
        if (!$ret)
        {
            $ret = include __DIR__ . '/' . $class . '.class.php';
            if (!$ret)
            {
                $ret = include __DIR__ . '/Exception/' . $class . '.class.php';
            }
        }
    }
}

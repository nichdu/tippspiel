<?php
/**
 * Diese Klasse laed alles benoetigte
 */
class Loader
{
    public function __construct()
    {
        include __DIR__ . '/../config/config.inc';
        spl_autoload_register(array($this, 'autoLoad'));
    }

    private function autoLoad($class)
    {
        $ret = false;
        if (substr($class, -4) === 'Page')
        {
            $cn = substr($class, 0, -4);
            if (file_exists(__DIR__ . '/Page.' . $cn . '.class.php'))
                $ret = include __DIR__ . '/Page.' . $cn . '.class.php';
        }
        if (!$ret)
        {
            if (file_exists(__DIR__ . '/' . $class . '.class.php'))
            {
                $ret = include __DIR__ . '/' . $class . '.class.php';
            }
            if (!$ret)
            {
                if (file_exists(__DIR__ . '/Exception/' . $class . '.class.php'))
                {
                    $ret = include __DIR__ . '/Exception/' . $class . '.class.php';
                }
            }
        }
    }
}

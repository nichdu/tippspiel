<?php
/**
 * Startet die Anwendung
 */
class StartUp
{
    public function __construct()
    {
        include './Session.class.php';
        session_start();
        $this->sendHeader();
        include './Loader.class.php';
        new Loader();
        include '../lib/rain.tpl.class.php';
        include '../lib/PasswordHash.php';
        $this->createSession();
        $this->defineConstants();
        $this->configureRainTpl();
        if (!$_SESSION['session']->getLogin())
        {
            $_SESSION['session']->checkPermanentLogin();
        }
    }

    private function createSession()
    {
        if (!isset($_SESSION['session']))
        {
            $_SESSION['session'] = new Session();
        }
    }

    private function sendHeader()
    {
        header("Content-type: text/html; charset=UTF-8");
    }

    private function defineConstants()
    {
        define('TIPPSPIEL_AUSWAERTSSIEG', 31999);
        define('TIPPSPIEL_UNENTSCHIEDEN', 32000);
        define('TIPPSPIEL_HEIMSIEG', 32001);

        define('TIPPSPIEL_USER_SUCCESS', 32100);
        define('TIPPSPIEL_USER_EXIST', 32101);
        define('TIPPSPIEL_USER_UNKNOWN_ERROR', 32102);

        define('ERR_DUP_ENTRY', 1062);

        define('TIPPSPIEL_OWN_PROFILE', 32050);
        define('TIPPSPIEL_OTHER_PROFILE', 32051);
    }

    private function configureRainTpl()
    {
        RainTPL::configure('base_url', TIPPSPIEL_CONF_PROTO . '://' . TIPPSPIEL_CONF_DOMAIN . TIPPSPIEL_CONF_PATH);
        RainTPL::configure( 'path_replace_list', array( 'img', 'link', 'script' ) );
    }

    /**
     * @static
     * @param RainTPL $tpl Das zuzuweisende Template
     * @param $title string Der Titel der Seite
     */
    public static function AssignVars(RainTPL &$tpl, $title = '')
    {
        $tpl->assign('login', $_SESSION['session']->getLogin());
        $tpl->assign('title', $title === '' ? TIPPSPIEL_CONF_TITLE : $title . ' | ' . TIPPSPIEL_CONF_TITLE);
        if ($_SESSION['session']->getErrno() !== 0)
        {
            $tpl->assign('permlogin_error', true);
            $tpl->assign('permlogin_msg', $_SESSION['session']->getError());
        }
        else
        {
            $tpl->assign('permlogin_error', false);
        }
    }
}

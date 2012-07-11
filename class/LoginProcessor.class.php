<?php
/**
 * Diese Seite bearbeitet Login-Requests
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class LoginProcessor
{
    public function __construct($logout = false)
    {
        if ($logout)
        {
            $_SESSION['session']->logout();
            header("Location: " . TIPPSPIEL_CONF_PROTO . "://" . TIPPSPIEL_CONF_DOMAIN . TIPPSPIEL_CONF_PATH);
            exit;
        }
        else
        {
            $ret = null;
            if (!isset($_POST['login_access_token']) || $_POST['login_access_token'] !== $_SESSION['login_access_token'])
            {
                unset($_SESSION['login_access_token']);
                exit;
            }
            else
            {
                unset($_SESSION['login_access_token']);
                $username = $_POST['username'];
                $password = $_POST['password'];
                $ret = array();
                if ($_SESSION['session']->login($username, $password, isset($_POST['stayLoggedIn']) && $_POST['stayLoggedIn'] == 'true'))
                {
                    $ret['message'] = 'Erfolgreich eingeloggt.';
                    $ret['error'] = 0;
                }
                else
                {
                    $ret['error'] = $_SESSION['session']->getLoginErrno();
                    $ret['message'] = $_SESSION['session']->getLoginError();
                }
            }
            header("Content-type: application/json");
            print json_encode($ret);
        }
    }
}

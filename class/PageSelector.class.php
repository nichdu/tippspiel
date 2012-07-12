<?php
/**
 * Waeht die aufgerufene Seite aus und ruft die dazugehoerige Klasse auf
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class PageSelector
{
    /**
     * @var PageSelector
     */
    private static $selector;

    /**
     * Gibt den Selektor zurueck, der die aktuelle Anfrage haelt
     *
     * @static
     * @return PageSelector
     */
    public static function getSelector()
    {
        if (self::$selector === null)
        {
            self::$selector = new PageSelector();
        }
        return self::$selector;
    }

    /**
     * @var string[]
     */
    private $q;

    /**
     * @var string
     */
    private $fullQuery;

    /**
     * @var bool
     */
    private $alreadyCalled = false;

    private function __construct()
    {
        $this->q = isset($_SERVER['PATH_INFO']) ? explode('/', substr($_SERVER['PATH_INFO'], 1)) : array();
        $this->fullQuery = $_SERVER['REQUEST_URI'];
    }

    /**
     * Waehlt die korrekte Seite nach dem aufgerufenen Pfad aus und ruft die dazugehoerige Klasse auf
     */
    public function selectPage()
    {
        try
        {
            $this->callPage();
        }
        catch (Error404Exception $e)
        {
            new Error404Page();
        }
        catch (Error403Exception $e)
        {
            new HomePage();
        }
        catch (Exception $e)
        {
            print 'Ein unbekannter Fehler ist aufgetreten. Bitte versuchen Sie es später noch einmal '
                . 'oder wenden Sie sich an einen Administrator.';
        }
    }

    public function callPage()
    {
        if ($this->alreadyCalled) { return; }
        $this->alreadyCalled = true;
        if (!isset($_SERVER['PATH_INFO']) || trim($_SERVER['PATH_INFO']) === '' || trim($_SERVER['PATH_INFO']) === '/')
        {
            new HomePage();
            return;
        }
        switch ($this->q[0])
        {
            case 'registrieren':
                if (isset($this->q[1]) && $this->q[1] === 'submit')
                {
                    new RegisterProcessor();
                }
                else
                {
                    if ($_SESSION['session']->getLogin()) // der Benutzer ist bereits eingelogt, Registrierung ist also unnoetig
                    {
                        new HomePage(); // Startseite wird aufgerufen
                    }
                    else
                    {
                        $tpl = new RainTPL();
                        $_SESSION['register_access_token'] = $_SESSION['session']->token();
                        $tpl->assign('register_access_token', $_SESSION['register_access_token']);
                        StartUp::AssignVars($tpl, 'Registrieren');
                        $tpl->draw('registrieren');
                    }
                }
                break;
            case 'rangliste':
                new RanglistePage();
                break;
            /*
            case 'tippabgabe':
                if (isset($this->q[1]) && $this->q[1] === 'submit')
                {
                    new TippabgabeProcessor();
                }
                else
                {
                    new TippabgabePage();
                }
                break;
            */
            case 'spieluebersicht':
                new ErgebnissePage();
                break;
            case 'profil':
                if (isset($this->q[1]) && $this->q[1] === 'aendern')
                {
                    new EditProfileProcessor();
                }
                else if (isset($this->q[1]))
                {
                    new FremdProfilPage($this->q[1]);
                }
                else
                {
                    new EigeneProfilPage();
                }
                break;
            case 'logout':
                new LoginProcessor(true);
                break;
            case 'login':
                new LoginProcessor();
                break;
            default:
                new DatabasePage($this->q[0]);
        }
    }

    /**
     * Gibt den Querystring fuer eine gegebene Position zurueck
     * @param $position int
     * @return string|null Der Querystring an gegebener Position oder null, wenn nicht existent
     */
    public function getQuery($position)
    {
        return isset($this->q[$position]) ? $this->q[$position] : null;
    }

    /**
     * Gibt den gesamten Request-URI der Anfrage zurueck
     * @return string
     */
    public function getFullQuery()
    {
        return $this->fullQuery;
    }
}

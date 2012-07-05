<?php
/**
 * Allgemeine Klasse fuer Profilseiten
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
abstract class ProfilPage
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var User
     */
    private $user;

    /**
     * @var RainTPL
     */
    protected $tpl;

    /**
     * @param $username string Der Benutzername des anzuzeigenden Benutzers
     */
    protected function __construct($username)
    {
        $this->name = $username;
        $this->loadFromDatabase();
        $this->tpl = new RainTPL();
        $this->tpl->assign('name', $this->getUser()->getName());
    }

    private function loadFromDatabase()
    {
        $this->user = new User($this->name);
    }

    /**
     * @return User
     */
    protected function getUser()
    {
        return $this->user;
    }

    /**
     * Gibt die Seite aus
     * @abstract
     */
    abstract protected function draw();

    protected function assignPunkte()
    {
        $this->tpl->assign('punkte', $this->getUser()->getPunkteDetail());
    }
}

<?php
/**
 * Stellt das Profil des aktuelle eingelogten Benutzers dar
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class EigeneProfilPage extends ProfilPage
{
    public function __construct()
    {
        parent::__construct($_SESSION['session']->getUserName());
        $this->assignUserVars();
        $this->draw();
    }

    private function assignUserVars()
    {
        $this->tpl->assign('email', $this->getUser()->getEmail());
    }

    protected function draw()
    {
        StartUp::AssignVars($this->tpl, $this->getUser()->getName());
        $this->assignPunkte();
        $this->tpl->assign('ownprofile', true);
        $this->tpl->draw('profil');
    }
}

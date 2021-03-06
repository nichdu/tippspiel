<?php
/**
 * Zeigt eine Profilseite fuer andere als den aufrufenden Benutzer an
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class FremdProfilPage extends ProfilPage
{
    public function __construct($username)
    {
        if ($_SESSION['session']->getLogin())
        {
            try
            {
                parent::__construct($username);
            }
            catch (UserExistiertNichtException $e)
            {
                throw new Error404Exception();
            }
            $this->draw();
        }
        else
        {
            throw new Error403Exception();
        }
    }

    protected function draw()
    {
        StartUp::AssignVars($this->tpl, $this->getUser()->getName());
        $this->assignPunkte();
        $this->tpl->assign('ownprofile', false);
        $this->tpl->draw('profil');
    }
}

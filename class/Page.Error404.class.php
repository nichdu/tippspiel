<?php
/**
 * Klasse fuer 404-Fehler
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class Error404Page
{
    public function __construct()
    {
        header("HTTP/1.1 404 Not Found");
        $tpl = new RainTPL();
        StartUp::AssignVars($tpl, 'Seite nicht gefunden');
        $pageSelector = PageSelector::getSelector();
        $tpl->assign('uri', htmlentities($pageSelector->getFullQuery()));
        $tpl->draw('404');
    }
}

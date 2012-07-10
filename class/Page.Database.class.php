<?php
/**
 * Laedt eine Seite aus der Datenbank und zeigt diese an
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class DatabasePage
{
    /**
     * @var string
     */
    private $id;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $ctnt;
    /**
     * @var string
     */
    private $requireLogin;

    const regex = '/^[a-z\-]{3,60}$/';

    public function __construct($id)
    {
        if (preg_match(self::regex, $id) !== 1)
        {
            throw new Error404Exception();
        }
        $this->id = $id;
        $this->loadFromDatabase();
        if ($this->requireLogin && $_SESSION['session']->getLogin())
        {
            throw new Error403Exception();
        }
        $this->draw();
    }

    private function loadFromDatabase()
    {
        $db = Database::getDbObject();
        $stmt = $db->prepare("SELECT `title`, `content`, `requires_login` FROM `pages` WHERE `id` = ?;");
        $stmt->bind_param('s', $this->id);
        if (!$stmt->execute())
        {
            throw new mysqli_sql_exception($stmt->error, $stmt->errno);
        }
        $stmt->store_result();
        if ($stmt->num_rows === 0)
        {
            throw new Error404Exception();
        }
        $bool = 0;
        $stmt->bind_result($this->title, $this->ctnt, $bool);
        $this->requireLogin = $bool == 1;
        $stmt->fetch();
    }

    private function draw()
    {
        $tpl = new RainTPL();
        StartUp::AssignVars($tpl, $this->title);
        $tpl->assign('heading', $this->title);
        $tpl->assign('ctnt', $this->ctnt);
        $tpl->draw('db_page');
    }
}

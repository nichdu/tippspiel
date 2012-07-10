<?php
/**
 * Uebernimmt die Ueberpruefung von Eingaben des Benutzers
 * @author Tjark Saul <tjark@saul.li>
 * @copyright Copyright (c) 2012 Tjark Saul. All rights reserved.
 */
class Checker
{
    const userRegEx = '/^[a-zA-Z0-9_]{5,60}$/';
    const pwdRegEx = '/^\S.{6,70}\S$/';
    const emailRegEx = '/^([A-Za-z0-9_\-\.\+]+)@([A-Za-z0-9_\-\.]+)\.([A-Za-z]{1,5})/';

    public function checkUserName($username)
    {
        $check = preg_match(self::userRegEx, $username) === 1;
        if ($check)
            $check = strtolower($username) !== 'aendern';
        return $check;
    }

    public function checkEmailAddress($address)
    {
        $check = preg_match(self::emailRegEx, $address) === 1;
        return $check;
    }

    public function checkPassword($password)
    {
        $check = preg_match(self::pwdRegEx, $password) === 1;
        return $check;
    }
}

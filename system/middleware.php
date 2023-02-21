<?php

use App\Page;

if ((isUserSessionExist() || isUserCookieExist()) && !bot_detected()) {

    //Check if user exist & valide
    if (!getUserIdSession() or !appoeMinRole() or !isUserExist(getUserIdSession()) or !getUserStatus()) {

        disconnectUser(false);
        if (!headers_sent()) {
            header('location:' . WEB_DIR_URL);
        }
        exit();
    }

    //Check valid session
    $key = sha1(getUserLogin() . $_SERVER['REMOTE_ADDR']);
    if ($key != getUserConnexion()['loginUserConnexion']) {

        disconnectUser(false);
        if (!headers_sent()) {
            header('location:' . WEB_DIR_URL);
        }
        exit();
    }

    //Check if user have right access to this page
    $Page = new Page(substr(basename($_SERVER['PHP_SELF']), 0, -4));
    if (!$Page->isExist() or $Page->getMinRoleId() > getUserRoleId()) {

        disconnectUser(false);
        if (!headers_sent()) {
            header('location:' . WEB_DIR_URL);
        }
        exit();
    }

    setAppPageName($Page->getName());
    setAppPageSlug($Page->getSlug());

} else {

    disconnectUser(false);
    if (!headers_sent()) {
        header('location:' . WEB_DIR_URL);
    }
    exit();
}
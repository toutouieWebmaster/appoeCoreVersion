<?php

use App\Flash;
use App\Shinoui;
use App\Users;

if (pageSlug() == 'hibour') {
    if ((isUserSessionExist() || isUserCookieExist()) && !bot_detected()) {

        if (!empty($_GET['forwardPage'])) {
            if (!headers_sent()) {
                $_GET['forwardPage'] = cleanData($_GET['forwardPage']);
                header('location:' . $_GET['forwardPage']);
            }
            exit();
        }

        header('location:app/page/');
        exit();
    }
    disconnectUser(false);
}
if (isset($_POST['APPOECONNEXION'])) {

    if (checkPostAndTokenRequest(false)) {

        //Clean form
        $_POST = cleanRequest($_POST);

        if (!empty($_POST['loginInput'])
            and !empty($_POST['passwordInput'])
            and empty($_POST['identifiant'])
            and !empty($_POST['checkPass'])) {

            $login = trim($_POST['loginInput']);
            $pass = $_POST['passwordInput'];

            //check length of login & pass
            if (strlen($login) < 70 && strlen($pass) < 30) {

                $User = new Users();
                $User->setLogin($login);
                $User->setPassword($pass);

                //if user not exist or not have minimum permission role
                if (!$User->authUser() || !appoeMinRole(getRoleId($User->getRole()))) {
                    Flash::setMsg(trans('Vous n\'êtes pas identifié') . ' !');

                } else {

                    $sessionCrypted = Shinoui::Crypter($User->getId() . '!a6fgcb!f152ddb3!' . sha1($User->getLogin() . $_SERVER['REMOTE_ADDR']));
                    $_SESSION['auth' . slugify($_SERVER['HTTP_HOST'])] = $sessionCrypted;
                    $options = array('expires' => time() + (12 * 3600), 'path' => '/', 'secure' => false, 'httponly' => true, 'samesite' => 'Strict');
                    setcookie('hibour' . slugify($_SERVER['HTTP_HOST']), $sessionCrypted, $options);
                    mehoubarim_connecteUser();

                    //Backup database
                    appBackup();

                    //Check for forwarding page
                    if (!empty($_GET['forwardPage'])) {
                        if (!headers_sent()) {
                            $_GET['forwardPage'] = cleanData($_GET['forwardPage']);
                            header('location:' . $_GET['forwardPage']);
                        }
                        exit();
                    }

                    header('location:/app/page/');
                    exit();
                }
            }
        }
    }
}
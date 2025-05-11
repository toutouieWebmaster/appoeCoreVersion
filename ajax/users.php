<?php
require_once('header.php');

use App\Users;

if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        //Ban user
        if (!empty($_POST['idDeleteUser'])) {
            $User = new Users($_POST['idDeleteUser']);
            if ($User->delete()) {
                echo json_encode(true);
            }
            exit();
        }

        //Valide user
        if (!empty($_POST['idValideUser'])) {
            $User = new Users($_POST['idValideUser']);
            $User->setStatut(1);
            if ($User->update()) {
                echo json_encode(true);
            }
            exit();
        }

        //Get users roles
        if (!empty($_POST['GETUSERSROLES'])) {
            echo json_encode(getRoles(), JSON_UNESCAPED_UNICODE);
            exit();
        }
    }
}
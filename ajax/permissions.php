<?php
require_once('header.php');

use App\Menu;

if (checkAjaxRequest()) {

    if (false !== getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        //update permission
        if (isset($_POST['updatePermission'])
            && !empty($_POST['id'])
            && !empty($_POST['name'])
            && !empty($_POST['slug'])
            && !empty($_POST['minRoleId'])
            && !empty($_POST['parentId'])
            && isset($_POST['statut'])
            && isset($_POST['orderMenu'])
            && isset($_POST['pluginName'])
        ) {
            $Menu = new Menu();
            $Menu->feed($_POST);
            if ($Menu->updateMenu()){
                echo 'true';
                exit();
            }
        }

        //add new permission
        if (isset($_POST['ADDPERMISSION'])
            && !empty($_POST['id'])
            && !empty($_POST['slug'])
            && !empty($_POST['name'])
            && !empty($_POST['minRoleId'])
            && isset($_POST['statut'])
            && !empty($_POST['parentId'])
            && isset($_POST['orderMenu'])
            && isset($_POST['pluginName'])
        ) {
            $Menu = new Menu();
            $Menu->feed($_POST);
            if ($Menu->insertMenu()){
                echo 'true';
                exit();
            }
        }
    }
}
echo 'false';
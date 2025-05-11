<?php
require_once('header.php');
if (checkAjaxRequest()) {

    if (!empty($_POST['lang']) && !empty($_POST['interfaceLang'])) {

        if ($_POST['interfaceLang'] == 'interface') {

            //set lang for app content interface
            $_SESSION['APP_LANG'] = $_POST['lang'];

        } else {

            //TODO if ($_POST['interfaceLang'] == 'content') {}
            //On cherche à traduire le contenu du b-o en une autre langue, par ex. "dashboard" au lieu de "tableau de bord"

            //default set lang for app content and website
            setCookiesLang($_POST['lang']);
        }

        echo 'true';
        exit();
    }
}
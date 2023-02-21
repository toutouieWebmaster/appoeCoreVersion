<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once(WEB_SYSTEM_PATH . 'middleware.php');
includePluginsFiles(true);
?>
<!doctype html>
<html lang="<?= APP_LANG; ?>">
<head>
    <meta charset="utf-8">
    <?= getAppoeFavicon(); ?>
    <title><?= trans(getAppPageName()); ?></title>
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="<?= WEB_LIB_URL; ?>js/appoEditor/appoEditor.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css"
    integrity="sha384-Bfad6CLCknfcloXFOyFnlgtENryhrpZCe29RTifKEixXQZ38WheV+i/6YWSzkz3V" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="<?= WEB_LIB_URL; ?>js/datatable/dataTables.min.css"/>
    <link rel="stylesheet" type="text/css" href="<?= WEB_TEMPLATE_URL; ?>plugins/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?= WEB_TEMPLATE_URL; ?>plugins/waves/waves.min.css">
    <link rel="stylesheet" href="<?= WEB_TEMPLATE_URL; ?>css/appoe.css" type="text/css">
    <?php showThemeRoot(); includePluginsStyles(); ?>
    <!-- SCRIPT -->
    <script src="<?= WEB_LIB_URL; ?>js/jquery-3.5.1.min.js"></script>
</head>
<body>
<div id="loader">
    <div class="loaderContent">
        <div class="spinnerAppoe">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
        <div class="inline"><?= trans('Chargement'); ?></div>
        <div id="loaderInfos"></div>
    </div>
</div>
<div id="site">
    <a id="back-top" href="#"><i class="fas fa-chevron-up"></i></a>
    <header class="main-navbar-header">
        <?php include(WEB_TEMPLATE_PATH . 'menuUser.php'); ?>
    </header>
    <div class="wrapper">
        <nav id="sidebar" class="nav-sidebar">
            <?php include(WEB_TEMPLATE_PATH . 'menu.php'); ?>
        </nav>
        <div id="content-area">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12" id="content-size">
                    <div class="panel">
                        <?php if (defined('MEHOUBARIM_MSG') && !empty(MEHOUBARIM_MSG)): ?>
                            <div class="p-2 float-right text-danger"><?= MEHOUBARIM_MSG; ?></div>
                        <?php endif; ?>
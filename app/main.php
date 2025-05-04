<?php
//Rename session
session_name('APPOE');

//Start session
session_start();

//Change PHP version in header information
if (!headers_sent()) {
    header("x-powered-by: PHP/999");
}

//Get ini.php
require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/system/ini.php');

//Get custom Autoloader
require(WEB_APP_PATH . 'Autoloader.php');

//Set default Autoloader
\App\Autoloader::register();

//Get Hook system
require_once(WEB_APP_PATH . 'class/Hook.php');

//Get all system functions
require_once(WEB_APP_PATH . 'functions.php');
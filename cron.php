<?php

use App\Hook;

//Get APPOE System
require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/main.php');

//Set the page filename for getting functions from declared plugins (ini.main)
setPageParam('currentPageFilename', 'cron');

//Get all plugins functions
includePluginsFiles(true);

//Attach a Hook
Hook::apply('cron');
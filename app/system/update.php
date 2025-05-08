<?php
require('ini.php');
require($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/Autoloader.php');
Autoloader::register();
require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/functions.php');

use App\Autoloader;
use App\Version;

//Github links
$gitHub = 'https://github.com/toutouieWebmaster/';
$gitHubUserContent = 'https://raw.githubusercontent.com/toutouieWebmaster/';

//Core version
Version::setFile(WEB_APP_PATH . 'version.json');
if (Version::show() && Version::getVersion() < getHttpRequest($gitHubUserContent . 'appoeCoreVersion/master/version.json')) {

    //Update Core
    if (downloadFile(ROOT_PATH . 'appoeCore.zip', 'https://github.com/toutouieWebmaster/appoeCoreVersion/archive/master.zip')) {
        if (unzipSkipFirstFolder(ROOT_PATH . 'appoeCore.zip', ROOT_PATH, 'appoeCoreVersion-master', WEB_APP_PATH)) {

            //Update Rooter
            if (downloadFile(ROOT_PATH . 'rooter.zip', 'https://github.com/toutouieWebmaster/appoeRooterVersion/archive/master.zip')) {
                if (unzipSkipFirstFolder(ROOT_PATH . 'rooter.zip', ROOT_PATH, 'appoeRooterVersion-master', ROOT_PATH)) {

                    //Update DataBase
                    updateDB();
                }
            }
        }
    }
}

//Plugins versions
$plugins = getPlugins();
if (!isArrayEmpty($plugins)) {
    foreach ($plugins as $plugin) {
        if (!empty($plugin['versionPath'])) {
            Version::setFile($plugin['versionPath']);
            if (Version::show() && Version::getVersion() < getHttpRequest($gitHubUserContent . 'appoePluginsVersions/master/' . $plugin['name'] . '/version.json')) {

                //Update Plugin
                if (downloadFile(ROOT_PATH . 'plugins.zip', 'https://github.com/toutouieWebmaster/appoePluginsVersions/archive/master.zip')) {
                    unzipSkipFirstFolder(ROOT_PATH . 'plugins.zip', ROOT_PATH, 'appoePluginsVersions-master', WEB_PLUGIN_PATH);
                    exit();
                }
            }
        }
    }
}
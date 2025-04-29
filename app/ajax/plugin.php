<?php
require_once('header.php');

use App\DB;
use App\Menu;
use App\Plugin\Cms\Cms;
use App\Plugin\ItemGlue\Article;

if (checkAjaxRequest()) {

    if (!empty($_POST['checkUserSession'])) {
        echo isUserSessionExist() ? 'true' : 'false';
        exit();
    }

    if (!empty($_REQUEST['setupPath'])) {
        activePlugin($_REQUEST['setupPath']);
        exit();
    }

    if (!empty($_REQUEST['deletePluginName'])) {

        $pluginName = $_REQUEST['deletePluginName'];

        //Backup
        DB::backup(date('Y-m-d'), 'db-' . date('H_i_s'));
        saveFiles('app/plugin/' . $pluginName);

        //Delete tables
        if (file_exists(WEB_PLUGIN_PATH . $pluginName . DIRECTORY_SEPARATOR . 'ini.php')) {
            include_once(WEB_PLUGIN_PATH . $pluginName . DIRECTORY_SEPARATOR . 'ini.php');

            if (!empty(PLUGIN_TABLES)) {
                foreach (PLUGIN_TABLES as $key => $table) {
                    DB::initialize()->deleteTable($table);
                }
            }
        }

        //Delete Menu
        $Menu = new Menu();
        $Menu->deletePluginMenu($pluginName);

        //Delete Directories & Files
        deleteAllFolderContent(WEB_PLUGIN_PATH . $pluginName);

        echo json_encode(true);
        exit();
    }

    if (!empty($_POST['checkTable'])) {

        $pluginName = $_POST['checkTable'];
        $existTable = 0;

        if (file_exists(WEB_PLUGIN_PATH . $pluginName . DIRECTORY_SEPARATOR . 'ini.php')) {
            include_once(WEB_PLUGIN_PATH . $pluginName . DIRECTORY_SEPARATOR . 'ini.php');

            if (!empty(PLUGIN_TABLES)) {
                foreach (PLUGIN_TABLES as $key => $table) {
                    if (DB::initialize()->checkTable($table)) {
                        $existTable++;
                    }
                }
            }
        }

        echo $existTable;
        exit();
    }

    //Github links
    $gitHub = 'https://github.com/toutouieWebmaster/';
    $gitHubUserContent = 'https://raw.githubusercontent.com/toutouieWebmaster/';
    //$gitHubAccessToken = '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';

    //Plugin version
    if (!empty($_POST['checkVersion'])) {
        echo getHttpRequest($gitHubUserContent . 'appoePluginsVersions/master/' . $_POST['checkVersion'] . '/version.json');
        exit();
    }

    //Core version
    if (!empty($_POST['checkSystemVersion'])) {
        echo getHttpRequest($gitHubUserContent . 'appoeCoreVersion/master/version.json');
        exit();
    }

    if (!empty($_POST['downloadPlugins'])) {

        if (downloadFile(ROOT_PATH . 'plugins.zip', $gitHub . 'appoePluginsVersions/archive/refs/heads/master.zip')) {
            if (unzipSkipFirstFolder(ROOT_PATH . 'plugins.zip', ROOT_PATH, 'appoePluginsVersions-master', WEB_PLUGIN_PATH)) {
                echo 'true';
            }
        }
        exit();
    }

    if (!empty($_POST['downloadSystemCore'])) {

        if (downloadFile(ROOT_PATH . 'appoeCore.zip', $gitHub . 'appoeCoreVersion/archive/refs/heads/master.zip')) {
            if (unzipSkipFirstFolder(ROOT_PATH . 'appoeCore.zip', ROOT_PATH, 'appoeCoreVersion-master', WEB_APP_PATH)) {
                if (downloadFile(ROOT_PATH . 'rooter.zip', $gitHub . 'appoeRooterVersion/archive/refs/heads/master.zip')) {
                    if (unzipSkipFirstFolder(ROOT_PATH . 'rooter.zip', ROOT_PATH, 'appoeRooterVersion-master', ROOT_PATH)) {

                        updateDB();
                        echo 'true';
                    }
                }
            }
        }
        exit();
    }

    if (!empty($_POST['updateSitemap'])) {

        $data = [];

        //Get Pages Slug
        $Cms = new Cms();
        $pages = $Cms->showAllPages();

        if ($pages) {
            foreach ($pages as $page) {
                if (file_exists(WEB_PATH . $page->filename . '.php')) {
                    $data[] = array('slug' => webUrl($page->slug . '/'), 'name' => $page->name);
                }
            }
        }

        //Get Articles Slug
        if (defined('DEFAULT_ARTICLES_PAGE') && !empty(DEFAULT_ARTICLES_PAGE)) {

            $Article = new Article();
            $articles = $Article->showAll();

            if ($articles) {
                foreach ($articles as $article) {
                    $data[] = array(
                        'slug' => webUrl(DEFAULT_ARTICLES_PAGE . '/' . $article->slug),
                        'name' => $article->name
                    );
                }
            }
        }

        if (generateSitemap($data)) {
            echo 'true';
        }
        exit();
    }

    if (!empty($_POST['optimizeDb'])) {

        /*$tables = DB::initialize()->getTables();
        $Menu = new Menu();
        $optimizeTables = [];
        foreach ($tables as $table) {
            $tableName = array_values((array)$table);
            if (!in_array($tableName[0], APP_TABLES) && false !== strpos($tableName[0], 'appoe')) {
                list($pre, $plugin, $pluginName, $pluginExtension) = array_pad(explode('_', $tableName[0], 4), 4, null);
                if (!is_null($pluginName) && !is_dir(WEB_PLUGIN_PATH . $pluginName)) {
                    if (DB::deleteTable($tableName[0])) {
                        $Menu->deletePluginMenu($pluginName);
                        $optimizeTables[] = $tableName[0];
                    }
                }
            }
        }*/
        $dbFolderName = date('Y-m-d');
        $dbFileName = 'db-' . date('H_i');
        DB::backup($dbFolderName, $dbFileName);
        $urlFile = WEB_APP_URL . 'backup/' . $dbFolderName . '/' . $dbFileName . '.sql.gz';
        echo trans('Tables enregistrées') . '. <a href="' . $urlFile . '"> ' . trans('Télécharger') . '</a>';// . '.' . (count($optimizeTables) > 0 ? '<br><strong>Tables supprimées :</strong> ' . implode(', ', $optimizeTables) : '');
        exit();
    }

    if (!empty($_POST['saveFile']) && !empty($_POST['folder'])) {

        $folders = [$_POST['folder']];

        if ($_POST['folder'] == 'tous') {
            $folders = ['public', 'include'];
            $dbFolderName = date('Y-m-d');
            $dbFileName = 'db-' . date('H_i');
            DB::backup($dbFolderName, $dbFileName);
            $urlFile = WEB_APP_URL . 'backup/' . $dbFolderName . '/' . $dbFileName . '.sql.gz';
            echo trans('Tables enregistrées') . '. <a href="' . $urlFile . '"> ' . trans('Télécharger') . '</a><br>';
        }

        foreach ($folders as $folder) {
            $saveFiles = saveFiles($folder);
            if (false !== $saveFiles) {
                echo trans('Dossier') . ' <strong>' . $folder . '</strong> ' . trans('enregistré') . ' (' . getSizeName($saveFiles['copySize']) . ')', '. <a href="' . $saveFiles['downloadLink'] . '"> ' . trans('Télécharger') . ' (' . getSizeName($saveFiles['zipSize']) . ')</a><br>';
            }
        }

        exit();
    }

    if (!empty($_POST['getDefinedConst'])) {

        echo defined($_POST['getDefinedConst']) ? constant($_POST['getDefinedConst']) : '';
        exit();
    }
}
<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/main.php');
includePluginsFiles(true);

//Clean data
$_POST = cleanRequest($_POST);
$allowApi = getOption('PREFERENCE', 'allowApi');
$token = getOption('DATA', 'apiToken');
if (!empty($_POST['token']) && $allowApi === 'true' && $_POST['token'] == $token) {

    if (!headers_sent()) {
        header("Content-Type: application/json; charset=UTF-8");
    }

    $lang = !empty($_POST['lang']) && is_string($_POST['lang']) ? $_POST['lang'] : false;
    $length = !empty($_POST['length']) && is_numeric($_POST['length']) ? $_POST['length'] : false;

    //Get recent articles
    if (!empty($_POST['getArticles']) && $_POST['getArticles'] == 'all') {

        echo jsonHtmlParse(getRecentArticles($length, $lang));
        exit();
    }

    //Get articles by category
    if (!empty($_POST['getArticleByCategory']) && is_numeric($_POST['getArticleByCategory'])) {

        $parent = !empty($_POST['parent']) && $_POST['parent'] == 'true';
        echo jsonHtmlParse(getArticlesByCategory($_POST['getArticleByCategory'], $parent, $length, $lang));
        exit();
    }

}

if (!headers_sent()) {
    header('HTTP/1.1 404 Not Found', true, 404);
}
echo file_exists(ROOT_PATH . '404.php') ? getFileContent(ROOT_PATH . '404.php') : getAsset('404', true);
exit();
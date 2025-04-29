<?php
require('main.php');

use App\Plugin\ItemGlue\Article;
use App\Plugin\ItemGlue\ArticleContent;
use App\Plugin\ItemGlue\ArticleMeta;
use \App\Plugin\ItemGlue\ArticleRelation;

$Article = new Article();
$ArticleContent = new ArticleContent();
$ArticleMeta = new ArticleMeta();
$ArticleRelation = new ArticleRelation();

//Creating table
$pluginSetup = $Article->createTable();
echo $pluginSetup ? trans('Table') . ' Articles ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $ArticleContent->createTable();
echo $pluginSetup ? trans('Table') . ' Articles Content ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $ArticleMeta->createTable();
echo $pluginSetup ? trans('Table') . ' Articles Meta ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $ArticleRelation->createTable();
echo $pluginSetup ? trans('Table') . ' Articles Relation ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new \App\Menu();
$data = array(
    1 => array(
        'id' => 600,
        'slug' => 'itemGlue',
        'name' => 'Articles',
        'minRoleId' => 1,
        'statut' => 1,
        'parentId' => 10,
        'pluginName' => 'itemGlue',
        'orderMenu' => '4'
    ),
    2 => array(
        'id' => 601,
        'slug' => 'allArticles',
        'name' => 'Tous les articles',
        'minRoleId' => 1,
        'statut' => 1,
        'parentId' => 600,
        'pluginName' => 'itemGlue',
        'orderMenu' => '601'
    ),
    3 => array(
        'id' => 602,
        'slug' => 'addArticle',
        'name' => 'Nouvel article',
        'minRoleId' => 1,
        'statut' => 1,
        'parentId' => 600,
        'pluginName' => 'itemGlue',
        'orderMenu' => '602'
    ),
    4 => array(
        'id' => 603,
        'slug' => 'updateArticleContent',
        'name' => 'Contenu de l\'article',
        'minRoleId' => 1,
        'statut' => 0,
        'parentId' => 600,
        'pluginName' => 'itemGlue',
        'orderMenu' => '603'
    ),
    5 => array(
        'id' => 604,
        'slug' => 'articlesArchives',
        'name' => 'Archives',
        'minRoleId' => 1,
        'statut' => 1,
        'parentId' => 600,
        'pluginName' => 'itemGlue',
        'orderMenu' => '604'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    $Menu->feed($menuData);
    if ($Menu->insertMenu()) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'itemGlue/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}

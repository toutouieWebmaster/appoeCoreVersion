<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
includePluginsFiles();
require_once(WEB_SYSTEM_PATH . 'control_public.php');
require_once(WEB_SYSTEM_PATH . 'auth_user.php');

use App\DB;
use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsMenu;
use App\Plugin\Shop\Product;
use App\Plugin\Shop\ProductContent;

//Check maintenance mode
if (checkMaintenance()) {
    setPageParam('currentPageFilename', 'maintenance');
    if (!headers_sent()) {
        header('HTTP/1.1 503 Service Unavailable', true, 503);
        header('Status: 503 Service Temporarily Unavailable');
        header('Retry-After: 3600');
    }
    echo file_exists(ROOT_PATH . 'maintenance.php') ? getFileContent(ROOT_PATH . 'maintenance.php') : getAsset('maintenance', true);
    exit();
}

//Backup database
appBackup();
if (DB::checkTable(TABLEPREFIX . 'appoe_plugin_cms')) {
    //Get Page
    $Cms = new Cms();
    $existPage = false;
    //Get Page parameters
    if (!empty($_GET['slug'])) {		
        if ($filename = $Cms->getFilenameBySlug($_GET['slug'])) {
            $Cms->setLang(LANG);
            $Cms->setFilename($filename);
            $existPage = $Cms->showByFilename();
        }

        if (!$existPage) {			
            //Check for similar page slug
            if (defined('SIMILAR_PAGES_SLUG') && !isArrayEmpty(SIMILAR_PAGES_SLUG)) {
                if (array_key_exists($_GET['slug'], SIMILAR_PAGES_SLUG)) {
                    if ($filename = $Cms->getFilenameBySlug(SIMILAR_PAGES_SLUG[$_GET['slug']])) {
                        $Cms->setLang(LANG);
                        $Cms->setFilename($filename);
                        $existPage = $Cms->showByFilename();
                        $Cms->setSlug($_GET['slug']);
                    }
                }
            }
        }

    } else {
        $existPage = $Cms->showDefaultSlug(LANG);
    }

    //Check if Page exist and accessible
    if (!$existPage || $Cms->getStatut() != 1) {
        if (!headers_sent()) {
            header('HTTP/1.1 404 Not Found', true, 404);
        }
        echo file_exists(ROOT_PATH . '404.php') ? getFileContent(ROOT_PATH . '404.php') : getAsset('404', true);
        exit();
    }

    //Get default page informations
    setPageParam('currentPageCmsID', $Cms->getId());
    setPageParam('currentPageLang', $Cms->getLang());
    setPageParam('currentPageID', $Cms->getId());
    setPageParam('currentPageType','PAGE');
    setPageParam('currentPageName', $Cms->getName());
    setPageParam('currentPageMenuName', $Cms->getMenuName());
    setPageParam('currentPageDescription', $Cms->getDescription());
    setPageParam('currentPageSlug', $Cms->getSlug());
    setPageParam('currentPageFilename', $Cms->getFilename());

    //Check if is Page or plugin page
    if (!empty($_GET['type'])) {
        if (!empty($_GET['typeSlug'])) {
            $pluginType = getPageTypes($_GET['type']);
            if (false !== $pluginType) {
                $pluginSlug = $_GET['typeSlug'];

                //TYPE ITEMGLUE
                if ($pluginType == 'ITEMGLUE') {
                    if (DB::checkTable(TABLEPREFIX . 'appoe_plugin_itemGlue_articles')) {
                        //Get Article infos
                        $Article = getArticlesBySlug($pluginSlug);

                        //Check if Article exist
                        if ($Article) {
                            setPageParam('currentPageID', $Article->getId());
                            setPageParam('currentPageType','ARTICLE');
                            setPageParam('currentPageName', $Article->getName());
                            setPageParam('currentPageSlug', $Article->getSlug());
                            setPageParam('currentPageDescription', $Article->getDescription());
                            setPageParam('currentPageImage',getArtFeaturedImg($Article, ['tmpPos' => 1, 'onlyUrl' => true]));
                            setArticle($Article);
                        }
                    }

                    //TYPE SHOP
                } elseif ($pluginType == 'SHOP') {
                    if (class_exists('App\Plugin\Shop\Product')) {
                        //Get Product infos
                        $ProductPage = new Product();
                        $ProductPage->setSlug($pluginSlug);

                        //Check if Product exist
                        if ($ProductPage->showBySlug()) {
                            $ProductPageContent = new ProductContent($ProductPage->getId(), LANG);
                            setPageParam('currentPageID', $ProductPage->getId());
                            setPageParam('currentPageType','SHOP');
                            setPageParam('currentPageName', $ProductPage->getName());
                            setPageParam('currentPageSlug', $ProductPage->getSlug());
                            setPageParam('currentPageDescription', $ProductPageContent->getResume());
                        }
                    }
                }
            }
        }

        //shortcut for articles
    } elseif (!empty($_GET['id'])) {
        if (DB::checkTable(TABLEPREFIX . 'appoe_plugin_itemGlue_articles')) {
            //Get Article infos
            $Article = getArticlesBySlug($_GET['id']);

            //Check if Article exist
            if ($Article) {
                setPageParam('currentPageID', $Article->getId());
                setPageParam('currentPageType','ARTICLE');
                setPageParam('currentPageName', $Article->getName());
                setPageParam('currentPageSlug', $Article->getSlug());
                setPageParam('currentPageDescription', $Article->getDescription());
                setPageParam('currentPageImage',getArtFeaturedImg($Article, ['tmpPos' => 1, 'onlyUrl' => true]));
                setArticle($Article);
            }
        }
    }

    //Create menu
    $maintenance = getOptionPreference('maintenance');
    $cacheProcess = getOptionPreference('cacheProcess');
    if (empty($_SESSION['MENU']) || getSessionLang() !== LANG
        || 'true' === $maintenance
        || 'false' === $cacheProcess) {
        $CmsMenu = new CmsMenu();
        $_SESSION['MENU'] = constructMenu($CmsMenu->showAll());
        unset($CmsMenu);
    }

    //Delete vars
    unset($PageBySlug, $Article, $ProductPage, $ProductPageContent, $existPage, $pluginType, $pluginSlug);
}
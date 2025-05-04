<?php

use App\DB;
use App\Hook;
use App\Plugin\Cms\CmsCache;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsTemplate;

require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/system/middleware_public.php');
includePluginsFiles();

if (DB::checkTable(TABLEPREFIX . 'appoe_plugin_cms')) {

    Hook::apply('core_front_before_html');

    //Get page content
    $CmsContent = new CmsContent(getPageParam('currentPageCmsID'), getPageParam('currentPageLang'));
    $Cache = new CmsCache(getPageParam('currentPageSlug') . '.php');

    //Show page content in template with cache (if is user)
    if (!$Cache->start()):

        inc(WEB_PATH . 'header.php');

        $Template = new CmsTemplate(WEB_PATH . getPageParam('currentPageFilename'). '.php', $CmsContent->getData(), true);
        echo $Template->get();

        inc(WEB_PATH . 'footer.php');

    endif;
    $Cache->end();

    getAsset('adminDashPublic');
}
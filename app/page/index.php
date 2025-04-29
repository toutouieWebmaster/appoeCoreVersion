<?php
require('header.php');

use App\DB;
use App\Plugin\Cms\Cms;
use App\Plugin\ItemGlue\Article;

echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row mb-3">
        <div class="d-flex col-md-12 col-lg-7">
            <div class="card border-0 w-100">
                <div class="card-header bg-white pb-0 border-0 boardBlock1Title">
                    <h5 class="m-0 pl-4 colorPrimary"><?= trans('Modifié récemment'); ?></h5>
                    <hr class="mx-4">
                </div>
                <div class="card-body pt-0" id="recentUpdates">
                    <?php
                    //Check for CMS
                    $lastPage = [];
                    if (DB::checkTable(TABLEPREFIX . 'appoe_plugin_cms')) {
                        $lastPage = getLastFromDb('plugin_cms_content', 'idCms', 5);
                        $Cms = new Cms();
                        $Cms->setLang(APP_LANG);
                    }
                    if (!isArrayEmpty($lastPage)): ?>
                        <strong><?= trans('Pages'); ?></strong>
                        <div class="my-4">
                            <?php foreach ($lastPage as $id => $idPage):
                                $Cms->setId($idPage);
                                if ($Cms->show()): ?>
                                    <div class="my-2 ms-0 ms-lg-4" style="position: relative;">
                                        <span class="w-100 d-block" style="padding-right: 100px;">
                                            <?= $Cms->getMenuName(); ?>
                                        </span>
                                        <span class="visitsStatsBadge bgColorPrimary">
                                        <a href="<?= getPluginUrl('cms/page/pageContent/', $Cms->getId()) ?>"
                                           class="btn btn-sm p-0 align-top" title="<?= trans('Consulter'); ?>">
                                                <span class="text-white"><i class="fas fa-cog"></i></span>
                                            </a>
                                        </span>
                                    </div>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    <?php endif;

                    //Check for ITEMGLUE
                    $lastArticle = [];
                    if (DB::checkTable(TABLEPREFIX . 'appoe_plugin_itemGlue_articles')) {
                        $lastArticle = getLastFromDb('plugin_itemGlue_articles_content', 'idArticle', 5);
                        $Article = new Article();
                    }
                    if (!isArrayEmpty($lastArticle)): ?>
                        <strong><?= trans('Articles'); ?></strong>
                        <div class="my-4">
                            <?php foreach ($lastArticle as $id => $idArticle):
                                $Article->setId($idArticle);
                                $Article->setLang(APP_LANG);
                                if ($Article->show()): ?>
                                    <div class="my-2 ms-0 ms-lg-4" style="position: relative;">
                                        <span class="w-100 d-block" style="padding-right: 100px;">
                                            <?= $Article->getName(); ?>
                                        </span>
                                        <span class="visitsStatsBadge bgColorPrimary">
                                            <a href="<?= getPluginUrl('itemGlue/page/articleContent/', $Article->getId()) ?>"
                                               class="btn btn-sm p-0 align-top" title="<?= trans('Consulter'); ?>">
                                                <span class="text-white"><i class="fas fa-cog"></i></span>
                                            </a>
                                        </span>
                                    </div>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="d-flex col-md-12 col-lg-5">
            <div class="card border-0 w-100">
                <div class="card-header bg-white pb-0 border-0 boardBlock2Title">
                    <h5 class="m-0 pl-4 colorSecondary"><?= trans('Visiteurs'); ?>
                        <span type="button" id="refreshTracker" class="float-end">
                            <i class="fas fa-sync-alt fa-sm"></i></span>
                    </h5>
                    <hr class="mx-4">
                </div>
                <div class="card-body pt-0" id="visitorsTracker"></div>
            </div>
        </div>
    </div>
<?php
\App\Hook::apply('core_admin_after_dashboard');
require('footer.php');
?>
<?php require('header.php');

use App\Version;

echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="container-fluid">
        <a class="btn btn-info mb-4" href="<?= getUrl('updatePermissions/'); ?>">
            <?= trans('Les Permissions'); ?>
        </a>
        <div class="row" id="pluginsContainer">
            <div class="col-12 col-lg-6">
                <?php
                $plugins = getPlugins();
                if (is_array($plugins) && !empty($plugins)) :?>
                    <h2 class="subTitle text-uppercase"><?= trans('Plugins'); ?></h2>
                    <div class="row">
                        <?php foreach ($plugins as $plugin) :?>
                            <div class="plugin col-md-12 col-lg-6" data-name="<?= $plugin['name']; ?>">
                                <div class="p-3 bg-info text-white">
                                    <?= strtoupper(implode(' ', splitAtUpperCase($plugin['name']))); ?>
                                    <?php if (!empty($plugin['setupPath'])): ?>
                                        <button type="button" class="btn btn-light btn-sm activePlugin float-right"
                                                data-pluginpath="<?= $plugin['setupPath']; ?>"><?= trans('Activer'); ?>
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn btn-light btn-sm deletePlugin float-right"
                                                data-pluginname="<?= $plugin['name']; ?>"><?= trans('Supprimer'); ?>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <div class="pt-2 px-2 pb-1 mb-1 bg-light returnContainer"></div>
                                <?php if (!empty($plugin['versionPath'])):
                                    Version::setFile($plugin['versionPath']);
                                    if (Version::show()): ?>
                                        <div class="py-1 px-3 mb-2 bg-light">
                                            <small class="pluginVersion"
                                                   data-pluginname="<?= $plugin['name']; ?>">
                                                <?= Version::getVersion(); ?>
                                            </small>
                                            <small class="responseVersion float-right"></small>
                                        </div>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <div class="my-4"></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-12 col-lg-6">
                <h2 class="subTitle text-uppercase">
                    <?= trans('Système'); ?>
                </h2>
                <div class="row">
                    <div class="col-12" id="updateSystemContainer">
                        <div class="p-3 bg-info text-white">
                            <?= mb_strtoupper(trans('Application')); ?>
                        </div>
                        <div class="p-2 mb-2 bg-light" id="updateSystemBtnContainer" style="display: none;">
                            <button type="button" id="updateSystem" class="btn btn-danger btn-sm operationBtn">
                                <?= trans('Mettre à jour'); ?>
                            </button>
                        </div>
                        <?php
                        Version::setFile(WEB_APP_PATH . 'version.json');
                        if (Version::show()):
                            ?>
                            <div class="py-1 px-3 mb-2 bg-light">
                                <small id="systemVersion" data-systemversion="<?= Version::getVersion(); ?>">
                                    <?= trans('Version'); ?> <?= Version::getVersion(); ?>
                                </small>
                                <small class="responseVersion float-right"></small>
                            </div>
                        <?php endif; ?>
                        <div class="my-4"></div>
                    </div>
                    <div class="col-12" id="pluginSystemContenair" style="display: none">
                        <div class="p-3 bg-info text-white">
                            <?= mb_strtoupper(trans('Plugins')); ?>
                        </div>
                        <div class="p-2 mb-2 bg-light">
                            <button type="button" id="updatePlugins" class="btn btn-danger btn-sm operationBtn">
                                <?= trans('Mettre à jour'); ?>
                            </button>
                        </div>
                        <div class="my-4"></div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-info text-white">
                            <?= mb_strtoupper(trans('Enregistrement')); ?>
                        </div>
                        <div class="p-2 mb-2 bg-light">
                            <div class="row">
                                <div class="col-12 col-md-9">
                                    <button type="button" id="cleanDataBase"
                                            class="btn btn-warning btn-sm operationBtn">
                                        <?= trans('Base de donnée'); ?>
                                    </button>
                                </div>
                                <div class="col-12 col-md-3">
                                    <div class="input-group">
                                        <?php
                                        $files = array('tous', 'public', 'include');
                                        echo \App\Form::select('Fichier', 'files', array_combine($files, $files), 'tous', true, '', '', '', 'custom-select-sm', false); ?>
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="saveFiles" style="cursor: pointer;">
                                                <i class="fas fa-download"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="saveInfos" class="mt-3"></div>
                        </div>
                        <div class="my-4"></div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-info text-white">
                            <?= mb_strtoupper(trans('Visuel')); ?>
                        </div>
                        <div class="p-2 mb-2 bg-light">
                            <button type="button" id="updateSitemap" class="btn btn-warning btn-sm operationBtn">
                                <?= trans('Actualiser le Sitemap'); ?>
                            </button>
                        </div>
                        <div class="my-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="/APPOE/app/lib/template/js/setting.js"></script>
<?php require('footer.php'); ?>
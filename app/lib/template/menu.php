<?php

use App\Menu;

$Menu = new Menu();
$menuAll = [];
$autorisedMenu = $Menu->displayMenu(getUserRoleId());

if (is_array($autorisedMenu)) {
    $autorisedMenu = array_sort($autorisedMenu, 'order_menu');
    foreach ($autorisedMenu as $menuPage) {
        $menuAll[$menuPage['parent_id']][] = $menuPage;
    }
}
?>
<ul class="list-unstyled components" id="adminMenu">
    <div class="user-profile">
        <div class="dropdown user-pro-body">
            <div class="sidebar-header"><?= getLogo(); ?>
                <?php if (isTechnicien(getUserRoleId())): ?>
                    <div class="updateImgOverlay" data-update-img-overlay="headerLogo"></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php if (!empty($menuAll[10])) :
        foreach ($menuAll[10] as $menu):
            if (!empty($menuAll[$menu['id']])): sort($menuAll[$menu['id']]); ?>
                <li class="<?= 'icon-' . $menu['slug']; ?> <?= activePage($menu['slug'], 'active'); ?>"
                    id="menu-<?= $menu['slug']; ?>">
                    <a href="#<?= 'menu-admin' . $menu['id']; ?>" data-toggle="collapse" aria-expanded="false"
                       class="accordion-toggle wave-effect sidebarLink"><?= trans($menu['name']); ?></a>
                    <ul class="collapse list-unstyled" id="<?= 'menu-admin' . $menu['id']; ?>"
                        data-parent="#adminMenu">
                        <?php foreach ($menuAll[$menu['id']] as $sous_menu): ?>
                            <li class="<?= activePage($sous_menu['slug'], 'active'); ?>"
                                id="sousmenu-<?= $sous_menu['slug']; ?>">
                                <?php if (!empty($menuAll[$sous_menu['id']])): ?>
                                    <a href="#<?= 'menu-admin' . $sous_menu['id']; ?>"
                                       class="accordion-toggle wave-effect sidebarLink" data-toggle="collapse"
                                       aria-expanded="false"><?= trans($sous_menu['name']); ?></a>
                                    <ul class="collapse list-unstyled" id="<?= 'menu-admin' . $sous_menu['id']; ?>"
                                        data-parent="#<?= 'menu-admin' . $menu['id']; ?>">
                                        <?php foreach ($menuAll[$sous_menu['id']] as $sous_sous_menu): ?>
                                            <li class="<?= activePage($sous_sous_menu['slug'], 'active') . ' icon-' . $sous_sous_menu['slug']; ?>"
                                                id="menu-<?= $menu['slug']; ?>">
                                                <a href="<?= (!empty($sous_sous_menu['pluginName'])) ? getPluginUrl($sous_sous_menu['pluginName'] . '/page/' . $sous_sous_menu['slug']) : getUrl($sous_sous_menu['slug']); ?>/"><?= trans($sous_sous_menu['name']); ?></a>
                                            </li>
                                        <?php endforeach ?>
                                    </ul>
                                <?php else: ?>
                                    <a href="<?= (!empty($menu['pluginName'])) ? getPluginUrl($menu['pluginName'] . '/page/' . $sous_menu['slug']) : getUrl($sous_menu['slug']); ?>/">
                                        <?= trans($sous_menu['name']); ?>
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </li>
            <?php else: ?>
                <li class="<?= activePage($menu['slug'], 'active') . ' icon-' . $menu['slug']; ?>"
                    id="menu-<?= $menu['slug']; ?>">
                    <a class="wave-effect"
                       href="<?= (!empty($menu['pluginName']) ? getPluginUrl($menu['pluginName'] . '/page/' . $menu['slug']) : (getUrl(($menu['slug'] == 'index') ? 'home' : $menu['slug']))); ?>/"><?= trans($menu['name']); ?></a>
                </li>
            <?php endif;
        endforeach;
    endif;
    includePluginsPrimaryMenu(); ?>
    <div id="liUserStatutMenu">
        <ul class="list-inline" id="usersStatsSubMenu"></ul>
    </div>
    <div class="progress mt-2" style="height: 1px;">
        <div id="appStatus" class="progress-bar progress-bar-striped bg-light" role="progressbar"
             aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
    </div>
    <div id="sidebarInfos" class="mb-2 text-center">
        <small><img src="<?= getLogo(true); ?>" width="17" style="vertical-align: initial;" alt="APPOE"
                    title="APPOE | Art Of Event - Communication">
            &nbsp;<strong>APPOE</strong>
        </small><small><em><?= getAppoeVersion(); ?></em></small>
    </div>
</ul>
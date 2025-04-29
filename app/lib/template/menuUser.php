<nav class="navbar navbar-expand-md navbar-dark fixed-top bgColorPrimary">
    <div class="menu-toggle-button">
        <button class="nav-link sidebarLink wave-effect" id="sidebarCollapse">
            <span class="fas fa-bars"></span>
        </button>
    </div>

    <?php if (class_exists('App\Plugin\Cms\Cms')): ?>
        <div class="menu-toggle-button">
            <a class="nav-link wave-effect" target="_blank" href="<?= WEB_DIR_URL; ?>">
                <span><i class="fas fa-external-link-alt"></i></span>
                <span class="d-none d-md-inline">Aller sur le site</span>
            </a>
        </div>
    <?php endif;
    if (defined('LANGUAGES') && is_array(LANGUAGES) && count(LANGUAGES) >= 2): ?>
        <div class="dropdown menu-toggle-button" id="languageSelectorContainer">
            <a class="nav-link sidebarLink wave-effect" id="languageSelectorBtn" data-toggle="dropdown" href="#"
               aria-expanded="false" aria-haspopup="true" role="button">
                <img src="<?= getAppImg('flag-' . APP_LANG . '.svg'); ?>">
                <span class="d-none d-md-inline" style="vertical-align: middle;"><?= LANGUAGES[APP_LANG]; ?></span>
            </a>
            <div class="dropdown-menu">
                <?php foreach (getLangs() as $lang => $language):
                    if ($lang != APP_LANG): ?>
                        <div class="langSelector" id="<?= $lang; ?>">
                            <a class="dropdown-item sidebarLink" href="#">
                                <img src="<?= getAppImg('flag-' . $lang . '.svg'); ?>">
                                <small><?= LANGUAGES[$lang]; ?></small>
                            </a>
                        </div>
                    <?php endif;
                endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    <div class="ml-auto"></div>

    <?php if (isUserAuthorized('tools') || isUserAuthorized('setting')): ?>
        <div class="dropdown menu-toggle-button">
            <a class="nav-link dropdown-toggle sidebarLink wave-effect hideNavArrows" href="#"
               id="navbarDropdownSetting" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-cog"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUserMenu">
                <a class="dropdown-item" href="<?= getUrl('tools/'); ?>">
                    <small><span class="mr-2"><i class="fas fa-tools"></i></span> <?= trans('Outils'); ?></small>
                </a>
                <?php if (isTechnicien(getUserRoleId())): ?>
                    <div class="dropdown-divider m-0"></div>
                    <a class="dropdown-item" href="<?= getUrl('setting/'); ?>">
                        <small><span class="mr-2"><i class="fas fa-cog"></i></span> <?= trans('Réglages'); ?></small>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif;
    includePluginsSecondaryMenu(); ?>
    <div class="dropdown menu-toggle-button">
        <a class="nav-link dropdown-toggle sidebarLink wave-effect hideNavArrows" href="#" id="navbarDropdownUserMenu"
           role="button"
           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="mr-2 d-none d-md-inline"><?= getUserLogin(); ?></span> <i class="fas fa-user"></i>
        </a>
        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownUserMenu">
            <?php if (isUserAuthorized('updateUser')): ?>
                <a class="dropdown-item" href="<?= getUrl('user/', getUserIdSession()); ?>">
                    <small><span class="mr-2"><i class="fas fa-user"></i></span> <?= trans('Mon profil'); ?></small>
                </a>
            <?php endif; ?>
            <div class="dropdown-divider m-0"></div>
            <a class="dropdown-item" href="<?= WEB_APP_URL . 'logout.php'; ?>">
                <small><span class="mr-2"><i class="fas fa-power-off"></i></span> <?= trans('Déconnexion'); ?></small>
            </a>
        </div>
    </div>
</nav>
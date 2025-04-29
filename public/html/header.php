<!doctype html>
<html lang="<?= LANG; ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="icon" type="image/png" href="<?= WEB_PUBLIC_URL; ?>images/Logo.png">
    <title><?= getPageParam('currentPageName'); ?></title>
    <meta name="description" content="<?= getPageParam('currentPageDescription'); ?>">
    <?= getMetaData(); ?>
    <?php includePluginsStyles(); ?>
</head>
<body>
<?php if (hasMenu()): ?>
    <?php foreach (getSessionMenu() as $menuPage): ?>
        <li class="<?= activePage($menuPage->slug); ?>">
            <a href="<?= webUrl($menuPage->slug); ?>"><?= $menuPage->name; ?></a>
            <?php if (hasSubMenu($menuPage->id)): ?>
                <ul>
                    <?php foreach (getSessionMenu(1, $menuPage->id) as $subMenu): ?>
                        <li class="<?= activePage($subMenu->slug); ?>">
                            <a href="<?= webUrl($subMenu->slug); ?>"><?= $subMenu->name; ?></a>
                            <?php if (hasSubMenu($subMenu->id)): ?>
                                <ul>
                                    <?php foreach (getSessionMenu(1, $subMenu->id) as $subSubMenu): ?>
                                        <li class="<?= activePage($subSubMenu->slug); ?>">
                                            <a href="<?= webUrl($subSubMenu->slug); ?>"><?= $subSubMenu->name; ?></a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </li>
    <?php endforeach; ?>
<?php endif; ?>
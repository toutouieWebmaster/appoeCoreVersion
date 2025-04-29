<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
if (isUserAuthorized('updatePageContent')): ?>
    <div id="adminDashPublic">
        <a href="<?= WEB_ADMIN_URL; ?>" title="Tableau de bord">
            <img src="<?= APP_IMG_URL; ?>dashboard.svg" alt="">
        </a>
        <?php if (getPageParam('currentPageType') === 'PAGE'): ?>
            <a href="<?= getPluginUrl('cms/page/pageContent/', getPageParam('currentPageID')); ?>" title="Modifier la page">
                <img src="<?= APP_IMG_URL; ?>cog.svg" alt="">
            </a>
        <?php elseif (getPageParam('currentPageType') === 'ARTICLE'): ?>
            <a href="<?= getPluginUrl('itemGlue/page/articleContent/', getPageParam('currentPageID')); ?>" title="Modifier l'article">
                <img src="<?= APP_IMG_URL; ?>cog.svg" alt="">
            </a>
        <?php endif;
        if (getOptionPreference('cacheProcess') === 'true'): ?>
            <a href="#" id="clearCach" data-page-slug="<?= getPageParam('currentPageSlug'); ?>" data-page-lang="<?= LANG; ?>"
               title="Vider le cache">
                <img src="<?= APP_IMG_URL; ?>clear.svg" alt="">
            </a>
            <script type="text/javascript">
                document.getElementById('clearCach').addEventListener('click', function (e) {
                    e.preventDefault();

                    let page = e.target.parentNode;
                    if (null != page.getAttribute('data-page-slug') && null != page.getAttribute('data-page-lang')) {

                        let xhr = new XMLHttpRequest();
                        xhr.open("POST", '/app/plugin/cms/process/ajaxProcess.php', true);

                        //Envoie les informations du header adaptées avec la requête
                        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");

                        //Appelle une fonction au changement d'état.
                        xhr.onreadystatechange = function () {
                            if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
                                document.location.reload(true);
                            }
                        }
                        xhr.send('clearPageCache=OK&pageSlug=' + page.getAttribute('data-page-slug') + '&pageLang=' + page.getAttribute('data-page-lang'));
                    }
                }, false);
            </script>
        <?php endif; ?>
        <hr>
        <a href="<?= WEB_APP_URL . 'logout.php'; ?>" title="Déconnexion">
            <img src="<?= APP_IMG_URL; ?>power.svg" alt="">
        </a>
    </div>
<?php endif; ?>
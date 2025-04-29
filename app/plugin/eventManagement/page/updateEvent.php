<?php if (!empty($_GET['id'])): ?>
    <?php require('header.php');
    $Event = new \App\Plugin\EventManagement\Event($_GET['id']);
    if ($Event->getStatut()) :
        require_once(EVENTMANAGEMENT_PATH . 'process/updateEvent.php');
        echo getTitle( getAppPageName(), getAppPageSlug() );
        showPostResponse(); ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <a href="<?= getPluginUrl('eventManagement/page/event/', $Event->getId()) ?>"
                       class="btn btn-info btn-sm float-right"
                       title="<?= trans('Consulter'); ?>">
                        <span class="fa fa-eye"></span> <?= trans('Consulter l\'évènement'); ?>
                    </a>
                </div>
            </div>
            <?php if (!empty($Event->getImage())): ?>
                <div class="row">
                    <div class="col-12 d-flex justify-content-center">
                        <img src="<?= WEB_DIR_INCLUDE . $Event->getImage(); ?>" class="img-fluid img-thumbnail">
                    </div>
                </div>
            <?php endif; ?>

            <form action="" method="post" id="updateEventForm" enctype="multipart/form-data">
                <?= getTokenField(); ?>
                <?= App\Form::text('', 'id', 'hidden', $Event->getId(), true); ?>
                <?= App\Form::text('', 'image', 'hidden', $Event->getImage(), true); ?>
                <div class="row">
                    <div class="col-12 col-lg-4">
                        <?php
                        $Auteur = new \App\Plugin\EventManagement\Auteur();
                        $auteurs = extractFromObjToSimpleArr($Auteur->showByType(), 'id', 'name');
                        echo App\Form::select('Auteur', 'auteurId', $auteurs, $Event->getAuteurId(), true);
                        ?>
                    </div>
                    <div class="col-12 col-lg-4">
                        <?= App\Form::text('Titre', 'titre', 'text', $Event->getTitre(), true, 250); ?>
                    </div>
                    <div class="col-12 col-lg-4">
                        <?= App\Form::text('Pitch', 'pitch', 'text', $Event->getPitch(), false, 255); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::textarea('Participation', 'participation', $Event->getParticipation(), 5, false); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::textarea('Description', 'description', $Event->getDescription(), 5, true, '', 'ckeditor'); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <?= App\Form::radio('Type de Spectacle', 'spectacleType', array_map('trans', SPECTACLES_TYPES), $Event->getSpectacleType()); ?>
                    </div>
                    <div class="col-12 col-lg-4">
                        <?= App\Form::radio('IN / OFF', 'indoor', array_map('trans', INDOOR_OFF), $Event->getIndoor()); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::text('Nouvelle Image', 'image', 'file', '', false); ?>
                    </div>
                </div>
                <div class="my-4"></div>
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::target('UPDATEEVENT'); ?>
                        <?= App\Form::submit('Enregistrer', 'UPDATEEVENTSUBMIT'); ?>
                    </div>
                </div>
            </form>
            <div class="my-4"></div>
        </div>
        <script>
            $(document).ready(function () {
                $('form').submit(function () {
                    $('#loader').fadeIn('fast');
                });
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cet évènement n\'existe pas')); ?>
    <?php endif; ?>
    <?php require('footer.php'); ?>
<?php else: ?>
    <?= trans('Cet évènement n\'existe pas'); ?>
<?php endif; ?>
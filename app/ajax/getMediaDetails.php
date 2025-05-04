<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/main.php');

use App\Category;
use App\Form;
use App\Media;

if (checkAjaxRequest() && !empty($_GET['fileId']) && is_numeric($_GET['fileId'])):

    $Category = new Category();
    $Category->setType('MEDIA');
    $listCatgories = extractFromObjToArrForList($Category->showByType(), 'id');

    $Media = new Media();
    $Media->setId($_GET['fileId']);
    $Media->setLang(APP_LANG);

    if ($Media->show()): ?>
        <div class="pb-2">
            <div class="col-12 p-0">
                <?php
                $file = FILE_DIR_PATH . $Media->getName();
                $fileSize = getSizeName(filesize($file));
                if (isImage($file)):
                    $fileDimensions = getimagesize($file); ?>
                    <div class="mediaItem">
                        <img src="<?= getThumb($Media->getName(), 370); ?>"
                             alt="<?= $Media->getTitle(); ?>"
                             data-originsrc="<?= WEB_DIR_INCLUDE . $Media->getName(); ?>"
                             data-filename="<?= $Media->getName(); ?>"
                             class="img-fluid seeOnOverlay">
                        <div class="mediaCaption">
                            <small><?= $Media->getName(); ?></small><br>
                            <strong>L:</strong> <?= $fileDimensions[0]; ?>px
                            <strong>| H:</strong> <?= $fileDimensions[1]; ?>px
                            <strong>| P:</strong> <?= $fileSize; ?>
                        </div>
                    </div>

                <?php elseif (isAudio($file)): ?>
                    <div class="mediaItem">
                        <audio controls src="<?= WEB_DIR_INCLUDE . $Media->getName(); ?>"
                               data-originsrc="<?= WEB_DIR_INCLUDE . $Media->getName(); ?>"></audio>
                        <div class="mediaCaption">
                            <small><?= $Media->getName(); ?></small><br>
                            <strong>Poids:</strong> <?= $fileSize; ?>
                        </div>
                    </div>
                <?php elseif (isVideo($file)): ?>
                    <div class="mediaItem">
                        <video controls class="d-block">
                            <source src="<?= WEB_DIR_INCLUDE . $Media->getName(); ?>"
                                    data-originsrc="<?= WEB_DIR_INCLUDE . $Media->getName(); ?>"
                                    type="<?= mime_content_type($file); ?>">
                        </video>
                        <div class="mediaCaption">
                            <small><?= $Media->getName(); ?></small><br>
                            <strong>Poids:</strong> <?= $fileSize; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="<?= WEB_DIR_INCLUDE . $Media->getName(); ?>" target="_blank">
                        <img src="<?= getImgAccordingExtension(getFileExtension($Media->getName())); ?>"
                             data-filename="<?= $Media->getName(); ?>" alt="<?= $Media->getTitle(); ?>">
                    </a>
                <?php endif; ?>
                <div class="row m-0">
                    <button type="button" class="btn btn-secondary col copyLinkOnClick"
                            data-src="<?= WEB_DIR_INCLUDE . $Media->getName(); ?>"
                            title="<?= trans('Copier le lien du média'); ?>">
                        <i class="far fa-copy"></i>
                    </button>
                    <a title="<?= trans('Visualiser le fichier dans un nouvel onglet'); ?>"
                       href="<?= WEB_DIR_INCLUDE . $Media->getName(); ?>" target="_blank"
                       data-file-mime="<?= mime_content_type($file); ?>" class="btn btn-secondary col">
                        <i class="fas fa-external-link-alt"></i>
                    </a>
                    <button type="button" class="btn btn-secondary renameMediaFile col"
                            title="<?= trans('Renommer le fichier'); ?>">
                        <i class="fas fa-wrench"></i>
                    </button>
                    <button type="button" class="btn btn-secondary seeMediaCaption col"
                            title="<?= trans('Voir les informations de l\'image'); ?>">
                        <i class="fas fa-info"></i>
                    </button>
                </div>
            </div>
            <div class="col-12">
                <h5 class="my-2" id="mediaTitle"><?= $Media->getTitle(); ?></h5>
                <form method="post" class="my-2" id="filenameInputForm" style="display:none;">
                    <input type="hidden" name="id" value="<?= $Media->getId(); ?>">
                    <input type="hidden" name="oldName" value="<?= $Media->getName(); ?>">
                    <div class="mb-2">
                        <?= Form::text('Nom du fichier', 'filename', 'text', $Media->getName(), true, 255, '', '', 'form-control-sm'); ?>
                    </div>
                    <div class="mb-2">
                        <?= Form::submit('Enregistrer', 'RENAMEFILENAME'); ?>
                    </div>
                </form>
                <form method="post" id="mediaDetailsForm" class="my-2">
                    <input type="hidden" name="id" value="<?= $Media->getId(); ?>">
                    <input type="hidden" name="imageType" value="<?= $Media->getType(); ?>">
                    <div class="mb-2">
                        <?= Form::text('Titre (texte alternatif)', 'title', 'text', $Media->getTitle(), false, 255, '', '', 'form-control-sm imageTitle upImgForm'); ?>
                    </div>
                    <div class="mb-2">
                        <?= Form::textarea('Description', 'description', $Media->getDescription(), 1, false, '', 'form-control-sm imageDescription upImgForm'); ?>
                    </div>
                    <div class="mb-2">
                        <?= Form::text('Lien', 'link', 'url', $Media->getLink(), false, 255, '', '', 'form-control-sm imagelink upImgForm'); ?>
                    </div>
                    <div class="mb-2">
                        <?= Form::text('Position', 'position', 'text', $Media->getPosition(), false, 5, '', '', 'form-control-sm imagePosition upImgForm'); ?>
                    </div>
                    <?php if ($Media->getType() === 'MEDIA'): ?>
                        <div class="mb-2">
                            <?= Form::select('Bibliothèques', 'typeId', $listCatgories, $Media->getTypeId(), true, ' data-old-type="' . $Media->getTypeId() . '" ', '', '', 'custom-select-sm imageTypeId upImgForm'); ?>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="typeId" class="imageTypeId" value="<?= $Media->getTypeId(); ?>">
                        <div class="mb-2">
                            <?= Form::select('Zone du thème', 'templatePosition', FILE_TEMPLATE_POSITIONS, getSerializedOptions($Media->getOptions(), 'templatePosition'), '', '', '', '', 'custom-select-sm templatePosition upImgForm'); ?>
                        </div>
                    <?php endif; ?>
                </form>
                <small id="infosMedia" class="float-right text-success"></small>
                <hr class="mx-5 my-3">
                <div class="row">
                    <div class="col-6 text-left">
                        <button type="button" class="closeMediaDetails btn btn-outline-secondary btn-sm">
                            Fermer <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="col-6 text-right">
                        <button type="button" class="deleteImage btn btn-outline-danger btn-sm"
                                data-imageid="<?= $Media->getId(); ?>" data-thumbwidth="370">
                            <i class="fas fa-times"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif;
else: ?>
    <p><?= trans('Ce fichier n\'existe pas'); ?> !</p>
<?php endif; ?>
<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/main.php');

use App\Category;
use App\Media;

if (checkAjaxRequest()):

    //Get media & library infos
    $Media = new Media();
    $Media->setLang(APP_LANG);

    $Category = new Category();
    $Category->setType('MEDIA');
    $allCategory = $Category->showByType();

    $allLibrary = extractFromObjToSimpleArr($allCategory, 'id', 'name');
    $libraryParent = groupLibraryByParents($allCategory, $allLibrary);

    if ($allLibrary): ?>
        <div id="shortAccessBtns" class="mb-4 float-right">
            <button type="button" class="btn btn-sm btn-secondary"
                    data-library-parent-id="all"><?= trans('Tous'); ?></button>
        </div>
        <?php foreach ($allLibrary as $id => $name):
            $Media->setTypeId($id);
            $allFiles = $Media->showFiles();
            if ($allFiles): ?>
                <div class="mediaContainer"
                     data-library-parent-id="<?= $libraryParent[$id]['id']; ?>">
                    <h6 class="libraryName p-3" id="media-<?= $id; ?>"
                        data-library-parent-id="<?= $libraryParent[$id]['id']; ?>"
                        data-library-parent-name="<?= $libraryParent[$id]['name']; ?>"><?= $name; ?></h6>
                    <hr class="my-3 mx-5">
                    <div class="card-columns">
                        <?php foreach ($allFiles as $file): ?>
                            <div class="card fileContent bg-none border-0">
                                <?php if (isImage(FILE_DIR_PATH . $file->name)): ?>
                                    <img src="<?= getThumb($file->name, 370); ?>"
                                         alt="<?= $file->title; ?>"
                                         data-originsrc="<?= WEB_DIR_INCLUDE . $file->name; ?>"
                                         data-filename="<?= $file->name; ?>"
                                         class="img-fluid seeOnOverlay seeDataOnHover">
                                <?php else: ?>
                                    <a href="<?= WEB_DIR_INCLUDE . $file->name; ?>" target="_blank">
                                        <img src="<?= getImgAccordingExtension(getFileExtension($file->name)); ?>"
                                             class="seeDataOnHover" data-filename="<?= $file->name; ?>"
                                             alt="<?= $file->name; ?>">
                                    </a>
                                <?php endif; ?>
                                <div class="form-group mt-1 mb-0">
                                    <small style="font-size: 9px;">
                                        <strong class="fileLink" data-src="<?= WEB_DIR_INCLUDE . $file->name; ?>">
                                            <button class="btn btn-sm btn-outline-info btn-block copyLinkOnClick">
                                                <?= trans('Choisir'); ?>
                                            </button>
                                        </strong>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="my-3"></div>
            <?php endif;
        endforeach;
    endif;
endif; ?>
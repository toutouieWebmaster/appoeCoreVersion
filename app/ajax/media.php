<?php
require_once('../main.php');

use App\File;
use App\FileContent;
use App\Media;
use App\Plugin\Cms\CmsContent;

if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (!empty($_POST['UPDATEIMGOVERLAY'])) {

            if (!isArrayEmpty($_FILES['headerLogo'])) {

                if (!empty($_POST['oldFile'])) {
                    $oldFile = pathinfo($_POST['oldFile']);
                    if (file_exists(WEB_PUBLIC_PATH . 'images/' . $oldFile['basename'])) {
                        unlink(WEB_PUBLIC_PATH . 'images/' . $oldFile['basename']);
                    }
                }

                $ext = pathinfo($_FILES['headerLogo']['name'], PATHINFO_EXTENSION);
                $_FILES['headerLogo']['name'] = 'appoe-logo.' . $ext;

                $Media = new Media();
                $Media->setMaxSize(512000);
                $Media->setFilePath(WEB_PUBLIC_PATH . 'images/');
                $Media->setUploadFiles($_FILES['headerLogo']);

                $file = $Media->uploadOneFile(false);

                if (empty($file['errors'])) {
                    echo json_encode(WEB_DIR_IMG . $file['filename']);
                } else {
                    echo json_encode(false);
                }
                exit();
            }

            echo json_encode(false);
            exit();
        }

        if (isset($_POST['ADDIMAGES']) && !empty($_POST['library']) && isset($_POST['textareaSelectedFile'])) {

            $html = '';
            $selectedFilesCount = 0;

            $Media = new Media();
            $Media->setTypeId($_POST['library']);
            $Media->setUserId(getUserIdSession());

            //Get uploaded files
            if (!empty($_FILES)) {
                $Media->setUploadFiles($_FILES['inputFile']);
                $files = $Media->upload();
                $html .= trans('Fichiers importés') . ' : <strong>' . $files['countUpload'] . '</strong><br>'
                    . trans('Fichiers enregistrés dans la BDD') . ' : <strong>' . $files['countDbSaved'] . '</strong>'
                    . (!empty($files['errors']) ? '<br><span class="text-danger">' . $files['errors'] . '</span>' : '');
            }

            //Get selected files
            if (!empty($_POST['textareaSelectedFile'])) {

                $selectedFiles = $_POST['textareaSelectedFile'];

                if (strpos($selectedFiles, '|||')) {
                    $files = explode('|||', $selectedFiles);
                } else {
                    $files = array($selectedFiles);
                }

                foreach ($files as $key => $file) {
                    $Media->setName($file);
                    if (!$Media->exist()) {
                        if ($Media->save()) $selectedFilesCount++;
                    }
                }

                $html .= '<br>' . trans('Fichiers sélectionnés enregistrés dans la BDD') . ' : <strong>' . $selectedFilesCount . '</strong>';
            }

            echo json_encode($html);
            exit();
        }

        if (isset($_POST['updateDetailsImg']) && !empty($_POST['idImage'])
            && !empty($_POST['typeId']) && is_numeric($_POST['typeId'])) {

            $Media = new Media();
            $Media->setId($_POST['idImage']);
            if ($Media->show()) {

                $FileContent = new FileContent();
                $FileContent->setFileId($Media->getId());
                $FileContent->setLang(APP_LANG);
                $FileContent->setUserId(getUserIdSession());

                if ($FileContent->showByFile()) {

                    $FileContent->setTitle($_POST['title']);
                    $FileContent->setDescription($_POST['description']);
                    $FileContent->update();

                } else {
                    $FileContent->setTitle($_POST['title']);
                    $FileContent->setDescription($_POST['description']);
                    $FileContent->save();
                }

                if (!empty($_POST['link'])) {
                    $Media->setLink($_POST['link']);
                }

                if (!empty($_POST['position']) && is_numeric($_POST['position'])) {
                    $Media->setPosition($_POST['position']);
                }

                $Media->setTypeId($_POST['typeId']);
                $Media->setUserId(getUserIdSession());

                if (!empty($_POST['templatePosition'])) {

                    $mediaOptions = getSerializedOptions($Media->getOptions());

                    if (is_array($mediaOptions)) {
                        $Media->setOptions(serialize(array_replace_recursive(
                            $mediaOptions,
                            array('templatePosition' => $_POST['templatePosition'])
                        )));
                    } else {
                        $Media->setOptions(serialize(array('templatePosition' => $_POST['templatePosition'])));
                    }
                }

                if ($Media->update()) {
                    echo 'true';
                }
            }
            exit();
        }

        if (isset($_POST['deleteImage']) && !empty($_POST['idImage'])) {

            $Media = new Media();
            $Media->setId($_POST['idImage']);
            if ($Media->show()) {

                if (!empty($_POST['thumbWidth'])) {
                    deleteThumb($Media->getName(), $_POST['thumbWidth']);
                }

                if ($Media->delete()) {
                    echo 'true';
                }
            }
            exit();
        }

        if (isset($_POST['renameMediaFile']) && !empty($_POST['idImage'])
            && !empty($_POST['oldName']) && !empty($_POST['newName'])) {

            if (substr_count($_POST['newName'], '.') > 1) {
                echo trans('Impossible de renommer le fichier avec plus d\'une extension');
                exit();
            }

            $Media = new Media();
            $Media->setId($_POST['idImage']);
            if ($Media->show()) {

                if (renameFile(FILE_DIR_PATH . $_POST['oldName'], FILE_DIR_PATH . $_POST['newName'])) {

                    deleteThumb($Media->getName(), 370);
                    $Media->setName($_POST['newName']);
                    thumb($Media->getName(), 370);

                    if ($Media->rename($_POST['oldName'])) {

                        if (class_exists('App\Plugin\Cms\Cms')) {

                            $CmsContent = new CmsContent();
                            if (!$CmsContent->renameFilename(WEB_DIR_INCLUDE . $_POST['oldName'], WEB_DIR_INCLUDE . $_POST['newName'])) {
                                echo trans('Impossible de renommer les fichiers dans les pages');
                                exit();
                            }
                        }
                        echo 'true';
                    }

                } else {
                    echo trans('Impossible de renommer le fichier');
                }
            }
            exit();
        }

        if (isset($_REQUEST['getAllMedia'])) {

            $html = '<div class="card-columns">';
            $includeFiles = getFilesFromDir(FILE_DIR_PATH, [
                'onlyFiles' => true,
                'allExtensionsExceptOne' => 'php'
            ]);

            foreach ($includeFiles as $key => $includeFile) {

                $html .= '<div class="card fileContent allMediaCard" data-filename="' . $includeFile . '">';

                if (isImage(FILE_DIR_PATH . $includeFile)) {
                    $html .= '<img src="' . WEB_DIR_INCLUDE . $includeFile . '" class="img-fluid seeOnOverlay seeDataOnHover" data-originsrc="' . WEB_DIR_INCLUDE . $includeFile . '">';
                } else {
                    $html .= '<span class="contentOnHover">' . $includeFile . '</span><a href="' . WEB_DIR_INCLUDE . $includeFile . '" target="_blank"><img src = "' . getImgAccordingExtension(getFileExtension($includeFile)) . '" class="img-fluid seeDataOnHover" alt="APPOEmedia"></a>';
                }

                $html .= '<button class="btn btn-sm littleBtn addLittleBtn selectParentOnClick bgColorPrimary"><i class="fas fa-plus"></i></button>';
                $html .= '<button type="button" class="deleteDefinitelyImageByName btn btn-sm btn-danger littleBtn deleteLittleBtn" data-imagename="' . $includeFile . '"> <i class="fas fa-times"></i></button></div>';
            }

            $html .= '</div>';
            echo $html;
            exit();
        }

        if (!empty($_POST['deleteDefinitelyImageByName']) && !empty($_POST['filename'])) {
            $File = new File();
            $File->setName($_POST['filename']);
            $fileDeleted = $File->deleteFileByPath();

            if (true !== $fileDeleted) {

                if (false === $fileDeleted) {
                    echo trans('Un problème est survenu lors de la suppression du fichier');
                } else {
                    echo $fileDeleted;
                }

            } else {

                if ($File->deleteFileByName()) {
                    echo json_encode(true);
                } else {
                    echo trans('Le fichier a été supprimé mais un problème est survenu lors de la suppression du fichier dans la base de données');
                }
            }
            exit();
        }
    }
}
<?php

use App\Media;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Clean data
    $_POST = cleanRequest($_POST);

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

        \App\Flash::setMsg($html, 'secondary');
    }
}
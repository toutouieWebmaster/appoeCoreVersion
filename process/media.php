<?php

use App\Media;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDIMAGES']) && isset($_POST['textareaSelectedFile'])) {

        if (empty($_POST['library'])) {
            $html = 'Veuillez sélectionner une bibliothèque afin d\'importer votre fichier.';
            $status = 'danger';
        } else {
            $html = handleMediaUploadAndSelection($_POST, $_FILES);
            $status = 'secondary';
        }

        \App\Flash::setMsg($html, $status);

    }
}
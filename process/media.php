<?php

use App\Media;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //Clean data
    $_POST = cleanRequest($_POST);

    if (isset($_POST['ADDIMAGES']) && !empty($_POST['library']) && isset($_POST['textareaSelectedFile'])) {
        $html = handleMediaUploadAndSelection($_POST, $_FILES);
        \App\Flash::setMsg($html, 'secondary');
    }
}
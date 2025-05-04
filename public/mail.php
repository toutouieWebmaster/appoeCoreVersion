<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/main.php');
includePluginsFiles();
//if (checkAjaxPostRecaptcha($_POST, '6LeExqwUAAAAAH5sJrvOg52vPo2XXuRr20JWTLD5')){

    $_POST = cleanRequest($_POST);

    $html = '<p>Vous y êtes presque !<br> Merci de cliquer sur le bouton ci-dessous et vous rejoindrez 
            la liste de diffusion réservée aux fidèles passionnés d\'APPOE.</p>';

    $data = array(
        'toName' => $_POST['name'],
        'toEmail' => 'esther@pp-communication.frs',
        'confirmationPageSlug' => 'confirmation-email',
        'message' => $html
    );

    if (emailVerification($data)) {
        echo json_encode(true);
        exit();
    }

/*
    $email = $_POST['email'];

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $data = array(
            'fromEmail' => $email,
            'fromName' => $_POST['name'],
            'toName' => 'APPOE DEMO',
            'toEmail' => 'yona@aoe-communication.com',
            'object' => 'Message APPOE DEMO',
            'message' => nl2br($_POST['message'])
        );

        if (sendMail($data)) {
            echo json_encode(true);
            exit();
        }
    }*/
//}
echo json_encode(false);
exit();
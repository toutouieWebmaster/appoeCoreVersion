<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
includePluginsFiles();

if (checkAjaxPostRecaptcha($_POST, '6LcyucsUAAAAAHv9JWrsQKN_8fnMTgOuwvL4L9nF') && !empty($_POST['formType'])) {

    $_POST = cleanRequest($_POST);
    $subject = !empty($_POST['object']) ? $_POST['object'] : 'Formulaire de '.WEB_TITLE;
    $data = false;

    /************************ DEVIS **********************/
    if ($_POST['formType'] == 'devis' && !empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['email']) && !empty($_POST['message'])
    && isEmail($_POST['email']) && isTel($_POST['phone'])) {

        $message = '<p><strong>Nom:</strong> ' . $_POST['name'];
        $message .= '<br><strong>Téléphone:</strong> ' . $_POST['phone'];
        $message .= '<br><strong>Mail:</strong> ' . $_POST['email'] . '</p>';

        if (!empty($_POST['for'])) {
            $message .= '<p><strong>Devis pour:</strong> ' . $_POST['for'] . '</p>';
        }
        $message .= '<p><strong>Message:</strong><br>' . nl2br($_POST['message']) . '</p>';

        $data = array(
            'fromEmail' => $_POST['email'],
            'fromName' => $_POST['name'],
            'toName' => 'Yona',
            'toEmail' => 'yona@smilevitch.fr',
            'object' => $subject,
            'message' => $message
        );
    }

    /************************ CONTACT **********************/
    if ($_POST['formType'] == 'contact' && !empty($_POST['name']) && !empty($_POST['phone']) && !empty($_POST['email']) && !empty($_POST['message'])
    && isEmail($_POST['email']) && isTel($_POST['phone'])) {

        $message = '<p><strong>Nom:</strong> ' . $_POST['name'];
        $message .= '<br><strong>Téléphone:</strong> ' . $_POST['phone'];
        $message .= '<br><strong>Mail:</strong> ' . $_POST['email'] . '</p>';
        $message .= '<p><strong>Message:</strong><br>' . nl2br($_POST['message']) . '</p>';

        $data = array(
            'fromEmail' => $_POST['email'],
            'fromName' => $_POST['name'],
            'toName' => 'Yona',
            'toEmail' => 'yona@smilevitch.fr',
            'object' => $subject,
            'message' => $message
        );
    }

    /************************ MAINTENANCE **********************/
    if ($_POST['formType'] == 'maintenance' && !empty($_POST['name']) && !empty($_POST['email'])
        && !empty($_POST['message']) && isEmail($_POST['email'])) {

        $message = '<h3>' . $_POST['subject'] . '</h3>';
        $message .= '<p><strong>Nom:</strong> ' . $_POST['name'];
        $message .= !empty($_POST['tel']) && isTel($_POST['tel']) ? '<br><strong>Téléphone:</strong> ' . $_POST['tel'] : '';
        $message .= '<br><strong>Mail:</strong> ' . $_POST['email'] . '</p>';
        $message .= '<p><strong>Message:</strong><br>' . nl2br($_POST['message']) . '</p>';

        $data = array(
            'fromEmail' => 'noreply@aoe-communication.com',
            'fromName' => 'Art Of Event - Communication',
            'toName' => 'AOE',
            'toEmail' => 'contact@aoe-communication.com',
            'object' => $subject,
            'message' => $message
        );
    }

    if ($data && sendMail($data)) {
        echo json_encode(true);
        exit();
    }
}
echo json_encode(false);
exit();
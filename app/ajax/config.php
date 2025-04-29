<?php
require_once('header.php');

use App\Option;

if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        if (!empty($_POST['key']) && !empty($_POST['val']) && !empty($_POST['type'])) {

            $Option = new Option($_POST);

            switch ($_POST['key']) {
                case 'maintenance':
                    $htaccessContent = DEFAULT_HTACCESS;
                    if ($_POST['val'] === 'false') {
                        $htaccessContent .= "\n\r" . HTACCESS_CACHE;
                    }

                    updateFile(ROOT_PATH . '.htaccess', ['content' => $htaccessContent]);
                    break;
                case 'allowApi':
                    $Option->setType('DATA');
                    $Option->setKey('apiToken');
                    if ($_POST['val'] === 'true') {
                        $Option->setVal(setToken(false));
                    } else {
                        $Option->setVal('');
                    }
                    $Option->update();
                    break;
                default:
                    break;
            }

            switch ($_POST['type']) {
                case 'THEME':
                    if (file_exists(WEB_TEMPLATE_PATH . 'css/theme.css')) {
                        unlink(WEB_TEMPLATE_PATH . 'css/theme.css');
                    }
                    break;
                default:
                    break;
            }

            echo json_encode(true);
            exit();
        }

        if (!empty($_POST['addAccessPermission']) && !empty($_POST['ipAddress']) && isIp($_POST['ipAddress'])) {

            $Option = new Option(['type' => 'IPACCESS', 'key' => $_POST['ipAddress'], 'val' => 'add by ip : ' . getIP()]);
            echo json_encode(true);
            exit();
        }

        if (!empty($_POST['deleteAccessPermission']) && !empty($_POST['ipAddressId']) && is_numeric($_POST['ipAddressId'])) {

            $Option = new Option();
            $Option->setId($_POST['ipAddressId']);
            if ($Option->delete()) {
                echo json_encode(true);
                exit();
            }
        }

        if (!empty($_POST['clearServerCache']) && $_POST['clearServerCache'] == 'OK') {

            if (purgeVarnishCache()) {

                echo json_encode(true);
                exit();
            }
        }
    }
}
echo json_encode(false);
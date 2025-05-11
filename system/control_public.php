<?php
if (file_exists(ROOT_PATH . 'setup.php')) {
    header(sprintf('Location: %s', WEB_DIR_URL . 'setup.php'));
    exit();
}

if (getOption('PREFERENCE', 'forceHTTPS') === 'true') {

    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {

        if (!headers_sent()) {

            header("Status: 301 Moved Permanently");
            header(sprintf('Location: https://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['REQUEST_URI']));
            exit();
        }
    }
}
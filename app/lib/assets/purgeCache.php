<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/APPOE/app/main.php');
header("Cache-Control: max-age=1");
$purge = false;

if ($ch = curl_init(WEB_DIR_URL)) {
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PURGEALL');
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    curl_setopt($ch, CURLOPT_NOBODY, true);

    if (curl_exec($ch)) {
        $purge = true;
    }
    curl_close($ch);
}
echo json_encode($purge);
exit();
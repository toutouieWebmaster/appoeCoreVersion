<?php
header('Expires: 0');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Cache-Control: no-cache', false);
header('Pragma: no-cache');

header('Location: ../');
exit;
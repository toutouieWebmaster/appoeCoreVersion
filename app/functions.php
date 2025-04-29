<?php

use App\AppLogging;
use App\Category;
use App\MailLogger;
use App\Media;
use App\Option;
use App\Plugin\ItemGlue\Article;
use App\Plugin\Traduction\Traduction;
use App\Users;
use App\Version;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//Get PHPMAILER
require_once WEB_PHPMAILER_PATH . 'Exception.php';
require_once WEB_PHPMAILER_PATH . 'PHPMailer.php';
require_once WEB_PHPMAILER_PATH . 'SMTP.php';


/**
 * @return string
 */
function pageSlug(): string
{
    return str_replace('/', '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
}


/**
 * @param string $paramName
 * @param $param
 * @return void
 */
function setPageParam(string $paramName, $param): void
{
    $pageParametresNames = [
        'currentPageCmsID',
        'currentPageLang',
        'currentPageID',
        'currentPageType',
        'currentPageName',
        'currentPageMenuName',
        'currentPageDescription',
        'currentPageImage',
        'currentPageSlug',
        'currentPageFilename',
    ];
    if (in_array($paramName, $pageParametresNames, true))
    {
        checkSession();
        $_SESSION[$paramName] = $param;
    }
}

/**
 * @param Article $Article
 */
function setArticle(Article $Article): void
{
    checkSession();
    $_SESSION['currentArticle'] = base64_encode(serialize($Article));
}

/**
 * @param string $param
 *
 * @return string
 */
function getPageParam(string $param): string
{
    $pageParametres = [
        'currentPageCmsID',
        'currentPageLang',
        'currentPageID',
        'currentPageType',
        'currentPageName',
        'currentPageMenuName',
        'currentPageDescription',
        'currentPageImage',
        'currentPageSlug',
        'currentPageFilename',
        ];
    if (in_array($param, $pageParametres, true)) {
        return $_SESSION[$param] ?? '';
    }
    return '';
}

/**
 * @return bool|mixed
 */
function getArticle(): mixed
{
    return !empty($_SESSION['currentArticle']) ? unserialize(base64_decode($_SESSION['currentArticle'])) : false;
}

/**
 * @return array
 */
function getPageData(): array
{
    return array(
        'id' => getPageParam('currentPageID'),
        'type' => getPageParam('currentPageType'),
        'name' => getPageParam('currentPageName'),
        'slug' => getPageParam('currentPageSlug')
    );
}

/**
 * @return mixed|string
 */
function getMetaData(): mixed
{
    $header = '<meta name="publisher" content="toutOuïe Communication" />';

    if (getPageParam('currentPageFilename') == 'index') {
        $header .= '<link rel="canonical" href="' . WEB_DIR_URL . '" />';
    }

    //Schema.org meta
    $header .= '<meta itemprop="name" content="' . getPageParam('currentPageName') . '" />';
    $header .= '<meta itemprop="description" content="' . getPageParam('currentPageDescription') . '" />';
    $header .= '<meta itemprop="image" content="' . getPageParam('currentPageImage') . '" />';
    $header .= '<link rel="author" href="' . WEB_DIR_URL . '" />';
    $header .= '<link rel="publisher" href="' . WEB_DIR_URL . '" />';

    //Open Graph meta
    $header .= '<meta property="og:title" content="' . getPageParam('currentPageName') . '" />';
    $header .= '<meta property="og:type" content="' . (getPageParam('currentPageType') === 'PAGE' ? 'website' : 'article') . '" />';
    $header .= '<meta property="og:url" content="' . WEB_DIR_URL . ltrim($_SERVER["REQUEST_URI"], '/') . '" />';
    $header .= '<meta property="og:image" content="' . getPageParam('currentPageImage') . '" />';
    $header .= '<meta property="og:description" content="' . getPageParam('currentPageDescription') . '" />';
    $header .= '<meta property="og:site_name" content="' . WEB_TITLE . '" />';

    //JSON-LD
    if (getPageParam('currentPageType') === 'ARTICLE') {
        $header .= '<script type="application/ld+json">{';
        $header .= '"@context": "https://schema.org",';
        $header .= '"@type": "NewsArticle",';
        $header .= '"image": ["' . getPageParam('currentPageImage') . '"],';
        $header .= '"headline": "' . htmlspecialchars(getPageParam('currentPageName')) . '",';
        $header .= '"description": "' . htmlspecialchars(getPageParam('currentPageDescription')) . '",';
        $header .= '"datePublished": "' . getArticle()->getCreatedAt() . '",';
        $header .= '"dateModified": "' . getArticle()->getUpdatedAt() . '",';
        $header .= '"mainEntityOfPage": {"@type": "WebPage","@id": "' . WEB_DIR_URL . ltrim($_SERVER["REQUEST_URI"], '/') . '"},';
        $header .= '"publisher": {"@type": "Organization","name": "' . WEB_TITLE . '","logo": {"@type": "ImageObject","url": "' . getLogo(false, true) . '"}},';
        $header .= '"author": {"@type": "Organization","name": "' . WEB_TITLE . '"}';
        $header .= '}</script>';
    } else {
        $header .= '<script type="application/ld+json">{';
        $header .= '"@context": "https://schema.org",';
        $header .= '"@type": "Organization",';
        $header .= '"name": "' . WEB_TITLE . '",';
        $header .= '"url": "' . WEB_DIR_URL . '",';
        $header .= '"logo": "' . getLogo(false, true) . '"';
        $header .= '}</script>';
    }
    return $header;
}

/**
 * @return string
 */
function getAppoeFavicon(): string
{
    $html = '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, shrink-to-fit=no">';
    $html .= '<link rel="apple-touch-icon" sizes="180x180" href="' . APP_LOGO_URL . 'apple-touch-icon.png">';
    $html .= '<link rel="icon" type="image/png" sizes="32x32" href="' . APP_LOGO_URL . 'favicon-32x32.png">';
    $html .= '<link rel="icon" type="image/png" sizes="16x16" href="' . APP_LOGO_URL . 'favicon-16x16.png">';
    $html .= '<link rel="manifest" href="' . APP_LOGO_URL . 'site.webmanifest">';
    $html .= '<link rel="mask-icon" href="' . APP_LOGO_URL . 'safari-pinned-tab.svg" color="#5ea3a3">';
    $html .= '<meta name="apple-mobile-web-app-title" content="APPOE">';
    $html .= '<meta name="application-name" content="APPOE">';
    $html .= '<meta name="msapplication-TileColor" content="#ffffff">';
    $html .= '<meta name="theme-color" content="#ffffff">';
    return $html;
}

/**
 * @param $pageName
 */
function setAppPageName($pageName): void
{
    checkSession();
    $_SESSION['currentAppPageName'] = $pageName;
}

/**
 * @param $pageSlug
 */
function setAppPageSlug($pageSlug): void
{
    checkSession();
    $_SESSION['currentAppPageSlug'] = $pageSlug;
}

/**
 * @return mixed|string
 */
function getAppPageName(): mixed
{
    return !empty($_SESSION['currentAppPageName']) ? $_SESSION['currentAppPageName'] : '';
}

/**
 * @return mixed|string
 */
function getAppPageSlug(): mixed
{
    return !empty($_SESSION['currentAppPageSlug']) ? $_SESSION['currentAppPageSlug'] : '';
}

/**
 * @param $url
 *
 * @return bool
 */
function isUrlRoot($url): bool
{

    if ($url == 'index' && (false !== strpos($_SERVER['REQUEST_URI'], 'home')
            || basename($_SERVER["SCRIPT_FILENAME"]) == 'index.php')) {
        return true;
    }

    return false;
}

/**
 * @param $url
 * @param string $classNameAdded
 *
 * @return string
 */
function activePage($url, string $classNameAdded = 'active'): string
{
    if (!empty($url)) {

        if (isUrlRoot($url)) {
            return $classNameAdded;
        }

        $urlParts = array_filter(explode('/', $_SERVER['REQUEST_URI']), function ($item) {
            return !empty($item);
        });

        if ($url == end($urlParts)) {
            return $classNameAdded;
        }

    }

    return '';
}

/**
 * Show Maintenance Header
 *
 * @param String $text
 */
function showMaintenanceHeader($text = 'Page en maintenance !'): void
{
    echo '<h1 class="bg-danger m-5 text-white">' . $text . '</h1>';
}

/**
 * Construct general menu
 *
 * @param $allPages
 *
 * @return array
 */

function constructMenu($allPages): array
{
    //Create menu
    $menu = [];
    if (!empty($allPages)) {
        foreach ($allPages as $menuPage) {

            //check if is Page or URL
            if (is_null($menuPage->slug)) {
                $menuPage->slug = $menuPage->idCms;
            }

            //check if is homepage
            if ($menuPage->filename === 'index') {
                $menuPage->slug = WEB_DIR_URL;
            }

            //First level menu sorting by location and second level by parent Id.
            $menu[$menuPage->location][$menuPage->parentId][] = $menuPage;
        }
    }

    return $menu;
}

/**
 * @param int $primaryIndex
 * @param int $parent
 *
 * @return array
 */
function getSessionMenu($primaryIndex = 1, $parent = 10): array
{

    $sessionMenu = [];
    if (!isArrayEmpty($_SESSION['MENU']) && array_key_exists($primaryIndex, $_SESSION['MENU'])) {

        if (array_key_exists($parent, $_SESSION['MENU'][$primaryIndex])) {
            return $_SESSION['MENU'][$primaryIndex][$parent];
        }

        return $_SESSION['MENU'][$primaryIndex];
    }

    return $sessionMenu;
}

/**
 * @param $lang
 */
function setCookiesLang($lang): void
{
    $options = array('expires' => time() + (12 * 3600), 'path' => WEB_DIR, 'secure' => false, 'httponly' => true, 'samesite' => 'Lax');
    setcookie('LANG', $lang, $options);
    setSessionLang($lang);
}

/**
 * @param string $lang
 */
function setSessionLang($lang = LANG): void
{
    $_SESSION['LANG'] = $lang;
}

/**
 * @return bool|mixed
 */
function getSessionLang()
{
    return !empty($_SESSION['LANG']) ? $_SESSION['LANG'] : false;
}

/**
 * @param int $primaryIndex
 *
 * @return bool
 */
function hasMenu(int $primaryIndex = 1): bool
{
    return array_key_exists('MENU', $_SESSION) && array_key_exists($primaryIndex, $_SESSION['MENU']);
}

/**
 * @param $index
 * @param int $menuPrimaryIndex
 *
 * @return bool
 */
function hasSubMenu($index, int $menuPrimaryIndex = 1): bool
{
    return array_key_exists($index, $_SESSION['MENU'][$menuPrimaryIndex]);
}

/**
 * @param $filename
 * @param string $jsonKey
 * @param string $jsonSecondKey
 *
 * @return bool|array
 */
function getJsonContent($filename, string $jsonKey = '', string $jsonSecondKey = ''): mixed
{
    if (file_exists($filename)) {

        $json = file_get_contents($filename);
        $parsed_json = $json ? json_decode($json, true) : false;

        if (is_array($parsed_json)) {

            if (!empty($jsonKey)) {

                if (array_key_exists($jsonKey, $parsed_json)) {

                    if (!empty($jsonSecondKey && array_key_exists($jsonSecondKey, $parsed_json[$jsonKey]))) {

                        return $parsed_json[$jsonKey][$jsonSecondKey];
                    }

                    return $parsed_json[$jsonKey];

                }

                return false;

            } else {
                return $parsed_json;
            }
        }

    }

    return false;
}

/**
 * @param $filename
 * @param $content
 * @param $mode
 *
 * @return bool
 */
function putJsonContent($filename, $content, $mode = 'w+')
{

    $json_file = fopen($filename, $mode);
    if (false !== $json_file) {
        fwrite($json_file, json_encode($content));

        return fclose($json_file);
    }

    return false;
}

/**
 * @param $object
 *
 * @return string
 */
function jsonHtmlParse($object)
{
    return !empty($object) ? json_encode($object, JSON_UNESCAPED_UNICODE) : '';
}

/**
 * @param string $name
 * @param string $slug
 * @param string $appendName
 * @param string $appendHtml
 *
 * @return string
 */
function getTitle($name = '', $slug = '', $appendName = '', $appendHtml = '')
{
    $html = '<div class="row"><div class="col-12 position-relative">
            <h1 class="bigTitle icon-' . $slug . '"><span class="colorPrimary mr-2"></span>' . trans($name) . $appendName . '</h1>
            ' . $appendHtml . '</div></div><hr class="mx-5 mt-3 mb-4">';

    return $html;
}

/**
 * @param string $color
 *
 * @return string
 */
function getAppoeCredit($color = "")
{
    return 'Propulsé par <a target="_blank" ' . (!empty($color) ? 'style="color:' . $color . '"' : '') . ' href="https://tout-ouie.com/" title="APPOE">APPOE</a>';
}

/**
 * @param $key
 * @return bool
 */
function getOptionPreference($key)
{
    $Option = new Option();
    $Option->setType('PREFERENCE');
    $Option->setKey($key);
    return $Option->getValByKey();
}

/**
 * @param $key
 * @return bool
 */
function getOptionData($key)
{
    $Option = new Option();
    $Option->setType('DATA');
    $Option->setKey($key);
    return $Option->getValByKey();
}

/**
 * @param $key
 * @return bool
 */
function getOptionTheme($key)
{
    $Option = new Option();
    $Option->setType('THEME');
    $Option->setKey($key);
    return $Option->getValByKey();
}

/**
 * @param $type
 * @param $key
 * @return bool
 */
function getOption($type, $key)
{
    $Option = new Option();
    $Option->setType($type);
    $Option->setKey($key);
    return $Option->getValByKey();
}

/**
 * Show custom APPOE theme
 */
function showThemeRoot()
{
    if (!file_exists(WEB_TEMPLATE_PATH . 'css/theme.css')) {
        $Option = new Option();
        $Option->setType('THEME');
        $theme = $Option->showByType();

        $themeRoot = ':root{';
        if ($theme) {
            foreach ($theme as $style) {
                $themeRoot .= $style->key . ': ' . $style->val . ';';
            }
        } else {
            foreach (THEME_DEFAULT_STYLE as $key => $value) {
                $themeRoot .= $key . ': ' . $value . ';';
            }
        }
        $themeRoot .= '}';

        createFile(WEB_TEMPLATE_PATH . 'css/theme.css', ['content' => $themeRoot . THEME_CONTENT]);
    }
    echo '<link rel="stylesheet" type="text/css" href="' . WEB_TEMPLATE_URL . 'css/theme.css">';
}

/**
 * @param $array
 *
 * @return mixed
 */
function isArrayEmpty($array)
{
    $empty = true;

    if ($array && is_array($array)) {
        array_walk_recursive($array, function ($leaf) use (&$empty) {
            if ($leaf === [] || $leaf === '') {
                return;
            }

            $empty = false;
        });
    }

    return $empty;
}

/**
 * Multidimensional Array Sort
 *
 * @param $array
 * @param $keyName
 * @param int $order
 *
 * @return array
 */
function array_sort($array, $keyName, $order = SORT_ASC)
{

    $new_array = [];
    $sortable_array = [];

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v) || is_object($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $keyName) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
                break;
            case SORT_DESC:
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}

/**
 * @param $mediaPath
 *
 * @return bool
 */
function isImage($mediaPath)
{
    if (file_exists($mediaPath)) {
        $mime = exif_imagetype($mediaPath);
        return filesize($mediaPath) > 11 ? ($mime == IMAGETYPE_JPEG || $mime == IMAGETYPE_PNG || $mime == IMAGETYPE_GIF
            || $mime == IMAGETYPE_WEBP || isSvg($mediaPath)) : false;
    }

    return false;
}

/**
 * @param $mediaPath
 * @return bool
 */
function isSvg($mediaPath)
{
    return 'image/svg+xml' === mime_content_type($mediaPath) || 'image/svg' === mime_content_type($mediaPath);
}

/**
 * @param $mediaPath
 *
 * @return bool
 */
function isAudio($mediaPath)
{
    if (file_exists($mediaPath)) {
        $mime = mime_content_type($mediaPath);

        return (false !== strpos($mime, 'audio/')) ? true : false;
    }

    return false;
}

/**
 * @param $mediaPath
 *
 * @return bool
 */
function isVideo($mediaPath)
{
    if (file_exists($mediaPath)) {
        $allowed = array(
            'application/ogg'
        );

        $mime = mime_content_type($mediaPath);

        return (false !== strpos($mime, 'video/')) || in_array($mime, $allowed) ? true : false;
    }

    return false;
}

/**
 * Return a data URI for file
 * @param $file
 * @return string
 */
function dataURIencode($file)
{

    $mime_type = mime_content_type($file);
    $file_binary = file_get_contents($file);
    return 'data:' . $mime_type . ';base64,' . base64_encode($file_binary);
}

/**
 * @param $text
 * @param $size
 *
 * @return string
 */
function shortenText($text, $size)
{
    return mb_strimwidth(strip_tags(htmlspecialchars_decode($text)), 0, $size, '...', 'utf-8');
}

/**
 * Unset same key in array and return it sliced
 *
 * @param array $data
 * @param $compareKey
 * @param bool $returnSliceArray
 *
 * @return array
 */
function unsetSameKeyInArr(array $data, $compareKey, $returnSliceArray = false)
{

    if (in_array($compareKey, array_keys($data))) {
        unset($data[$compareKey]);
    }

    if (false !== $returnSliceArray && is_int($returnSliceArray)) {
        $data = array_slice($data, 0, $returnSliceArray, true);
    }

    return $data;
}

/**
 * @param $text
 *
 * @return string
 */
function minimalizeText($text)
{
    return strtolower(removeAccents(trim($text)));
}

/**
 * @param $item
 *
 * @return string
 */
function htmlSpeCharDecode($item): string
{
    return htmlspecialchars_decode($item ?? '', ENT_QUOTES);
}

/**
 * @param $item
 *
 * @return string
 */
function htmlEntityDecode($item)
{
    return html_entity_decode($item, ENT_QUOTES);
}

/**
 * @param $key
 * @param string $doc
 *
 * @return mixed
 */
function trans($key, $doc = 'general')
{
    $trans = minimalizeText($key);
    $currLang = isUserInApp() ? INTERFACE_LANG : LANG;
    if ($currLang != 'fr' && file_exists(FILE_LANG_PATH . $currLang . DIRECTORY_SEPARATOR . $doc . '.json')) {

        //get lang file
        $json = file_get_contents(FILE_LANG_PATH . $currLang . DIRECTORY_SEPARATOR . $doc . '.json');
        $parsedJson = json_decode($json, true);

        //preparing to compare
        $langArray = array_map('minimalizeText', array_keys($parsedJson));

        //comparing
        $tradPos = (in_array($trans, $langArray)) ? array_search($trans, $langArray) : null;


        return !is_null($tradPos) ? $parsedJson[array_keys($parsedJson)[$tradPos]] : $key;

    } else {
        return $key;
    }
}

/**
 * @param $text
 * @param $tradToOrigin
 * @param $lang
 *
 * @return mixed
 */
function trad($text, $tradToOrigin = false, $lang = LANG)
{
    if (class_exists('App\Plugin\Traduction\Traduction')) {

        $Traduction = new Traduction($lang);

        return !$tradToOrigin ? $Traduction->trans($text) : $Traduction->transToOrigin($text);
    }

    return $text;
}

/**
 * @param $text
 *
 * @return null|string|string[]
 */
function slugify($text)
{
    if (is_array($text)) {
        $text = implode('-', $text);
    }

    $text = removeAccents($text);

    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '-');

    // remove duplicate -
    $text = preg_replace('~-+~', '-', $text);

    // lowercase
    $text = mb_strtolower($text);

    if (empty($text)) {
        return '-';
    }

    return $text;
}

/**
 * @param string $dirname
 * @param array $options
 *
 * @return array
 */
function getFilesFromDir(string $dirname, array $options = []): array
{
    $defaultOptions = [
        'onlyFiles' => false,
        'onlyExtension' => false,
        'allExtensionsExceptOne' => false,
        'noExtensionDisplaying' => false
    ];

    $options = array_merge($defaultOptions, $options);
    $files = [];

    if ($options['onlyFiles']) {

        $iterator = new FilesystemIterator($dirname, FilesystemIterator::SKIP_DOTS);

        foreach ($iterator as $fileinfo) {
            if ($fileinfo->isFile()) {

                if (false !== $options['onlyExtension'] && $fileinfo->getExtension() !== $options['onlyExtension']) {
                    continue;
                }

                if (false !== $options['allExtensionsExceptOne'] && $fileinfo->getExtension() === $options['allExtensionsExceptOne']) {
                    continue;
                }

                $suffix = false !== $options['noExtensionDisplaying'] ? '.' . $fileinfo->getExtension() : '';
                $files[] = $fileinfo->getBasename($suffix);
            }
        }

        return $files;
    }

    return array_diff(scandir($dirname), ['..', '.']);
}


/**
 * @return array
 */
function getLangs()
{
    return LANGUAGES;
}

/**
 * @return array
 */
function getAppLangs()
{
    return getFilesFromDir(WEB_SYSTEM_PATH . 'lang/');
}

/**
 * @param $name
 *
 * @return string
 */
function getAppImg($name)
{
    return APP_IMG_URL . $name;
}

/**
 * @param $lang
 *
 * @return bool
 */
function langExist($lang)
{

    if (array_key_exists($lang, LANGUAGES)) {
        return true;
    }

    return false;
}

/**
 * @return bool
 */
function getIP()
{
    foreach (
        array(
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ) as $key
    ) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                    return $ip;
                }
            }
        }
    }

    return false;
}

/**
 * @param $request
 *
 * @return bool
 */
function checkRequest($request)
{
    $unauthorized_characters = '"\'/\\#<>$*%!§;?([{)]}+=&²~|£µ';
    $request_error = 0;

    if (is_array($request)) {
        foreach ($request as $key => $value) {

            $requestLenght = strlen($value);

            for ($i = 0; $i < $requestLenght; $i++) {
                if (strpos($unauthorized_characters, strtolower($value[$i]))) {
                    $request_error++;
                }
            }
        }
    } else {

        $requestLenght = strlen($request);

        for ($i = 0; $i < $requestLenght; $i++) {
            if (strpos($unauthorized_characters, strtolower($request[$i]))) {
                $request_error++;
            }
        }
    }

    if ($request_error > 0) {
        return false;
    } else {
        return true;
    }

}

/**
 * @param $data
 *
 * @return bool
 */
function generateSitemap($data)
{
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';

    $sitemap .= '<url>';
    $sitemap .= '<loc>' . WEB_DIR_URL . '</loc>';
    $sitemap .= '<priority>1.0</priority>';
    $sitemap .= '</url>';

    if (!isArrayEmpty($data)) {

        foreach ($data as $location) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . $location['slug'] . '</loc>';
            $sitemap .= '</url>';
        }
    }

    $sitemap .= '</urlset>';

    if (false !== $file = fopen(ROOT_PATH . 'sitemap.xml', 'w+')) {
        if (false !== fwrite($file, $sitemap)) {

            //Robots file
            $robotTxt = 'User-agent: *' . "\r\n";
            $robotTxt .= 'Allow: /' . "\r\n";
            $robotTxt .= 'Sitemap: ' . WEB_DIR_URL . 'sitemap.xml';

            if (false !== $robotsFile = fopen(ROOT_PATH . 'robots.txt', 'w+')) {
                fwrite($robotsFile, $robotTxt);
            }

            return true;
        }
    }


    return false;
}

/**
 * Function to clean requests
 *
 * @param mixed $data
 * @param array|null $exclude
 *
 * @return array|string
 */
function cleanRequest($data, array $exclude = [])
{
    if (is_array($data)) {

        foreach ($data as $key => $value) {

            if (!in_array($key, $exclude)) {
                $data[$key] = cleanRequest($value, $exclude);
            }
        }

    } else {
        $data = cleanData($data);
    }

    return $data;
}

/**
 * @param $data
 *
 * @return string
 */
function cleanData($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);

    return $data;
}

/**
 * @param array $post
 * @param $serverSecretKey
 * @return bool
 */
function checkAjaxPostRecaptcha(array $post, $serverSecretKey)
{
    //Check Ajax Request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

        //Clean Post
        $recaptchaField = cleanRequest($post['g-recaptcha-response']);

        //Check and Confirm Recaptcha V3
        if (!empty($recaptchaField)) {
            return checkRecaptcha($serverSecretKey, $recaptchaField);
        }
    }

    return false;
}

/**
 * @param $secret
 * @param $token
 *
 * @return bool
 */
function checkRecaptcha($secret, $token)
{

    if (!empty($secret) && !empty($token)) {

        $recaptcha_data = array(
            'secret' => $secret,
            'response' => $token
        );

        $recap_verify = curl_init();
        curl_setopt($recap_verify, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
        curl_setopt($recap_verify, CURLOPT_POST, true);
        curl_setopt($recap_verify, CURLOPT_POSTFIELDS, http_build_query($recaptcha_data));
        curl_setopt($recap_verify, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($recap_verify, CURLOPT_RETURNTRANSFER, true);
        $recap_response = curl_exec($recap_verify);

        $g_response = json_decode($recap_response);

        if (property_exists($g_response, 'success') && $g_response->success === true) {
            return true;
        }
    }

    return false;
}

/**
 * Show unlimited params in array
 */
function debug(): void
{
    echo '<pre>';
    print_r(func_get_args());
    echo '</pre>';
}

/**
 * @param $dateStr
 * @param string $format
 *
 * @return bool
 */
function isValidDateTime($dateStr, string $format = 'Y-m-d H:i:s'): bool
{
    $date = DateTime::createFromFormat($format, $dateStr);

    return $date && ($date->format($format) === $dateStr);
}

/**
 * @param $timestamp
 * @param bool $hour
 *
 * @return string
 * @throws Exception
 */
function displayTimeStamp($timestamp, bool $hour = true): string
{
    $Date = new DateTime($timestamp, new DateTimeZone('Europe/Paris'));

    if ($hour) {
        return $Date->format('d/m/Y H:i');
    }

    return $Date->format('d/m/Y');
}


/**
 * @param string $date
 * @param bool $hour
 * @param bool|string $defaultFormat
 *
 * @return bool|string
 * @throws Exception
 */
function displayCompleteDate(string $date, bool $hour = false, string|false $defaultFormat = false): string|false
{
    $dateFormat = 'Y-m-d' . ($hour ? ' H:i:s' : '');
    $dateFormatFr = 'd/m/Y' . ($hour ? ' H:i:s' : '');

    $Date = DateTime::createFromFormat($dateFormatFr, $date)
        ?: DateTime::createFromFormat($dateFormat, $date);

    if (!$Date) {
        return false;
    }

    if ($defaultFormat === false) {
        $defaultFormat = $hour ? 'l d F Y, H:i' : 'l d F Y';
    }

    $formatted = $Date->format($defaultFormat);

    $en = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday',
        'January','February','March','April','May','June','July','August','September','October','November','December'];
    $fr = ['Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche',
        'Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];

    return str_replace($en, $fr, $formatted);
}



/**
 * @param $duree
 *
 * @return string
 */
function displayDuree($duree)
{
    return (strlen($duree) == 2) ? $duree . ' min' : $duree;
}

/**
 * @param $date1
 * @param string $date2
 *
 * @return string
 * @throws Exception
 */
function getHoursFromDate($date1, $date2 = '')
{
    $Date1 = new DateTime($date1);

    if (empty($date2)) {
        return $Date1->format('H:i');
    } else {
        $Date2 = new DateTime($date2);

        return trans('De') . ' ' . $Date1->format('H:i') . ' ' . trans('à') . ' ' . $Date2->format('H:i');
    }
}

/**
 * @param $date
 * @param $format
 *
 * @return string
 * @throws Exception
 */
function displayFrDate($date, $format = 'Y-m-d')
{
    if (isValidDateTime($date, $format)) {

        $Date = new DateTime($date);

        return $Date->format('d/m/Y');
    } else {
        return '';
    }
}

/**
 * @param $date
 * @param $format
 *
 * @return string
 * @throws Exception
 */
function displayDBDate($date, $format = 'd/m/Y')
{
    if (isValidDateTime($date, $format)) {

        $Date = new DateTime($date);

        return $Date->format('Y-m-d');
    } else {
        return '';
    }
}

/**
 * @param $s
 *
 * @return array[]|false|string[]
 */
function splitAtUpperCase($s)
{
    return preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY);
}

/**
 * @param $array
 * @param $searchingFor
 *
 * @return bool
 */
function checkIfInArrayString($array, $searchingFor)
{
    foreach ($array as $element) {
        if (strpos($searchingFor, $element) !== false) {
            return true;
        }
    }

    return false;
}

/**
 * @return bool
 */
function checkMaintenance()
{
    if ('true' === getOptionPreference('maintenance')) {
        if (isIpAdministrator()) {
            return false;
        }
        return true;
    }
    return false;
}

/**
 * @return bool
 */
function isIpAdministrator()
{
    //Check IP permission
    $ip = getIP();

    $Option = new Option();
    $Option->setType('IPACCESS');

    //Check Ip from db
    if (in_array($ip, extractFromObjToSimpleArr($Option->showByType(), 'key', 'key'))) {
        return true;
    }

    //Check Ip from ini.main
    if (defined('IP_ALLOWED')) {

        //IPV6
        if (false !== strpos($ip, ':')) {
            $ipv6 = implode(':', explode(':', $ip, -4));
            if (in_array($ipv6, IP_ALLOWED)) {
                return true;
            }
        }

        if (in_array($ip, IP_ALLOWED)) {
            return true;
        }
    }

    return false;
}

/**
 * @return array
 */
function getPlugins()
{

    $plugins = [];

    if ($pluginsDir = opendir(WEB_PLUGIN_PATH)) {
        while (false !== ($dossier = readdir($pluginsDir))) {

            $setupPath = '';
            $version = '';
            if ($dossier != '.' && $dossier != '..' && $dossier != 'index.php') {

                if (file_exists(WEB_PLUGIN_PATH . $dossier . '/setup.php')) {
                    $setupPath = WEB_PLUGIN_URL . $dossier . '/setup.php';
                }

                if (file_exists(WEB_PLUGIN_PATH . $dossier . '/version.json')) {
                    $version = WEB_PLUGIN_PATH . $dossier . '/version.json';
                }

                array_push($plugins, array(
                    'name' => $dossier,
                    'pluginPath' => WEB_PLUGIN_PATH . $dossier . '/',
                    'setupPath' => $setupPath,
                    'versionPath' => $version
                ));
            }
        }
    }

    return $plugins;
}

/**
 * @return array
 */
function getPluginsName()
{
    return array_keys(groupMultipleKeysArray(getPlugins(), 'name'));
}

/**
 * @param $setupPath
 *
 * @return bool|string
 */
function activePlugin($setupPath)
{
    stream_context_set_default([
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false
        ]
    ]);
    return file_get_contents($setupPath);
}

/**
 * @param $pluginName
 *
 * @return bool
 */
function pluginExist($pluginName)
{
    $dir = WEB_PLUGIN_PATH . $pluginName;

    return file_exists($dir) && is_dir($dir);
}

/**
 * @param $octets
 *
 * @return false|string
 */
function getSizeName($octets)
{
    if (is_numeric($octets)) {
        for ($i = 0; $i < 8 && $octets >= 1024; $i++) {
            $octets = $octets / 1024;
        }
        if ($i > 0) {
            return preg_replace('/,00$/', '', number_format($octets, 2, ',', ''))
                . ' ' . substr('KMGTPEZY', $i - 1, 1) . 'o';
        } else {
            return $octets . ' o';
        }
    }
    return false;
}


/**
 * @param bool $DB
 *
 * @return bool
 * @throws Exception
 */
function appBackup($DB = true)
{

    //check existing main backup folder or created it
    if (!file_exists(WEB_APP_PATH . 'backup/')) {
        if (mkdir(WEB_APP_PATH . 'backup', 0705)) {
            return appBackup();
        }
    }

    $backUpFolder = WEB_BACKUP_PATH . date('Y-m-d');

    //check existing backup folder for today or created it
    if (!file_exists($backUpFolder)) {
        if (@mkdir($backUpFolder, 0705)) {
            if ($DB) {

                //save db
                \App\DB::backup(date('Y-m-d'));

                //check if db was saved
                if (!file_exists($backUpFolder . DIRECTORY_SEPARATOR . 'db.sql.gz')) {
                    error_log(date('d/m/Y H:i') . ' : La sauvegarde de la base de données de ' . WEB_TITLE . ' n\'a pas été effectuée.', 0);
                }
            }
        }

        //delete old folders (-30 days)
        $maxAutorizedFolderDate = new DateTime('-30 days');
        $directories = scandir(WEB_BACKUP_PATH);
        foreach ($directories as $directory) {
            if ($directory != '.' and $directory != '..') {
                if (is_dir(WEB_BACKUP_PATH . $directory)) {
                    if ($maxAutorizedFolderDate > new DateTime($directory)) {
                        deleteAllFolderContent(WEB_BACKUP_PATH . $directory);
                    }
                }
            }
        }
    }

    return true;
}

/**
 * @param $text
 */
function appLog($text)
{
    $AppLog = new AppLogging();
    $AppLog->write($text);
}

/**
 * @param $path
 * @param $url
 *
 * @return bool
 */
function downloadFile($path, $url)
{
    $fh = fopen($path, 'wb');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FILE, $fh);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // this will follow redirects
    curl_exec($ch);
    curl_close($ch);
    fclose($fh);

    return true;
}


/**
 * Recursively copy files from one directory to another
 *
 * @param String $src - Source of files being moved
 * @param String $dest - Destination of files being moved
 *
 * @return bool
 */
function rcopy($src, $dest)
{

    // If source is not a directory stop processing
    if (!is_dir($src)) {
        return false;
    }

    // If the destination directory does not exist create it
    if (!is_dir($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    // Open the source directory to read in files
    $i = new DirectoryIterator($src);
    foreach ($i as $f) {
        if ($f->isFile()) {
            copy($f->getRealPath(), "$dest/" . $f->getFilename());
        } else if (!$f->isDot() && $f->isDir()) {
            rcopy($f->getRealPath(), "$dest/$f");
        }
    }

    return true;
}

/**
 * @param string $folder
 *
 * @return array|bool
 */
function saveFiles($folder = 'public')
{

    // Get real path for our folder
    $rootPath = realpath(getenv('DOCUMENT_ROOT') . DIRECTORY_SEPARATOR . 'APPOE' . DIRECTORY_SEPARATOR . $folder);

    $dest = ROOT_PATH . 'app/backup/' . date('Y-m-d');

    if (!is_dir($dest) && !is_file($dest)) {
        if (!mkdir($dest)) {
            return false;
        }
    }

    $filename = slugify($folder) . '-' . date('H_i_s') . '-files.zip';
    $saveFileName = $dest . DIRECTORY_SEPARATOR . $filename;
    $downloadFileName = WEB_DIR_URL . 'app/backup/' . date('Y-m-d') . DIRECTORY_SEPARATOR . $filename;

    // Initialize archive object
    $zip = new ZipArchive();
    if ($zip->open($saveFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE)) {

        // Create recursive directory iterator
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        $filesSize = 0;

        foreach ($files as $name => $file) {

            // Skip directories (they would be added automatically)
            if (!$file->isDir()) {

                // Get real and relative path for current file
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($rootPath) + 1);

                // Add current file to archive
                $zip->addFile($filePath, $relativePath);

                $filesSize += $file->getSize();
            }
        }

        // Zip archive will be created only after closing object
        if ($zip->close()) {
            return array(
                'copySize' => $filesSize,
                'zipSize' => filesize($saveFileName),
                'downloadLink' => $downloadFileName
            );
        }
    }

    return false;
}

/**
 * Create folder
 *
 * @param string $structure
 * @param int $chmod
 * @param bool $recursive
 * @return bool
 */
function createFolder($structure, $chmod = 0755, $recursive = false)
{
    if (!is_dir($structure)) {
        if (!mkdir($structure, $chmod, $recursive)) {
            return false;
        }
    }

    return true;
}

/**
 * Create file with options
 *
 * @param string $structure
 * @param array $options can contains [mode, chmod, content]
 * @return bool
 */
function createFile($structure, array $options = [])
{
    $defaultOptions = array('mode' => 'w', 'chmod' => 0644, 'content' => null);
    $options = array_merge($defaultOptions, $options);

    if (!is_file($structure)) {
        if (!fopen($structure, 'w')) {
            return false;
        }

        chmod($structure, $options['chmod']);

        if ($options['content']) {
            $file = fopen($structure, $options['mode']);
            fwrite($file, $options['content']);
            fclose($file);
        }
    }
    return true;
}

/**
 * Update file with options
 *
 * @param string $structure
 * @param array $options can contains [mode, chmod, content]
 * @return bool
 */
function updateFile($structure, array $options = [])
{
    $defaultOptions = array('mode' => 'w', 'chmod' => 0644, 'content' => null);
    $options = array_merge($defaultOptions, $options);

    if (!is_file($structure)) {
        if (!fopen($structure, 'w')) {
            return false;
        }
    }

    chmod($structure, $options['chmod']);

    if ($options['content']) {
        $file = fopen($structure, $options['mode']);
        fwrite($file, $options['content']);
        fclose($file);
    }

    return true;
}

/**
 * Recursively move files from one directory to another
 *
 * @param String $src - Source of files being moved
 * @param String $dest - Destination of files being moved
 *
 * @return bool
 */
function rmove($src, $dest)
{

    // If source is not a directory stop processing
    if (!is_dir($src) && !is_file($dest)) {
        return false;
    }

    // If the destination directory does not exist create it
    if (!is_dir($dest) && !is_file($dest)) {
        if (!mkdir($dest)) {
            // If the destination directory could not be created stop processing
            return false;
        }
    }

    if (is_file($dest)) {
        rename(realpath($src), "$dest");
    } else {
        // Open the source directory to read in files
        $i = new DirectoryIterator($src);
        foreach ($i as $f) {
            if ($f->isFile()) {
                if ($f->getFilename() != 'setup.php') {
                    rename($f->getRealPath(), "$dest/" . $f->getFilename());
                }
            } else if (!$f->isDot() && $f->isDir()) {
                rmove($f->getRealPath(), "$dest/$f");
                if ($f->getRealPath() && !is_dir($f->getRealPath())) {
                    unlink($f->getRealPath());
                }
            }
        }
    }

    deleteAllFolderContent($src);

    return true;
}

/**
 * @param $oldName
 * @param $newName
 *
 * @return bool
 */
function renameFile($oldName, $newName)
{

    if (!file_exists($newName)) {
        return rename($oldName, $newName);

    }

    return false;
}

/**
 * @param $src
 * @param $path
 * @param $firstFolderName
 * @param $replaceInPath
 * @param bool $deleteZip
 *
 * @return bool
 */
function unzipSkipFirstFolder($src, $path, $firstFolderName, $replaceInPath, $deleteZip = true)
{
    $tempFolder = $path . 'unzip';
    $pluginsName = getPluginsName();

    $zip = new ZipArchive;
    $res = $zip->open($src);
    if ($res === true) {
        $zip->extractTo($tempFolder);
        $directories = scandir($tempFolder . '/' . $firstFolderName);
        foreach ($directories as $directory) {
            if ($directory != '.' and $directory != '..') {

                if (is_dir($tempFolder . '/' . $firstFolderName . '/' . $directory)) {

                    if (!is_dir($replaceInPath . $directory) && in_array($directory, $pluginsName)) {
                        createFolder($replaceInPath . $directory);
                    }

                    if (is_dir($replaceInPath . $directory)) {
                        rmove($tempFolder . '/' . $firstFolderName . '/' . $directory, $replaceInPath . $directory);
                    }

                } elseif (is_file($tempFolder . '/' . $firstFolderName . '/' . $directory)) {

                    if (!is_file($replaceInPath . $directory)) {
                        createFile($replaceInPath . $directory);
                    }

                    rmove($tempFolder . '/' . $firstFolderName . '/' . $directory, $replaceInPath . $directory);
                }
            }
        }

        $zip->close();
    }
    deleteAllFolderContent($tempFolder);
    if ($deleteZip) {
        unlink($src);
    }

    return true;
}

/**
 * @param $src
 * @param $path
 * @param bool $deleteZip
 *
 * @return bool
 */
function unzip($src, $path, $deleteZip = true)
{
    $zip = new ZipArchive;
    if (true === $zip->open($src)) {
        $zip->extractTo($path);
        $zip->close();
    }

    if ($deleteZip) {
        unlink($src);
    }

    return true;
}

/**
 * Remove dir with all files
 *
 * @param $dirPath
 */
function deleteAllFolderContent($dirPath)
{
    if (is_dir($dirPath)) {
        $objects = scandir($dirPath);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dirPath . "/" . $object) == "dir") {
                    deleteAllFolderContent($dirPath . "/" . $object);
                } else {
                    unlink($dirPath . "/" . $object);
                }
            }
        }
        reset($objects);
        rmdir($dirPath);
    } elseif (is_file($dirPath)) {
        unlink($dirPath);
    }
}

/**
 * @return array
 */
function getAppTypes()
{
    //get plugin types
    $allTypes = getPluginsName();
    $allTypes = array_combine(array_map('strtoupper', $allTypes), array_map('strtoupper', $allTypes));

    //get app types
    $appTypes = array_combine(array_map('strtoupper', CATEGORY_TYPES), array_map('strtoupper', CATEGORY_TYPES));

    return array_merge($allTypes, $appTypes);
}

/**
 * @return array
 */
function listPays()
{
    return array
    (
        "ZA" => "Afrique du Sud",
        "AL" => "Albanie",
        "DZ" => "Algérie",
        "DE" => "Allemagne",
        "AD" => "Andorre",
        "AO" => "Angola",
        "AI" => "Anguilla",
        "AG" => "Antigua-et-Barbuda",
        "AN" => "Antilles néerlandaises",
        "SA" => "Arabie saoudite",
        "AR" => "Argentine",
        "AM" => "Arménie",
        "AW" => "Aruba",
        "AU" => "Australie",
        "AT" => "Autriche",
        "AZ" => "Azerbaïdjan",
        "BS" => "Bahamas",
        "BH" => "Bahreïn",
        "BB" => "Barbade",
        "BE" => "Belgique",
        "BZ" => "Belize",
        "BJ" => "Bénin",
        "BM" => "Bermudes",
        "BT" => "Bhoutan",
        "BY" => "Biélorussie",
        "BO" => "Bolivie",
        "BA" => "Bosnie-Herzégovine",
        "BW" => "Botswana",
        "BR" => "Brésil",
        "BN" => "Brunéi Darussalam",
        "BG" => "Bulgarie",
        "BF" => "Burkina Faso",
        "BI" => "Burundi",
        "KH" => "Cambodge",
        "CM" => "Cameroun",
        "CA" => "Canada",
        "CV" => "Cap-Vert",
        "CL" => "Chili",
        "C2" => "Chine",
        "CY" => "Chypre",
        "CO" => "Colombie",
        "KM" => "Comores",
        "CG" => "Congo-Brazzaville",
        "CD" => "Congo-Kinshasa",
        "KR" => "Corée du Sud",
        "CR" => "Costa Rica",
        "CI" => "Côte d’Ivoire",
        "HR" => "Croatie",
        "DK" => "Danemark",
        "DJ" => "Djibouti",
        "DM" => "Dominique",
        "EG" => "Égypte",
        "SV" => "El Salvador",
        "AE" => "Émirats arabes unis",
        "EC" => "Équateur",
        "ER" => "Érythrée",
        "ES" => "Espagne",
        "EE" => "Estonie",
        "VA" => "État de la Cité du Vatican",
        "FM" => "États fédérés de Micronésie",
        "US" => "États-Unis",
        "ET" => "Éthiopie",
        "FJ" => "Fidji",
        "FI" => "Finlande",
        "FR" => "France",
        "GA" => "Gabon",
        "GM" => "Gambie",
        "GE" => "Géorgie",
        "GI" => "Gibraltar",
        "GR" => "Grèce",
        "GD" => "Grenade",
        "GL" => "Groenland",
        "GP" => "Guadeloupe",
        "GT" => "Guatemala",
        "GN" => "Guinée",
        "GW" => "Guinée-Bissau",
        "GY" => "Guyana",
        "GF" => "Guyane française",
        "HN" => "Honduras",
        "HU" => "Hongrie",
        "NF" => "Île Norfolk",
        "KY" => "Îles Caïmans",
        "CK" => "Îles Cook",
        "FO" => "Îles Féroé",
        "FK" => "Îles Malouines",
        "MH" => "Îles Marshall",
        "PN" => "Îles Pitcairn",
        "SB" => "Îles Salomon",
        "TC" => "Îles Turques-et-Caïques",
        "VG" => "Îles Vierges britanniques",
        "IN" => "Inde",
        "ID" => "Indonésie",
        "IE" => "Irlande",
        "IS" => "Islande",
        "IL" => "Israël",
        "IT" => "Italie",
        "JM" => "Jamaïque",
        "JP" => "Japon",
        "JO" => "Jordanie",
        "KZ" => "Kazakhstan",
        "KE" => "Kenya",
        "KG" => "Kirghizistan",
        "KI" => "Kiribati",
        "KW" => "Koweït",
        "RE" => "La Réunion",
        "LA" => "Laos",
        "LS" => "Lesotho",
        "LV" => "Lettonie",
        "LI" => "Liechtenstein",
        "LT" => "Lituanie",
        "LU" => "Luxembourg",
        "LY" => "Libye",
        "MK" => "Macédoine",
        "MG" => "Madagascar",
        "MY" => "Malaisie",
        "MW" => "Malawi",
        "MV" => "Maldives",
        "ML" => "Mali",
        "MT" => "Malte",
        "MA" => "Maroc",
        "MQ" => "Martinique",
        "MU" => "Maurice",
        "MR" => "Mauritanie",
        "YT" => "Mayotte",
        "MX" => "Mexique",
        "MD" => "Moldavie",
        "MC" => "Monaco",
        "MN" => "Mongolie",
        "ME" => "Monténégro",
        "MS" => "Montserrat",
        "MZ" => "Mozambique",
        "NA" => "Namibie",
        "NR" => "Nauru",
        "NP" => "Népal",
        "NI" => "Nicaragua",
        "NE" => "Niger",
        "NG" => "Nigéria",
        "NU" => "Niue",
        "NO" => "Norvège",
        "NC" => "Nouvelle-Calédonie",
        "NZ" => "Nouvelle-Zélande",
        "OM" => "Oman",
        "UG" => "Ouganda",
        "PW" => "Palaos",
        "PA" => "Panama",
        "PG" => "Papouasie-Nouvelle-Guinée",
        "PY" => "Paraguay",
        "NL" => "Pays-Bas",
        "PE" => "Pérou",
        "PH" => "Philippines",
        "PL" => "Pologne",
        "PF" => "Polynésie française",
        "PT" => "Portugal",
        "QA" => "Qatar",
        "HK" => "R.A.S. chinoise de Hong Kong",
        "DO" => "République dominicaine",
        "CZ" => "République tchèque",
        "RO" => "Roumanie",
        "GB" => "Royaume-Uni",
        "RU" => "Russie",
        "RW" => "Rwanda",
        "KN" => "Saint-Christophe-et-Niévès",
        "SM" => "Saint-Marin",
        "PM" => "Saint-Pierre-et-Miquelon",
        "VC" => "Saint-Vincent-et-les-Grenadines",
        "SH" => "Sainte-Hélène",
        "LC" => "Sainte-Lucie",
        "WS" => "Samoa",
        "ST" => "Sao Tomé-et-Principe",
        "SN" => "Sénégal",
        "RS" => "Serbie",
        "SC" => "Seychelles",
        "SL" => "Sierra Leone",
        "SG" => "Singapour",
        "SK" => "Slovaquie",
        "SI" => "Slovénie",
        "SO" => "Somalie",
        "LK" => "Sri Lanka",
        "SE" => "Suède",
        "CH" => "Suisse",
        "SR" => "Suriname",
        "SJ" => "Svalbard et Jan Mayen",
        "SZ" => "Swaziland",
        "TJ" => "Tadjikistan",
        "TW" => "Taïwan",
        "TZ" => "Tanzanie",
        "TD" => "Tchad",
        "TH" => "Thaïlande",
        "TG" => "Togo",
        "TO" => "Tonga",
        "TT" => "Trinité-et-Tobago",
        "TN" => "Tunisie",
        "TM" => "Turkménistan",
        "TR" => "Turquie",
        "TV" => "Tuvalu",
        "UA" => "Ukraine",
        "UY" => "Uruguay",
        "VU" => "Vanuatu",
        "VE" => "Venezuela",
        "VN" => "Vietnam",
        "WF" => "Wallis-et-Futuna",
        "YE" => "Yémen",
        "ZM" => "Zambie",
        "ZW" => "Zimbabwe"
    );
}

/**
 * @param $iso
 *
 * @return mixed|string
 */
function getPaysName($iso)
{
    $countries = listPays();
    if (array_key_exists($iso, $countries)) {
        return $countries[$iso];
    } else {
        return 'Pays inconnu';
    }
}

/**
 * @param $paysName
 *
 * @return mixed|string
 */
function getIso($paysName)
{
    $countries = listPays();
    if (in_array($paysName, $countries)) {
        return array_search($paysName, $countries);
    } else {
        return 'Pays inconnu';
    }
}

/**
 *
 */
function checkSession()
{
    if (!headers_sent() && session_status() === PHP_SESSION_DISABLED) {
        session_start();
    }
}

/**
 * set new token
 *
 * @param bool $forSession
 *
 * @return bool|string
 */
function setToken(bool $forSession = true): bool|string
{
    $token = '';
    $str = 'a0b1c2d3e4f5g6h7i8j9klmnpqrstuvwxy123456789';
    $strLength = strlen($str);

    for ($i = 0; $i < 70; $i++) {
        $token .= $str[random_int(0, $strLength - 1)];
    }

    if ($forSession) {
        checkSession();
        $_SESSION['_token'] = !bot_detected() ? $token : 'a1b2c3-d4e5f6';

        return true;
    }

    return $token;
}


/**
 * @return string
 */
function getTokenField()
{
    checkSession();
    if (!isset($_SESSION['_token'])) {
        setToken();
    }

    return '<input type="hidden" name="_token" value="' . getToken() . '">';
}

/**
 * @return mixed
 */
function getToken()
{
    if (isset($_SESSION['_token'])) {
        return $_SESSION['_token'];
    }

    return '';
}

/**
 * remove token session
 */
function unsetToken()
{
    if (isset($_SESSION['_token'])) {
        unset($_SESSION['_token']);
    }
}

/**
 * display notifications & alerts
 */
function getSessionNotifications()
{
    $sessionsNotifs = array(
        'notifications',
        'alert'
    );
    foreach ($sessionsNotifs as $sessionNotif) {
        if (!empty($_SESSION[$sessionNotif])) {
            foreach ($_SESSION[$sessionNotif] as $notif) {
                if (!empty($notif['alert'])) {
                    echo $notif['alert'];
                }
            }
        }
    }
}

/**
 * Include or Require file, once or more and safely
 *
 * @param $filePath
 * @param bool $requireMethod
 *
 * @param bool $once
 *
 * @return bool
 */
function inc($filePath, $requireMethod = false, $once = false)
{
    if (file_exists($filePath)) {
        if (!$requireMethod) {
            !$once ? include($filePath) : include_once($filePath);

            return true;
        }

        !$once ? require($filePath) : require_once($filePath);

        return true;
    }

    return false;
}

/**
 * @param $assetName
 * @param bool $getStream
 * @param null $params
 *
 * @return bool|false|string
 */
function getAsset($assetName, $getStream = false, $params = null)
{
    $fileDirname = 'assets/' . $assetName . '.php';
    $filePath = WEB_LIB_PATH . $fileDirname;

    if (file_exists($filePath)) {
        return $getStream ? getFileContent($filePath, $params) : include_once($filePath);
    }

    return false;
}

/**
 * Purge du cache Varnish en utilisant la requête HTTP PURGE/PURGEALL
 * @param null $url
 * @return bool
 */
function purgeVarnishCache($url = null)
{

    $return = false;
    $purge = !is_null($url) ? 'PURGE' : 'PURGEALL';
    $url = !is_null($url) ? $url : WEB_DIR_URL;
    if ($ch = curl_init($url)) {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $purge);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        if (curl_exec($ch)) {
            $return = true;
        }
        curl_close($ch);
    }

    return $return;
}

/**
 * @param $filename
 * @param int $desired_width
 * @param int $quality
 *
 * @param bool $webp
 * @return bool|int
 */
function thumb($filename, $desired_width = 100, $quality = 80, $webp = false)
{
    $src = FILE_DIR_PATH . $filename;
    $dest = FILE_DIR_PATH . 'thumb' . DIRECTORY_SEPARATOR . $desired_width . '_' . $filename;
    if ($webp) {
        $fileInfo = pathinfo($filename);
        $dest = FILE_DIR_PATH . 'webp' . DIRECTORY_SEPARATOR . $desired_width . '_' . $fileInfo['filename'] . '.WEBP';
    }

    if (!file_exists(FILE_DIR_PATH . 'thumb/')) {
        mkdir(FILE_DIR_PATH . 'thumb', 0705);
    }

    if (!file_exists(FILE_DIR_PATH . 'webp/')) {
        mkdir(FILE_DIR_PATH . 'webp', 0705);
    }

    if (is_file($src) && !is_file($dest) && isImage($src)) {

        list($src_width, $src_height, $src_type, $src_attr) = getimagesize($src);

        //check if thumb can be realized
        if ($desired_width < $src_width) {

            // Find format
            $ext = strtoupper(pathinfo($src, PATHINFO_EXTENSION));

            /* read the source image */
            if ($ext == "JPG" or $ext == "JPEG") {
                $source_image = @imagecreatefromjpeg($src);
            } elseif ($ext == "PNG") {
                $source_image = @imagecreatefrompng($src);
            } elseif ($ext == "GIF") {
                $source_image = @imagecreatefromgif($src);
            } elseif ($ext == "WEBP" && function_exists('imagecreatefromwebp')) {
                $source_image = @imagecreatefromwebp($src);
            } else {
                return false;
            }

            if (!$source_image) {
                return false;
            }

            $width = imagesx($source_image);
            $height = imagesy($source_image);


            /* find the "desired height" of this thumbnail, relative to the desired width  */
            $desired_height = floor($height * ($desired_width / $width));

            /* create a new, "virtual" image */
            $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

            /* saving alpha color */
            if ($ext == "PNG" || $ext == "WEBP") {
                imageAlphaBlending($virtual_image, false);
                imageSaveAlpha($virtual_image, true);
            }

            /* copy source image at a resized size */
            imagecopyresampled($virtual_image, $source_image, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

            if ($webp) {
                if (!function_exists('imagewebp')) {
                    return false;
                }
                imagewebp($virtual_image, $dest, $quality);

            } else {

                /* create the physical thumbnail image to its destination */
                if ($ext == "JPG" or $ext == "JPEG") {
                    imagejpeg($virtual_image, $dest, $quality);
                } elseif ($ext == "PNG") {
                    imagepng($virtual_image, $dest);
                } elseif ($ext == "GIF") {
                    imagegif($virtual_image, $dest);
                } elseif ($ext == "WEBP") {
                    if (!function_exists('imagewebp')) {
                        return false;
                    }
                    imagewebp($virtual_image, $dest, $quality);
                }
            }
            return true;
        }
    }
    return false;
}

/**
 * @param $filename
 * @param $desired_width
 * @param bool $webp
 * @param int $quality
 *
 * @return string
 */
function getThumb($filename, $desired_width, $webp = false, $quality = 100)
{
    if ($webp && !empty($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') !== false) {

        $basepath = FILE_DIR_PATH . 'webp' . DIRECTORY_SEPARATOR . $desired_width . '_';
        $baseurl = WEB_DIR_INCLUDE . 'webp' . DIRECTORY_SEPARATOR . $desired_width . '_';
        $filepath = $basepath . $filename;

        //Check if webp format exist
        if (is_file($filepath)) {
            return $baseurl . $filename;
        }

        $fileInfo = pathinfo($filename);
        $filepath = $basepath . $fileInfo['filename'] . '.WEBP';

        if (is_file($filepath)) {
            return $baseurl . $fileInfo['filename'] . '.WEBP';
        }

    } else {

        $webp = false;
        $basepath = FILE_DIR_PATH . 'thumb' . DIRECTORY_SEPARATOR . $desired_width . '_';
        $baseurl = WEB_DIR_INCLUDE . 'thumb' . DIRECTORY_SEPARATOR . $desired_width . '_';
        $filepath = $basepath . $filename;

        //Check if file exist
        if (is_file($filepath)) {
            return $baseurl . $filename;
        }
    }

    //Create thumb
    $thumb = thumb($filename, $desired_width, $quality, $webp);
    return $thumb ? getThumb($filename, $desired_width, $webp) : WEB_DIR_INCLUDE . $filename;
}

/**
 * @param $filename
 * @param $desired_width
 *
 * @return bool
 */
function deleteThumb($filename, $desired_width)
{
    $thumbPath = FILE_DIR_PATH . 'thumb' . DIRECTORY_SEPARATOR . $desired_width . '_' . $filename;
    if (file_exists($thumbPath)) {
        if (unlink($thumbPath)) {
            appLog('Delete thumb -> name: ' . $filename);
            return true;
        }
    }

    return false;
}

/**
 * @param $message
 * @param string $status
 * @param mixed $data
 */
function setPostResponse($message, $status = 'danger', $data = null)
{
    checkSession();

    $_SESSION['messagePostResponse'] = trans($message);
    $_SESSION['statusPostResponse'] = $status;
    $_SESSION['dataPostResponse'] = $data;
}

/**
 * show alert and destroy response
 *
 * @param string $additionalText
 */
function showPostResponse($additionalText = '')
{
    $html = '';
    if (!empty($_SESSION['messagePostResponse']) && !empty($_SESSION['statusPostResponse'])) {
        $html .= '<div class="row"><div class="col-12"><div class="alert alert-' . $_SESSION['statusPostResponse'] . '" role="alert">'
            . $_SESSION['messagePostResponse'] . ' ' . $additionalText . '</div></div></div>';

    }
    unset($_SESSION['messagePostResponse'], $_SESSION['statusPostResponse']);
    echo $html;
}

/**
 * @return mixed|string
 */
function getDataPostResponse()
{
    $dataPostResponse = '';
    if (!empty($_SESSION['dataPostResponse'])) {
        $dataPostResponse = $_SESSION['dataPostResponse'];
        unset($_SESSION['dataPostResponse']);
    }

    return $dataPostResponse;
}

/**
 * @param $error
 */
function setSqlError($error)
{
    checkSession();

    $_SESSION['sqlError'] = $error;
}

/**
 * @return mixed
 */
function getSqlError()
{
    if (isset($_SESSION['sqlError'])) {

        $error = $_SESSION['sqlError'];
        unset($_SESSION['sqlError']);

        return $error;
    }

    return false;
}

/**
 * Update DataBase
 * @throws Exception
 */
function updateDB(): bool
{

    $dbUpdateFile = WEB_SYSTEM_PATH . 'dbUpdate.json';

    if (file_exists($dbUpdateFile)) {

        //Get sql content
        $sqlToUpdate = getJsonContent($dbUpdateFile);

        if (is_array($sqlToUpdate)) {

            $updateError = false;
            appLog('Updating db...');

            foreach ($sqlToUpdate as $num => $sql) {

                //Send sql request
                $stmt = \App\DB::exec($sql);

                //If database return error
                if (!$stmt) {

                    //Writing error in applog.log
                    $error = getSqlError();
                    appLog('Database error: ' . $error[2]);

                    //Declare an error
                    $updateError[] = $error[2];
                }
            }

            //Check if error in sql
            if (is_array($updateError)) {

                //Send mail to admin
                $data = array(
                    'fromEmail' => 'system@pp-communication.fr',
                    'fromName' => 'APPOE System - ' . WEB_TITLE,
                    'toName' => 'Admin',
                    'toEmail' => 'esther@pp-communication.fr',
                    'object' => 'Erreur de mise à jour de la base de données',
                    'message' => '<p>Le site <strong>' . WEB_TITLE . '</strong> a rencontré un problème de mise à jour de la base de données.</p><p>' . implode('<br>', $updateError) . '</p>'
                );
                sendMail($data);

                //Delete dbUpdate file
                unlink($dbUpdateFile);

                //Show error screen
                return false;
            }
        }

        //Delete dbUpdate file
        unlink($dbUpdateFile);
    }

    return true;
}

/**
 * @param $url
 * @param $params
 * @param array $headers
 * @param array $options
 * @return mixed|string
 */
function postHttpRequest($url, $params, $headers = [], $options = [])
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_ENCODING, '');

    if (!isArrayEmpty($options)) {
        foreach ($options as $curlOpt => $curlVal) {
            curl_setopt($ch, $curlOpt, $curlVal);
        }
    }

    $response = curl_exec($ch);
    $errorRequest = curl_error($ch);

    curl_close($ch);

    return !empty($response) ? $response : $errorRequest;
}

/**
 * @param $url
 *
 * @param array $options
 * @return mixed|string
 */
function getHttpRequest($url, $options = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_TIMEOUT, 0);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

    if (!isArrayEmpty($options)) {
        foreach ($options as $curlOpt => $curlVal) {
            curl_setopt($ch, $curlOpt, $curlVal);
        }
    }

    $response = curl_exec($ch);
    $errorRequest = curl_error($ch);
    curl_close($ch);

    return !empty($response) ? $response : $errorRequest;
}

/**
 * @param $path
 * @param null $params
 *
 * @return string
 */
function getFileContent($path, $params = null)
{
    if (is_array($params)) {
        extract($params, EXTR_SKIP);
    }

    ob_start();
    inc($path);
    $pageContent = ob_get_clean();

    if (is_array($params) && preg_match_all("/{{(.*?)}}/", $pageContent, $match)) {

        foreach ($match[1] as $i => $zone) {

            $pageContent = str_replace($match[0][$i], sprintf('%s', !empty($params[$zone]) ? $params[$zone] : ''), $pageContent);
        }
    }

    return trim($pageContent);
}


/**
 * @param $object
 * @param $attribute
 *
 * @return bool|string
 */
function getXmlAttribute($object, $attribute)
{
    if (isset($object[$attribute])) {
        return (string)$object[$attribute];
    }

    return false;
}

/**
 * Place fields for verification and control of the login form
 *
 * @return string
 */
function getFieldsControls(): string
{
    $html = getTokenField();
    $html .= '<noscript><input type="hidden" name="secure-connection" value="..."></noscript>';
    $html .= '<input type="hidden" name="identifiant" value="">';
    $html .= '<input type="hidden" id="checkPass" name="checkPass" value="">';
    $html .= '<script type="text/javascript">';
    $html .= 'window.setTimeout(function(){';
    $html .= 'document.getElementById("checkPass").value = "APPOE";';
    $html .= '}, 300)</script>';

    return $html;
}

/**
 * @param int $parentId
 *
 * @return array|bool
 */
function getMediaCategories($parentId = null)
{

    $Category = new Category();
    $Category->setType('MEDIA');
    $allCategories = $Category->showByType($parentId);

    return $allCategories ? extractFromObjToSimpleArr($allCategories, 'id', 'name') : false;
}

/**
 * Deprecated
 *
 * @param int $id
 * @param bool $parentId
 * @param string $categoryType
 *
 * @return array
 */
function getSpecificMediaCategory($id, $parentId = false, $categoryType = 'MEDIA')
{
    $Media = new Media();
    $Category = new Category();

    $Category->setType($categoryType);
    $allCategories = $Category->showByType();

    $allMedia = [];
    foreach ($allCategories as $category) {

        if (false !== $parentId) {

            if ($category->parentId != $id && $category->id != $id) {
                continue;
            }

        } else {
            if ($category->id != $id) {
                continue;
            }
        }

        $Media->setTypeId($category->id);
        $allMedia[$category->id] = $Media->showFiles();
    }

    return $allMedia;
}

/**
 * @param int $id
 * @param bool $parentId
 * @param bool $flatten
 * @param int $arraySort
 * @param string $type
 *
 * @return array
 */
function getMediaByCategory($id, $parentId = false, $flatten = false, $arraySort = SORT_ASC, $type = 'MEDIA')
{
    $Media = new Media();
    $Media->setType($type);

    $Category = new Category();
    $Category->setType($type);
    $allCategories = $Category->showByType();

    $allMedia = [];
    foreach ($allCategories as $category) {

        $Media->setTypeId($category->id);

        if (false !== $parentId) {

            if ($category->parentId != $id && $category->id != $id) {
                continue;
            }

            $categoryMedia = $Media->showFiles();
            if ($categoryMedia) {
                $allMedia[$category->id] = $categoryMedia;
            }

        } else {

            if ($category->id != $id) {
                continue;
            }

            $categoryMedia = $Media->showFiles();
            if ($categoryMedia) {
                $allMedia = array_merge($allMedia, $categoryMedia);
            }
        }
    }

    return !$flatten ? $allMedia : array_sort(array_flatten($allMedia), 'position', $arraySort);
}

/**
 * @param $allCategory
 * @param $allLibrary
 *
 * @return array
 */
function groupLibraryByParents($allCategory, $allLibrary)
{

    $allLibraryByParent = extractFromObjToSimpleArr($allCategory, 'id', 'parentId');

    $libraryParent = [];
    foreach ($allLibraryByParent as $id => $parentId) {

        if ($parentId == 10) {
            $libraryParent[$id] = array('id' => $id, 'name' => $allLibrary[$id]);

        } else {

            if ($allLibraryByParent[$parentId] == 10) {
                $libraryParent[$id] = array('id' => $allLibraryByParent[$id], 'name' => $allLibrary[$parentId]);

            } else {
                $libraryParent[$id] = array(
                    'id' => $allLibraryByParent[$parentId],
                    'name' => $allLibrary[$allLibraryByParent[$parentId]]
                );

            }
        }
    }

    return $libraryParent;
}

/**
 * @param int|float $amount
 * @param bool $forDB
 * @param int $decimals
 * @param string $dec_point
 *
 * @return string
 */
function financial($amount, $forDB = false, $decimals = 2, $dec_point = '.')
{
    return is_numeric($amount) ? number_format($amount, $decimals, $dec_point, (!$forDB ? ' ' : '')) : $amount;
}


/**
 * @param $array
 * @param $key
 * @param $val
 * @param string $return
 * @return bool|object
 */
function isValInMultiArrObj($array, $key, $val, $return = 'bool')
{
    foreach ($array as $item) {

        if (is_array($item)) {
            return isValInMultiArrObj($item, $key, $val);
        }

        if (property_exists($item, $key) && $item->$key == $val) {
            return $return === 'bool' ? true : $item;
        }
    }
    return false;
}

/**
 * Raise an grandchildren array, at the level of children
 *
 * @param array|null $multipleArrays
 *
 * @return array
 */
function transformMultipleArraysTo1(array $multipleArrays = null)
{
    foreach ($multipleArrays as $ckey => $child) {
        foreach ($child as $gckey => $grandchild) {
            if (is_array($grandchild)) {
                $multipleArrays[] = $grandchild;
                array_splice($multipleArrays[$ckey], $gckey);
            }
        }
    }
    $length = count($multipleArrays);
    for ($i = 0; $i < $length; $i++) {
        if (!empty($multipleArrays[$i][0]) && is_array($multipleArrays[$i][0])) {
            array_splice($multipleArrays, $i, 1);
            $i = -1;
            $length = count($multipleArrays);
        }
    }

    return $multipleArrays;
}

/**
 * Raise an string children array, at the top level
 *
 * @param array| $array
 *
 * @return array
 */
function flatten(array $array)
{
    $return = [];
    array_walk_recursive($array, function ($a) use (&$return) {
        $return[] = $a;
    });

    return array_unique($return);
}

/**
 * Raise an objects children array, at the top level
 *
 * @param array| $array
 *
 * @return array
 */
function array_flatten(array $array)
{
    $result = [];
    foreach ($array as $key => $value) {

        if (is_array($value)) {

            $result = array_merge($result, array_flatten($value));

        } else {
            $result = array_merge($result, array($key => $value));
        }
    }

    return $result;
}

/**
 * @param $data
 * @param string $keyName
 *
 * @return array
 */
function groupMultipleKeysObjectsArray($data, $keyName)
{
    $output = [];
    if (!isArrayEmpty($data)) {

        $tmp = [];
        foreach ($data as $key => $arg) {

            if (isset($arg->$keyName)) {
                $tmp[$arg->$keyName][$key] = $arg;
            }
        }

        foreach ($tmp as $type => $labels) {
            $output[cleanData($type)] = $labels;
        }
    }

    return $output;
}

/**
 * @param array $data
 * @param string $keyName
 *
 * @return array
 */
function groupMultipleKeysArray(array $data, $keyName)
{
    $output = [];
    if (!isArrayEmpty($data)) {

        $tmp = [];
        foreach ($data as $key => $arg) {

            if (array_key_exists($keyName, $arg)) {
                $tmp[$arg[$keyName]][$key] = $arg;
            }
        }

        foreach ($tmp as $type => $labels) {
            $output[cleanData($type)] = $labels;
        }
    }

    return $output;
}

/**
 * @param $allContentArr
 * @param $key
 *
 * @return array
 */
function extractFromObjArr($allContentArr, $key)
{
    $allContent = [];
    if (!empty($allContentArr)) {
        foreach ($allContentArr as $contentArr) {
            $allContent[$contentArr->$key] = $contentArr;
        }
    }

    return $allContent;
}

/**
 * @param $allContentArr
 * @param $key
 *
 * @return array
 */
function extractFromObjToArrForList($allContentArr, $key)
{
    //extract object to array with key = id
    $newArray = extractFromObjArr($allContentArr, $key);

    //build tree id [parent id]
    $newArray = buildTree($newArray, 10);

    //order the list
    $ordonnedList = [];

    foreach ($newArray as $category) {
        $ordonnedList[$category->id] = $category->name;

        if (!empty($category->children)) {
            foreach ($category->children as $children) {
                $ordonnedList[$children->id] = '- ' . $children->name;

                if (!empty($children->children)) {

                    foreach ($children->children as $subChildren) {
                        $ordonnedList[$subChildren->id] = '-- ' . $subChildren->name;
                    }
                }
            }
        }
    }

    return $ordonnedList;
}

/**
 * @param $allContentArr
 * @param $key
 * @param string $value
 * @param string $value2
 * @param string $separator
 *
 * @return array
 */
function extractFromObjToSimpleArr($allContentArr, $key, $value = '', $value2 = '', $separator = ' ')
{
    $allContent = [];

    if (!isArrayEmpty($allContentArr)) {

        if (!empty($value)) {

            foreach ($allContentArr as $contentArr) {

                if (!empty($value2)) {
                    $allContent[$contentArr->$key] = $contentArr->$value . $separator . $contentArr->$value2;
                } else {
                    $allContent[$contentArr->$key] = $contentArr->$value;
                }
            }

        } else {

            foreach ($allContentArr as $contentArr) {
                $allContent[$contentArr->$key] = $contentArr->$key;
            }
        }
    }

    return $allContent;
}

/**
 * @param array $elements
 * @param int $parentId
 *
 * @return array
 */
function buildTree(array $elements, $parentId = 0)
{

    $branch = [];

    foreach ($elements as $element) {
        if ($element->parentId == $parentId) {
            $children = buildTree($elements, $element->id);

            if ($children) {
                $element->children = $children;
            }

            $branch[] = $element;
        }
    }

    return $branch;
}

/**
 * @param $dbname
 * @param string $groupBy
 * @param int $limit
 * @param string $column
 * @param string $order
 *
 * @return array|bool
 */
function getLastFromDb($dbname, $groupBy = '', $limit = 2, $column = 'updated_at', $order = 'DESC')
{

    $dbh = \App\DB::connect();
    if(\App\DB::checkTable(TABLEPREFIX . 'appoe_' . $dbname)) {
        $sql = 'SELECT * FROM ' . TABLEPREFIX . 'appoe_' . $dbname . ' ORDER BY ' . $column . ' ' . $order;
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $error = $stmt->errorInfo();

        if ($error[0] != '00000') {
            return false;
        }

        return
            empty($groupBy)
                ? $stmt->fetchAll(PDO::FETCH_OBJ)
                : array_slice(array_unique(extractFromObjToSimpleArr($stmt->fetchAll(PDO::FETCH_OBJ), $groupBy)), 0, $limit);
    }
    return false;
}

/**
 * @param $forApp
 */
function includePluginsFiles($forApp = false)
{
    $plugins = getPlugins();

    if (is_array($plugins) && !empty($plugins)) {

        foreach ($plugins as $plugin) {
            $filePath = WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR . 'include';
            if (file_exists($filePath) && !file_exists(WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR.'setup.php') && loadPluginForFilename($plugin['name'])) {
                foreach (getFilesFromDir($filePath, ['onlyExtension' => 'php']) as $file) {
                    $src = $filePath . DIRECTORY_SEPARATOR . $file;
                    include_once($src);
                }
            }
        }
    }

    if ($forApp) {
        includePluginsFilesForApp();
    }
}


/**
 *
 */
function includePluginsFilesForApp()
{
    $plugins = getPlugins();

    if (is_array($plugins) && !empty($plugins)) {

        foreach ($plugins as $plugin) {
            $filePath = WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR . 'includeApp';
            if (file_exists($filePath) && !file_exists(WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR.'setup.php')) {
                foreach (getFilesFromDir($filePath, ['onlyExtension' => 'php']) as $file) {
                    $src = $filePath . DIRECTORY_SEPARATOR . $file;
                    include_once($src);
                }
            }
        }
    }
}

/**
 *
 */
function includePluginsFilesForAppInFooter()
{
    $plugins = getPlugins();

    if (is_array($plugins) && !empty($plugins)) {

        foreach ($plugins as $plugin) {
            $filePath = WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR . 'includeAppFooter';
            if (file_exists($filePath) && !file_exists(WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR.'setup.php')) {
                foreach (getFilesFromDir($filePath, ['onlyExtension' => 'php']) as $file) {
                    $src = $filePath . DIRECTORY_SEPARATOR . $file;
                    include_once($src);
                }
            }
        }
    }
}


/**
 *
 */
function includePluginsPrimaryMenu()
{
    $plugins = getPlugins();

    if (is_array($plugins) && !empty($plugins)) {

        foreach ($plugins as $plugin) {
            $filePath = WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR . 'menu';
            if (file_exists($filePath) && !file_exists(WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR.'setup.php')) {
                $phpFiles = getFilesFromDir($filePath);
                foreach ($phpFiles as $file) {
                    $src = $filePath . DIRECTORY_SEPARATOR . $file;
                    include_once($src);
                }
            }
        }
    }
}


/**
 *
 */
function includePluginsSecondaryMenu()
{
    $plugins = getPlugins();

    if (is_array($plugins) && !empty($plugins)) {

        foreach ($plugins as $plugin) {
            $filePath = WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR . 'littleMenu';
            if (file_exists($filePath) && !file_exists(WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR.'setup.php')) {
                $phpFiles = getFilesFromDir($filePath);
                foreach ($phpFiles as $file) {
                    $src = $filePath . DIRECTORY_SEPARATOR . $file;
                    include_once($src);
                }
            }
        }
    }
}


/**
 * @param bool $forApp
 * @param bool $min
 */
function includePluginsJs($forApp = false, $min = false)
{
    if (!$min) {
        $plugins = getPlugins();

        if (is_array($plugins) && !empty($plugins)) {

            foreach ($plugins as $plugin) {

                if (loadPluginForFilename($plugin['name'])) {

                    $pluginPath = WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR;
                    $filePath = $pluginPath . 'js';
                    $setupPath = $pluginPath . 'setup.php';

                    if (is_dir($filePath) && !file_exists($setupPath)) {

                        foreach (getFilesFromDir($filePath, ['onlyExtension' => 'js']) as $file) {

                            //File path
                            $src = WEB_PLUGIN_URL . $plugin['name'] . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . $file;

                            //Show js file in html doc
                            echo '<script type="text/javascript" src="' . $src . '"></script>';

                        }
                    }
                }
            }
        }
    }
    echo '<script type="text/javascript" src="' . WEB_LIB_URL . 'js/functions.js"></script>';

    if ($forApp) {
        includePluginsJsForApp();
    }
}

/**
 *
 */
function includePluginsJsForApp()
{
    echo '<script type="text/javascript" src="' . WEB_TEMPLATE_URL . 'js/all.js"></script>';

    $plugins = getPlugins();
    if (is_array($plugins) && !empty($plugins)) {

        foreach ($plugins as $plugin) {

            $pluginPath = WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR;
            $filePath = $pluginPath . 'jsApp';
            $setupPath = $pluginPath . 'setup.php';

            if (is_dir($filePath) && !file_exists($setupPath)) {

                foreach (getFilesFromDir($filePath, ['onlyExtension' => 'js']) as $file) {

                    //File path
                    $src = WEB_PLUGIN_URL . $plugin['name'] . DIRECTORY_SEPARATOR . 'jsApp' . DIRECTORY_SEPARATOR . $file;

                    //Show js file in app doc
                    echo '<script type="text/javascript" src="' . $src . '"></script>';
                }
            }
        }
    }
}


/**
 * @param bool $min
 */
function includePluginsStyles($min = false)
{
    if (!$min) {
        $plugins = getPlugins();

        if (is_array($plugins) && !empty($plugins)) {

            foreach ($plugins as $plugin) {

                $pluginPath = WEB_PLUGIN_PATH . $plugin['name'] . DIRECTORY_SEPARATOR;
                $filePath = $pluginPath . 'css';
                $setupPath = $pluginPath . 'setup.php';

                if (is_dir($filePath) && !file_exists($setupPath)) {

                    foreach (getFilesFromDir($filePath, ['onlyExtension' => 'css']) as $file) {

                        //File path
                        $src = WEB_PLUGIN_URL . $plugin['name'] . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $file;

                        //Show css file in html doc
                        echo loadPluginForFilename($plugin['name']) || substr($file, -8, 4) == 'base' ?
                            '<link rel="stylesheet" href="' . $src . '" type="text/css">' : '';
                    }
                }
            }
        }
    }
    echo '<link rel="stylesheet" href="' . WEB_TEMPLATE_URL . 'css/utils.css" type="text/css">';
}

/**
 * @param $pluginName
 *
 * @return bool
 */
function loadPluginForFilename($pluginName)
{
    if (!isUserInApp()) {
        if (defined('PLUGIN_FOR_PUBLIC_FILENAME') && is_array(PLUGIN_FOR_PUBLIC_FILENAME)
            && array_key_exists($pluginName, PLUGIN_FOR_PUBLIC_FILENAME)) {

            if ((!isArrayEmpty(PLUGIN_FOR_PUBLIC_FILENAME[$pluginName]) && !in_array(getPageParam('currentPageFilename'), PLUGIN_FOR_PUBLIC_FILENAME[$pluginName]))
                || false === PLUGIN_FOR_PUBLIC_FILENAME[$pluginName]) {
                return false;
            }

            return true;
        }
    } else {

        $pluginForAppFilename = INI_LOAD_PLUGIN_FOR_APP_FILENAME;

        if (defined('PLUGIN_FOR_APP_FILENAME') && is_array(PLUGIN_FOR_APP_FILENAME)) {
            $pluginForAppFilename = array_merge($pluginForAppFilename, PLUGIN_FOR_APP_FILENAME);
        }

        if (array_key_exists($pluginName, $pluginForAppFilename)) {

            if ((!isArrayEmpty($pluginForAppFilename[$pluginName]) && !in_array(getAppPageSlug(), $pluginForAppFilename[$pluginName]))
                || false === $pluginForAppFilename[$pluginName]) {
                return false;
            }

            return true;
        }
    }

    return true;
}

/**
 * @param array $headers
 * @param array $data
 * @param string $filename
 * @param string $delimiter
 *
 * @return bool
 */
function exportCsv(array $headers, array $data, $filename = 'data', $delimiter = ',')
{
    if (!is_array($headers) || !is_array($data)) {
        return false;
    }

    if (!headers_sent()) {
        header('Content-Encoding: UTF-8');
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        header('Cache-Control: max-age=0');
    }

    $output = fopen("php://output", "w");
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
    fputcsv($output, $headers, $delimiter);

    foreach ($data as $item) {
        fputcsv($output, $item, $delimiter);
    }

    fclose($output);

    return true;
}

/**
 * @return bool
 */
function valideToken()
{
    if (!empty($_REQUEST['_token']) && !empty($_SESSION['_token']) && $_REQUEST['_token'] == $_SESSION['_token']) {

        unsetToken();

        return true;
    }

    return false;
}

/**
 * @return bool
 */
function valideAjaxToken()
{
    if (!empty($_REQUEST['_token']) && !empty($_SESSION['_token']) && $_REQUEST['_token'] == $_SESSION['_token']) {

        return true;
    }

    return false;
}

/**
 * @param $updateUserStatus
 *
 * @return bool
 */
function checkPostAndTokenRequest($updateUserStatus = true)
{
    if ($_SERVER["REQUEST_METHOD"] == "POST"
        && !empty($_POST['_token']) && !empty($_SESSION['_token'])
        && $_POST['_token'] == $_SESSION['_token']) {

        unsetToken();

        if ($updateUserStatus) {
            if (function_exists('mehoubarim_connecteUser')) {
                mehoubarim_connecteUser();
            }
        }

        return true;
    }

    return false;
}

/**
 * @return bool
 */
function checkAjaxRequest()
{
    if (
        !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    ) {
        return true;
    }

    return false;
}

/**
 * Check if user are connected & return user id
 *
 * @return bool
 */
function getUserIdSession()
{
    $userConnexion = getUserConnexion();

    return $userConnexion ? $userConnexion['idUserConnexion'] : false;
}

/**
 * @param $slug
 *
 * @return bool
 */
function isUserAuthorized($slug)
{

    $Menu = new \App\Menu();

    return $Menu->checkUserPermission(getUserRoleId(), $slug);
}

/**
 * @param string $idUser
 *
 * @return bool|string
 */
function checkAndGetUserId($idUser = null)
{
    return !is_null($idUser) ? $idUser : getUserIdSession();
}

/**
 * @param bool $max : get the lower roles than the current user
 * @param bool $min : get the superior roles than the current user
 * @param null $roleIdReference : to compare with role IDs
 *
 * @return array|bool
 */
function getAllUsers($max = false, $min = false, $roleIdReference = null)
{
    //Get all users in a const
    if (!defined('ALLUSERS')) {
        $USER = new Users();
        $USER->setStatut(0);
        define('ALLUSERS', serialize(extractFromObjArr($USER->showAll(), 'id')));
    }

    $allUsers = unserialize(ALLUSERS);

    if (is_array($allUsers)) {

        if ($max || $min) {

            $reference = is_numeric($roleIdReference) ? $roleIdReference : getUserRoleId();
            foreach ($allUsers as $idUser => $user) {

                if (($max && $reference < getUserRoleId($idUser)) || ($min && $reference > getUserRoleId($idUser))) {
                    unset($allUsers[$idUser]);
                }
            }
        }

        return $allUsers;
    }

    return false;
}

/**
 * @param $idUser
 *
 * @return bool
 */
function isUserExist($idUser)
{

    $ALLUSERS = getAllUsers();

    if (is_array($ALLUSERS) && is_numeric($idUser)) {
        return array_key_exists($idUser, $ALLUSERS);
    }

    return false;
}

/**
 * @param $idUser
 *
 * @return string|array
 */
function getUserData($idUser = null)
{
    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? $ALLUSERS[$idUser] : '';
}

/**
 * @param $idUser
 *
 * @return string
 */
function getUserName($idUser = null)
{
    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? $ALLUSERS[$idUser]->nom : '';
}

/**
 * @param $idUser
 *
 * @return string
 */
function getUserFirstName($idUser = null)
{
    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? $ALLUSERS[$idUser]->prenom : '';
}

/**
 * @param $idUser
 * @param string $separator
 *
 * @return string
 */
function getUserEntitled($idUser = null, $separator = ' ')
{
    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? $ALLUSERS[$idUser]->nom . $separator . $ALLUSERS[$idUser]->prenom : '';
}

/**
 * @param $idUser
 *
 * @return array|string
 */
function getUserLogin($idUser = null)
{
    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? $ALLUSERS[$idUser]->login : '';
}

/**
 * @param $idUser
 *
 * @return array|string
 */
function getUserEmail($idUser = null)
{
    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? $ALLUSERS[$idUser]->email : '';
}

/**
 * @param $idUser
 *
 * @return array|bool
 */
function getUserStatus($idUser = null)
{
    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? $ALLUSERS[$idUser]->statut : false;
}

/**
 * @param string $idUser
 *
 * @return string
 */
function getUserRoleId($idUser = null)
{

    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? getRoleId($ALLUSERS[$idUser]->role) : false;
}

/**
 * @param string $idUser
 *
 * @return mixed
 */
function getUserRoleName($idUser = null)
{

    $idUser = checkAndGetUserId($idUser);

    $ALLUSERS = getAllUsers();

    return isUserExist($idUser) ? getRoleName($ALLUSERS[$idUser]->role) : '';
}

/**
 * @return array
 */
function getAdminRoles()
{
    return array(11 => 'Technicien', 12 => 'King');
}

/**
 * @return array
 */
function getRoles()
{
    $usersRoles = getAdminRoles();
    if (defined('ROLES')) {

        $usersRoles = $usersRoles + ROLES;
        ksort($usersRoles);

        return $usersRoles;
    }

    return $usersRoles;
}

/**
 * @param $roleId
 *
 * @return mixed
 */
function getRoleName($roleId)
{
    if (defined('ROLES')) {
        $roleId = getRoleId($roleId);

        return getRoles()[$roleId];
    }

    return $roleId;
}

/**
 * @param $cryptedRole
 *
 * @return string
 */
function getRoleId($cryptedRole)
{
    return strlen($cryptedRole) < 3 ? $cryptedRole : \App\Shinoui::Decrypter($cryptedRole);
}


/**
 * @return false|int|string
 */
function getTechnicienRoleId()
{
    return array_search('Technicien', getRoles());
}

/**
 * @param $roleId
 *
 * @return bool
 */
function isTechnicien($roleId)
{

    $userRole = getRoleId($roleId);
    if ($userRole >= 11) {
        return true;
    }

    return false;
}

/**
 * @param $roleId
 *
 * @return bool
 */
function isKing($roleId)
{

    $userRole = getRoleId($roleId);
    if ($userRole == 12) {
        return true;
    }

    return false;
}

/**
 * @param bool $destroyAndRedirect
 * Unset User Session & Cookie
 */
function disconnectUser($destroyAndRedirect = true)
{
    if (function_exists('mehoubarim_freeUser')) {
        mehoubarim_freeUser(getUserIdSession());
    }

    //Delete auth sessions
    if (isset($_SESSION['auth' . slugify($_SERVER['HTTP_HOST'])])) {
        unset($_SESSION['auth' . slugify($_SERVER['HTTP_HOST'])]);
    }

    //Delete auth cookie
    if (isset($_COOKIE['hibour' . slugify($_SERVER['HTTP_HOST'])])) {
        $options = array('expires' => -3600, 'path' => '/', 'secure' => false, 'httponly' => true, 'samesite' => 'Strict');
        setcookie('hibour' . slugify($_SERVER['HTTP_HOST']), '', $options);
        unset($_COOKIE['hibour' . slugify($_SERVER['HTTP_HOST'])]);
    }

    if (true === $destroyAndRedirect) {

        session_unset();
        session_destroy();

        if (!headers_sent()) {
            header('location:' . WEB_DIR . 'hibour');
        }
        exit();
    }
}

/**
 * @return bool|string
 */
function getUserSession()
{
    if (!empty($_SESSION['auth' . slugify($_SERVER['HTTP_HOST'])])) {
        return \App\Shinoui::Decrypter($_SESSION['auth' . slugify($_SERVER['HTTP_HOST'])]);
    }

    return false;
}

/**
 * @return bool|string
 */
function getUserCookie()
{
    if (!empty($_COOKIE['hibour' . slugify($_SERVER['HTTP_HOST'])])) {
        return \App\Shinoui::Decrypter($_COOKIE['hibour' . slugify($_SERVER['HTTP_HOST'])]);
    }

    return false;
}

/**
 * Set user Session
 */
function setUserSession()
{
    checkSession();

    $_SESSION['auth' . slugify($_SERVER['HTTP_HOST'])] = $_COOKIE['hibour' . slugify($_SERVER['HTTP_HOST'])];
}

/**
 * @return bool
 */
function isUserSessionExist()
{
    return isset($_SESSION['auth' . slugify($_SERVER['HTTP_HOST'])]);
}

/**
 * @return bool
 */
function isUserCookieExist()
{
    return isset($_COOKIE['hibour' . slugify($_SERVER['HTTP_HOST'])]);
}

/**
 * @return array|bool
 */
function getUserConnexion()
{

    $checkStr = '!a6fgcb!f152ddb3!';
    $pos = false;

    if (isUserSessionExist()) {

        $pos = strpos(getUserSession(), $checkStr);
        list($idUserConnexion, $loginUserConnexion) = explode($checkStr, getUserSession());

    } elseif (isUserCookieExist()) {

        $pos = strpos(getUserCookie(), $checkStr);
        setUserSession();
        list($idUserConnexion, $loginUserConnexion) = explode($checkStr, getUserSession());
    }

    return $pos !== false ? array(
        'idUserConnexion' => $idUserConnexion,
        'loginUserConnexion' => $loginUserConnexion
    ) : false;
}

/**
 * Check if user is in App directory
 * @return bool
 */
function isUserInApp()
{
    $url_parts = explode('/', $_SERVER['PHP_SELF']);

    return in_array('app', $url_parts);
}

/**
 * get real file path
 *
 * @param $chemin
 * @param array $ext
 *
 * @return array|bool|mixed
 */
function getPathFiles($chemin, $ext = [])
{
    if (!empty($chemin) && is_array($ext)) {
        $files = glob($chemin . '*.{' . implode(',', $ext) . '}', GLOB_BRACE);

        return array_map('realpath', $files);
    } else {
        return false;
    }
}

/**
 * Try to detect bots and subdomain
 * @return bool
 */
function bot_detected()
{
    return (
        (isset($_GET['access_method'])) || (isset($_SERVER['HTTP_USER_AGENT'])
            && preg_match('/bot|crawl|curl|dataprovider|search|get|spider|find|java|majesticsEO|google|yahoo|contaxe|libwww-perl|mediapartners|baidu|bingbot|facebookexternalhit|googlebot|-google|ia_archiver|msnbot|naverbot|pingdom|seznambot|slurp|teoma|twitter|yandex|yeti/i', $_SERVER['HTTP_USER_AGENT']))
    );
}

/**
 * @param null $userRole
 *
 * @return bool
 */
function appoeMinRole($userRole = null)
{
    return APPOE_MIN_ROLE <= (is_null($userRole) ? getUserRoleId() : $userRole);
}

/**
 * @param $url
 *
 * @return bool
 */
function url_exists($url)
{
    return (!$fp = curl_init($url)) ? false : true;
}

/**
 * @param $tel
 *
 * @return bool
 */
function isTel($tel)
{
    $cleanTel = str_replace("-", "", filter_var($tel, FILTER_SANITIZE_NUMBER_INT));

    return strlen($cleanTel) >= 10 && strlen($cleanTel) < 15;
}

/**
 * @param $email
 *
 * @return bool
 */
function isEmail($email)
{
    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailParts = explode("@", $email);

        return checkdnsrr(array_pop($emailParts), "MX");
    }

    return false;
}

/**
 * @param $url
 *
 * @return bool
 */
function isUrl($url)
{
    return filter_var($url, FILTER_VALIDATE_URL);
}

/**
 * @param $ip
 *
 * @return mixed
 */
function isIp($ip)
{
    return !empty($ip) && strlen($ip) <= 45 && (filter_var($ip, FILTER_VALIDATE_IP)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)
            || filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6));
}

/**
 * @return string
 */
function patternTel()
{
    return ' pattern="[0-9-+ ]+" ';
}

/**
 * @return string
 */
function patternMail()
{
    return ' pattern="[^@\s]+@[^@\s]+\.[^@\s]+" ';
}

/**
 * @return string
 */
function patternUrl()
{
    return ' pattern="https?://.+" ';
}

/**
 * get real web file url
 *
 * @param $file
 * @param null $param
 *
 * @return string
 */
function webUrl($file, $param = null)
{
    $url = '';
    if (!is_null($param)) {
        $url .= $param;
    }

    if (false !== strpos($file, '#') && substr($file, -1) == '/') {
        $file = substr($file, 0, -1);

        if ($file === '#') {
            return $file;
        }
    }

    if (substr($file, 0, 4) === "http") {
        return $file;
    }

    if (false === strpos($file, '#') && false === strpos($file, '?') && substr($file, -1) != '/') {
        $file .= '/';
    }

    return WEB_DIR_URL . $file . $url;
}

/**
 * get real web url file by filename
 *
 * @param $filename
 * @param null $param
 *
 * @return string
 */
function url($filename, $param = null)
{
    if (!empty($filename)) {
        if ($Cms = getPageByFilename($filename)) {
            return WEB_DIR_URL . $Cms->getSlug() . DIRECTORY_SEPARATOR . (!is_null($param) ? $param : '');
        }
    }
    return '';
}

/**
 * @param $link
 *
 * @return string
 */
function externalLink($link)
{
    if (!empty($link) && substr($link, 0, 4) === "http") {

        $linkData = parse_url($link);

        if ($_SERVER['SERVER_NAME'] != $linkData['host']) {
            return ' target="_blank" ';
        }
    }

    return '';
}

/**
 * @param mixed $menu
 * @param array $params
 *
 * @return string
 */
function linkBuild($menu, $params = [])
{
    if (is_object($menu)) {

        $defaultParams = [
            'linkParams' => [],
            'class' => '',
            'activePage' => 'active',
            'parent' => false
        ];

        $slug = $menu->slug;
        $title = 'title="' . $menu->name . '"';

        $params = array_merge($defaultParams, $params);

        //External link
        if (substr($slug, 0, 4) === "http") {

            $target = '';
            $linkData = parse_url($slug);

            if ($_SERVER['SERVER_NAME'] != $linkData['host'] || false !== strpos($linkData['path'], '.')) {
                $target = 'target="_blank"';
            }

            return '<a href="' . $slug . '" class="' . $params['class'] . '" ' . $target . ' ' . $title . '>' . trad($menu->name) . '</a>';
        }

        //Anchor
        if ($slug === '#' || $params['parent'] == true) {
            return '<a href="#" onclick="return false;" class="' . $params['class'] . '" ' . $title . '>' . trad($menu->name) . '</a>';
        }

        $slug .= '/';

        //Parameters
        if (!isArrayEmpty($params['linkParams'])) {

            if (count($params['linkParams']) == 1) {
                $slug .= $params['linkParams'][0];
            } else {
                $slug .= implode('/', $params['linkParams']);
            }

            $slug .= '/';
        }

        return '<a href="' . WEB_DIR_URL . $slug . '" ' . $title . ' class="' . $params['class'] . ' ' . activePage($menu->slug, $params['activePage']) . '">' . $menu->name . '</a>';
    }

    return $menu;
}

/**
 * get real admin file url
 *
 * @param $file
 * @param null $param
 *
 * @return string
 */
function getUrl($file, $param = null)
{
    $url = '';
    if (!is_null($param)) {
        $url .= $param . '/';
    }

    return WEB_ADMIN_URL . $file . $url;
}

/**
 * @param $file
 * @param null $param
 *
 * @return string
 */
function getPluginUrl($file, $param = null)
{
    $url = '';
    if (!is_null($param)) {
        $url .= $param . '/';
    }

    return WEB_PLUGIN_URL . $file . $url;
}

/**
 * @param $type
 *
 * @return bool|string
 */
function getPageTypes($type)
{
    if (in_array(mb_strtolower($type), array_keys(PAGE_TYPES))) {
        return PAGE_TYPES[mb_strtolower($type)];
    }

    return false;
}

/**
 * @param $path
 *
 * @return mixed
 */
function getFileName($path)
{
    $pathInfos = pathinfo($path);

    return $pathInfos['filename'];
}

/**
 * @param $fileOptions
 * @param string $key
 *
 * @return array|mixed
 */
function getSerializedOptions($fileOptions, $key = '')
{
    $arrayOptions = [];
    if (!empty($fileOptions)) {

        $arrayOptions = @unserialize($fileOptions);

        if ($arrayOptions && !isArrayEmpty($arrayOptions)) {

            if (!empty($key) && array_key_exists($key, $arrayOptions)) {
                return $arrayOptions[$key];
            }
        }
    }

    return $arrayOptions;
}

/**
 * @param $filesArray
 * @param $position
 * @param int|bool $forcedPosition
 *
 * @return array
 */
function getFileTemplatePosition($filesArray, $position, $forcedPosition = false)
{

    $newFilesArray = [];
    if ($filesArray && !isArrayEmpty($filesArray)) {
        foreach ($filesArray as $key => $file) {
            if (is_object($file) && $position == getSerializedOptions($file->options, 'templatePosition')) {
                array_push($newFilesArray, $file);
            }
        }

        if (isArrayEmpty($newFilesArray)) {

            if (true === $forcedPosition) {
                $newFilesArray = $filesArray;
            }

            if ($forcedPosition > 0) {
                $forcedFilesArray = getFileTemplatePosition($filesArray, $forcedPosition);
                $newFilesArray = !isArrayEmpty($forcedFilesArray) ? $forcedFilesArray : $filesArray;
            }
        }
    }

    return $newFilesArray;
}

/**
 * @param array $imageArray
 * @param string $otherClass
 * @param bool $thumbSize
 * @param bool $onlyUrl
 * @param bool $onlyPath
 *
 * @param bool $webp
 * @return bool|string
 */
function getFirstImage(array $imageArray, $otherClass = '', $thumbSize = false, $onlyUrl = false, $onlyPath = false, $webp = false)
{
    if ($imageArray) {
        $firstImage = current($imageArray);
        if (isImage(FILE_DIR_PATH . $firstImage->name)) {
            if ($onlyUrl) {
                return !$thumbSize ? WEB_DIR_INCLUDE . $firstImage->name : getThumb($firstImage->name, $thumbSize, $webp);
            } else if ($onlyPath) {
                return FILE_DIR_PATH . $firstImage->name;
            } else {
                return '<img src="' . (!$thumbSize ? WEB_DIR_INCLUDE . $firstImage->name : getThumb($firstImage->name, $thumbSize, $webp))
                    . '" alt="' . $firstImage->title . '" data-originsrc="' . WEB_DIR_INCLUDE . $firstImage->name . '" class="' . $otherClass . '">';
            }
        } else {
            return getFirstImage(array_slice($imageArray, 1), $otherClass, $thumbSize);
        }
    }

    return false;
}

/**
 * @param $videoArray
 * @param $otherClass
 * @param $otherAttr
 * @param $onlyUrl
 * @param $onlyPath
 *
 * @return bool|string
 */
function getFirstVideo(array $videoArray, $otherClass = '', $otherAttr = '', $onlyUrl = false, $onlyPath = false)
{
    if ($videoArray) {
        $firstVideo = current($videoArray);
        if (isVideo(FILE_DIR_PATH . $firstVideo->name)) {
            if ($onlyUrl) {
                return WEB_DIR_INCLUDE . $firstVideo->name;
            } else if ($onlyPath) {
                return FILE_DIR_PATH . $firstVideo->name;
            } else {
                return '<video src="' . WEB_DIR_INCLUDE . $firstVideo->name . '" class="' . $otherClass . '" ' . $otherAttr . '></video>';
            }
        } else {
            return getFirstVideo(array_slice($videoArray, 1), $otherClass);
        }
    }

    return false;
}

/**
 * @param $imageArray
 *
 * @return bool|string
 */
function getLastImage($imageArray)
{
    if ($imageArray) {
        $lastImage = end($imageArray);
        if (isImage(FILE_DIR_PATH . $lastImage->name)) {
            return '<img src="' . WEB_DIR_INCLUDE . $lastImage->name . '"
                                 alt="' . $lastImage->title . '">';
        }
    }

    return false;
}

/**
 * @param $imageArray
 *
 * @return bool|string
 */
function getLittleImage($imageArray)
{
    if ($imageArray) {

        $littleImage = current($imageArray);
        $littleImageSize = getimagesize(FILE_DIR_PATH . $littleImage->name);

        foreach ($imageArray as $key => $img) {
            if (isImage(FILE_DIR_PATH . $img->name)) {

                $imageSize = getimagesize(FILE_DIR_PATH . $img->name);

                if ($imageSize[0] < $littleImageSize[0] && $imageSize[1] < $littleImageSize[1]) {
                    $littleImage = $img;
                    $littleImageSize = $imageSize;
                } else {
                    $proportionW = $imageSize[0] - $littleImageSize[0];
                    $proportionY = $imageSize[1] - $littleImageSize[1];
                    $proportion = $proportionW + $proportionY;

                    if ($proportion < $littleImageSize[0] && $proportion < $littleImageSize[1]) {
                        $littleImage = $img;
                        $littleImageSize = $imageSize;
                    }
                }
            }
        }

        return '<img src="' . WEB_DIR_INCLUDE . $littleImage->name . '"
                                 alt="' . $littleImage->title . '">';
    }

    return false;
}

/**
 * @param $imageArray
 *
 * @return array
 */
function getOnlyImages($imageArray)
{
    $imagesFiltredArray = [];
    if ($imageArray) {

        foreach ($imageArray as $image) {
            if (isImage(FILE_DIR_PATH . $image->name)) {
                array_push($imagesFiltredArray, WEB_DIR_INCLUDE . $image->name);
            }
        }
    }

    return $imagesFiltredArray;
}

/**
 * @param stdClass $media
 * @param string $class
 * @param string $attr
 * @param bool $thumbSize
 *
 * @param int $quality
 * @param bool $webp
 * @param bool $onlyUrl
 * @return string
 */
function showImage(stdClass $media, $class = '', $attr = '', $thumbSize = false, $quality = 80, $webp = false, $onlyUrl = false)
{
    if (property_exists($media, 'name') && property_exists($media, 'title')) {

        $url = imgUrl($media, $thumbSize, $quality, $webp);

        if ($onlyUrl) return $url;

        return '<img src="' . $url . '" alt="' . $media->title . '" class="' . $class . '" ' . $attr . '>';
    }

    return '';
}

/**
 * @param stdClass $media
 * @param bool $thumbSize
 *
 * @param int $quality
 * @param bool $webp
 * @return string|null
 */
function imgUrl(stdClass $media, $thumbSize = false, $quality = 80, $webp = false)
{
    if (property_exists($media, 'name')) {
        return !$thumbSize ? WEB_DIR_INCLUDE . $media->name : getThumb($media->name, $thumbSize, $webp, $quality);
    }

    return null;
}

/**
 * @param $path
 *
 * @return mixed
 */
function getFileExtension($path)
{
    $pathInfos = pathinfo($path);

    return isset($pathInfos['extension']) ? $pathInfos['extension'] : false;
}

/**
 * @param $extension
 *
 * @return string
 */
function getImgAccordingExtension($extension)
{

    $src = WEB_TEMPLATE_URL . 'images/';
    switch (strtolower($extension)) {
        case 'jpg':
        case 'jpeg':
        case 'gif':
        case 'png':
        case 'svg':
        case 'webp':
            return 'img';
        case 'pdf':
            return $src . 'Pdf.png';
        case 'doc':
        case 'docx':
            return $src . 'Word.png';
        case 'xls':
        case 'xlsx':
            return $src . 'Excel.png';
        case 'ppt':
        case 'pptx':
            return $src . 'PowerPoint.png';
        case 'mp3':
        case 'wma':
        case 'wov':
            return $src . 'Music.png';
        case 'mp4':
        case 'webm':
        case 'ogg':
        case 'ogv':
            return $src . 'Videos.png';
        default:
            return $src . 'AllFileType.png';
    }
}

/**
 * @param $msg
 *
 * @return string
 */
function getContainerErrorMsg($msg)
{
    return '<div class="container-fluid"><div class="row"><div class="col-12"><p>' . $msg . '</p></div></div></div>';
}

/**
 *
 */
function setPays()
{
    if (empty($_SESSION['pays'])) {
        $json = file_get_contents('https://geobytes.com/GetCityDetails?fqcn=' . getIP());
        $json = json_decode($json);
        $_SESSION['pays'] = $json->geobytesinternet;
    }
}

/**
 * @param $numTel
 *
 * @return string
 */
function FormatTel($numTel)
{
    $i = 0;
    $j = 0;
    $format = "";
    while ($i < strlen($numTel)) {
        if ($j < 2) {
            if (preg_match('/^[0-9]$/', $numTel[$i])) {
                $format .= $numTel[$i];
                $j++;
            }
            $i++;
        } else {
            $format .= " ";
            $j = 0;
        }
    }

    return $format;
}

/**
 * @param string $str
 * @param string $charset
 *
 * @return string|null
 */
function removeAccents(string $str, string $charset = 'UTF-8'): string|null
{
    $str = htmlentities($str, ENT_NOQUOTES, $charset);

    $str = preg_replace('/&([a-zA-Z])[a-z]+;/', '$1', $str);

    $str = preg_replace('/&([a-zA-Z]{2})lig;/', '$1', $str);

    return preg_replace('/&[^;]+;/', '', $str);
}


/**
 * @param $start
 * @param null $end
 *
 * @return string
 * @throws DateMalformedStringException
 */
function formatDateDiff($start, $end = null): string
{
    if (!($start instanceof DateTime)) {
        $start = new DateTime($start);
    }

    if ($end === null) {
        $end = new DateTime();
    }

    if (!($end instanceof DateTime)) {
        $end = new DateTime($start);
    }

    $interval = $start->diff($end);

    if ($start == $end) {
        return "Aujourd'hui";
    }
    $doPlural = function ($nb, $str) {
        return $nb > 1 && $str != 'mois' ? $str . 's' : $str;
    };

    $format = [];
    if ($interval->y !== 0) {
        $format[] = "%y " . $doPlural($interval->y, "année");
    }
    if ($interval->m !== 0) {
        $format[] = "%m " . $doPlural($interval->m, "mois");
    }
    if ($interval->d !== 0) {
        $format[] = "%d " . $doPlural($interval->d, "jour");
    }
    if ($interval->h !== 0) {
        $format[] = "%h " . $doPlural($interval->h, "heure");
    }
    if ($interval->i !== 0) {
        $format[] = "%i " . $doPlural($interval->i, "minute");
    }
    if ($interval->s !== 0) {
        if (!count($format)) {
            return "moins d\'une minute";
        } else {
            $format[] = "%s " . $doPlural($interval->s, "seconde");
        }
    }
    if (!empty($interval->format('%r'))) {
        $time = 'il y a ';
    } else {
        $time = 'dans ';
    }

    if (count($format) > 1) {
        $format = array_shift($format) . " et " . array_shift($format);
    } else {
        $format = array_pop($format);
    }

    return $time . $interval->format($format);
}

/**
 * @param $nb
 *
 * @return string
 */
function formatBillingNB($nb)
{
    switch ($nb) {
        case $nb < 10:
            $nb = '0000' . $nb;
            break;
        case $nb < 100:
            $nb = '000' . $nb;
            break;
        case $nb < 1000:
            $nb = '00' . $nb;
            break;
        case $nb < 10000:
            $nb = '0' . $nb;
            break;
        default:
            break;
    }

    return $nb;
}

/**
 * Doc on https://www.kernel.org/doc/Documentation/filesystems/proc.txt (section 1.8)
 * Return CPU of :
 * user - normal processes executing in user mode
 * nice - niced processes executing in user mode
 * sys - processes executing in kernel mode
 * idle - twiddling thumbs
 * @return array
 */
function getServerPerformances()
{
    $cpu = [];
    $dif = [];

    $stat1 = file('/proc/stat');
    sleep(1);
    $stat2 = file('/proc/stat');

    $info1 = explode(" ", preg_replace("!cpu +!", "", $stat1[0]));
    $info2 = explode(" ", preg_replace("!cpu +!", "", $stat2[0]));

    $dif['user'] = $info2[0] - $info1[0];
    $dif['nice'] = $info2[1] - $info1[1];
    $dif['sys'] = $info2[2] - $info1[2];
    $dif['idle'] = $info2[3] - $info1[3];

    $total = array_sum($dif);

    foreach ($dif as $x => $y) {
        $cpu[$x] = str_replace(',', '.', round($y / $total * 100, 1));
    }

    return $cpu;
}

/**
 * @param $cpu
 *
 * @return string
 */
function getServerPerformanceColor($cpu)
{

    $color = 'success';

    if ($cpu > 50) {
        $color = 'warning';
    } elseif ($cpu > 80) {
        $color = 'danger';
    }

    return $color;
}

/**
 * @param array $data
 * @param array $otherAddr
 * @param array $options
 * @return bool
 * @throws Exception
 */
function emailVerification(array $data, array $otherAddr = [], array $options = [])
{
    //Encrypt key for email
    $key = setToken(false);
    $keyToConfirm = base64_encode(password_hash($key, PASSWORD_DEFAULT));

    //Mail options
    $data = array_merge(array(
        'fromEmail' => 'noreply@' . $_SERVER['HTTP_HOST'],
        'fromName' => WEB_TITLE,
        'toName' => '',
        'object' => WEB_TITLE . ' - Authentification requise',
        'message' => '<p>Cet email vous a été envoyé suite à une demande d\'inscription à la newsletter.<br>Vous avez 2h pour confirmer votre adresse email</p>',
        'params' => [],
        'confirmationPageSlug' => 'confirmation-email',
        'confirmationBtnText' => 'Confirmer mon adresse email'
    ), $data);

    $urlParams = !empty($data['params']) ? '&' . http_build_query($data['params']) : '';

    //Confirm mail button
    $url = webUrl($data['confirmationPageSlug']) . '?email=' . base64_encode($data['toEmail']) . '&key=' . $keyToConfirm . $urlParams;
    $data['message'] .= '<p style="text-align:center;"><a class="btn" href="' . $url . '" title="' . $data['confirmationBtnText'] . '">' . $data['confirmationBtnText'] . '</a></p>';

    //Saving key and email in db
    $Option = new Option();
    $Option->setType('CONFIRMATIONMAIL');
    $Option->setKey($data['toEmail']);
    $Option->setVal($key);
    if ($Option->save()) {

        //Sanding confirmation mail
        if (sendMail($data, $otherAddr, $options)) {
            return true;
        }
    }
    return false;
}

/**
 * @param $get
 * @param int $timeLimit in hours
 * @return bool|void|array
 */
function approveEmail($get, $timeLimit = 5)
{
    if (!empty($get['email']) && !empty($get['key'])) {

        //Clean data
        $email = cleanData(base64_decode($get['email']));
        $key = cleanData(base64_decode($get['key']));

        //Check mail in db
        $Option = new Option();
        $Option->setType('CONFIRMATIONMAIL');
        $Option->setKey($email);
        if ($demande = $Option->showByKey()) {

            //Check encrypted key
            if (password_verify($demande->val, $key)) {

                //Delete confirmation email
                $Option->setId($demande->id);
                if ($Option->delete()) {

                    //Check time lost since sending the email
                    if ((strtotime($demande->updated_at) + ($timeLimit * 60 * 60)) > time()) {
                        return $email;
                    }
                }
            }
        }
    }
    return false;
}

/**
 * @param array $data
 * @param array $otherAddr
 * @param array $options
 *
 * @return bool
 * @throws Exception
 */
function sendMail(array $data, array $otherAddr = [], array $options = [])
{
    $Mail = new PHPMailer();

    $Mail->CharSet = 'utf-8';
    $Mail->SMTPDebug = !empty($data['debug']) ? $data['debug'] : 0;

    // Synchronizing options
    $options = array_merge(array(
        'viewSenderSource' => true,
        'maxFileSizeAllowed' => 5 * 1024 * 1024,
        'priority' => 2
    ), $options);

    // SMTP data
    if (empty($data['smtp'])) {

        $Mail->IsMail();

    } else {

        $Mail->isSMTP();

        $Mail->SMTPKeepAlive = !empty($data['keepAlive']) ? $data['keepAlive'] : false;
        $Mail->SMTPSecure = !empty($data['encryption']) ? $data['encryption'] : '';

        $Mail->Host = $data['smtp']['host'];
        $Mail->Port = $data['smtp']['port'];

        if (!empty($data['smtp']['auth'])) {
            $Mail->SMTPAuth = $data['smtp']['auth'];
            $Mail->Username = $data['smtp']['username'];
            $Mail->Password = $data['smtp']['password'];
        }
    }

    // Sender
    $senderEmail = !empty($data['sender']) ? $data['sender'] : 'noreply@' . $_SERVER['HTTP_HOST'];
    $senderName = !empty($data['fromName']) ? $data['fromName'] : WEB_TITLE;

    $Mail->SetFrom($senderEmail, $senderName);

    // Reply to
    if (!empty($data['fromEmail']) && !empty($data['fromName'])) {
        $Mail->addReplyTo($data['fromEmail'], $data['fromName']);
    }

    $data['fromName'] = !empty($data['fromName']) ? $data['fromName'] : WEB_TITLE;
    $data['fromEmail'] = !empty($data['fromEmail']) ? $data['fromEmail'] : $senderEmail;

    // Recipient
    $Mail->ClearAddresses();
    $Mail->AddAddress($data['toEmail'], $data['toName']);

    // Adding Recipients
    if (!isArrayEmpty($otherAddr)) {
        foreach ($otherAddr as $email => $name) {
            $Mail->AddAddress($email, $name);
        }
    }

    // Object
    $Mail->Subject = $data['object'];

    //Priority
    if ($options['priority'] == 1) {
        $Mail->Priority = 1;
        $Mail->AddCustomHeader("X-MSMail-Priority: High");
        $Mail->AddCustomHeader("Importance: High");
    }

    // View sender's source
    if ($options['viewSenderSource']) {
        $sources = '<p>--<br><small><strong>Date:</strong> ' . date('d/m/Y H:i:s')
            . '<br><strong>Adresse IP:</strong> ' . getIP()
            . '<br><strong>Source:</strong> ' . (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_SERVER['HTTP_HOST']) . '</small></p>';
        $data['message'] .= $sources;
    }

    // Your message (with template)
    $Mail->MsgHTML(getAsset('emailTemplate', true, ['object' => $data['object'], 'message' => $data['message']]));

    //Attach files from form
    if (!empty($data['files'])) {
        $files = $data['files'];

        $File = new \App\File();

        if (is_array($files['name'])) {
            for ($i = 0; $i < count($files['name']); $i++) {
                if (!empty($files['name'][$i]) && $files['size'][$i] < $options['maxFileSizeAllowed'] && $File->authorizedMediaFormat($files['type'][$i])) {
                    $Mail->AddAttachment($files['tmp_name'][$i], $files['name'][$i]);
                }
            }
        } else {
            if (!empty($files['name']) && $files['size'] < $options['maxFileSizeAllowed'] && $File->authorizedMediaFormat($files['type'])) {
                $Mail->AddAttachment($files['tmp_name'], $files['name']);
            }
        }
    }

    //Attach files from url
    if (!empty($data['docs'])) {
        foreach ($data['docs'] as $doc) {
            if (filesize($doc['src']) < $options['maxFileSizeAllowed']) {
                $Mail->AddAttachment($doc['src'], $doc['name']);
            }
        }
    }

    //Attach files from string
    if (!empty($data['strAttach'])) {
        foreach ($data['strAttach'] as $str) {
            $Mail->addStringAttachment($str['src'], $str['name']);
        }
    }

    $MailLogger = new MailLogger($data);

    //Sending mail
    if ($Mail->Send()) {
        $MailLogger->save();
        return true;
    } else {
        $MailLogger->setSent(false);
        $MailLogger->save();
        return !empty($data['debug']) ? $Mail->ErrorInfo : false;
    }
}

/**
 * @param $month
 *
 * @return array|boolean
 */
function getMonth($month = '')
{
    $month_arr[1] = "Janvier";
    $month_arr[2] = "Février";
    $month_arr[3] = "Mars";
    $month_arr[4] = "Avril";
    $month_arr[5] = "Mai";
    $month_arr[6] = "Juin";
    $month_arr[7] = "Juillet";
    $month_arr[8] = "Août";
    $month_arr[9] = "Septembre";
    $month_arr[10] = "Octobre";
    $month_arr[11] = "Novembre";
    $month_arr[12] = "Décembre";

    return !empty($month) && array_key_exists($month, $month_arr) ? $month_arr[$month] : $month_arr;
}

/**
 * @param $date
 *
 * @return false|string
 */
function age($date)
{
    $dna = strtotime($date);
    $now = time();
    $age = date('Y', $now) - date('Y', $dna);
    if (strcmp(date('md', $dna), date('md', $now)) > 0) {
        $age--;
    }

    return $age;
}


/**
 * @param $file
 */
function fichierType($file)
{

    $type = pathinfo(strtolower($file), PATHINFO_EXTENSION);
    if ($type == 'jpg' || $type == 'png' || $type == 'jpeg' || $type == 'gif' || $type == 'webp') {
        echo '<a href="' . WEB_DIR_INCLUDE . $file . '" target="_blank"><img src="' . WEB_DIR_INCLUDE . $file . '" alt="' . $file . '" title="' . $file . '" class="img-responsive"></a>';
    } elseif ($type == 'doc' || $type == 'dot' || $type == 'docx') {
        echo '<a href="' . WEB_DIR_INCLUDE . $file . '" target="_blank"><img src="' . WEB_TEMPLATE_URL . 'images/Word.png" alt="' . $file . '" title="' . $file . '" class="img-responsive"></a>';
    } elseif ($type == 'xlsx' || $type == 'xlt' || $type == 'xls' || $type == 'xla') {
        echo '<a href="' . WEB_DIR_INCLUDE . $file . '" target="_blank"><img src="' . WEB_TEMPLATE_URL . 'images/Excel.png" alt="' . $file . '" title="' . $file . '" class="img-responsive"></a>';
    } elseif ($type == 'pptx' || $type == 'ppt' || $type == 'pot' || $type == 'pps' || $type == 'ppa') {
        echo '<a href="' . WEB_DIR_INCLUDE . $file . '" target="_blank"><img src="' . WEB_TEMPLATE_URL . 'images/PowerPoint.png" alt="' . $file . '" title="' . $file . '" class="img-responsive"></a>';
    } elseif ($type == 'pdf') {
        echo '<a href="' . WEB_DIR_INCLUDE . $file . '" target="_blank"><img src="' . WEB_TEMPLATE_URL . 'images/Pdf.png" alt="' . $file . '" title="' . $file . '" class="img-responsive"></a>';
    } elseif ($type == 'mp3') {
        echo '<audio src="' . WEB_DIR_INCLUDE . $file . '" type="audio/mpeg" controls></audio>';
    } else {
        echo '<a href="' . WEB_DIR_INCLUDE . $file . '" target="_blank"><img src="' . WEB_TEMPLATE_URL . 'images/AllFileType.png" alt="' . $file . '" title="' . $file . '" class="img-responsive"></a><span class="fileType">' . $type . '</span>';
    }

}

/**
 * Get APPOE logo. if $appoelogo is true, return only appoe logo
 *
 * @param bool $appoeLogo
 * @param bool $onlySrc
 *
 * @return string
 */
function getLogo($appoeLogo = false, $onlySrc = false)
{
    $src = APP_IMG_URL . 'appoe-logo-black-sm.png';
    if (true === $appoeLogo) {
        return $src;
    }
    if (is_string($appoeLogo)) {
        return $appoeLogo;
    }

    $urlFolder = WEB_DIR_IMG;
    $pathFolder = WEB_PUBLIC_PATH . 'images/';
    $name = 'appoe-logo';

    $extensions = array('png', 'jpg', 'jpeg', 'gif', 'svg');
    $extensions = array_merge($extensions, array_map('strtoupper', $extensions));

    foreach ($extensions as $extension) {

        $logo = $name . '.' . $extension;
        if (file_exists($pathFolder . $logo)) {
            $src = $urlFolder . $logo;
            break;
        }
    }

    return !$onlySrc ? '<img class="img-responsive logoNavbar" src="' . $src . '" alt="APPOE">' : $src;
}

/**
 * @param $url
 *
 * @return string
 */
function getOnlyPath($url)
{
    return isUrl($url) ? $_SERVER['DOCUMENT_ROOT'] . parse_url($url, PHP_URL_PATH) : '';
}

/**
 * @return string
 */
function getAppoeVersion()
{

    Version::setFile(WEB_APP_PATH . 'version.json');
    if (Version::show()):
        return Version::getVersion();
    endif;

    return '';
}

/**
 * @param $hours
 * @return float|int|mixed|string
 */
function hoursToMinutes($hours)
{
    $minutes = 0;
    if (strpos($hours, ':') !== false) {
        list($hours, $minutes) = explode(':', $hours);
        settype($minutes, 'integer');
    }
    settype($hours, 'integer');
    return $hours * 60 + $minutes;
}

/**
 * @param $time
 * @param string $format
 * @return int|string
 */
function minutesToHours($time, $format = '%s:%s')
{
    settype($time, 'integer');
    if ($time < 0 || $time >= 1440) {
        return 0;
    }
    $hours = floor($time / 60);
    $minutes = $time % 60;
    if ($hours < 10) {
        $hours = '0' . $hours;
    }
    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }
    return sprintf($format, $hours, $minutes);
}

/**
 * fonction permettant de transformer une valeur numérique en valeur en lettre
 * @param int $Nombre le nombre a convertir
 * @param int $Devise (0 = aucune, 1 = Euro €, 2 = Dollar $)
 * @param int $Langue (0 = Français, 1 = Belgique, 2 = Suisse)
 * @return string
 */
function moneyAsLetters($Nombre, $Devise = 1, $Langue = 0)
{
    $dblEnt = '';
    $byDec = '';
    $bNegatif = '';
    $strDev = '';
    $strCentimes = '';

    if ($Nombre < 0) {
        $bNegatif = true;
        $Nombre = abs($Nombre);
    }
    $dblEnt = intval($Nombre);
    $byDec = round(($Nombre - $dblEnt) * 100);
    if ($byDec == 0) {
        if ($dblEnt > 999999999999999) {
            return "#TropGrand";
        }
    } else {
        if ($dblEnt > 9999999999999.99) {
            return "#TropGrand";
        }
    }
    switch ($Devise) {
        case 0 :
            if ($byDec > 0) {
                $strDev = " virgule";
            }
            break;
        case 1 :
            $strDev = " Euro";
            if ($byDec > 0) {
                $strCentimes = $strCentimes . " Centimes";
            }
            break;
        case 2 :
            $strDev = " Dollar";
            if ($byDec > 0) {
                $strCentimes = $strCentimes . " Cent";
            }
            break;
    }
    if (($dblEnt > 1) && ($Devise != 0)) {
        $strDev = $strDev . "s";
    }
    if ($byDec > 0) {
        $NumberLetter = ConvNumEnt(floatval($dblEnt), $Langue) . $strDev . " et " . ConvNumDizaine($byDec, $Langue) . $strCentimes;
    } else {
        $NumberLetter = ConvNumEnt(floatval($dblEnt), $Langue) . $strDev;
    }

    return $NumberLetter;
}

/**
 * @param $Nombre
 * @param $Langue
 *
 * @return mixed|string
 */
function ConvNumEnt($Nombre, $Langue)
{
    $byNum = $iTmp = $dblReste = '';
    $StrTmp = '';
    $NumEnt = '';
    $iTmp = $Nombre - (intval($Nombre / 1000) * 1000);
    $NumEnt = ConvNumCent(intval($iTmp), $Langue);
    $dblReste = intval($Nombre / 1000);
    $iTmp = $dblReste - (intval($dblReste / 1000) * 1000);
    $StrTmp = ConvNumCent(intval($iTmp), $Langue);
    switch ($iTmp) {
        case 0 :
            break;
        case 1 :
            $StrTmp = "mille ";
            break;
        default :
            $StrTmp = $StrTmp . " mille ";
    }
    $NumEnt = $StrTmp . $NumEnt;
    $dblReste = intval($dblReste / 1000);
    $iTmp = $dblReste - (intval($dblReste / 1000) * 1000);
    $StrTmp = ConvNumCent(intval($iTmp), $Langue);
    switch ($iTmp) {
        case 0 :
            break;
        case 1 :
            $StrTmp = $StrTmp . " million ";
            break;
        default :
            $StrTmp = $StrTmp . " millions ";
    }
    $NumEnt = $StrTmp . $NumEnt;
    $dblReste = intval($dblReste / 1000);
    $iTmp = $dblReste - (intval($dblReste / 1000) * 1000);
    $StrTmp = ConvNumCent(intval($iTmp), $Langue);
    switch ($iTmp) {
        case 0 :
            break;
        case 1 :
            $StrTmp = $StrTmp . " milliard ";
            break;
        default :
            $StrTmp = $StrTmp . " milliards ";
    }
    $NumEnt = $StrTmp . $NumEnt;
    $dblReste = intval($dblReste / 1000);
    $iTmp = $dblReste - (intval($dblReste / 1000) * 1000);
    $StrTmp = ConvNumCent(intval($iTmp), $Langue);
    switch ($iTmp) {
        case 0 :
            break;
        case 1 :
            $StrTmp = $StrTmp . " billion ";
            break;
        default :
            $StrTmp = $StrTmp . " billions ";
    }
    $NumEnt = $StrTmp . $NumEnt;

    return $NumEnt;
}

/**
 * @param $Nombre
 * @param $Langue
 *
 * @return mixed|string
 */
function ConvNumDizaine(int $nombre, int $langue): string
{
    $tabUnit = [
        "", "un", "deux", "trois", "quatre", "cinq",
        "six", "sept", "huit", "neuf", "dix",
        "onze", "douze", "treize", "quatorze", "quinze",
        "seize", "dix-sept", "dix-huit", "dix-neuf"
    ];

    $tabDiz = [
        "", "", "vingt", "trente", "quarante", "cinquante",
        "soixante", "soixante", "quatre-vingt", "quatre-vingt"
    ];

    // Adapter pour la Suisse ou la Belgique
    if ($langue === 1) { // Belgique
        $tabDiz[7] = "septante";
        $tabDiz[9] = "nonante";
    } elseif ($langue === 2) { // Suisse
        $tabDiz[7] = "septante";
        $tabDiz[8] = "huitante";
        $tabDiz[9] = "nonante";
    }

    $dizaine = intdiv($nombre, 10);
    $unite = $nombre % 10;
    $liaison = ($unite === 1) ? " et " : "-";

    // Traitement spécial pour 70-79, 90-99 (en français standard)
    if (in_array($dizaine, [7, 9], true) && $langue === 0) {
        $unite += 10;
    }

    if ($dizaine === 1) { // Les nombres 10-19 sont spéciaux
        $unite += 10;
        $liaison = "";
    }

    $numDizaine = $tabDiz[$dizaine];

    // Pluriel pour "quatre-vingts" uniquement en français standard sans unité
    if ($dizaine === 8 && $langue !== 2 && $unite === 0) {
        $numDizaine .= "s";
    }

    if (!empty($tabUnit[$unite])) {
        $numDizaine .= $liaison . $tabUnit[$unite];
    }

    return $numDizaine;
}


/**
 * @param $Nombre
 * @param $Langue
 *
 * @return mixed|string
 */
function ConvNumCent(int $nombre, string $langue): string
{
    $tabUnit = [
        "", "un", "deux", "trois", "quatre",
        "cinq", "six", "sept", "huit", "neuf", "dix"
    ];

    $centaine = intdiv($nombre, 100);
    $reste = $nombre % 100;
    $strReste = ConvNumDizaine($reste, $langue);

    return match (true) {
        $centaine === 0 => $strReste,
        $centaine === 1 => $reste === 0 ? "cent" : "cent " . $strReste,
        default => $reste === 0
            ? $tabUnit[$centaine] . " cents"
            : $tabUnit[$centaine] . " cent " . $strReste,
    };
}


/****** JOURS FERIES ******/
function dimanche_paques($annee)
{
    return date("Y-m-d", easter_date($annee));
}

/**
 * @param $annee
 *
 * @return false|string
 */
function vendredi_saint($annee)
{
    $dimanche_paques = dimanche_paques($annee);

    return date("Y-m-d", strtotime("$dimanche_paques -2 day"));
}

/**
 * @param $annee
 *
 * @return false|string
 */
function lundi_paques($annee)
{
    $dimanche_paques = dimanche_paques($annee);

    return date("Y-m-d", strtotime("$dimanche_paques +1 day"));
}

/**
 * @param $annee
 *
 * @return false|string
 */
function jeudi_ascension($annee)
{
    $dimanche_paques = dimanche_paques($annee);

    return date("Y-m-d", strtotime("$dimanche_paques +39 day"));
}

/**
 * @param $annee
 *
 * @return false|string
 */
function lundi_pentecote($annee)
{
    $dimanche_paques = dimanche_paques($annee);

    return date("Y-m-d", strtotime("$dimanche_paques +50 day"));
}


/**
 * @param $annee
 * @param bool $alsacemoselle
 *
 * @return array
 */
function jours_feries($annee, $alsacemoselle = false)
{
    $jours_feries = array(
        dimanche_paques($annee),
        lundi_paques($annee),
        jeudi_ascension($annee),
        lundi_pentecote($annee),
        "$annee-01-01",
        "$annee-05-01",
        "$annee-05-08",
        "$annee-05-15",
        "$annee-07-14",
        "$annee-11-11",
        "$annee-11-01",
        "$annee-12-25"
    );
    if ($alsacemoselle) {
        $jours_feries[] = "$annee-12-26";
        $jours_feries[] = vendredi_saint($annee);
    }
    sort($jours_feries);

    return $jours_feries;
}

/**
 * @param $jour
 * @param bool $alsaceMoselle
 *
 * @return bool
 */
function isferie($jour, $alsaceMoselle = false)
{
    $jour = date("Y-m-d", strtotime($jour));
    $annee = substr($jour, 0, 4);

    return in_array($jour, jours_feries($annee, $alsaceMoselle));
}
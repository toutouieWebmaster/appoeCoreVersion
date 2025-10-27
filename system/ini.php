<?php
/**
 * DataBase config
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/ini.main.php');

/**
 * Charset
 */
ini_set('default_charset', 'UTF-8');

//Table prefix
if (!defined('TABLEPREFIX')) {
    define("TABLEPREFIX", '');
}

//Debug APPOE
if (!defined('DEBUG')) {
    define('DEBUG', false);
}

//Default cache duration in seconds
if (!defined('CACHE_DURATION')) {
    define('CACHE_DURATION', 60 * 60 * 24 * 10);
}

//Acces restriction to APPOE with a min role id
if (!defined('APPOE_MIN_ROLE')) {
    define('APPOE_MIN_ROLE', 1);
}

/**
 * App paths
 */
const FILE_DIR_NAME = 'include/';
const WEB_APP_PATH = ROOT_PATH . 'app/';
const WEB_PUBLIC_PATH = ROOT_PATH . 'public/';
const WEB_PATH = WEB_PUBLIC_PATH . 'html/';
const WEB_LIB_PATH = WEB_APP_PATH . 'lib/';
const WEB_PHPMAILER_PATH = WEB_LIB_PATH . 'php/PHPMailer/';
const WEB_TEMPLATE_PATH = WEB_LIB_PATH . 'template/';
const APP_LOGO_PATH = WEB_TEMPLATE_PATH . 'logo/';
const WEB_AJAX_PATH = WEB_APP_PATH . 'ajax/';
const WEB_PLUGIN_PATH = WEB_APP_PATH . 'plugin/';
const WEB_PROCESS_PATH = WEB_APP_PATH . 'process/';
const WEB_SYSTEM_PATH = WEB_APP_PATH . 'system/';
const WEB_BACKUP_PATH = WEB_APP_PATH . 'backup/';
const FILE_DIR_PATH = ROOT_PATH . FILE_DIR_NAME;
const THUMB_DIR_PATH = FILE_DIR_PATH . 'thumb/';
const MAIL_DIR_PATH = ROOT_PATH . '/ressources/mail/';
const FILE_LANG_PATH = WEB_SYSTEM_PATH . 'lang/';
const INCLUDE_PLUGIN_PATH = WEB_DIR . 'app/plugin/';

/**
 * App urls
 */
const WEB_APP_URL = WEB_DIR_URL . 'app/';
const WEB_ADMIN_URL = WEB_APP_URL . 'page/';
const WEB_LIB_URL = WEB_APP_URL . 'lib/';
const WEB_TEMPLATE_URL = WEB_LIB_URL . 'template/';
const APP_IMG_URL = WEB_TEMPLATE_URL . 'images/';
const APP_LOGO_URL = WEB_TEMPLATE_URL . 'logo/';
const WEB_PLUGIN_URL = WEB_APP_URL . 'plugin/';
const WEB_PUBLIC_URL = WEB_DIR_URL . 'public/';
const WEB_DIR_IMG = WEB_PUBLIC_URL . 'images/';
const WEB_DIR_INCLUDE = WEB_DIR_URL . FILE_DIR_NAME;
const WEB_DIR_SYSTEM = WEB_APP_URL . 'system/';
const WEB_DIR_MAIL = WEB_DIR_URL . '/ressources/mail/';

/**
 * Errors config
 */
error_reporting(E_ALL);
ini_set('display_errors', defined('DEBUG') && DEBUG ? 1 : 0);
ini_set('log_errors', 1);
ini_set('error_log', ROOT_PATH . 'error.log');
const  ERROR_LOG_PATH = ROOT_PATH . 'error.log';

/**
 * Set App Interface lang
 */
const INTERFACE_LANG = 'fr';

/**
 * Set App Content lang
 */
if (!empty($_SESSION['APP_LANG'])) {
    define('APP_LANG', $_SESSION['APP_LANG']);
} else {
    define('APP_LANG', 'fr');
}

/**
 * Set website lang
 */
if (!empty($_COOKIE['LANG']) && array_key_exists($_COOKIE['LANG'], LANGUAGES)) {
    define('LANG', $_COOKIE['LANG']);
} else {
    if (defined('DEFAULT_LANG')) {
        define('LANG', DEFAULT_LANG);
    } elseif (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE']) && array_key_exists($_SERVER['HTTP_ACCEPT_LANGUAGE'], LANGUAGES)) {
        define('LANG', substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
    } else {
        define('LANG', 'fr');
    }
    $options = array('expires' => strtotime('+30 days'), 'path' => WEB_DIR, 'secure' => false, 'httponly' => true, 'samesite' => 'Strict');
    setcookie('LANG', LANG, $options);
}

/**
 * Defined local timezone & date format
 */
setlocale(LC_ALL, strtolower(LANG) . '_' . strtoupper(LANG) . '.UTF-8');

/**
 * Defined App const & params
 */
const NUM_OF_ATTEMPTS = 30;

const APP_TABLES = array(
    TABLEPREFIX . 'appoe_users',
    TABLEPREFIX . 'appoe_options',
    TABLEPREFIX . 'appoe_menu',
    TABLEPREFIX . 'appoe_files',
    TABLEPREFIX . 'appoe_filesContent',
    TABLEPREFIX . 'appoe_categories',
    TABLEPREFIX . 'appoe_categoryRelations',
    TABLEPREFIX . 'appoe_mailLogger'
);

const CATEGORY_TYPES = array(
    'APPOE',
    'MEDIA',
    'AUTRE'
);

const PAGE_TYPES = array(
    'boutique' => 'SHOP',
    'shop' => 'SHOP',
    'produit' => 'SHOP',
    'product' => 'SHOP',
    'article' => 'ITEMGLUE',
    'news' => 'ITEMGLUE',
    'archives' => 'ITEMGLUE',
    'blog' => 'ITEMGLUE'
);

//Load plugin files only for a specific app filename : plugin name => [filename, filename, ] || false (without extension)
const INI_LOAD_PLUGIN_FOR_APP_FILENAME = array(
    'leaflet' => false,
    'interactiveMap' => ['updateInterMapContent', 'updateInterMap']
);

//Index file default content
const DEFAULT_INDEX_CONTENT = '<?php 
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); 
header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT"); 
header("Cache-Control: no-store, no-cache, must-revalidate"); 
header("Cache-Control: post-check=0, pre-check=0", false); 
header("Pragma: no-cache");
header("Location: ../"); 
exit;';

//Htaccess file default content
const DEFAULT_HTACCESS = '############## START DEFAULT HTACCESS ##############
AddDefaultCharset UTF-8
Options All -Indexes
IndexIgnore *
ServerSignature Off

<IfModule mod_rewrite.c>
Options +FollowSymlinks
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteRule ^hibour/?$  hibour.php [L]
RewriteRule ^api/?$  /app/system/api.php [L]
RewriteRule ^([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)/?$  index.php?type=$1&slug=$2&typeSlug=$3 [QSA,L]
RewriteRule ^([a-zA-Z0-9-]+)/([a-zA-Z0-9-]+)/?$  index.php?slug=$1&id=$2 [QSA,L]
RewriteRule ^([a-zA-Z0-9-]+)/?$  index.php?slug=$1 [QSA,L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^([A-Za-z0-9-\/\.&_\s]+)/?$ index.php?slug=$1 [L]
</IfModule>

# STRONG HTACCESS PROTECTION
<Files ~ "^.*\.([Hh][Tt][Aa])">
order allow,deny
deny from all
satisfy all
</Files>

<FilesMatch "\.(htaccess|htpasswd|ini|phps|fla|psd|log|sh)$">
	Order Allow,Deny
	Deny from all
</FilesMatch>

<Files "ini.php">
    Order Deny,Allow
    Deny from all
</Files>

<Files "ini.main.php">
    Order Deny,Allow
    Deny from all
</Files>

<IfModule mod_headers.c>
Header always set X-FRAME-OPTIONS "SAMEORIGIN"
</IfModule>

###FILTRE CONTRE CERTAINS ROBOTS DES PIRATES
RewriteEngine On
## EXCEPTION: TOUS LES ROBOTS MEMES ANONYMES OU BANNIS PEUVENT ACCEDER A CES FICHIERS
RewriteCond %{REQUEST_URI} !^/robots.txt
RewriteCond %{REQUEST_URI} !^/sitemap.xml

RewriteCond %{HTTP_USER_AGENT} ^-?$ [OR] ## ANONYMES
RewriteCond %{HTTP_USER_AGENT} ^curl|^Fetch\ API\ Request|GT::WWW|^HTTP::Lite|httplib|^Java|^LeechFTP|lwp-trivial|^LWP|libWeb|libwww|^PEAR|PECL::HTTP|PHPCrawl|PycURL|python|^ReGet|Rsync|Snoopy|URI::Fetch|urllib|WebDAV|^Wget [NC] ## BIBLIOTHEQUES / CLASSES HTTP DONT ON NE VEUT PAS. ATTENTION, CELA PEUT BLOQUER CERTAINES FONCTIONS DE VOTRE CMS. NE PAS TOUT EFFACER, MAIS CHERCHEZ LE NOM DE LA CLASSE HTTP CONCERNEE (DEMANDEZ AUX DEVELOPPEURS DE VOTRE CMS). CETTE LISTE BLOQUE 80% DES ROBOTS SPAMMEURS. IL FAUT LA CONSERVER.
RewriteCond %{HTTP_USER_AGENT} ^[bcdfghjklmnpqrstvwxz\ ]{10,}|^[0-9a-z]{15,}|^[0-9A-Za-z]{19,}|^[A-Za-z]{3,}\ [a-z]{4,}\ [a-z]{4,} [OR] ## CEUX QUI INVENTENT DES NOMS AU HASARD, RETIREZ LES 2 DIESES EN DEBUT DE LIGNE POUR L\'ACTIVER
RewriteRule (.*) - [F]

### FILTRE CONTRE XSS, REDIRECTIONS HTTP, base64_encode, VARIABLE PHP GLOBALS VIA URL, MODIFIER VARIABLE _REQUEST VIA URL, TEST DE FAILLE PHP, INJECTION SQL SIMPLE
RewriteEngine On
RewriteCond %{REQUEST_METHOD} (GET|POST) [NC]
RewriteCond %{QUERY_STRING} ^(.*)(%3C|<)/?script(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)(%3D|=)?javascript(%3A|:)(.*)$ [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)document\.location\.href(.*)$ [OR]
RewriteCond %{QUERY_STRING} ^.*(%24&x).* [NC,OR]
RewriteCond %{QUERY_STRING} ^.*(127\.0).* [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)(%3D|=)(https?|ftp|mosConfig)(%3A|:)//(.*)$ [NC,OR] ## ATTENTION A CETTE REGLE. ELLE PEUT CASSER CERTAINES REDIRECTIONS RESSEMBLANT A: http://www.truc.fr/index.php?r=http://www.google.fr ##
RewriteCond %{QUERY_STRING} ^.*(_encode|localhost|loopback).* [NC,OR]
RewriteCond %{QUERY_STRING} ^(.*)GLOBALS(=|[|%[0-9A-Z]{0,2})(.*)$ [OR]
RewriteCond %{QUERY_STRING} ^(.*)_REQUEST(=|[|%[0-9A-Z]{0,2})(.*)$ [OR]
RewriteCond %{QUERY_STRING} ^(.*)(SELECT(%20|\+)|UNION(%20|\+)ALL|INSERT(%20|\+)|DELETE(%20|\+)|CHAR\(|UPDATE(%20|\+)|REPLACE(%20|\+)|LIMIT(%20|\+)|CONCAT(%20|\+)|DECLARE(%20|\+))(.*)$ [NC]
RewriteRule (.*) - [F]

### DES FAUX URLS OU VIEUX SYSTEMES OBSOLETES, ON LES NEUTRALISE
RedirectMatch 403 (\.\./|base64|boot\.ini|eval\(|\(null\)|^[-_a-z0-9/\.]*//.*|/etc/passwd|^/_vti.*|^/MSOffice.*|/fckeditor/|/elfinder/|zoho/|/jquery-file-upload/server/|/assetmanager/|wwwroot|e107\_)
# DESACTIVE LES METHODES DE REQUETES TRACE TRACK DELETE
RewriteEngine On
RewriteCond %{REQUEST_METHOD} ^(TRACE|DELETE|TRACK) [NC]
RewriteRule ^.* - [F]

############## END DEFAULT HTACCESS ##############';

//Htaccess file cache content
const HTACCESS_CACHE = '############## START HTACCESS CACHE ##############
<IfModule mod_deflate.c>
AddOutputFilterByType DEFLATE text/plain
AddOutputFilterByType DEFLATE text/html
AddOutputFilterByType DEFLATE text/xml
AddOutputFilterByType DEFLATE text/shtml
AddOutputFilterByType DEFLATE text/css
AddOutputFilterByType DEFLATE text/javascript
AddOutputFilterByType DEFLATE application/xml
AddOutputFilterByType DEFLATE application/xhtml+xml
AddOutputFilterByType DEFLATE application/rss+xml
AddOutputFilterByType DEFLATE application/javascript
AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

<IfModule mod_expires.c>
ExpiresActive On
 ExpiresDefault "access plus 7200 seconds"
 ExpiresByType image/jpg "access plus 259200 seconds"
 ExpiresByType image/jpeg "access plus 259200 seconds"
 ExpiresByType image/png "access plus 259200 seconds"
 ExpiresByType image/gif "access plus 259200 seconds"
 AddType image/x-icon .ico
 ExpiresByType image/ico "access plus 259200 seconds"
 ExpiresByType image/icon "access plus 259200 seconds"
 ExpiresByType image/x-icon "access plus 259200 seconds"
 ExpiresByType text/css "access plus 259200 seconds"
 ExpiresByType text/javascript "access plus 259200 seconds"
 ExpiresByType text/html "access plus 7200 seconds"
 ExpiresByType application/xhtml+xml "access plus 7200 seconds"
 ExpiresByType application/javascript "access plus 259200 seconds"
 ExpiresByType application/x-javascript "access plus 259200 seconds"
 ExpiresByType application/x-shockwave-flash "access plus 259200 seconds"
</IfModule>

<IfModule mod_headers.c>
 <FilesMatch "\.(ico|jpe?g|png|gif|swf|css|gz)$">
Header set Cache-Control "max-age=259200, public"
</FilesMatch>
 <FilesMatch "\.(js)$">
Header set Cache-Control "max-age=259200, private"
</FilesMatch>
<filesMatch "\.(html|htm)$">
Header set Cache-Control "max-age=7200, public"
</filesMatch>
# Disable caching for scripts and other dynamic files
<FilesMatch "\.(pl|php|cgi|spl|scgi|fcgi)$">
Header unset Cache-Control
    </FilesMatch>
</IfModule>
############## END HTACCESS CACHE ##############';

const THEME_DEFAULT_STYLE = array(
    '--colorPrimary' => '#3eb293',
    '--colorPrimaryOpacity' => 'rgba(62, 178, 147, 0.7)',
    '--textBgColorPrimary' => '#FFF',
    '--colorSecondary' => '#FF9373',
    '--colorSecondaryOpacity' => 'rgba(255, 147, 117, 0.7)',
    '--textBgColorSecondary' => '#FFF',
    '--colorTertiary' => '#3eb293',
    '--colorTertiaryOpacity' => 'rgba(62, 178, 147, 0.7)',
    '--textBgColorTertiary' => '#FFF'
);

const THEME_CONTENT = '
[class*="colorPrimary"],
[class*="colorSecondary"],
[class*="colorTertiary"] {
    transition: all 0.3s;
}

/************** PRIMARY COLOR **************/
.colorPrimary {
    color: var(--colorPrimary) !important;
}

.borderColorPrimary {
    border-color: var(--colorPrimary) !important;
}

.bgColorPrimary, a[class*="colorPrimary"]:focus, a[class*="colorPrimary"]:hover,
button[class*="colorPrimary"]:focus, button[class*="colorPrimary"]:hover {
    background: var(--colorPrimary) !important;
    color: var(--textBgColorPrimary) !important;
}

/************** SECONDARY COLOR **************/
.colorSecondary {
    color: var(--colorSecondary) !important;
}

.borderColorSecondary {
    border-color: var(--colorSecondary) !important;
}

.bgColorSecondary, #sidebar ul li.active > a, .dashboardCard:focus, .dashboardCard:hover, table.table th,
a[class*="colorSecondary"]:focus, a[class*="colorSecondary"]:hover, button[class*="colorSecondary"]:focus,
button[class*="colorSecondary"]:hover {
    background: var(--colorSecondary) !important;
    color: var(--textBgColorSecondary) !important;
}

/************** TERTIARY COLOR **************/
.colorTertiary {
    color: var(--colorTertiary);
}

.borderColorTertiary {
    border-color: var(--colorTertiary);
}

.bgColorTertiary, a[class*="colorTertiary"]:focus, a[class*="colorTertiary"]:hover,
button[class*="colorTertiary"]:focus, button[class*="colorTertiary"]:hover {
    background: var(--colorTertiary);
    color: var(--textBgColorTertiary);
}

/************** DASHBOARD **************/
.dashboardCard a {
    color: var(--colorSecondary);
}

.dashboardCard h2 a:before {
    color: var(--colorSecondary);
    position: absolute;
    top: 0;
    left: 10px;
    font-size: 100px;
    opacity: 0.1;
}

.dashboardCard:focus a, .dashboardCard:hover a,
.dashboardCard:focus h2 a:before, .dashboardCard:hover h2 a:before {
    color: var(--textBgColorSecondary);
}

.dashboardNum {
    background: var(--colorSecondary);
    color: var(--textBgColorSecondary);
}

.dashboardCard:focus span.dashboardNum,
.dashboardCard:hover span.dashboardNum {
    background: var(--textBgColorSecondary);
    color: var(--colorSecondary);
}

/************* MEDIA *************/
.view > .mask {
    background-color: var(--colorSecondaryOpacity);
}

/************* TABLE *************/
table.table tr:hover {
    background: var(--colorSecondary) !important;
}

table.table tr:hover td {
    color: var(--textBgColorSecondary) !important;
}';
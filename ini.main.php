<?php
/**
 * Web application Mode
 */
const DEBUG = true;

/**
 * Database params
 */
const DBHOST = '127.0.0.1';
const DBNAME = 'appoe_demo';
const DBUSER = 'root';
const DBPASS = '';
const DBPATH = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME . ';charset=UTF8';

/**
 * App main params
 */
const WEB_TITLE = 'APPOE';
const DEFAULT_EMAIL = '';
const WEB_DIR = '/';
define('WEB_DIR_URL', 'https://' . $_SERVER['HTTP_HOST'] . WEB_DIR);
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'] . WEB_DIR);

/**
 * Localisation : Date & Time
 */
date_default_timezone_set('Europe/Paris');

/**
 * Available languages
 */
const DEFAULT_LANG = 'fr';
const LANGUAGES = array(
    'fr' => 'Français',
    'en' => 'English'
);

/**
 * Allowed IP adresses
 */
const IP_ALLOWED = array(
    '2a01:cb10:2ff:bc00',
);

/**
 * Declared custom App const
 */

//Default articles page
const DEFAULT_ARTICLES_PAGE = '';

//Get acces to APPOE with a min role id
const APPOE_MIN_ROLE = '';

//Default pdf template folder
const PDF_TEMPLATE_PATH = ROOT_PATH . 'public/pdfTemplates/';

//Twitter API connection keys & tokens
const TWITTER_CONSUMER_KEY = '';
const TWITTER_CONSUMER_SECRET = '';
const TWITTER_ACCESS_TOKEN = '';
const TWITTER_ACCESS_TOKEN_SECRET = '';
const TWITTER_USERNAME = '';

//Facebook API connection keys & tokens
const FACEBOOK_APP_ID = '';

//Instagram API connection keys & tokens
const INSTAGRAM_API_URL = 'https://graph.instagram.com/';
const INSTAGRAM_USERNAME = '';
const INSTAGRAM_ID = '';
const INSTAGRAM_TOKEN = '';


//Create every article with meta associate : metaKey => metaValue
const ARTICLE_META = [];

//Load plugin files only for a specific public filename : plugin name => [filename, filename, ] || false (without extension)
const PLUGIN_FOR_PUBLIC_FILENAME = array(
    'leaflet' => ['contact']
);

//Load plugin files only for a specific app filename : plugin name => [filename, filename, ] || false (without extension) replace ini
const PLUGIN_FOR_APP_FILENAME = [];

//Get accessible positions in a template file
const FILE_TEMPLATE_POSITIONS = array(
    1 => 'Article',
    2 => 'Mise à la une',
    3 => 'Sidebar',
    4 => 'Slider',
    5 => 'En tête'
);

// APPOE accessible roles
const ROLES = array(
    1 => 'Abonné',
    2 => 'Rédacteur',
    3 => 'Responsable',
    4 => 'Administrateur',
    5 => 'Super Administrateur'
);

//Similar pages for one slug
const SIMILAR_PAGES_SLUG = [];
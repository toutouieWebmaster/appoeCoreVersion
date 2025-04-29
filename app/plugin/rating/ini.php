<?php
define('RATING_PATH', WEB_PLUGIN_PATH . 'rating/');
define('RATING_URL', WEB_PLUGIN_URL . 'rating/');

const PLUGIN_TABLES = array(
	TABLEPREFIX.'appoe_plugin_rating'
);

const TYPES_NAMES = array(
    'ITEMGLUE' => 'Article',
    'CMS' => 'Page',
    'SHOP' => 'Produit'
);
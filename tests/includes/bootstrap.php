<?php

declare(strict_types=1);

// Define globals to mock WordPress data.
// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
$GLOBALS['wp_query'] = new \stdClass();

$GLOBALS['wp_options'] = array();

$GLOBALS['rewrite_tags']          = array();
$GLOBALS['rewrite_rules']         = array();
$GLOBALS['flushed_rewrite_rules'] = array();

$GLOBALS['wp_enqueued_styles']  = array();
$GLOBALS['wp_enqueued_scripts'] = array();

require_once __DIR__ . '/../../vendor/autoload.php';

// Include the class for PluginTestCase.
require_once __DIR__ . '/PluginTestCase.php';

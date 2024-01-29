<?php
/**
 * Event Registration
 *
 * @package leomuniz\event-registration
 * @author            Léo Muniz
 * @copyright         2023 Léo Muniz
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name: Event Registration
 * Plugin URI: https://leomuniz.dev
 * Description: Manage event registration without worries.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: Leo Muniz
 * Author URI: https://leomuniz.dev
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: event-registration
 * Domain Path: /languages
 */

declare(strict_types=1);

namespace leomuniz\Event_Registration;

use leomuniz\Event_Registration\Core;
use leomuniz\Event_Registration\Admin;
use leomuniz\Event_Registration\Frontend;

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

const DIR     = __DIR__;
const VERSION = '1.0.0';

define( __NAMESPACE__ . '\URL', plugin_dir_url( __FILE__ ) );

if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require __DIR__ . '/vendor/autoload.php';
}

$package = Core\Plugin::instance();

// Register plugin dependencies.
$package->register( Core\Settings::class );
$package->register( Core\Main::class );

$package->register( Admin\Pages::class );

// Execute the load() function from the Main class.
$package->execute( Core\Main::class );

add_action(
	'plugins_loaded',
	static function () use ( $package ): void {
		$package->load();
	}
);

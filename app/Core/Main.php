<?php
/**
 * Main class file.
 * Main execution flow of the plugin.
 *
 * @package leomuniz\event-registration
 */

declare(strict_types=1);

namespace leomuniz\Event_Registration\Core;

use leomuniz\Event_Registration as Plugin;
use leomuniz\Event_Registration\Interfaces\{
	Settings_Interface,
	Admin_Pages_Interface
};

/**
 * Main class.
 *
 * @since 1.0.0
 */
class Main {

	/**
	 * Settings class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var leomuniz\Event_Registration\Core\Settings;
	 */
	private $settings;

	/**
	 * Settings class instance.
	 *
	 * @since 1.0.0
	 *
	 * @var leomuniz\Event_Registration\Core\Settings;
	 */
	private $admin_pages;

	/**
	 * __construct method.
	 * Used to initiliaze the dependencies classes through the DI container.
	 *
	 * @since 1.0.0
	 *
	 * @param Settings_Interface $settings Settings class instance.
	 */
	public function __construct( Settings_Interface $settings, Admin_Pages_Interface $admin_pages ) {

		$this->settings    = $settings;
		$this->admin_pages = $admin_pages;
	}

	/**
	 * Start the engine.
	 *
	 * @since 1.0.0
	 */
	public function load() {
		add_action( 'init', array( $this, 'register_blocks' ) );
	}

	public function register_blocks() {
		register_block_type( Plugin\DIR . '/build/blocks/copyright-date-block' );
	}


	/**
	 * Loads a HTML view from the ./views folder.
	 *
	 * @since 1.0.0
	 *
	 * @param string $view_file View file without .php extensoin.
	 * @param mixed  $vars      Variable to be used by the view file.
	 *
	 * @return string Processed HTML content from the view.
	 */
	public function load_view( string $view_file, mixed $vars ): string { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed
		ob_start();

		$filename = Plugin\DIR . '/views/' . $view_file . '.php';

		if ( file_exists( $filename ) ) {
			include $filename;
		}

		return ob_get_clean();
	}
}

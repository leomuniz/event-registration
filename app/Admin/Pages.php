<?php

namespace leomuniz\Event_Registration\Admin;

use leomuniz\Event_Registration as Plugin;
use leomuniz\Event_Registration\Interfaces\{
	Admin_Pages_Interface,
	Settings_Interface
};

/**
 * Class Pages
 *
 * This class allows creating WordPress admin pages.
 *
 * @package leomuniz\event-registration
 */
class Pages implements Admin_Pages_Interface {
	private $admin_pages = array();
	private $pages_slugs = array();
	private $pages_content = array();
	private $settings;

	/**
	 * Constructor.
	 *
	 * @param Settings_Interface $settings The settings object.
	 */
	public function __construct( Settings_Interface $settings = null ) {
		$this->settings    = $settings;
		$this->admin_pages = $this->settings->get( 'admin_pages' );

		if ( $this->admin_pages ) {
			add_action( 'admin_menu', array( $this, 'register' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		}
	}

	/**
	 * Registers the admin pages.
	 *
	 * @param string $context     The context of the admin page.
	 * @param array  $admin_pages The admin pages to register.
	 */
	public function register( string $context = '', array $admin_pages = array() ): void {

		if ( empty( $admin_pages ) ) {
			$admin_pages = $this->admin_pages;
		}

		foreach ( $admin_pages as $admin_page ) {

			// Top level menu page.
			if ( empty( $admin_page['page_type'] ) || $admin_page['page_type'] === 'menu_page' ) {
				$this->add_menu_page( $admin_page );
				continue;
			}

			// Sub menu page.
			if ( $admin_page['page_type'] === 'sub_menu' ) {
				$this->add_submenu_page( $admin_page );
				continue;
			}

			// Implement other page types here.
			// admin_bar, dashboard_menu and options_page.
		}
	}

	/**
	 * Adds a top level menu page.
	 *
	 * @param array $admin_page The admin page to add.
	 */
	public function add_menu_page( array $admin_page ): void {

		$menu_slug = $admin_page['menu_slug'] ?? sanitize_title_with_dashes( $admin_page['page_title'] );

		add_menu_page(
			$admin_page['page_title'],
			$admin_page['menu_title'],
			$admin_page['capability'] ?? 'manage_options',
			$menu_slug,
			array( $this, 'render' ),
			$admin_page['icon_url'] ?? 'dashicons-admin-generic',
			$admin_page['position'] ?? 10
		);

		array_push( $this->pages_slugs, $menu_slug );
	}

	/**
	 * Adds a submenu page.
	 *
	 * @param array $admin_page The admin page to add.
	 */
	public function add_submenu_page( array $admin_page ): void {

		$menu_slug = $admin_page['menu_slug'] ?? sanitize_title_with_dashes( $admin_page['page_title'] );

		add_submenu_page(
			$admin_page['parent_page'] ?? null, // null for hidden menu page.
			$admin_page['page_title'],
			$admin_page['menu_title'],
			$admin_page['capability'] ?? 'manage_options',
			$menu_slug,
			array( $this, 'render' )
		);

		array_push( $this->pages_slugs, $menu_slug );
	}


	/**
	 * Renders the content of the admin page or submenu page.
	 */
	public function render() {
		?>
		<div class="wrap">
			<div id="page-content"></div>
		</div>
		<?php
	}

	/**
	 * Enqueues the admin scripts.
	 */
	public function enqueue_admin_scripts() {

		if ( empty ( $_GET['page'] ) ) { // phpcs:ignore
			return;
		}

		$page_slug = sanitize_text_field( wp_unslash( $_GET['page'] ) ); // phpcs:ignore

		if ( ! in_array( $page_slug, $this->pages_slugs, true ) ) {
			return;
		}

		// Fallback to default dependencies.
		$deps = array(
			'dependencies' => array( 'wp-element', 'wp-components', 'wp-i18n', 'wp-api-fetch' ),
		);

		if ( file_exists( Plugin\DIR . '/build/' . $page_slug . '.asset.php' ) ) {
			$deps = require Plugin\DIR . '/build/' . $page_slug . '.asset.php';
		}

		wp_enqueue_script(
			'event-registration-admin-' . $page_slug . '-script',
			Plugin\URL . '/build/admin/' . $page_slug . '.js',
			$deps['dependencies'],
			Plugin\VERSION,
			true
		);
	}
}

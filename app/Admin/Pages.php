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
	public function register( string $context = '', array $admin_pages = array() ) {

		if ( empty( $admin_pages ) ) {
			$admin_pages = $this->admin_pages;
		}

		foreach ( $admin_pages as $admin_page ) {
			$capability = isset( $admin_page['capability'] ) ? $admin_page['capability'] : 'manage_options';
			$menu_slug  = isset( $admin_page['menu_slug'] ) ? $admin_page['menu_slug'] : sanitize_title_with_dashes( $admin_page['page_title'] );

			add_menu_page(
				$admin_page['page_title'],
				$admin_page['menu_title'],
				$capability,
				$menu_slug,
				array( $this, 'render' )
			);

			array_push( $this->pages_slugs, $menu_slug );

			if ( isset( $admin_page['sub_menus'] ) && is_array( $admin_page['sub_menus'] ) ) {

				if ( isset( $admin_page['sub_menu_label'] ) ) {
					add_submenu_page(
						$menu_slug,
						$admin_page['sub_menu_label'],
						$admin_page['sub_menu_label'],
						$capability,
						$menu_slug,
						array( $this, 'render' )
					);
				}

				foreach ( $admin_page['sub_menus'] as $sub_page ) {
					$sub_menu_slug = isset( $sub_page['menu_slug'] ) ? $sub_page['menu_slug'] : sanitize_title_with_dashes( $sub_page['page_title'] );

					add_submenu_page(
						$menu_slug,
						$sub_page['page_title'],
						$sub_page['menu_title'],
						$capability,
						$sub_menu_slug,
						array( $this, 'render' )
					);

					array_push( $this->pages_slugs, $sub_menu_slug );
				}
			}
		}
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

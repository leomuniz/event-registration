<?php

namespace leomuniz\Event_Registration\Admin;

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

					$page_slug = sanitize_title_with_dashes( $sub_page['page_title'] );
				}
			}

			$page_slug = sanitize_title_with_dashes( $admin_page['page_title'] );
		}
	}

	/**
	 * Renders the content of the admin page or submenu page.
	 */
	public function render() {
		echo '<div id="page-content" class=""></div>';
	}
}

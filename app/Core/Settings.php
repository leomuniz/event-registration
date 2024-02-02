<?php

declare(strict_types=1);

namespace leomuniz\Event_Registration\Core;

use leomuniz\Event_Registration;
use leomuniz\Event_Registration\Interfaces\Settings_Interface;

/**
 * Class Settings
 *
 * This class represents the settings for the Event Registration plugin.
 * It implements the Settings_Interface.
 */
class Settings implements Settings_Interface {

	private $admin_pages;

	public function __construct() {

		// Admin Pages.
		$this->admin_pages = array(
			array(
				'page_title' => __( 'Events', 'event-registration' ),
				'menu_title' => __( 'Events', 'event-registration' ),
				'capability' => 'manage_options',
				'menu_slug'  => 'lm-event-registration',
				'icon_url'   => 'dashicons-tickets-alt',
				'position'   => 10,
			),
			array(
				'page_type'   => 'sub_menu', // menu_page (default), sub_menu, admin_bar or dashboard_menu.
				'parent_page' => 'lm-event-registration',
				'page_title'  => __( 'All Events', 'event-registration' ),
				'menu_title'  => __( 'All Events', 'event-registration' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'lm-event-registration',
			),
			array(
				'page_type'   => 'sub_menu', // menu_page (default), sub_menu, admin_bar or dashboard_menu.
				'parent_page' => 'lm-event-registration',
				'page_title'  => __( 'Add New Event', 'event-registration' ),
				'menu_title'  => __( 'Add New Event', 'event-registration' ),
				'capability'  => 'manage_options',
				'menu_slug'   => 'lm-new-event',
			)
		);
	}

	/**
	 * Get a specific setting data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting key.
	 *
	 * @return mixed Setting value.
	 */
	public function get( string $name ): mixed {
		return ! empty( $this->{$name} ) ? $this->{$name} : null;
	}

	/**
	 * Check if a specific setting exist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting key.
	 *
	 * @return bool
	 */
	public function has( string $name ): bool {
		return isset( $this->{$name} );
	}
}

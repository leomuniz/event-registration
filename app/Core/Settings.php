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
				'page_title'     => __( 'Event Registration', 'event-registration' ),
				'menu_title'     => __( 'Event Registration', 'event-registration' ),
				'capability'     => 'manage_options',
				'menu_slug'      => 'event-registration',
				'icon_url'       => 'dashicons-tickets-alt',
				'position'       => 2,
				'sub_menu_label' => __( 'All Events', 'event-registration' ),
				'sub_menus'      => array(
					array(
						'page_title' => __( 'Entries', 'event-registration' ),
						'menu_title' => __( 'Entries', 'event-registration' ),
						'capability' => 'manage_options',
						'menu_slug'  => 'entries',
					),
					array(
						'page_title' => __( 'Settings', 'event-registration' ),
						'menu_title' => __( 'Settings', 'event-registration' ),
						'capability' => 'manage_options',
						'menu_slug'  => 'event-registration-settings',
					),
				),
			),
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

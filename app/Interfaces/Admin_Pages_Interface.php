<?php

namespace leomuniz\Event_Registration\Interfaces;

/**
 * Interface Admin_Page
 *
 * @package leomuniz\event-registration
 */
interface Admin_Pages_Interface {
	/**
	 * Register all the admin pages.
	 *
	 * @param string $context     The context of the admin page.
	 * @param array  $admin_pages The admin pages to be created.
	 */
	public function register( string $context = '', array $admin_pages = array() );

	/**
	 * Render the admin page content.
	 */
	public function render();
}

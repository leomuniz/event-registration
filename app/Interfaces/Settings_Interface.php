<?php
/**
 * Interface SettingsInterface
 *
 * Settings Interface.
 *
 * @package leomuniz\event-registration
 */

declare(strict_types=1);

namespace leomuniz\Event_Registration\Interfaces;

interface Settings_Interface {

	/**
	 * Get a specific setting data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting key.
	 *
	 * @return mixed Setting value.
	 */
	public function get( string $name ): mixed;

	/**
	 * Check if a specific setting exist.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Setting key.
	 *
	 * @return bool
	 */
	public function has( string $name ): bool;
}

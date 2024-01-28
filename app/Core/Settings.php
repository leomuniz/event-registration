<?php

declare(strict_types=1);

namespace leomuniz\Event_Registration\Core;

use leomuniz\Event_Registration;
use leomuniz\Event_Registration\Interfaces\Settings_Interface;

class Settings implements Settings_Interface {

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
		return $this->{$name};
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

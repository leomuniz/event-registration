<?php
/**
 * Basic setup to be extended for testing.
 *
 * @package leomuniz\wp-event-registration
 */

declare(strict_types=1);

namespace leomuniz\Event_Registration;

use PHPUnit\Framework\TestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Brain\Monkey;

/**
 * PluginTestCase class.
 * Abstraction for test cases with commom mock functions.
 *
 * @since 1.0.0
 */
class PluginTestCase extends TestCase {
	use MockeryPHPUnitIntegration;

	// phpcs:disable Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed, Generic.CodeAnalysis.UnusedFunctionParameter.Found

	/**
	 * Setup which calls \WP_Mock setup
	 *
	 * @since 1.0.0
	 */
	public function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Teardown which calls \WP_Mock tearDown
	 *
	 * @since 1.0.0
	 */
	public function tearDown(): void {
		Monkey\tearDown();
		parent::tearDown();
	}

	/**
	 * Mock WordPress Options functions => get_option; update_option and delete_option.
	 *
	 * @since 1.0.0
	 */
	public function mock_wp_options_functions(): void {
		Monkey\Functions\when( 'get_option' )->alias(
			static function ( string $key, mixed $defaul_value = false ): mixed {
				global $wp_options;
				return $wp_options[ $key ] ?? $defaul_value;
			}
		);

		Monkey\Functions\when( 'update_option' )->alias(
			static function ( string $key, mixed $value, string|bool $autoload = null ): bool {
				global $wp_options;
				$wp_options[ $key ] = $value;
				return true;
			}
		);

		Monkey\Functions\when( 'delete_option' )->alias(
			static function ( string $key ): bool {
				global $wp_options;
				unset( $wp_options[ $key ] );
				return true;
			}
		);
	}

	/**
	 * Mock WordPress Rewrite API functions.
	 * add_rewrite_tag, add_rewrite_rule and flush_rewrite_rules.
	 *
	 * @since 1.0.0
	 */
	public function mock_rewrite_functions(): void {

		Monkey\Functions\when( 'add_rewrite_rule' )->alias(
			static function ( string $regex, string|array $query, string $after = 'bottom' ): void {
				global $rewrite_rules;
				$rewrite_rules[ $regex ] = $query;
			}
		);

		Monkey\Functions\when( 'add_rewrite_tag' )->alias(
			static function ( string $tag, string $regex, string $query = '' ): void {
				global $rewrite_tags;
				$rewrite_tags[ $tag ] = $regex;
			}
		);

		// We should flush the rewrite rules only once after registering the pages.
		Monkey\Functions\expect( 'flush_rewrite_rules' )->once()->withArgs(
			static function ( bool $hard = true ): bool {
				global $flushed_rewrite_rules, $rewrite_rules;
				$flushed_rewrite_rules = $rewrite_rules;
				return true;
			}
		);
	}

	/**
	 * Mock WordPress assets enqueuing functions.
	 * wp_enqueue_style and wp_enqueue_script.
	 *
	 * @since 1.0.0
	 */
	public function mock_enqueue_functions(): void {
		Monkey\Functions\expect( 'wp_enqueue_style' )->andReturnUsing(
			static function (
				string $handle,
				string $src = '',
				array $deps = array(),
				string|bool $ver = false,
				string $media = 'all'
			): void {
				global $wp_enqueued_styles;

				$wp_enqueued_styles[ $handle ] = array(
					'src'          => $src,
					'dependencies' => $deps,
					'version'      => $ver,
					'media'        => $media,
				);
			}
		);

		Monkey\Functions\expect( 'wp_enqueue_script' )->andReturnUsing(
			static function (
				string $handle,
				string $src = '',
				array $deps = array(),
				string|bool $ver = false,
				array|bool $args = array()
			): void {
				global $wp_enqueued_scripts;

				$wp_enqueued_scripts[ $handle ] = array(
					'src'          => $src,
					'dependencies' => $deps,
					'version'      => $ver,
					'args'         => $args,
				);
			}
		);
	}

	/**
	 * Mock WordPress remote requets functions.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $response_body Response body for wp_remote_retrieve_body().
	 */
	public function mock_request_functions( mixed $response_body = array() ): void {
		Monkey\Functions\when( 'wp_remote_get' )->justReturn( array() );
		Monkey\Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
		Monkey\Functions\when( 'wp_remote_retrieve_body' )->justReturn( $response_body );
	}

	/**
	 * Mock WordPress sanitization functions.
	 *
	 * @since 1.0.0
	 */
	public function mock_sanitization_functions(): void {
		Monkey\Functions\when( 'sanitize_text_field' )->returnArg();
		Monkey\Functions\when( 'sanitize_email' )->returnArg();
		Monkey\Functions\when( 'sanitize_url' )->returnArg();
		Monkey\Functions\when( 'absint' )->returnArg();
	}

	/**
	 * Mock WordPress error on remote requets functions.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $error_message Error message returned by get_error_message().
	 */
	public function mockRequestError( mixed $error_message ): void {
		$response = \Mockery::mock( 'WP_Error' );
		$response->expects()->get_error_message()->andReturn( $error_message );

		Monkey\Functions\when( 'wp_remote_get' )->justReturn( $response );
		Monkey\Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 400 );
		Monkey\Functions\when( 'is_wp_error' )->justReturn( true );
	}
}

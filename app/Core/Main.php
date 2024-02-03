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
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'admin_init', array( $this, 'remove_duplicated_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_js_variables' ) );
		add_filter( 'post_row_actions', array( $this, 'event_row_actions' ), 10, 2 );
	}

	public function register_post_types() {
		register_post_type(
			'lm-events',
			array(
				'labels'                => array(
					'name'          => __( 'Events', 'event-registration' ),
					'singular_name' => __( 'Event', 'event-registration' ),
				),
				'public'                => true,
				'has_archive'           => true,
				'show_in_rest'          => true,
				'supports'              => array(),
				'menu_icon'             => 'dashicons-tickets-alt',
				'show_in_menu'          => true,
				'show_in_nav_menus'     => false,
				'show_in_admin_bar'     => false,
				'capability_type'       => 'post',
				'map_meta_cap'          => true,
				'hierarchical'          => false,
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'query_var'             => true,
				'can_export'            => true,
				'delete_with_user'      => false,
				'show_ui'               => true,
				'rest_base'             => 'lm-events',
				'rest_controller_class' => 'WP_REST_Posts_Controller',
			)
		);
	}

	public function register_blocks() {
		register_block_type( Plugin\DIR . '/build/blocks/copyright-date-block' );
	}

	public function remove_duplicated_admin_menu() {
		remove_submenu_page( 'edit.php?post_type=lm-events', 'post-new.php?post_type=lm-events' );
	}

	function event_row_actions( $actions, $post ) {

		// Remove unneded actions.
		if ( $post->post_type === 'lm-events' ) {
			unset( $actions['view'] );
			unset( $actions['inline hide-if-no-js'] );
		}

		// Tweak the edit link.
		$edit_link       = admin_url( 'edit.php?post_type=lm-events&page=lm-edit-event&post=' . $post->ID );
		$actions['edit'] = '<a href="' . $edit_link . '">' . __( 'Edit', 'event-registration' ) . '</a>';

		return $actions;
	}

	public function admin_js_variables() {

		$page = get_current_screen();

		if ( $page->base !== 'lm-events_page_lm-edit-event' ) {
			return;
		}

		$post_id = isset( $_GET['post'] ) ? absint( $_GET['post'] ) : null;

		if ( $post_id ) {
			$handle = 'event-registration-admin-lm-edit-event-script';

			$post_data = get_post( $post_id );

			wp_localize_script(
				$handle,
				'scriptData',
				array(
					'postData' => $post_data,
				)
			);
		}
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

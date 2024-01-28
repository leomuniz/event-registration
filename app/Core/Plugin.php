<?php
/**
 * Plugin class.
 * Implements a DI Container to load all plugin classes.
 *
 * @package leomuniz\wp-event-registration
 */

declare(strict_types=1);

namespace leomuniz\Event_Registration\Core;

/**
 * Class Plugin.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * Holds all the modules added to the container.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $modules = array();

	/**
	 * Map of all the interfaces implemented by loaded modules.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $interfaces_map = array();

	/**
	 * Execution queue to be processed when calling Plugin::load() method.
	 * Each class added to this queue must also have a load() method.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $load_queue = array();

	/**
	 * Holds the plugin instance.
	 *
	 * @since 1.0.0
	 *
	 * @var Plugin
	 */
	private static $instance;

	/**
	 * Whether a registered module in container has a singleton or not.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private $has_singleton = array();

	/**
	 * Holds the singleton instances.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private static $singletons = array();

	/**
	 * Singleton instance to avoid multiple Plugin instances.
	 *
	 * @since 1.0.0
	 */
	public static function instance(): Plugin {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Adds a module to the DI container.
	 * Uses a closure to create the classes instances with auto-wiring.
	 *
	 * @since 1.0.0
	 *
	 * @param string $id        Class to be added to the container.
	 * @param bool   $singleton Whether it should be a singleton or allow many instances.
	 */
	public function register( string $id, bool $singleton = true ) {
		$interface = class_implements( $id );

		if ( ! empty( $interface ) ) {
			// Currently replaces only the first interface extended by a class.
			$this->interfaces_map[ array_values( $interface )[0] ] = $id;
		}

		$this->has_singleton[ $id ] = $singleton;

		/**
		 * Closure to be executed when getting the module with Plugin::get().
		 *
		 * @since 1.0.0
		 *
		 * @param string $id        Class to be added to the container.
		 * @param bool   $singleton Whether it should be a singleton or allow many instances.
		 */
		$this->modules[ $id ] = static function () use ( $id, $singleton ): mixed {

			if ( $singleton && isset( self::$singletons[ $id ] ) ) {
				return self::$singletons[ $id ];
			}

			$class        = self::load_class( $id );
			$dependencies = self::load_dependencies( $class );

			if ( empty( $dependencies ) ) {
				return $class->newInstance();
			}

			$dependencies = self::resolve_dependencies( $dependencies );

			return $class->newInstanceArgs( $dependencies );
		};
	}

	/**
	 * Loads the ReflectionClass instance of a class to be loaded in the container.
	 * Static because it's called by the closure in register().
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name Class to be added to the container.
	 *
	 * @throws \Exception if it is not a valid class or if it's an interface or an abstract class.
	 *
	 * @return \ReflectionClass
	 */
	public static function load_class( string $class_name ): \ReflectionClass {
		try {
			$reflector = new \ReflectionClass( $class_name );
		} catch ( \ReflectionException $exception ) {
			throw new \Exception( esc_html( "Class [$class_name] not found." ), 0, esc_html( $exception ) );
		}

		// If it's an Interface or Abstract Class, drop.
		if ( ! $reflector->isInstantiable() ) {
			throw new \Exception( esc_html( "[$class_name] is not instantiable." ) );
		}

		return $reflector;
	}

	/**
	 * Prepare the class dependencies to be loaded.
	 * Static because it's called by the closure in register().
	 *
	 * @since 1.0.0
	 *
	 * @param \ReflectionClass $class_obj ReflectionClass of a valid class.
	 *
	 * @return array of the class __construct() method parameters.
	 */
	public static function load_dependencies( \ReflectionClass $class_obj ): array {

		$constructor = $class_obj->getConstructor();

		// No constructor, no dependencies.
		if ( $constructor === null ) {
			return array();
		}

		return $constructor->getParameters();
	}

	/**
	 * Loads dependencies to be used by the executed class.
	 * Static because it's called by the closure in register().
	 *
	 * @since 1.0.0
	 *
	 * @param array $parameters Class __construct() method parameters.
	 *
	 * @return array of the class __construct() method parameters.
	 */
	public static function resolve_dependencies( array $parameters ): array {
		$dependencies = array();

		foreach ( $parameters as $parameter ) {
			$type = $parameter->getType();

			// Not a class, interface or trait.
			if ( ! $type instanceof \ReflectionNamedType || $type->isBuiltin() ) {
				$dependencies[] = self::builtin_default_value( $parameter );
				continue;
			}

			$plugin = self::instance();
			$name   = $type->getName();
			$class  = new \ReflectionClass( $name );

			$name = ! $class->isInterface() ?: $plugin->interfaces_map[ $name ]; // phpcs:ignore Universal.Operators.DisallowShortTernary.Found

			$dependency     = $plugin->get( $name );
			$dependencies[] = $dependency;
		}

		return $dependencies;
	}

	/**
	 * Checks builtin parameters from a Class __construct method.
	 * A built-in type is any type that is not a class, interface, or trait.
	 * This method is Static because it's called by the static resolve_dependencies() method.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $parameter Non-class parameter to be resolved.
	 *
	 * @throws \Exception if the param does not have a default value or is not variadic.
	 *
	 * @return mixed Parameter default value or empty array for variadic params.
	 */
	public static function builtin_default_value( mixed $parameter ): mixed {
		if ( $parameter->isDefaultValueAvailable() ) {
			return $parameter->getDefaultValue();
		}

		if ( $parameter->isVariadic() ) { // Variadic ...param.
			return array();
		}

		// If it doesn't have a default value or is variadic, just throw an error.
		// Future improvement: maybe get non-class parameter value from a Settings class.
		$class_name    = $parameter->getDeclaringClass()->getName();
		$error_message = "Could not resolve dependency [$parameter] in class {$class_name}";
		throw new \Exception( esc_html( $error_message ) );
	}

	/**
	 * Get a class in the container to be loaded.
	 * Executes the prepared closure when calling register().
	 *
	 * @since 1.0.0
	 *
	 * @param string $id Class to be loaded.
	 *
	 * @throws \Exception if the class was not registered or is not found.
	 *
	 * @return mixed The instance of the class (singleton or not).
	 */
	public function get( string $id ): object {
		if ( ! isset( $this->modules[ $id ] ) ) {
			throw new \Exception( esc_html( "Target module [$id] not found." ) );
		}

		$module = $this->modules[ $id ];

		if ( $this->has_singleton[ $id ] ) {
			self::$singletons[ $id ] = $module( $this );
		}

		return self::$singletons[ $id ] ?? $module( $this );
	}

	/**
	 * Adds a class to the execution queue.
	 * All classes in the load_queue array need to have a load() method.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class_name Class to be executed. It must have load() method.
	 */
	public function execute( string $class_name ) {
		$this->load_queue[] = $class_name;
	}

	/**
	 * Start the engine. Loads the plugin (the DI Container).
	 *
	 * @since 1.0.0
	 */
	public function load() {
		foreach ( $this->load_queue as $class_name ) {
			$this->get( $class_name )->load();
		}
	}
}

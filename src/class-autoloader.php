<?php
/**
 * Autoloader class
 *
 * @package Moonwalking_Bits
 * @author Martin Pettersson
 * @license GPL-3.0
 * @since 0.1.0
 */

namespace Moonwalking_Bits;

/**
 * Class used to autoload classes following the WordPress coding standard.
 *
 * @since 0.1.0
 */
class Autoloader {
	/**
	 * A list of mappings between namespaces and their root directories.
	 *
	 * @var array
	 */
	private array $namespace_mappings = array();

	/**
	 * Resolves a file from the class name and includes it if possible.
	 *
	 * @since 0.1.0
	 *
	 * @param string $class_name Qualified class name.
	 * @return bool True if the file was found and included.
	 */
	public function load_class( string $class_name ): bool {
		$class_file = $this->get_class_file( $class_name );

		if ( ! is_null( $class_file ) ) {
			require_once $class_file;

			return true;
		}

		return false;
	}

	/**
	 * Adds a mapping from a namespace to a directory.
	 *
	 * @since 0.1.0
	 *
	 * @param string $namespace Unqualified/qualified namespace name.
	 * @param string $directory Namespace root directory.
	 */
	public function add_namespace_mapping( string $namespace, string $directory ): void {
		$normalized_namespace = rtrim( $namespace, '\\' );
		$normalized_directory = rtrim( $directory, '/' );

		$this->namespace_mappings[ $normalized_namespace ] = array_unique(
			array_merge(
				$this->namespace_mappings[ $normalized_namespace ] ?? array(),
				array( $normalized_directory )
			)
		);
	}

	/**
	 * Resolves a file from the class name and returns it.
	 *
	 * Will use the registered namespace mappings to try to resolve the file and
	 * will return the first match.
	 *
	 * @param string $class_name Qualified class name.
	 * @return string|null The class file if found.
	 */
	private function get_class_file( string $class_name ): ?string {
		foreach ( $this->namespace_mappings as $namespace => $directories ) {
			$class_file = $this->resolve_class_file( $class_name, $namespace, $directories );

			if ( ! is_null( $class_file ) ) {
				return $class_file;
			}
		}

		return null;
	}

	/**
	 * Resolves a file from the class name and returns it.
	 *
	 * @param string $class_name  Qualified class name.
	 * @param string $namespace   Unqualified/qualified namespace name.
	 * @param array  $directories Namespace root directories.
	 * @return string|null The class file if found.
	 */
	private function resolve_class_file( string $class_name, string $namespace, array $directories ): ?string {
		$class_namespace = $this->extract_namespace( $class_name );

		if ( strlen( $namespace ) > 0 && false === strpos( $class_namespace, $namespace ) ) {
			return null;
		}

		foreach ( $directories as $directory ) {
			$path       = $this->path_from_namespace( (string) substr( $class_namespace, strlen( $namespace ) + 1 ) );
			$file_name  = $this->file_name_from_class_name( $this->extract_class( $class_name ) );
			$class_file = str_replace( '//', '/', "{$directory}/{$path}/{$file_name}" );

			if ( is_readable( $class_file ) ) {
				return $class_file;
			}
		}

		return null;
	}

	/**
	 * Extracts the namespace portion of the class name.
	 *
	 * @param string $class_name Qualified class name.
	 * @return string Unqualified/qualified namespace name.
	 */
	private function extract_namespace( string $class_name ): string {
		$last_delimiter = strrpos( $class_name, '\\' );

		return false !== $last_delimiter ? (string) substr( $class_name, 0, $last_delimiter ) : '';
	}

	/**
	 * Extracts the class portion of the class name.
	 *
	 * @param string $class_name Qualified class name.
	 * @return string Unqualified class name.
	 */
	private function extract_class( string $class_name ): string {
		$last_delimiter = strrpos( $class_name, '\\' );

		return false !== $last_delimiter ? (string) substr( $class_name, $last_delimiter + 1 ) : $class_name;
	}

	/**
	 * Converts a namespace to a path.
	 *
	 * @param string $namespace Unqualified/qualified namespace name.
	 * @return string File path.
	 */
	private function path_from_namespace( string $namespace ): string {
		return strtolower( str_replace( array( '\\', '_' ), array( '/', '-' ), $namespace ) );
	}

	/**
	 * Converts a class to a file.
	 *
	 * @param string $class_name Unqualified class name.
	 * @return string File name.
	 */
	private function file_name_from_class_name( string $class_name ): string {
		$file_name = strtolower( str_replace( '_', '-', $class_name ) );

		return "class-{$file_name}.php";
	}
}

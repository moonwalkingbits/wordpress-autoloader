<?php

namespace Moonwalking_Bits;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \Moonwalking_Bits\Autoloader
 */
class Autoloader_Test extends TestCase {
	private Autoloader $autoloader;

	/**
	 * @before
	 */
	public function register_autoloader(): void {
		$this->autoloader = new Autoloader();

		spl_autoload_register( array( $this->autoloader, 'load_class' ) );
	}

	/**
	 * @after
	 */
	public function unregister_autoloader(): void {
		spl_autoload_unregister( array( $this->autoloader, 'load_class' ) );
	}

	/**
	 * @test
	 */
	public function class_should_not_be_loaded_by_default(): void {
		$this->assertFalse(class_exists('Wordpress\\Test_Class'));
	}

	/**
	 * @test
	 */
	public function should_fail_if_class_not_found(): void {
		$this->autoloader->add_namespace_mapping('Wordpress', __DIR__ );
		$this->autoloader->add_namespace_mapping('Other_Namespace', __DIR__ );

		$this->assertFalse(class_exists('Wordpress\\Test_Class'));
	}

	/**
	 * @test
	 */
	public function should_load_class(): void {
		$this->autoloader->add_namespace_mapping('Wordpress', __DIR__ . '/fixtures/' );

		$this->assertTrue(class_exists('Wordpress\\Test_Class'));
	}

	/**
	 * @test
	 */
	public function should_load_nested_class(): void {
		$this->autoloader->add_namespace_mapping('Wordpress', __DIR__ . '/fixtures/' );

		$this->assertTrue(class_exists('Wordpress\\Nested\\Test_Class'));
	}

	/**
	 * @test
	 */
	public function should_accept_namespace_with_trailing_delimiter(): void {
		$this->autoloader->add_namespace_mapping('Wordpress\\', __DIR__ . '/fixtures/' );

		$this->assertTrue(class_exists('Wordpress\\Test_Class'));
	}

	/**
	 * @test
	 */
	public function should_accept_nested_namespace(): void {
		$this->autoloader->add_namespace_mapping('Wordpress\\Nested\\', __DIR__ . '/fixtures/nested/' );

		$this->assertTrue(class_exists('Wordpress\\Nested\\Test_Class'));
	}

	/**
	 * @test
	 */
	public function should_accept_empty_namespace(): void {
		$this->autoloader->add_namespace_mapping('', __DIR__ . '/fixtures/empty/' );

		$this->assertTrue(class_exists('Test_Class'));
	}

	/**
	 * @test
	 */
	public function should_merge_namespace_mappings(): void {
		$this->autoloader->add_namespace_mapping('Wordpress\\Nested\\', __DIR__ );
		$this->autoloader->add_namespace_mapping('Wordpress\\Nested\\', __DIR__ . '/fixtures/nested/' );

		$this->assertTrue(class_exists('Wordpress\\Nested\\Test_Class'));
	}
}

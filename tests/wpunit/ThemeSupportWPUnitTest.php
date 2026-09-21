<?php

namespace NewfoldLabs\WP\Module\TenWeb;

/**
 * WPUnit tests for ThemeSupport.
 *
 * @coversDefaultClass \NewfoldLabs\WP\Module\TenWeb\ThemeSupport
 */
class ThemeSupportWPUnitTest extends \lucatume\WPBrowser\TestCase\WPTestCase {

	/**
	 * Stylesheet filter stub.
	 *
	 * @var callable|null
	 */
	private $stylesheet_stub;

	/**
	 * Template filter stub.
	 *
	 * @var callable|null
	 */
	private $template_stub;

	/**
	 * Remove theme slug stubs after each test.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		if ( $this->stylesheet_stub ) {
			\remove_filter( 'stylesheet', $this->stylesheet_stub, 99 );
			$this->stylesheet_stub = null;
		}
		if ( $this->template_stub ) {
			\remove_filter( 'template', $this->template_stub, 99 );
			$this->template_stub = null;
		}
		parent::tearDown();
	}

	/**
	 * Stub get_stylesheet()/get_template() to the given slugs.
	 *
	 * @param string $stylesheet Active stylesheet slug.
	 * @param string $template   Parent template slug.
	 * @return void
	 */
	private function stub_theme_slugs( string $stylesheet, string $template ): void {
		$this->stylesheet_stub = static function () use ( $stylesheet ) {
			return $stylesheet;
		};
		$this->template_stub   = static function () use ( $template ) {
			return $template;
		};
		\add_filter( 'stylesheet', $this->stylesheet_stub, 99 );
		\add_filter( 'template', $this->template_stub, 99 );
	}

	/**
	 * Non-WVC themes should not be treated as WVC editor sites.
	 *
	 * @covers ::is_wvc_theme_active
	 * @return void
	 */
	public function test_is_wvc_theme_active_false_for_non_wvc_theme(): void {
		$this->stub_theme_slugs( 'twentytwentyfive', 'twentytwentyfive' );

		$this->assertFalse( ThemeSupport::is_wvc_theme_active() );
	}

	/**
	 * Active wvc-theme should be detected.
	 *
	 * @covers ::is_wvc_theme_active
	 * @return void
	 */
	public function test_is_wvc_theme_active_true_when_stylesheet_is_wvc_theme(): void {
		$this->stub_theme_slugs( ThemeSupport::WVC_THEME_SLUG, ThemeSupport::WVC_THEME_SLUG );

		$this->assertTrue( ThemeSupport::is_wvc_theme_active() );
	}

	/**
	 * Child themes with a wvc-theme parent should be detected.
	 *
	 * @covers ::is_wvc_theme_active
	 * @return void
	 */
	public function test_is_wvc_theme_active_true_when_only_template_is_wvc_theme(): void {
		$this->stub_theme_slugs( 'wvc-child', ThemeSupport::WVC_THEME_SLUG );

		$this->assertTrue( ThemeSupport::is_wvc_theme_active() );
	}
}

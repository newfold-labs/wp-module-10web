<?php

namespace NewfoldLabs\WP\Module\TenWeb;

/**
 * WPUnit tests for TenWeb feature toggles.
 *
 * @coversNothing
 */
class TenWebFeaturesWPUnitTest extends \lucatume\WPBrowser\TestCase\WPTestCase {

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
	 * Admin restrictions should not be toggleable off-theme.
	 *
	 * @return void
	 */
	public function test_admin_restrictions_feature_not_toggleable_off_theme(): void {
		$this->stub_theme_slugs( 'twentytwentyfive', 'twentytwentyfive' );

		$feature = new TenWebAdminRestrictionsFeature();

		$this->assertFalse( $feature->isTogglable() );
	}

	/**
	 * Admin restrictions should be toggleable on WVC sites.
	 *
	 * @return void
	 */
	public function test_admin_restrictions_feature_toggleable_on_wvc_theme(): void {
		$this->stub_theme_slugs( ThemeSupport::WVC_THEME_SLUG, ThemeSupport::WVC_THEME_SLUG );

		$feature = new TenWebAdminRestrictionsFeature();

		$this->assertTrue( $feature->isTogglable() );
	}

	/**
	 * Editor support should not be toggleable off-theme.
	 *
	 * @return void
	 */
	public function test_editor_support_feature_not_toggleable_off_theme(): void {
		$this->stub_theme_slugs( 'twentytwentyfive', 'twentytwentyfive' );

		$feature = new TenWebEditorSupportFeature();

		$this->assertFalse( $feature->isTogglable() );
	}

	/**
	 * Editor support should be toggleable on WVC sites.
	 *
	 * @return void
	 */
	public function test_editor_support_feature_toggleable_on_wvc_theme(): void {
		$this->stub_theme_slugs( ThemeSupport::WVC_THEME_SLUG, ThemeSupport::WVC_THEME_SLUG );

		$feature = new TenWebEditorSupportFeature();

		$this->assertTrue( $feature->isTogglable() );
	}
}

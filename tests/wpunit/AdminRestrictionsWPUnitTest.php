<?php

namespace NewfoldLabs\WP\Module\TenWeb;

use NewfoldLabs\WP\ModuleLoader\Container;
use NewfoldLabs\WP\ModuleLoader\Plugin;

/**
 * WPUnit tests for AdminRestrictions.
 *
 * @coversDefaultClass \NewfoldLabs\WP\Module\TenWeb\AdminRestrictions
 */
class AdminRestrictionsWPUnitTest extends \lucatume\WPBrowser\TestCase\WPTestCase {

	/**
	 * Brand plugin basename used for approved-plugin enforcement tests.
	 */
	private const BRAND_PLUGIN_BASENAME = 'akismet/akismet.php';

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
	 * Deactivate plugins activated during a test.
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

		\deactivate_plugins( array( 'hello-dolly/hello.php', self::BRAND_PLUGIN_BASENAME ) );
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
	 * Build an AdminRestrictions instance with a brand plugin basename.
	 *
	 * @return AdminRestrictions
	 */
	private function create_restrictions(): AdminRestrictions {
		$container = new Container();
		$container->set(
			'plugin',
			new Plugin(
				array(
					'basename' => self::BRAND_PLUGIN_BASENAME,
				)
			)
		);

		return new AdminRestrictions( $container );
	}

	/**
	 * Restrictions should stay off for non-WVC themes.
	 *
	 * @covers ::should_apply_restrictions
	 * @return void
	 */
	public function test_should_apply_restrictions_false_when_not_wvc_theme(): void {
		$this->stub_theme_slugs( 'twentytwentyfive', 'twentytwentyfive' );

		$restrictions = $this->create_restrictions();

		$this->assertFalse( $restrictions->should_apply_restrictions() );
	}

	/**
	 * Restrictions should apply when wvc-theme is active.
	 *
	 * @covers ::should_apply_restrictions
	 * @return void
	 */
	public function test_should_apply_restrictions_true_when_wvc_theme_active(): void {
		$this->stub_theme_slugs( ThemeSupport::WVC_THEME_SLUG, ThemeSupport::WVC_THEME_SLUG );

		$restrictions = $this->create_restrictions();

		$this->assertTrue( $restrictions->should_apply_restrictions() );
	}

	/**
	 * Unapproved plugins should be deactivated on admin init.
	 *
	 * @covers ::enforce_approved_plugins
	 * @return void
	 */
	public function test_enforce_approved_plugins_deactivates_unapproved_plugin(): void {
		$this->stub_theme_slugs( ThemeSupport::WVC_THEME_SLUG, ThemeSupport::WVC_THEME_SLUG );

		\activate_plugin( self::BRAND_PLUGIN_BASENAME );
		\activate_plugin( 'hello-dolly/hello.php' );

		$restrictions = $this->create_restrictions();
		$restrictions->enforce_approved_plugins();

		$this->assertFalse( \is_plugin_active( 'hello-dolly/hello.php' ) );
		$this->assertTrue( \is_plugin_active( self::BRAND_PLUGIN_BASENAME ) );
	}

	/**
	 * Direct requests to plugins.php should redirect to the dashboard.
	 *
	 * @covers ::block_restricted_admin_pages
	 * @return void
	 */
	public function test_block_restricted_admin_pages_redirects_plugins_screen(): void {
		$this->stub_theme_slugs( ThemeSupport::WVC_THEME_SLUG, ThemeSupport::WVC_THEME_SLUG );

		global $pagenow;
		// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		$pagenow = 'plugins.php';

		$restrictions = $this->create_restrictions();

		\add_filter(
			'wp_redirect',
			static function ( $location ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
				throw new \RuntimeException( (string) $location );
			}
		);

		try {
			$restrictions->block_restricted_admin_pages();
			$this->fail( 'Expected redirect' );
		} catch ( \RuntimeException $e ) {
			$this->assertStringContainsString( 'wp-admin/index.php', $e->getMessage() );
		}
	}
}

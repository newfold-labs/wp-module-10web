<?php
namespace NewfoldLabs\WP\Module\TenWeb;

use NewfoldLabs\WP\ModuleLoader\Container;

/**
 * Loads TenWeb editor support assets on the WVC editor admin screen.
 */
class TenWeb {

	/**
	 * Script handle for the editor support bundle.
	 *
	 * @var string
	 */
	public static $handle = 'nfd-tenweb-editor-support';

	/**
	 * Dependency injection container.
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Constructor.
	 *
	 * @param Container $container The module container.
	 */
	public function __construct( Container $container ) {
		$this->container = $container;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_editor_scripts' ) );
	}

	/**
	 * Enqueue PostHog session replay on the WVC editor admin screen.
	 *
	 * @return void
	 */
	public function enqueue_editor_scripts() {
		$screen = get_current_screen();
		if ( ! $screen || ! isset( $screen->id ) || false === strpos( $screen->id, 'wvc-editor' ) ) {
			return;
		}

		$asset_file = NFD_TENWEB_BUILD_DIR . '/editor-support/index.asset.php';
		if ( ! is_readable( $asset_file ) ) {
			return;
		}

		$asset = require $asset_file;

		wp_enqueue_script(
			self::$handle,
			NFD_TENWEB_BUILD_URL . '/editor-support/index.js',
			$asset['dependencies'],
			$asset['version'],
			false
		);
	}
}

<?php
namespace NewfoldLabs\WP\Module\TenWeb;

/**
 * Feature flag for TenWeb editor PostHog session replay.
 */
class TenWebEditorSupportFeature extends \NewfoldLabs\WP\Module\Features\Feature {

	/**
	 * The feature name.
	 *
	 * @var string
	 */
	protected $name = 'tenwebEditorSupport';

	/**
	 * The feature value. Defaults to on.
	 *
	 * @var boolean
	 */
	protected $value = true;

	/**
	 * TenWeb editor support is only meaningful on WVC editor sites.
	 *
	 * @return bool
	 */
	public function canToggle() {
		return parent::canToggle() && ThemeSupport::is_wvc_theme_active();
	}

	/**
	 * Initialize editor support assets when the feature is enabled.
	 */
	public function initialize() {
		if ( function_exists( 'add_action' ) ) {
			add_action(
				'plugins_loaded',
				function () {
					new EditorSupport();
				}
			);
		}
	}
}

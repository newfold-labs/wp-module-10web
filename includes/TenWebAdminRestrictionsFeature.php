<?php
namespace NewfoldLabs\WP\Module\TenWeb;

use function NewfoldLabs\WP\ModuleLoader\container as getContainer;

/**
 * Feature flag for TenWeb admin restrictions.
 */
class TenWebAdminRestrictionsFeature extends \NewfoldLabs\WP\Module\Features\Feature {

	/**
	 * The feature name.
	 *
	 * @var string
	 */
	protected $name = 'tenwebAdminRestrictions';

	/**
	 * The feature value. Defaults to on.
	 *
	 * @var boolean
	 */
	protected $value = true;

	/**
	 * TenWeb admin toggles are only meaningful on WVC editor sites.
	 *
	 * @return bool
	 */
	public function canToggle() {
		return parent::canToggle() && ThemeSupport::is_wvc_theme_active();
	}

	/**
	 * Initialize admin restrictions when the feature is enabled.
	 */
	public function initialize() {
		if ( function_exists( 'add_action' ) ) {
			add_action(
				'plugins_loaded',
				function () {
					new AdminRestrictions( getContainer() );
				}
			);
		}
	}
}

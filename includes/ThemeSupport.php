<?php
namespace NewfoldLabs\WP\Module\TenWeb;

/**
 * Helpers for detecting the 10Web WVC theme.
 */
class ThemeSupport {

	/**
	 * Approved theme slug.
	 *
	 * @var string
	 */
	const WVC_THEME_SLUG = 'wvc-theme';

	/**
	 * Whether the active or parent theme directory is the 10Web WVC theme.
	 *
	 * @return bool
	 */
	public static function is_wvc_theme_active() {
		return self::WVC_THEME_SLUG === get_stylesheet()
			|| self::WVC_THEME_SLUG === get_template();
	}
}

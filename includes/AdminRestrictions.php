<?php
namespace NewfoldLabs\WP\Module\TenWeb;

use NewfoldLabs\WP\ModuleLoader\Container;

/**
 * Restricts theme switching and plugin access for 10Web AI editor sites.
 */
class AdminRestrictions {

	/**
	 * Approved theme slug.
	 *
	 * @var string
	 */
	const APPROVED_THEME = 'wvc-theme';

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

		if ( ! $this->should_apply_restrictions() ) {
			return;
		}

		add_action( 'admin_menu', array( $this, 'remove_admin_menus' ), 999 );
		add_action( 'admin_init', array( $this, 'block_restricted_admin_pages' ) );
		add_filter( 'map_meta_cap', array( $this, 'restrict_capabilities' ), 10, 4 );
		add_filter( 'option_active_plugins', array( $this, 'filter_active_plugins' ) );
		add_filter( 'all_plugins', array( $this, 'filter_all_plugins' ) );
		add_filter( 'pre_set_theme', array( $this, 'prevent_theme_switch' ), 10, 2 );
	}

	/**
	 * Whether admin restrictions should run on this site.
	 *
	 * @return bool
	 */
	public function should_apply_restrictions() {
		if ( self::APPROVED_THEME !== get_template() ) {
			return false;
		}

		/**
		 * Filter whether TenWeb admin restrictions are enabled.
		 *
		 * @param bool $enabled Whether restrictions are enabled.
		 */
		return (bool) apply_filters( 'nfd_tenweb_admin_restrictions_enabled', true );
	}

	/**
	 * Get approved plugin basenames.
	 *
	 * @return string[]
	 */
	public function get_approved_plugin_basenames() {
		$approved = array(
			'wordpress-seo/wp-seo.php',
			'wordpress-seo-premium/wp-seo-premium.php',
			'woocommerce/woocommerce.php',
			'wp-plugin-payments-shipping/wp-plugin-payments-shipping.php',
			'google-site-kit/google-site-kit.php',
			'akismet/akismet.php',
			'hello.php',
			$this->container->plugin()->basename, // brand plugin
		);

		/**
		 * Filter the list of approved plugin basenames.
		 *
		 * @param string[] $approved Approved plugin basenames.
		 */
		$approved = apply_filters( 'nfd_tenweb_approved_plugins', $approved );

		return array_values( array_unique( array_filter( $approved ) ) );
	}

	/**
	 * Remove theme and plugin admin menus.
	 *
	 * @return void
	 */
	public function remove_admin_menus() {
		remove_submenu_page( 'themes.php', 'themes.php' );
		remove_submenu_page( 'themes.php', 'theme-install.php' );
		remove_submenu_page( 'themes.php', 'theme-editor.php' );
		remove_menu_page( 'plugins.php' );
	}

	/**
	 * Redirect direct requests to restricted admin pages.
	 *
	 * @return void
	 */
	public function block_restricted_admin_pages() {
		global $pagenow;

		$restricted_pages = array(
			'themes.php',
			'theme-install.php',
			'plugins.php',
			'plugin-install.php',
			'plugin-editor.php',
		);

		if ( ! in_array( $pagenow, $restricted_pages, true ) ) {
			return;
		}

		wp_safe_redirect( admin_url() );
		exit;
	}

	/**
	 * Remove capabilities required for theme and plugin management.
	 *
	 * @param string[] $caps    Required capabilities.
	 * @param string   $cap     Capability being checked.
	 * @param int      $user_id User ID.
	 * @param array    $args    Additional arguments.
	 * @return string[]
	 */
	public function restrict_capabilities( $caps, $cap, $user_id, $args ) { // phpcs:ignore Generic.CodeAnalysis.UnusedFunctionParameter.FoundAfterLastUsed -- map_meta_cap callback signature.
		$restricted_caps = array(
			'switch_themes',
			'edit_themes',
			'install_themes',
			'delete_themes',
			'activate_plugins',
			'install_plugins',
			'delete_plugins',
			'edit_plugins',
			'update_plugins',
		);

		if ( ! in_array( $cap, $restricted_caps, true ) ) {
			return $caps;
		}

		$caps[] = 'do_not_allow';

		return $caps;
	}

	/**
	 * Limit active plugins to the approved set.
	 *
	 * @param mixed $plugins Active plugin basenames.
	 * @return mixed
	 */
	public function filter_active_plugins( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			return $plugins;
		}

		$approved = $this->get_approved_plugin_basenames();

		return array_values( array_intersect( $plugins, $approved ) );
	}

	/**
	 * Limit the plugins list shown in WP Admin.
	 *
	 * @param array $plugins All installed plugins.
	 * @return array
	 */
	public function filter_all_plugins( $plugins ) {
		if ( ! is_array( $plugins ) ) {
			return $plugins;
		}

		$approved = $this->get_approved_plugin_basenames();

		return array_intersect_key( $plugins, array_flip( $approved ) );
	}

	/**
	 * Prevent switching away from the approved theme.
	 *
	 * @param string $theme     Requested theme slug.
	 * @param string $old_theme Previous theme slug.
	 * @return string
	 */
	public function prevent_theme_switch( $theme, $old_theme ) {
		if ( self::APPROVED_THEME === $theme ) {
			return $theme;
		}

		if ( ! empty( $old_theme ) ) {
			return $old_theme;
		}

		return self::APPROVED_THEME;
	}
}

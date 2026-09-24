<?php
/**
 * Bootstrap file for wpunit tests.
 *
 * @package NewfoldLabs\WP\Module\TenWeb
 */

$module_root = dirname( dirname( __DIR__ ) );

require_once $module_root . '/vendor/autoload.php';

if ( ! defined( 'NFD_TENWEB_DIR' ) ) {
	define( 'NFD_TENWEB_DIR', $module_root );
}
if ( ! defined( 'NFD_TENWEB_BUILD_DIR' ) ) {
	define( 'NFD_TENWEB_BUILD_DIR', $module_root . '/build' );
}
if ( ! defined( 'NFD_TENWEB_BUILD_URL' ) ) {
	define( 'NFD_TENWEB_BUILD_URL', 'https://test.local/vendor/newfold-labs/wp-module-10web/build' );
}

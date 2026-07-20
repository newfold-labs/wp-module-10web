<?php

use NewfoldLabs\WP\ModuleLoader\Container;
use NewfoldLabs\WP\Module\TenWeb\TenWeb;
use function NewfoldLabs\WP\ModuleLoader\register;

/**
 * TenWeb module for integrating 10Web features into the plugin.
 * Note: Class namespace uses "TenWeb" (PSR-4 compliance); brand references use "10Web".
 */

if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! defined( 'NFD_TENWEB_MODULE_VERSION' ) ) {
	define( 'NFD_TENWEB_MODULE_VERSION', '1.0.0' );
}

if ( function_exists( 'add_action' ) ) {

	add_action(
		'plugins_loaded',
		function () {
			register(
				array(
					'name'     => 'tenweb',
					'label'    => __( 'TenWeb', 'newfold-tenweb-module' ),
					'callback' => function ( Container $container ) {
						if ( ! defined( 'NFD_TENWEB_DIR' ) ) {
							define( 'NFD_TENWEB_DIR', __DIR__ );
						}
						if ( ! defined( 'NFD_TENWEB_BUILD_DIR' ) ) {
							define( 'NFD_TENWEB_BUILD_DIR', __DIR__ . '/build' );
						}
						if ( ! defined( 'NFD_TENWEB_BUILD_URL' ) ) {
							define(
								'NFD_TENWEB_BUILD_URL',
								$container->plugin()->url . 'vendor/newfold-labs/wp-module-10web/build'
							);
						}

						return new TenWeb( $container );
					},
					'isActive' => true,
					'isHidden' => true,
				)
			);
		}
	);

}

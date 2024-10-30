<?php
/*
 *  Plugin Name: Medianova CDN
 *  Text Domain: Medianova CDN
 *  Description: Integrate our Content Delivery Network(CDN) into your WordPress site.
 *  Author: Medianova CDN
 *  Author URI: https://www.medianova.com
 *  License: GPLv2 or later
 *  Version: 2.2
 */

/* Check for illegal request and quit */
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

/* Constants for projectt */
define('CDN_MEDIANOVA_FILE', __FILE__);
define('CDN_MEDIANOVA_DIR', dirname(__FILE__));
define('CDN_MEDIANOVA_BASE', plugin_basename(__FILE__));
define('CDN_MEDIANOVA_DEFAULT_DIRECTORIES', "wp-content,wp-includes");
define('CDN_MEDIANOVA_DEFAULT_EXCLUDED', ".php");
define('CDN_MEDIANOVA_MIN_WP', '4.6');

/* Load plugin main classs */
add_action( 'plugins_loaded', [ 'Cdn_Medianova', 'instance' ] );

/* Load plugin main style.css */
function CDN_Medianova_my_admin_scripts() {
    wp_enqueue_style( 'style',  plugins_url( 'medianova-cdn/assets/css/style.css?v=1', '' ));
}

/* action for loading css */
add_action( 'admin_enqueue_scripts', 'CDN_Medianova_my_admin_scripts' );

/* Activation plugin */
register_activation_hook( __FILE__, [ 'Cdn_Medianova', 'call_activation_hook' ] );

/* Delete plugin */
register_uninstall_hook( __FILE__, [ 'Cdn_Medianova', 'call_uninstall_hook' ] );

/* load inits */
spl_autoload_register('CDN_Medianova_autoload');

/* load classes */
function CDN_Medianova_autoload($class) {
	if ( in_array($class, ['Cdn_Medianova', 'Cdn_Medianova_Change', 'Cdn_Medianova_Options']) ) {
		require_once(
		sprintf(
			'%s/classes/%s.php',
			CDN_MEDIANOVA_DIR,
			$class
		)
		);
	}
}
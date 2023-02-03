<?php
/**
 * WordPress to Ghost exporter
 *
 * @package   Ghost
 * @author    Ghost Foundation
 * @license   GPL-2.0+
 * @link      http://ghost.org
 * @copyright 2014 Ghost Foundation
 *
 * @ghost
 * Plugin Name: Ghost
 * Plugin URI:  http://ghost.org
 * Description: Plugin to export your WordPress blog so you can import it into your Ghost installation
 * Version:     1.2.0
 * Author:      Ghost Foundation
 * Author URI:  http://ghost.org
 * Text Domain: wp2ghost
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Plug the `wp_get_current_user` function, which isn't normally available in plugins
if ( !function_exists( 'wp_get_current_user' ) ) { include(ABSPATH . 'wp-includes/pluggable.php'); }

// If the user is an `administrator`, init the plugin
if ( current_user_can( 'administrator' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'class-ghost.php' );

	// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
	register_activation_hook( __FILE__, array( 'Ghost', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'Ghost', 'deactivate' ) );

	Ghost::get_instance();
}

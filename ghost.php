<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that also follow
 * WordPress coding standards and PHP best practices.
 *
 * @package   Ghost
 * @author    Ghost Foundation
 * @license   GPL-2.0+
 * @link      http://ghost.org
 * @copyright 2013 Ghost Foundation
 *
 * @ghost
 * Plugin Name: Ghost
 * Plugin URI:  http://ghost.org
 * Description: Plugin to export your WordPress blog so you can import it into your Ghost installation
 * Version:     0.3.0
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

// TODO: replace `class-plugin-name.php` with the name of the actual plugin's class file
require_once( plugin_dir_path( __FILE__ ) . 'class-ghost.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
// TODO: replace PluginName with the name of the plugin defined in `class-plugin-name.php`
register_activation_hook( __FILE__, array( 'Ghost', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Ghost', 'deactivate' ) );

// TODO: replace PluginName with the name of the plugin defined in `class-plugin-name.php`
Ghost::get_instance();

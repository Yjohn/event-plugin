<?php
/**
 * Event Functionality Custom Post Type.
 *
 * @package     yohannes\EventFunctionality
 * @author      yohannes
 * @copyright   2018 Code your future
 * @license     GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Event Functionality Custom Post Type
 * Plugin URI:  https://github.com/CodeYourFuture/src-website/tree/master/plugins
 * Description: Adds a new Events section and custom post type.
 * Version:     1.0
 * Author:      yohannes
 * Author URI:  https://codeyourfuture.io
 * Text Domain: events-functionality
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

namespace yohannes\EventFunctionality;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Access denied.' );
}

/**
 * Setup the plugin's constants.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_constants() {
	$plugin_url = plugin_dir_url( __FILE__ );
	if ( is_ssl() ) {
		$plugin_url = str_replace( 'http://', 'https://', $plugin_url );
	}

	//OPTIMIZE using constants like these is not recommended by WP Theme review team
	define( 'EVENT_FUNCTIONALITY_URL', $plugin_url );
	define( 'EVENT_FUNCTIONALITY_DIR', plugin_dir_path( __DIR__ ) );
}

/**
 * Initialize the plugin hooks
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_hooks() {
	register_activation_hook( __FILE__, __NAMESPACE__ . '\flush_rewrites' );
	register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
}

/**
 * Flush the rewrites.
 *
 * @since 1.0.0
 *
 * @return void
 */
function flush_rewrites() {
	init_autoloader();

	src\event_cpt_functionality();

	flush_rewrite_rules();
}

/**
 * Kick off the plugin by initializing the plugin files.
 *
 * @since 1.0.0
 *
 * @return void
 */
function init_autoloader() {
	require_once( 'src/support/autoloader.php' );

	Support\autoload_files( __DIR__ . '/src/' );
}

/**
 * Launch the plugin
 *
 * @since 1.0.0
 *
 * @return void
 */
function launch() {
	init_constants();
	init_hooks();
	init_autoloader();
}

launch();
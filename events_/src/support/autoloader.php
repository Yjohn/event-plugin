<?php
/**
 * File autoloader functionality
 *
 * @package     CarmeMias\EventsFunctionality\Support
 * @since       1.0.0
 * @author      carmemias
 * @link        http://carmemias.com
 * @license     GNU General Public License 2.0+
 */
namespace CarmeMias\EventsFunctionality\Support;

/**
 * Load all of the plugin's files.
 *
 * @since 1.0.0
 *
 * @param string $src_root_dir Root directory for the source files
 *
 * @return void
 */
function autoload_files( $src_root_dir ) {

	$filenames = array(
		 'event_cpt_definition'
		//  'event_cpt_shortcode',
		//  'event_cpt_info_meta_box'
	);

	foreach( $filenames as $filename ) {
		include_once( $src_root_dir . $filename . '.php' );
	}
}

<?php
/**
 * File autoloader functionality
 *
 * @package     CarmeMias\FAQsFunctionality\Support
 * @since       1.0.0
 * @author      carmemias
 * @link        http://carmemias.com
 * @license     GNU General Public License 2.0+
 */
namespace CarmeMias\FAQsFunctionality\Support;

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
		 'cm-faq-cpt-definition',
		 'cm-faq-shortcode',
		 'cm-faq-info-meta-box'
	);

	foreach( $filenames as $filename ) {
		include_once( $src_root_dir . $filename . '.php' );
	}
}

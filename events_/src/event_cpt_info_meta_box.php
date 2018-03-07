<?php
/**
 * Meta Box functionality to display the Events shortcode info in the Post/Page Edit pages.
 *
 * @package     CarmeMias\EventsFunctionality\src
 * @author      carmemias
 * @copyright   2017 Carme Mias Studio
 * @license     GPL-2.0+
 *
 */

namespace CarmeMias\EventsFunctionality\src;

add_action( 'add_meta_boxes_post', __NAMESPACE__.'\add_shortcode_info', 10, 2);
add_action( 'add_meta_boxes_page', __NAMESPACE__.'\add_shortcode_info', 10, 2);

function add_shortcode_info($post){
	add_meta_box(
		'shortcode-info', //metabox id
		__('Shortcode Information', 'events-functionality'),
		__NAMESPACE__.'\render_shortcode_info_view',
		['post', 'page'],
		'side',
		'low'
	);
}

function render_shortcode_info_view(){
	global $post;
	
	// Noncename needed to verify where the data originated
	//echo '<input type="hidden" name="shortcode_info_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';
	
 	require_once plugin_dir_path(__FILE__).'views/cm_event_info_admin_view.php';
}
?>
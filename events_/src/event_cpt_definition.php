<?php
/**
 * Events Custom Post Type.
 *
 * @package     yohannes\EventsFunctionality\src
 * @author      yohannes
 * @copyright   2018 Yjohn
 * @license     GPL-2.0+
 *
 */

namespace yohannes\EventsFunctionality\src;

add_action( 'init', __NAMESPACE__ . '\event_cpt' );
/**
 * Register CPT `event_event`
 * See http://web-profile.net/wordpress/docs/custom-post-types/
 * See https://codex.wordpress.org/Function_Reference/register_post_type
 */
function event_cpt_functionality() { //see https://codex.wordpress.org/Function_Reference/post_type_supports
	$features = get_all_post_type_features('post', array( #list of excluded features. See lines 59 and 88
		'trackbacks',
		'custom-fields',
		'revisions',
		'author',
		'post-formats'
	));

	register_post_type( 'event_cpt', array(
		'labels'  => array(
				'name' => __( 'Events', 'Events CPT general name' , 'events-functionality'),
				'singular_name' => __( 'Event', 'Events CPT singular name' , 'events-functionality'),
				'all_items' => __('All Events'),
 		    'add_new_item' => __('Add New Event', 'events-functionality'),
 		    'edit_item' => __('Edit Event', 'events-functionality'),
 		    'new_item' => __('New Event', 'events-functionality'),
			'view_item' => __('View Event', 'events-functionality'),
			'featued_image' => __('Event image', 'events-functionality'),
			'set_featued_image' => __('Choose Event image', 'events-functionality'),
			'remove_featued_image' => __('Remove Event image', 'events-functionality'),
			'use_featued_image' => __('Use Event image', 'events-functionality'),
 		    'view_items' => __('View Events', 'events-functionality'),
 		    'search_items' => __('Search Event', 'events-functionality'),
 		    'not_found' => __( 'No Events found.', 'events-functionality' ),
 		   	'not_found_in_trash' => __( 'No Events found in Trash.', 'events-functionality' )
		),
		'public' => true,
		'show_ui' => true,
		'has_archive' => true, #this means it'll have an "index/loop" page
		'rewrite' => array(
			'slug' => __( 'events', 'CPT permalink slug', 'event_cpt'),
			'with_front' => false,
		),
		'menu_icon'   => 'dashicons-images-alt2',
		'menu_position' => 20,
		'supports' => $features, #see line 21
		'taxonomies' => array('event-type')
		// 'register_meta_box_cb' => __NAMESPACE__.'\cm_events_add_meta_box'
	));

	register_taxonomy('event-type', 'cm_event', array(
		'hierarchical' => true,
		'show_in_nav_menus' => false,
		'labels' => array(
			'name' => __('Event type'),
			'singular_name' => __('Event type'),
			'all_items' => __('All Event types'),
			'edit_item' => __('Edit Event type'),
			'view_item' => __('View Event type'),
			'update_item' => __('Update Event type'),
			'add_new_item' => __('Add new Event type'),
			'new_item_name' => __('New Event type'),
			'popular_items' => __('Most used Event types'),
			'separate_items_with_commas' => __('Separate Event types with commas'),
			'add_or_remove_items' => __('Add or remove Event types'),
			'choose_from_most_used' => __('Choose from most used Event types'),
		)
	) ); //categories will be set as "children classes" and "teacher training"
}

/**
* Get all the post type features for the given post type
* @param string $post_type - given post type
* @param array $exclude_features - array of features to exclude
* @return array
**/
function get_all_post_type_features($post_type = 'post', $excluded_features = array()){
	#see https://knowthecode.io/labs/custom-post-type-basics#configure-features
	$raw_features = get_all_post_type_supports($post_type);

	if(!$excluded_features){return array_keys($raw_features);}

	$included_features = array();
	foreach($raw_features as $key => $value){
		if(!in_array($key, $excluded_features)){
			$included_features[]=$key;
		}
	}
	return $included_features;
}

add_filter( 'post_updated_messages',  __NAMESPACE__ . '\event_cpt_updated_messages' );

/*
* cm_event metabox
*/
// function cm_events_add_meta_box(){
// 		add_meta_box( 'cm_event_meta', 'Event order', __NAMESPACE__.'\render_cm_event_metabox', 'cm_event', 'side', 'default' );
// }

// function render_cm_event_metabox(){
// 	global $post;

// 	// Noncename needed to verify where the data originated
// 	echo '<input type="hidden" name="cm_event_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

//  	require_once plugin_dir_path(__FILE__).'views/cm_event_meta_admin_view.php';
// }

// function save_cm_event_meta($post_id, $post){
// 	if ( ! isset( $_POST['cm_event_noncename'] ) ) { return; }
// 	// verify this came from the our screen and with proper authorization, because save_post can be triggered at other times
// 	if( !wp_verify_nonce( $_POST['cm_event_noncename'], plugin_basename(__FILE__) ) ) {
// 						return $post->ID;}

// 	// is the user allowed to edit the post or page?
// 	if( ! current_user_can( 'edit_post', $post->ID )){
// 						return $post->ID;}

// 	// ok, we're authenticated: we need to find and save the data. We'll put it into an array to make it easier to loop through
// 	$event_meta['_cm_event_order'] = $_POST['_cm_event_order'];

// 	// Add values of $events_meta as custom fields
// 	foreach ($event_meta as $key => $value) { // Cycle through the $classes_meta array!
// 		if( $post->post_type == 'revision' ) return; // Don't store custom data twice

// 		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)

// 		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
// 		     update_post_meta($post->ID, $key, $value);
// 		} else { // If the custom field doesn't have a value
// 		     add_post_meta($post->ID, $key, $value);
// 		}

// 		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
// 	}
// }

// add_action('save_post', __NAMESPACE__.'\save_cm_event_meta', 1, 2);


/**
 * Events update messages.
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function event_cpt_updated_messages( $messages ) {
	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );

	$messages['event_cpt'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'Events updated.', 'events-functionality' ),
		2  => __( 'Custom field updated.', 'events-functionality' ),
		3  => __( 'Custom field deleted.', 'events-functionality' ),
		4  => __( 'Events updated.', 'events-functionality' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Event restored to revision from %s', 'events-functionality' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'Event published.', 'events-functionality' ),
		7  => __( 'Event saved.', 'events-functionality' ),
		8  => __( 'Event submitted.', 'events-functionality' ),
		9  => sprintf(
			__( 'Event scheduled for: <strong>%1$s</strong>.', 'events-functionality' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i', 'events-functionality' ), strtotime( $post->post_date ) )
		),
		10 => __( 'Event draft updated.', 'events-functionality' )
	);

	if ( $post_type_object->publicly_queryable && 'cm_event' === $post_type ) {
		$permalink = get_permalink( $post->ID );

		$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View Event', 'events-functionality' ) );
		$messages[ $post_type ][1] .= $view_link;
		$messages[ $post_type ][6] .= $view_link;
		$messages[ $post_type ][9] .= $view_link;

		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
		$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview Event', 'events-functionality' ) );
		$messages[ $post_type ][8]  .= $preview_link;
		$messages[ $post_type ][10] .= $preview_link;
	}

	return $messages;
}


add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\event_cpt_bulk_updated_messages', 10, 2 );
/**
 * Events bulk update messages.
 * See https://themeforest.net/forums/thread/custom-post-type-change-delete-message/114401
 * @param array $messages Existing post bulk update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function event_cpt_bulk_updated_messages( $bulk_messages, $bulk_counts ) {

    $bulk_messages['event_cpt'] = array(
        'updated'   => __( '%s Event updated.', '%s Events updated.', $bulk_counts['updated'] ),
        'locked'    => __( '%s Event not updated, somebody is editing it.', '%s Events not updated, somebody is editing them.', $bulk_counts['locked'] ),
        'deleted'   => __( '%s Event permanently deleted.', '%s Events permanently deleted.', $bulk_counts['deleted'] ),
        'trashed'   => __( '%s Event moved to the Bin.', '%s Events moved to the Bin.', $bulk_counts['trashed'] ),
        'untrashed' => __( '%s Event restored from the Bin.', '%s Events restored from the Bin.', $bulk_counts['untrashed'] ),
    );

    return $bulk_messages;

}

/***********************************/
/*    DISPLAY IN Events LIST TABLE   */
/***********************************/

// see http://shibashake.com/wordpress-theme/expand-the-wordpress-quick-edit-menu
// add_filter('manage_cm_event_posts_columns', __NAMESPACE__ .'\cm_event_add_custom_columns');
/*
* Add a new column for Event order in the Events List table.
*/
// function cm_event_add_custom_columns($columns) {
// 	//remove Date column from its default position
// 	$date = $columns['date'];
// 	unset($columns['date']);

//     $columns['cm_event_order'] = 'Order';
// 	$columns['cm_event-category'] = 'Category';

//     //Add the Date column again to the end of the table
// 	$columns['date'] = $date;

//     return $columns;
// }

// add_action( 'manage_posts_custom_column' , __NAMESPACE__ .'\cm_event_custom_columns', 10, 2 );
/*
* Display the metaboxes value in the Post List table
* See https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/manage_wordpress_posts_using_bulk_edit_and_quick_edit.php line 169
*/
// function cm_event_custom_columns( $column, $post_id ) {
// 	switch ( $column ) {
// 		case 'cm_event_order':
// 			echo '<div id="cm_event_order-' . $post_id . '">' . get_post_meta( $post_id, '_cm_event_order', true ) . '</div>';
// 			break;
// 		case 'cm_event-category':
// 			$terms = get_the_terms( $post_id, 'event-category' );
// 			$terms_list = '';
// 			if($terms) {
// 				foreach ( $terms as $term ) {$terms_list = $terms_list.$term->name.'</br>';}
// 			} else {
// 				$terms_list = 'not yet set </br>';
// 			}
// 			echo '<div id="cm_event-category-' . $post_id . '">' . $terms_list . '</div>';
// 			break;
// 	}
// }


// add_filter( 'manage_edit-cm_event_sortable_columns', __NAMESPACE__ .'\cm_event_order_sortable_column' );
// /*
// * Make new Post Priority column sortable
// */

// function cm_event_order_sortable_column( $columns ) {

// 	$columns['cm_event_order'] = 'cm_event_order';

//     return $columns;
// }



// add_action( 'pre_get_posts', __NAMESPACE__ .'\cm_event_order_orderby_backend' );
// /*
// * Priority sorting instructions for the backend only (front end is set in home.php)
// */
// function cm_event_order_orderby_backend( $query ) {
//     if( ! is_admin() )
//         return;

//     $orderby = $query->get( 'orderby');

//     if( 'cm_event_order' == $orderby ) {
// 		$query->set('meta_key','_cm_event_order');
//         $query->set('orderby','meta_value_num');
//     }
// }

/***********************************/
/*         QUICK EDIT MENU         */
/***********************************/

// add_action('quick_edit_custom_box',  __NAMESPACE__ .'\cm_event_add_metabox_to_quick_edit', 10, 2);
// /*
// * Add Priority and Highlighted metaboxes to Quick Edit Menu
// */
// function cm_event_add_metabox_to_quick_edit($column_name, $post_type) {
//     if ( !in_array( $column_name, array( 'cm_event_order') ) )
//         return;

// 	require_once plugin_dir_path(__FILE__).'views/cm_event_order_meta_quick_edit_view.php';
// }


// add_action('save_post', __NAMESPACE__ .'\cm_event_save_metabox_quick_edit_data', 1, 2);
//  /*
//  * Save new Event order value, attributed through the Quick Edit menu.
//  */
// function cm_event_save_metabox_quick_edit_data($post_id, $post) {
// 	//not to be run for new events.
// 	if (function_exists('get_current_screen')){
// 		$current_screen = get_current_screen(); //was clashing with other plugins without this
// 		if( null != $current_screen){
// 			$current_action = get_current_screen()->action;
// 			if( 'add' == $current_action )
// 				return;
// 		} else {return;}
// 	}

// 	$post_type = get_post_type( $post );
//     if ( !( 'cm_event' == $post_type) )
//         return;
//     // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
//     // to do anything

//     if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
//         return $post_id;
//     // Check permissions
//     if ( !current_user_can( 'edit_post', $post_id ) )
//         return $post_id;

// 	 // ok, we're authenticated: we need to find and save the data. We'll put it into an array to make it easier to loop through
// 		$event_meta['_cm_event_order'] = $_POST['_cm_event_order'];

// 	// Add value as custom fields
// 	foreach ($event_meta as $key => $value) { // Cycle through the array
// 		if( $post->post_type == 'revision' ) return; // Don't store custom data twice

// 		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)

// 		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
// 		     update_post_meta($post->ID, $key, $value);
// 		} else { // If the custom field doesn't already have a value or it has changed
// 		     add_post_meta($post->ID, $key, $value);
// 		}

// 		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
// 	}
// }

// add_action('admin_notices', __NAMESPACE__.'\cm_events_admin_notice');
// /*
// * Show a warning notice in the Event Edit page if the Event's current order value is higher than the number of EventS.
// */
// function cm_events_admin_notice(){
//     global $pagenow;
// 	global $post;

// 	//this notice should not even be attempted in admin sections other than cm_event edit.
// 	if( ('post.php'!=$pagenow) || ($post->post_type != 'cm_event') ){
// 		return;
// 	}

// 	$cm_event_order = get_post_meta($post->ID, '_cm_event_order', true);
// 	$current_num_events = wp_count_posts('cm_event')->publish;
// 	$display_warning = isset($cm_event_order) && ($cm_event_order != '10000') && (intval($cm_event_order)>intval($current_num_events));

//     if( $display_warning ) {		//TODO make translatable
//          echo '<div class="notice notice-warning is-dismissible">
//              <p>This Event\'s current order is '.$cm_event_order.' but there are only '.$current_num_events.' published Events. The Event will still show work but its order value will not be listed in the dropdown list further down this page.</p>
//          </div>';
//     }
// }


/*
* Enqueue javascript for the Events Quick Edit functionality
* See https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/manage_wordpress_posts_using_bulk_edit_and_quick_edit.php AND https://developer.wordpress.org/reference/functions/wp_enqueue_script/*/

// add_action( 'admin_print_scripts-edit.php', __NAMESPACE__ . '\cm_event_metabox_enqueue_admin_scripts' );

// function cm_event_metabox_enqueue_admin_scripts() {
// 	wp_register_script( 'cm_event_populate_metabox_scripts', Event_FUNCTIONALITY_URL . '/src/assets/js/cm_event_metabox_populate_quick_edit.js', array( 'jquery', 'inline-edit-post' ), false, false );
// 	wp_enqueue_script( 'cm_event_populate_metabox_scripts' );
// 	}

<?php
/**
 * FAQs Custom Post Type.
 *
 * @package     CarmeMias\FAQsFunctionality\src
 * @author      carmemias
 * @copyright   2017 Carme Mias Studio
 * @license     GPL-2.0+
 *
 */

namespace CarmeMias\FAQsFunctionality\src;

add_action( 'init', __NAMESPACE__ . '\cm_faq_cpt' );
/**
 * Register CPT `faq_faq`
 * See http://web-profile.net/wordpress/docs/custom-post-types/
 * See https://codex.wordpress.org/Function_Reference/register_post_type
 */
function cm_faq_cpt() { //see https://codex.wordpress.org/Function_Reference/post_type_supports
	$features = get_all_post_type_features('post', array( #list of excluded features. See lines 59 and 88
		'excerpt',
		'trackbacks',
		'custom-fields',
		'comments',
		'revisions',
		'author',
		'thumbnail',
		'post-formats'
	));

	//TO BE USED WITH GENESIS CHILD THEME. See https://carriedils.com/genesis-2-0-archive-settings-custom-post-types/
	//add support for Genesis archive template
	//array_push($features, 'genesis-seo', 'genesis-cpt-archives-settings');

	register_post_type( 'cm_faq', array(
		'labels'  => array(
				'name' => __( 'FAQs', 'faqs CPT general name' , 'faqs-functionality'),
				'singular_name' => __( 'FAQ', 'faqs CPT singular name' , 'faqs-functionality'),
				'all_items' => __('All FAQs'),
 		    'add_new_item' => __('Add New Question', 'faqs-functionality'),
 		    'edit_item' => __('Edit Question', 'faqs-functionality'),
 		    'new_item' => __('New Question', 'faqs-functionality'),
 		    'view_item' => __('View Question', 'faqs-functionality'),
 		    'view_items' => __('View Questions', 'faqs-functionality'),
 		    'search_items' => __('Search Question', 'faqs-functionality'),
 		    'not_found' => __( 'No questions found.', 'faqs-functionality' ),
 		   	'not_found_in_trash' => __( 'No questions found in Trash.', 'faqs-functionality' )
		),
		'public' => true,
		'show_ui' => true,
		'has_archive' => true, #this means it'll have an "index/loop" page
		'rewrite' => array(
			'slug' => __( 'faqs', 'CPT permalink slug', 'cm_faq'),
			'with_front' => false,
		),
		'menu_icon'   => 'dashicons-megaphone',
		'menu_position' => 20,
		'supports' => $features, #see line 21
		'taxonomies' => array('faq-category'),
		'register_meta_box_cb' => __NAMESPACE__.'\cm_faqs_add_meta_box'
	));

	register_taxonomy('faq-category', 'cm_faq', array(
		'hierarchical' => true,
		'show_in_nav_menus' => false,
		'labels' => array(
			'name' => __('FAQ categories'),
			'singular_name' => __('FAQ category'),
			'all_items' => __('All FAQ categories'),
			'edit_item' => __('Edit FAQ category'),
			'view_item' => __('View FAQ category'),
			'update_item' => __('Update FAQ category'),
			'add_new_item' => __('Add new FAQ category'),
			'new_item_name' => __('New FAQ category'),
			'popular_items' => __('Most used FAQ categories'),
			'separate_items_with_commas' => __('Separate FAQ categories with commas'),
			'add_or_remove_items' => __('Add or remove FAQ categories'),
			'choose_from_most_used' => __('Choose from most used FAQ categories'),
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

add_filter( 'post_updated_messages',  __NAMESPACE__ . '\cm_faq_updated_messages' );

/*
* cm_faq metabox
*/
function cm_faqs_add_meta_box(){
		add_meta_box( 'cm_faq_meta', 'FAQ order', __NAMESPACE__.'\render_cm_faq_metabox', 'cm_faq', 'side', 'default' );
}

function render_cm_faq_metabox(){
	global $post;

	// Noncename needed to verify where the data originated
	echo '<input type="hidden" name="cm_faq_noncename" value="' . wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

 	require_once plugin_dir_path(__FILE__).'views/cm_faq_meta_admin_view.php';
}

function save_cm_faq_meta($post_id, $post){
	if ( ! isset( $_POST['cm_faq_noncename'] ) ) { return; }
	// verify this came from the our screen and with proper authorization, because save_post can be triggered at other times
	if( !wp_verify_nonce( $_POST['cm_faq_noncename'], plugin_basename(__FILE__) ) ) {
						return $post->ID;}

	// is the user allowed to edit the post or page?
	if( ! current_user_can( 'edit_post', $post->ID )){
						return $post->ID;}

	// ok, we're authenticated: we need to find and save the data. We'll put it into an array to make it easier to loop through
	$faq_meta['_cm_faq_order'] = $_POST['_cm_faq_order'];

	// Add values of $events_meta as custom fields
	foreach ($faq_meta as $key => $value) { // Cycle through the $classes_meta array!
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice

		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)

		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
		     update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't have a value
		     add_post_meta($post->ID, $key, $value);
		}

		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}
}

add_action('save_post', __NAMESPACE__.'\save_cm_faq_meta', 1, 2);


/**
 * FAQs update messages.
 *
 * @param array $messages Existing post update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function cm_faq_updated_messages( $messages ) {
	$post             = get_post();
	$post_type        = get_post_type( $post );
	$post_type_object = get_post_type_object( $post_type );

	$messages['cm_faq'] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => __( 'FAQs updated.', 'faqs-functionality' ),
		2  => __( 'Custom field updated.', 'faqs-functionality' ),
		3  => __( 'Custom field deleted.', 'faqs-functionality' ),
		4  => __( 'FAQs updated.', 'faqs-functionality' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'FAQ restored to revision from %s', 'faqs-functionality' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => __( 'FAQ published.', 'faqs-functionality' ),
		7  => __( 'FAQ saved.', 'faqs-functionality' ),
		8  => __( 'FAQ submitted.', 'faqs-functionality' ),
		9  => sprintf(
			__( 'FAQ scheduled for: <strong>%1$s</strong>.', 'faqs-functionality' ),
			// translators: Publish box date format, see http://php.net/date
			date_i18n( __( 'M j, Y @ G:i', 'faqs-functionality' ), strtotime( $post->post_date ) )
		),
		10 => __( 'FAQ draft updated.', 'faqs-functionality' )
	);

	if ( $post_type_object->publicly_queryable && 'cm_faq' === $post_type ) {
		$permalink = get_permalink( $post->ID );

		$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View FAQ', 'faqs-functionality' ) );
		$messages[ $post_type ][1] .= $view_link;
		$messages[ $post_type ][6] .= $view_link;
		$messages[ $post_type ][9] .= $view_link;

		$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
		$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview FAQ', 'faqs-functionality' ) );
		$messages[ $post_type ][8]  .= $preview_link;
		$messages[ $post_type ][10] .= $preview_link;
	}

	return $messages;
}


add_filter( 'bulk_post_updated_messages', __NAMESPACE__ . '\cm_faq_bulk_updated_messages', 10, 2 );
/**
 * FAQs bulk update messages.
 * See https://themeforest.net/forums/thread/custom-post-type-change-delete-message/114401
 * @param array $messages Existing post bulk update messages.
 *
 * @return array Amended post update messages with new CPT update messages.
 */
function cm_faq_bulk_updated_messages( $bulk_messages, $bulk_counts ) {

    $bulk_messages['cm_faq'] = array(
        'updated'   => __( '%s FAQ updated.', '%s FAQs updated.', $bulk_counts['updated'] ),
        'locked'    => __( '%s FAQ not updated, somebody is editing it.', '%s FAQs not updated, somebody is editing them.', $bulk_counts['locked'] ),
        'deleted'   => __( '%s FAQ permanently deleted.', '%s FAQs permanently deleted.', $bulk_counts['deleted'] ),
        'trashed'   => __( '%s FAQ moved to the Bin.', '%s FAQs moved to the Bin.', $bulk_counts['trashed'] ),
        'untrashed' => __( '%s FAQ restored from the Bin.', '%s FAQs restored from the Bin.', $bulk_counts['untrashed'] ),
    );

    return $bulk_messages;

}

/***********************************/
/*    DISPLAY IN FAQs LIST TABLE   */
/***********************************/

// see http://shibashake.com/wordpress-theme/expand-the-wordpress-quick-edit-menu
add_filter('manage_cm_faq_posts_columns', __NAMESPACE__ .'\cm_faq_add_custom_columns');
/*
* Add a new column for FAQ order in the FAQs List table.
*/
function cm_faq_add_custom_columns($columns) {
	//remove Date column from its default position
	$date = $columns['date'];
	unset($columns['date']);

    $columns['cm_faq_order'] = 'Order';
	$columns['cm_faq-category'] = 'Category';

    //Add the Date column again to the end of the table
	$columns['date'] = $date;

    return $columns;
}

add_action( 'manage_posts_custom_column' , __NAMESPACE__ .'\cm_faq_custom_columns', 10, 2 );
/*
* Display the metaboxes value in the Post List table
* See https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/manage_wordpress_posts_using_bulk_edit_and_quick_edit.php line 169
*/
function cm_faq_custom_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'cm_faq_order':
			echo '<div id="cm_faq_order-' . $post_id . '">' . get_post_meta( $post_id, '_cm_faq_order', true ) . '</div>';
			break;
		case 'cm_faq-category':
			$terms = get_the_terms( $post_id, 'faq-category' );
			$terms_list = '';
			if($terms) {
				foreach ( $terms as $term ) {$terms_list = $terms_list.$term->name.'</br>';}
			} else {
				$terms_list = 'not yet set </br>';
			}
			echo '<div id="cm_faq-category-' . $post_id . '">' . $terms_list . '</div>';
			break;
	}
}


add_filter( 'manage_edit-cm_faq_sortable_columns', __NAMESPACE__ .'\cm_faq_order_sortable_column' );
/*
* Make new Post Priority column sortable
*/

function cm_faq_order_sortable_column( $columns ) {

	$columns['cm_faq_order'] = 'cm_faq_order';

    return $columns;
}



add_action( 'pre_get_posts', __NAMESPACE__ .'\cm_faq_order_orderby_backend' );
/*
* Priority sorting instructions for the backend only (front end is set in home.php)
*/
function cm_faq_order_orderby_backend( $query ) {
    if( ! is_admin() )
        return;

    $orderby = $query->get( 'orderby');

    if( 'cm_faq_order' == $orderby ) {
		$query->set('meta_key','_cm_faq_order');
        $query->set('orderby','meta_value_num');
    }
}

/***********************************/
/*         QUICK EDIT MENU         */
/***********************************/

add_action('quick_edit_custom_box',  __NAMESPACE__ .'\cm_faq_add_metabox_to_quick_edit', 10, 2);
/*
* Add Priority and Highlighted metaboxes to Quick Edit Menu
*/
function cm_faq_add_metabox_to_quick_edit($column_name, $post_type) {
    if ( !in_array( $column_name, array( 'cm_faq_order') ) )
        return;

	require_once plugin_dir_path(__FILE__).'views/cm_faq_order_meta_quick_edit_view.php';
}


add_action('save_post', __NAMESPACE__ .'\cm_faq_save_metabox_quick_edit_data', 1, 2);
 /*
 * Save new FAQ order value, attributed through the Quick Edit menu.
 */
function cm_faq_save_metabox_quick_edit_data($post_id, $post) {
	//not to be run for new faqs.
	if (function_exists('get_current_screen')){
		$current_screen = get_current_screen(); //was clashing with other plugins without this
		if( null != $current_screen){
			$current_action = get_current_screen()->action;
			if( 'add' == $current_action )
				return;
		} else {return;}
	}

	$post_type = get_post_type( $post );
    if ( !( 'cm_faq' == $post_type) )
        return;
    // verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
    // to do anything

    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE )
        return $post_id;
    // Check permissions
    if ( !current_user_can( 'edit_post', $post_id ) )
        return $post_id;

	 // ok, we're authenticated: we need to find and save the data. We'll put it into an array to make it easier to loop through
		$faq_meta['_cm_faq_order'] = $_POST['_cm_faq_order'];

	// Add value as custom fields
	foreach ($faq_meta as $key => $value) { // Cycle through the array
		if( $post->post_type == 'revision' ) return; // Don't store custom data twice

		$value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)

		if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
		     update_post_meta($post->ID, $key, $value);
		} else { // If the custom field doesn't already have a value or it has changed
		     add_post_meta($post->ID, $key, $value);
		}

		if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
	}
}

add_action('admin_notices', __NAMESPACE__.'\cm_faqs_admin_notice');
/*
* Show a warning notice in the FAQ Edit page if the FAQ's current order value is higher than the number of FAQS.
*/
function cm_faqs_admin_notice(){
    global $pagenow;
	global $post;

	//this notice should not even be attempted in admin sections other than cm_faq edit.
	if( ('post.php'!=$pagenow) || ($post->post_type != 'cm_faq') ){
		return;
	}

	$cm_faq_order = get_post_meta($post->ID, '_cm_faq_order', true);
	$current_num_faqs = wp_count_posts('cm_faq')->publish;
	$display_warning = isset($cm_faq_order) && ($cm_faq_order != '10000') && (intval($cm_faq_order)>intval($current_num_faqs));

    if( $display_warning ) {		//TODO make translatable
         echo '<div class="notice notice-warning is-dismissible">
             <p>This FAQ\'s current order is '.$cm_faq_order.' but there are only '.$current_num_faqs.' published FAQs. The FAQ will still show work but its order value will not be listed in the dropdown list further down this page.</p>
         </div>';
    }
}


/*
* Enqueue javascript for the FAQs Quick Edit functionality
* See https://github.com/bamadesigner/manage-wordpress-posts-using-bulk-edit-and-quick-edit/blob/master/manage_wordpress_posts_using_bulk_edit_and_quick_edit.php AND https://developer.wordpress.org/reference/functions/wp_enqueue_script/*/

add_action( 'admin_print_scripts-edit.php', __NAMESPACE__ . '\cm_faq_metabox_enqueue_admin_scripts' );

function cm_faq_metabox_enqueue_admin_scripts() {
	wp_register_script( 'cm_faq_populate_metabox_scripts', FAQ_FUNCTIONALITY_URL . '/src/assets/js/cm_faq_metabox_populate_quick_edit.js', array( 'jquery', 'inline-edit-post' ), false, false );
	wp_enqueue_script( 'cm_faq_populate_metabox_scripts' );
	}

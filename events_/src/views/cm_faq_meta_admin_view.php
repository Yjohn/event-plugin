<?php
namespace CarmeMias\FAQsFunctionality\src\views;

	//$custom = get_post_custom($post->ID);
	// Get the data if its already been entered
	$cm_faq_order = get_post_meta($post->ID, '_cm_faq_order', true);
	$current_num_faqs = wp_count_posts('cm_faq')->publish;
	//if in add new screen, increase num of faqs by 1.
	$screen = get_current_screen();
	if('add' == $screen->action){ $current_num_faqs++;}

?>
<style>
	.cm_faq_admin_notice select {background-color:#ffb900}
</style>

<table class="form-table"> <!-- TODO make translatable -->
	<tr>
		<td>Choose the question's position in the list:</br><em>Low numbers show first.</em></td>
	</tr>
	<tr style="border-bottom: 1px solid #eee;">
		<td <?php if(($cm_faq_order != '10000')&&(intval($cm_faq_order) > intval($current_num_faqs))){?>class="cm_faq_admin_notice"<?php } ?>>
			<label>FAQ Order:</label>
			 <select id="_cm_faq_order" name="_cm_faq_order" class="" value="<?php echo $cm_faq_order; ?>">
			  <option value="not set">Select order...</option>
			  <option value="hidden">Don't show</option>
			  <option value="10000" <?php if($cm_faq_order && $cm_faq_order == '10000') { ?> selected <?php } //end if ?>>Bottom of the list</option>
          	<?php for ( $i=1; $i <= $current_num_faqs; $i++ ) { ?>
          		<option value="<?php echo $i; ?>" <?php if($cm_faq_order && $cm_faq_order == $i) { ?> selected <?php } //end if ?> > <?php echo $i; ?> </option>
              <?php } //end for loop ?>
			</select>
		</td><!-- FAQ order -->
	</tr>
</table>

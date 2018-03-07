<?php
namespace CarmeMias\EventsFunctionality\src\views;

	//$custom = get_post_custom($post->ID);
	// Get the data if its already been entered
	$cm_event_order = get_post_meta($post->ID, '_cm_event_order', true);
	$current_num_events = wp_count_posts('cm_event')->publish;
	//if in add new screen, increase num of events by 1.
	$screen = get_current_screen();
	if('add' == $screen->action){ $current_num_events++;}

?>
<style>
	.cm_event_admin_notice select {background-color:#ffb900}
</style>

<table class="form-table"> <!-- TODO make translatable -->
	<tr>
		<td>Choose the Event's position in the list:</br><em>Low numbers show first.</em></td>
	</tr>
	<tr style="border-bottom: 1px solid #eee;">
		<td <?php if(($cm_event_order != '10000')&&(intval($cm_event_order) > intval($current_num_events))){?>class="cm_event_admin_notice"<?php } ?>>
			<label>Event Order:</label>
			 <select id="_cm_event_order" name="_cm_event_order" class="" value="<?php echo $cm_event_order; ?>">
			  <option value="not set">Select order...</option>
			  <option value="hidden">Don't show</option>
			  <option value="10000" <?php if($cm_event_order && $cm_event_order == '10000') { ?> selected <?php } //end if ?>>Bottom of the list</option>
          	<?php for ( $i=1; $i <= $current_num_events; $i++ ) { ?>
          		<option value="<?php echo $i; ?>" <?php if($cm_event_order && $cm_event_order == $i) { ?> selected <?php } //end if ?> > <?php echo $i; ?> </option>
              <?php } //end for loop ?>
			</select>
		</td><!-- event order -->
	</tr>
</table>

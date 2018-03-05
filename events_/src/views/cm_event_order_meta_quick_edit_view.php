<?php
namespace CarmeMias\EventsFunctionality\src\views;

	$current_num_events = wp_count_posts('cm_event')->publish;
?>

<fieldset class="inline-edit-col-right"> <!-- TODO make translatable -->
    <div class="inline-edit-col">
        <span class="title">Event Order</span>
        <input type="hidden" name="cm_event_noncename" id="cm_event_noncename" value="<?php echo wp_create_nonce('cm_event_order'); ?>" />

		 <select id="_cm_event_order" name="_cm_event_order" class=""> <!-- The selected attribute is set with javascript -->
		  <option value="not set">Select order...</option>
		  <option value="hidden">Don't show</option>
		  <option value="10000">Bottom of the list</option>
      	<?php for ( $i=1; $i <= $current_num_events; $i++ ) { ?>
      		<option value="<?php echo $i; ?>"> <?php echo $i; ?> </option>
          <?php } //end for loop ?>
		</select>
    </div>
</fieldset>

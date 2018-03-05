<?php
namespace CarmeMias\FAQsFunctionality\src\views;

	$current_num_faqs = wp_count_posts('cm_faq')->publish;
?>

<fieldset class="inline-edit-col-right"> <!-- TODO make translatable -->
    <div class="inline-edit-col">
        <span class="title">FAQ Order</span>
        <input type="hidden" name="cm_faq_noncename" id="cm_faq_noncename" value="<?php echo wp_create_nonce('cm_faq_order'); ?>" />

		 <select id="_cm_faq_order" name="_cm_faq_order" class=""> <!-- The selected attribute is set with javascript -->
		  <option value="not set">Select order...</option>
		  <option value="hidden">Don't show</option>
		  <option value="10000">Bottom of the list</option>
      	<?php for ( $i=1; $i <= $current_num_faqs; $i++ ) { ?>
      		<option value="<?php echo $i; ?>"> <?php echo $i; ?> </option>
          <?php } //end for loop ?>
		</select>
    </div>
</fieldset>

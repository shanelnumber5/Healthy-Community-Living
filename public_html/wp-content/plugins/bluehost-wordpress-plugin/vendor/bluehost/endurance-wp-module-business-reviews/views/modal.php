<?php
/**
 * Markup for Business Reviews modal window
 */
?>
<div class="eig_modal eig_modal_bg" onclick="eigbr.hideModal();"></div>
<div class="eig_modal eig_modal_content" role="dialog">
	<div class="eig_br_close" onclick="eigbr.hideModal();"><?php echo file_get_contents( plugin_dir_path( dirname( __FILE__ ) ) . 'assets/images/times.svg' ); ?></div>
	<div class="eig_br_list" style="display:none;"></div>
	<div class="eig_br_contact" style="display:none;">
		<h2>Thank you for helping us improve</h2>
		<p>Please tell us more so we can address your concerns.</p>
		<form action="/" method="POST" id="eig_br_contact_form">
			<label for="eig_br_contact_name">Your name<sup>*</sup></label><br />
			<input id="eig_br_contact_name" type="text" name="eig_br_name" value="" placeholder="Joe Smith" /><br />
			<label for="eig_br_contact_email">Email<sup>*</sup></label><br />
			<input id="eig_br_contact_email" type="email" name="eig_br_email" value="" placeholder="joesmith@example.com" /><br />
			<label for="eig_br_contact_message">Message</label><br />
			<textarea id="eig_br_contact_message" id="eig_br_message" name="eig_br_message" value="" placeholder="Please write your feedback here. We look forward to addressing your concerns."></textarea><br />
			<p class="eig_br_toggle_sentence">If you do not want us to address your concerns, <a href="#" onclick="eigbr.doReview()">please continue to reviews</a>.</p>
			<input type="submit" value="Submit" />
		</form>
	</div>
</div>
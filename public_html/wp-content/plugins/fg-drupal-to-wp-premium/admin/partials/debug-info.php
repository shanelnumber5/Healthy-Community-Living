<?php $debuginfo = new FG_Drupal_to_WordPress_DebugInfo(); ?>
<br />
<div style="display: flex; align-items: center;">
	<textarea readonly="readonly" aria-readonly="true" id="debug_info" rows="20" cols="100"><?php $debuginfo->display(); ?></textarea>
	&nbsp;
	<?php submit_button( __('Copy to clipboard', 'fg-drupal-to-wp'), 'primary copy_to_clipboard', 'copy_debug_info', false, array('data-field' => 'debug_info')); ?>
</div>

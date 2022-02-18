		<form id="form_empty_wordpress_content" method="post">
			<?php wp_nonce_field( 'empty', 'fgd2wp_nonce_empty' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e('If you want to restart the import from scratch, you must empty the WordPress content with the button hereafter.', 'fg-drupal-to-wp'); ?></th>
					<td>
						<input type="radio" name="empty_action" id="empty_action_imported" value="imported" /> <label for="empty_action_imported"><?php _e('Remove only previously imported data', 'fg-drupal-to-wp'); ?></label><br />
						<input type="radio" name="empty_action" id="empty_action_all" value="all" /> <label for="empty_action_all"><?php _e('Remove all WordPress content', 'fg-drupal-to-wp'); ?></label><br />
						<div class="submit_button_with_spinner">
							<?php submit_button( __('Empty WordPress content', 'fg-drupal-to-wp'), 'primary', 'empty', false ); ?>
							<span id="empty_spinner" class="spinner"></span>
						</div>
						<div id="empty_message" class="action_message"></div>
					</td>
				</tr>
			</table>
		</form>

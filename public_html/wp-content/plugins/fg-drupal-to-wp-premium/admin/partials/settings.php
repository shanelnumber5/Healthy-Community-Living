				<tr>
					<th scope="row"><?php _e('Automatic removal:', 'fg-drupal-to-wp'); ?></th>
					<td><input id="automatic_empty" name="automatic_empty" type="checkbox" value="1" <?php checked($data['automatic_empty'], 1); ?> /> <label for="automatic_empty" ><?php _e('Automatically remove all the WordPress content before each import', 'fg-drupal-to-wp'); ?></label></td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h3><?php _e('Drupal web site parameters', 'fg-drupal-to-wp'); ?></h3></th>
				</tr>
				<tr>
					<th scope="row"><label for="url"><?php _e('URL of the live Drupal web site', 'fg-drupal-to-wp'); ?></label></th>
					<td><input id="url" name="url" type="text" size="50" value="<?php echo esc_attr($data['url']); ?>" /><br />
						<small><?php _e('This field is used to pull the media off that site. It must contain the URL of the original site.', 'fg-drupal-to-wp'); ?></small>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Download the media by:', 'fg-drupal-to-wp'); ?></th>
					<td>
						<input id="download_protocol_http" name="download_protocol" type="radio" value="http" <?php checked($data['download_protocol'], 'http'); ?> /> <label for="download_protocol_http"><?php _e('HTTP', 'fg-drupal-to-wp'); ?></label> <small><?php _e('(Default)', 'fg-drupal-to-wp'); ?></small>&nbsp;&nbsp;
						<input id="download_protocol_ftp" name="download_protocol" type="radio" value="ftp" <?php checked($data['download_protocol'], 'ftp'); ?> /> <label for="download_protocol_ftp"><?php _e('FTP', 'fg-drupal-to-wp'); ?></label>&nbsp;&nbsp;
						<input id="download_protocol_file_system" name="download_protocol" type="radio" value="file_system" <?php checked($data['download_protocol'], 'file_system'); ?> /> <label for="download_protocol_file_system"><?php _e('File system', 'fg-drupal-to-wp'); ?></label> <small><?php _e('(Faster, but WordPress must be installed on the same server as the original site)', 'fg-drupal-to-wp'); ?></small>&nbsp;&nbsp;
					</td>
				</tr>
				<tr class="file_system_parameters">
					<th scope="row"><label for="base_dir"><?php _e('Drupal base directory', 'fg-drupal-to-wp'); ?></label></th>
					<td><input id="base_dir" name="base_dir" type="text" size="50" value="<?php echo esc_attr($data['base_dir']); ?>" /></td>
				</tr>
				<tr class="test_media">
					<th scope="row">&nbsp;</th>
					<td><?php submit_button( __('Test the media connection', 'fg-drupal-to-wp'), 'secondary', 'test_download' ); ?>
					<span id="download_test_message" class="action_message"></span></td>
				</tr>

				<tr class="ftp_parameters">
					<th scope="row" colspan="2"><h3><?php _e('Drupal FTP parameters', 'fg-drupal-to-wp'); ?></h3></th>
				</tr>
				<tr class="ftp_parameters">
					<th scope="row"><label for="ftp_host"><?php _e('FTP host', 'fg-drupal-to-wp'); ?></label></th>
					<td><input id="ftp_host" name="ftp_host" type="text" size="50" value="<?php echo esc_attr($data['ftp_host']); ?>" /></td>
				</tr>
				<tr class="ftp_parameters">
					<th scope="row"><label for="ftp_port"><?php _e('FTP port', 'fg-drupal-to-wp'); ?></label></th>
					<td><input id="ftp_port" name="ftp_port" type="text" size="50" value="<?php echo esc_attr($data['ftp_port']); ?>" /></td>
				</tr>
				<tr class="ftp_parameters">
					<th scope="row"><label for="ftp_login"><?php _e('FTP login', 'fg-drupal-to-wp'); ?></label></th>
					<td><input id="ftp_login" name="ftp_login" type="text" size="50" value="<?php echo esc_attr($data['ftp_login']); ?>" /></td>
				</tr>
				<tr class="ftp_parameters">
					<th scope="row"><label for="ftp_password"><?php _e('FTP password', 'fg-drupal-to-wp'); ?></label></th>
					<td><input id="ftp_password" name="ftp_password" type="password" size="50" value="<?php echo esc_attr($data['ftp_password']); ?>" /></td>
				</tr>
				<tr class="ftp_parameters">
					<th scope="row"><?php _e('Protocol', 'fg-drupal-to-wp'); ?></th>
					<td>
						<input id="ftp_connection_type_ftp" name="ftp_connection_type" type="radio" value="ftp" <?php checked($data['ftp_connection_type'], 'ftp'); ?> /> <label for="ftp_connection_type_ftp" ><?php _e('FTP', 'fg-drupal-to-wp'); ?></label>&nbsp;
						<input id="ftp_connection_type_ftps" name="ftp_connection_type" type="radio" value="ftps" <?php checked($data['ftp_connection_type'], 'ftps'); ?> /> <label for="ftp_connection_type_ftps" ><?php _e('FTPS', 'fg-drupal-to-wp'); ?></label>&nbsp;
						<input id="ftp_connection_type_sftp" name="ftp_connection_type" type="radio" value="sftp" <?php checked($data['ftp_connection_type'], 'sftp'); ?> /> <label for="ftp_connection_type_sftp" ><?php _e('SFTP', 'fg-drupal-to-wp'); ?></label> <small><?php printf(__('(SFTP requires the <a href="%s" target="_blank">WP Filesystem SSH2</a> plugin)', 'fg-drupal-to-wp'), 'https://www.fredericgilles.net/wp-filesystem-ssh2/'); ?></small>
					</td>
				</tr>
				<tr class="ftp_parameters">
					<th scope="row"><label for="ftp_dir"><?php _e('FTP base directory', 'fg-drupal-to-wp'); ?></label></th>
					<td><input id="ftp_dir" name="ftp_dir" type="text" size="50" value="<?php echo esc_attr($data['ftp_dir']); ?>" /></td>
				</tr>
				<tr class="ftp_parameters">
					<th scope="row">&nbsp;</th>
					<td><?php submit_button( __('Test the FTP connection', 'fg-drupal-to-wp'), 'secondary', 'test_ftp' ); ?>
					<span id="ftp_test_message" class="action_message"></span></td>
				</tr>
